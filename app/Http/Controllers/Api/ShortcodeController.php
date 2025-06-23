<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShortcodeProductResource;
use App\Http\Requests\ShortcodeSearchRequest;
use App\Services\ShortcodeService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Shortcode;

class ShortcodeController extends Controller
{
    public function __construct(
        private readonly ShortcodeService $shortcodeService
    ) {
    }

    public function index(ShortcodeSearchRequest $request): AnonymousResourceCollection
    {
        $shortcode = $request->validated('shortcode');

        $shortcodeModel = $this->shortcodeService->findShortcodeByNameOrId($shortcode);
        $results = $this->shortcodeService->executeShortcode($shortcodeModel);

        return ShortcodeProductResource::collection($results);
    }


    public function show(Shortcode $shortcode): AnonymousResourceCollection
    {
        $results = $this->shortcodeService->executeShortcode($shortcode);

        return ShortcodeProductResource::collection($results);
    }
}
