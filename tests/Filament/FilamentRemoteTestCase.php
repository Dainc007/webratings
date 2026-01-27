<?php

declare(strict_types=1);

namespace Tests\Filament;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/**
 * Base test case for remote HTTP tests against Filament admin panel.
 *
 * This test case handles:
 * - Configuration loading from environment variables
 * - HTTP client with cookie persistence for session management
 * - Filament admin authentication via Livewire
 * - Server hibernation handling with configurable timeout
 */
abstract class FilamentRemoteTestCase extends TestCase
{
    protected Client $httpClient;

    protected CookieJar $cookieJar;

    protected string $baseUrl;

    protected string $email;

    protected string $password;

    protected int $timeout;

    protected bool $isAuthenticated = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadConfiguration();
        $this->initializeHttpClient();
    }

    /**
     * Load configuration from environment variables.
     */
    protected function loadConfiguration(): void
    {
        // Load from .env.testing.remote if it exists
        $envFile = base_path('.env.testing.remote');
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }

        $this->baseUrl = rtrim($_ENV['REMOTE_TEST_URL'] ?? 'https://webratings.laravel.cloud', '/');
        $this->email = $_ENV['REMOTE_TEST_EMAIL'] ?? 'test@example.com';
        $this->password = $_ENV['REMOTE_TEST_PASSWORD'] ?? 'password';
        $this->timeout = (int) ($_ENV['REMOTE_TEST_TIMEOUT'] ?? 30);
    }

    /**
     * Initialize the HTTP client with cookie persistence.
     */
    protected function initializeHttpClient(): void
    {
        $this->cookieJar = new CookieJar();

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'cookies' => $this->cookieJar,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->timeout,
            'http_errors' => false,
            'verify' => true,
            'allow_redirects' => [
                'max' => 5,
                'strict' => false,
                'referer' => true,
                'track_redirects' => true,
            ],
            'headers' => [
                'User-Agent' => 'WebRatings-Test-Suite/1.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ],
        ]);
    }

    /**
     * Authenticate with the Filament admin panel using Livewire.
     *
     * @throws GuzzleException
     */
    protected function authenticate(): void
    {
        if ($this->isAuthenticated) {
            return;
        }

        // First, fetch the login page to get session cookies and Livewire data
        // Use longer timeout for first request due to server hibernation
        $loginPageResponse = $this->httpClient->get('/admin/login', [
            'timeout' => $this->timeout * 2, // Double timeout for hibernation
        ]);

        $this->assertResponseOk($loginPageResponse, 'Failed to load login page');

        $loginPageHtml = (string) $loginPageResponse->getBody();

        // Extract CSRF token
        $csrfToken = $this->extractCsrfToken($loginPageHtml);
        if (empty($csrfToken)) {
            $this->fail('Could not extract CSRF token from login page');
        }

        // Extract the login component's Livewire snapshot
        $loginComponent = $this->extractLoginComponentSnapshot($loginPageHtml);
        if (empty($loginComponent)) {
            $this->fail('Could not extract Livewire login component from login page');
        }

        // Create the Livewire request payload
        // Livewire 3 expects the snapshot as a JSON string
        $payload = [
            'components' => [
                [
                    'snapshot' => $loginComponent['snapshot'],
                    'updates' => new \stdClass(), // Empty object
                    'calls' => [
                        [
                            'path' => '',
                            'method' => 'authenticate',
                            'params' => [],
                        ],
                    ],
                ],
            ],
        ];

        // Send Livewire update request
        $livewireResponse = $this->httpClient->post('/livewire/update', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'text/html, application/xhtml+xml',
                'X-CSRF-TOKEN' => $csrfToken,
                'X-Livewire' => 'true',
                'Referer' => $this->baseUrl . '/admin/login',
            ],
            'json' => $payload,
        ]);

        $livewireStatusCode = $livewireResponse->getStatusCode();
        $livewireBody = (string) $livewireResponse->getBody();

        // Check if authentication was successful
        if ($livewireStatusCode !== 200) {
            $this->fail("Livewire authentication failed with status code: {$livewireStatusCode}. Response: " . substr($livewireBody, 0, 500));
        }

        // Check if response contains a redirect (successful login)
        $responseData = json_decode($livewireBody, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Check for redirect in the response
            if (isset($responseData['components'][0]['effects']['redirect'])) {
                $this->isAuthenticated = true;
                return;
            }
        }

        // Verify we're actually logged in by checking if we can access the admin panel
        $adminResponse = $this->httpClient->get('/admin');
        $adminStatusCode = $adminResponse->getStatusCode();
        $adminBody = (string) $adminResponse->getBody();

        // Check if we're on the admin page (not redirected to login)
        if ($adminStatusCode === 200 && ! str_contains($adminBody, 'admin/login')) {
            $this->isAuthenticated = true;
            return;
        }

        // If we got redirected to login, authentication failed
        if (str_contains($adminBody, 'admin/login')) {
            $this->fail('Authentication failed - redirected back to login page. Livewire response: ' . substr($livewireBody, 0, 1000));
        }

        $this->isAuthenticated = true;
    }

    /**
     * Extract CSRF token from HTML page.
     */
    protected function extractCsrfToken(string $html): ?string
    {
        // Try to find token in meta tag
        if (preg_match('/<meta\s+name="csrf-token"\s+content="([^"]+)"/i', $html, $matches)) {
            return $matches[1];
        }

        // Try to find token in hidden input field
        if (preg_match('/<input[^>]*name="_token"[^>]*value="([^"]+)"/i', $html, $matches)) {
            return $matches[1];
        }

        // Try alternative pattern for hidden input
        if (preg_match('/<input[^>]*value="([^"]+)"[^>]*name="_token"/i', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract Livewire login component snapshot from HTML page.
     *
     * @return array{snapshot: string, id: string}|null
     */
    protected function extractLoginComponentSnapshot(string $html): ?array
    {
        // Look for the login component specifically (app.filament.pages.login)
        // The pattern matches the wire:snapshot attribute containing the login component
        if (preg_match('/wire:snapshot="([^"]+)"[^>]*wire:id="([^"]+)"[^>]*class="fi-simple-page"/i', $html, $matches)) {
            return [
                'snapshot' => html_entity_decode($matches[1], ENT_QUOTES),
                'id' => $matches[2],
            ];
        }

        // Alternative: look for any snapshot that contains the login page name
        if (preg_match_all('/wire:snapshot="([^"]+)"/i', $html, $allMatches)) {
            foreach ($allMatches[1] as $snapshot) {
                $decoded = html_entity_decode($snapshot, ENT_QUOTES);
                if (str_contains($decoded, 'login') && str_contains($decoded, 'email')) {
                    // Extract the wire:id from the same element
                    $escapedSnapshot = preg_quote($snapshot, '/');
                    if (preg_match('/wire:snapshot="' . $escapedSnapshot . '"[^>]*wire:id="([^"]+)"/i', $html, $idMatch)) {
                        return [
                            'snapshot' => $decoded,
                            'id' => $idMatch[1],
                        ];
                    }
                    // If we can't find the ID in the same pattern, search separately
                    return [
                        'snapshot' => $decoded,
                        'id' => '',
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Make a GET request to the given URL.
     *
     * @throws GuzzleException
     */
    protected function getUrl(string $url): ResponseInterface
    {
        $this->authenticate();

        return $this->httpClient->get($url);
    }

    /**
     * Make a POST request to the given URL.
     *
     * @throws GuzzleException
     */
    protected function postUrl(string $url, array $formParams = []): ResponseInterface
    {
        $this->authenticate();

        // Get CSRF token from a page first
        $pageResponse = $this->httpClient->get($url);
        $csrfToken = $this->extractCsrfToken((string) $pageResponse->getBody());

        $formParams['_token'] = $csrfToken;

        return $this->httpClient->post($url, [
            'form_params' => $formParams,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Referer' => $this->baseUrl . $url,
            ],
        ]);
    }

    /**
     * Assert that the response has a successful status code (2xx or 3xx).
     */
    protected function assertResponseOk(ResponseInterface $response, string $message = ''): void
    {
        $statusCode = $response->getStatusCode();
        $this->assertTrue(
            $statusCode >= 200 && $statusCode < 400,
            $message ?: "Expected successful response, got {$statusCode}"
        );
    }

    /**
     * Assert that the response status code is exactly 200.
     */
    protected function assertResponseIs200(ResponseInterface $response, string $message = ''): void
    {
        $statusCode = $response->getStatusCode();
        $this->assertEquals(
            200,
            $statusCode,
            $message ?: "Expected status 200, got {$statusCode}"
        );
    }

    /**
     * Assert that the response is not a server error (5xx) or not found (404).
     */
    protected function assertNotErrorResponse(ResponseInterface $response, string $url): void
    {
        $statusCode = $response->getStatusCode();

        $this->assertNotEquals(
            404,
            $statusCode,
            "URL '{$url}' returned 404 Not Found"
        );

        $this->assertFalse(
            $statusCode >= 500,
            "URL '{$url}' returned server error: {$statusCode}"
        );
    }

    /**
     * Get the base URL for tests.
     */
    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
