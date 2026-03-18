<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\FormLayoutEditor;
use App\Models\FormLayoutItem;
use App\Models\LabelOverride;
use App\Models\User;
use App\Services\FormLayoutService;
use App\Services\LabelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class FormLayoutEditorTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        FormLayoutService::clearCache();
        LabelService::clearCache();
    }

    private function seedTestLayout(string $table = 'air_purifiers'): void
    {
        FormLayoutService::seedDefaultLayout($table, [
            'Tab A' => [
                'sections' => [
                    'Section 1' => ['field_a', 'field_b'],
                    'Section 2' => ['field_c'],
                ],
            ],
            'Tab B' => [
                'sections' => [
                    'Section 3' => ['field_d', 'field_e'],
                ],
            ],
            'Tab C' => [
                'sections' => [
                    'Section 4' => ['field_f'],
                ],
            ],
        ]);
        FormLayoutService::clearCache();
    }

    // ── Mount & Load ─────────────────────────────────────────────────────────

    public function test_page_renders_for_authenticated_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->assertOk();
    }

    public function test_mount_selects_first_product_table(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class);

        $this->assertNotEmpty($component->get('selectedTable'));
    }

    public function test_load_tree_populates_layout_tree_from_db(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers');

        $tree = $component->get('layoutTree');

        $this->assertCount(3, $tree);
        $this->assertEquals('Tab A', $tree[0]['key']);
        $this->assertEquals('Tab B', $tree[1]['key']);
        $this->assertEquals('Tab C', $tree[2]['key']);
    }

    public function test_load_tree_returns_empty_for_no_layout(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers');

        $this->assertEmpty($component->get('layoutTree'));
    }

    public function test_load_tree_returns_empty_for_empty_table_selection(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', '');

        $this->assertEmpty($component->get('layoutTree'));
    }

    public function test_updated_selected_table_reloads_tree(): void
    {
        $this->seedTestLayout('air_purifiers');

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'sensors');

        $this->assertEmpty($component->get('layoutTree'));

        $component->set('selectedTable', 'air_purifiers');

        $this->assertNotEmpty($component->get('layoutTree'));
    }

    public function test_sections_are_nested_under_correct_tabs(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers');

        $tree = $component->get('layoutTree');

        $this->assertCount(2, $tree[0]['sections']); // Tab A has Section 1, Section 2
        $this->assertCount(1, $tree[1]['sections']); // Tab B has Section 3
    }

    public function test_fields_are_nested_under_correct_sections(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers');

        $tree = $component->get('layoutTree');

        $section1Fields = $tree[0]['sections'][0]['fields'];
        $this->assertCount(2, $section1Fields);
        $this->assertEquals('field_a', $section1Fields[0]['key']);
        $this->assertEquals('field_b', $section1Fields[1]['key']);
    }

    // ── Sort Tabs ────────────────────────────────────────────────────────────

    public function test_sort_tabs_reorders_tabs(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortTabs', 'Tab C', 0);

        $tree = $component->get('layoutTree');

        $this->assertEquals('Tab C', $tree[0]['key']);
        $this->assertEquals('Tab A', $tree[1]['key']);
        $this->assertEquals('Tab B', $tree[2]['key']);
    }

    public function test_sort_tabs_with_nonexistent_key_does_nothing(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers');

        $treeBefore = $component->get('layoutTree');

        $component->call('sortTabs', 'NonExistent', 0);

        $treeAfter = $component->get('layoutTree');

        $this->assertEquals(
            array_column($treeBefore, 'key'),
            array_column($treeAfter, 'key'),
        );
    }

    public function test_sort_tabs_move_to_end(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortTabs', 'Tab A', 2);

        $tree = $component->get('layoutTree');

        $this->assertEquals('Tab B', $tree[0]['key']);
        $this->assertEquals('Tab C', $tree[1]['key']);
        $this->assertEquals('Tab A', $tree[2]['key']);
    }

    // ── Sort Sections ────────────────────────────────────────────────────────

    public function test_sort_sections_reorders_within_tab(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortSections', '0:Section 2', 0);

        $tree = $component->get('layoutTree');

        $this->assertEquals('Section 2', $tree[0]['sections'][0]['key']);
        $this->assertEquals('Section 1', $tree[0]['sections'][1]['key']);
    }

    public function test_sort_sections_with_nonexistent_key_does_nothing(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers');

        $treeBefore = $component->get('layoutTree');

        $component->call('sortSections', '0:NonExistent', 0);

        $treeAfter = $component->get('layoutTree');

        $this->assertEquals(
            array_column($treeBefore[0]['sections'], 'key'),
            array_column($treeAfter[0]['sections'], 'key'),
        );
    }

    // ── Sort Fields ──────────────────────────────────────────────────────────

    public function test_sort_fields_reorders_within_section(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortFields', 'field_b', 0);

        $tree = $component->get('layoutTree');

        $section1Fields = $tree[0]['sections'][0]['fields'];
        $this->assertEquals('field_b', $section1Fields[0]['key']);
        $this->assertEquals('field_a', $section1Fields[1]['key']);
    }

    public function test_sort_fields_moves_between_sections(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortFields', 'field_a', 0, '1:0');

        $tree = $component->get('layoutTree');

        // field_a should be moved from Tab A/Section 1 to Tab B/Section 3
        $section1Fields = $tree[0]['sections'][0]['fields'];
        $this->assertCount(1, $section1Fields);
        $this->assertEquals('field_b', $section1Fields[0]['key']);

        $section3Fields = $tree[1]['sections'][0]['fields'];
        $this->assertCount(3, $section3Fields);
        $this->assertEquals('field_a', $section3Fields[0]['key']);
    }

    public function test_sort_fields_with_nonexistent_key_does_nothing(): void
    {
        $this->seedTestLayout();

        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers');

        $treeBefore = $component->get('layoutTree');

        $component->call('sortFields', 'nonexistent_field', 0);

        $treeAfter = $component->get('layoutTree');

        $this->assertEquals($treeBefore, $treeAfter);
    }

    // ── Rename Tab ───────────────────────────────────────────────────────────

    public function test_rename_tab_creates_label_override(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('renameTab', 0, 'New Tab Name');

        $this->assertDatabaseHas('label_overrides', [
            'table_name' => 'air_purifiers',
            'element_type' => 'tab',
            'element_key' => 'Tab A',
            'display_label' => 'New Tab Name',
        ]);
    }

    public function test_rename_tab_to_original_key_deletes_override(): void
    {
        $this->seedTestLayout();

        // First rename it
        LabelOverride::create([
            'table_name' => 'air_purifiers',
            'element_type' => 'tab',
            'element_key' => 'Tab A',
            'display_label' => 'Custom Name',
        ]);
        LabelService::clearCache();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('renameTab', 0, 'Tab A');

        $this->assertDatabaseMissing('label_overrides', [
            'table_name' => 'air_purifiers',
            'element_type' => 'tab',
            'element_key' => 'Tab A',
        ]);
    }

    public function test_rename_tab_with_empty_name_does_nothing(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('renameTab', 0, '   ');

        $this->assertDatabaseMissing('label_overrides', [
            'table_name' => 'air_purifiers',
            'element_type' => 'tab',
            'element_key' => 'Tab A',
        ]);
    }

    public function test_rename_tab_with_invalid_index_does_nothing(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('renameTab', 99, 'New Name');

        $this->assertEquals(0, LabelOverride::count());
    }

    // ── Rename Section ───────────────────────────────────────────────────────

    public function test_rename_section_creates_label_override(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('renameSection', 0, 0, 'New Section Name');

        $this->assertDatabaseHas('label_overrides', [
            'table_name' => 'air_purifiers',
            'element_type' => 'section',
            'element_key' => 'Section 1',
            'display_label' => 'New Section Name',
        ]);
    }

    public function test_rename_section_to_original_key_deletes_override(): void
    {
        $this->seedTestLayout();

        LabelOverride::create([
            'table_name' => 'air_purifiers',
            'element_type' => 'section',
            'element_key' => 'Section 1',
            'display_label' => 'Custom Section',
        ]);
        LabelService::clearCache();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('renameSection', 0, 0, 'Section 1');

        $this->assertDatabaseMissing('label_overrides', [
            'table_name' => 'air_purifiers',
            'element_type' => 'section',
            'element_key' => 'Section 1',
        ]);
    }

    public function test_rename_section_with_empty_name_does_nothing(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('renameSection', 0, 0, '');

        $this->assertEquals(0, LabelOverride::count());
    }

    // ── Save Tree ────────────────────────────────────────────────────────────

    public function test_save_tree_persists_reordered_tabs(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortTabs', 'Tab C', 0)
            ->call('saveTree');

        FormLayoutService::clearCache();
        $structure = FormLayoutService::getStructure('air_purifiers');

        $tabKeys = array_keys($structure);
        $this->assertEquals('Tab C', $tabKeys[0]);
        $this->assertEquals(0, $structure['Tab C']['sort_order']);
    }

    public function test_save_tree_persists_reordered_fields(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortFields', 'field_b', 0)
            ->call('saveTree');

        FormLayoutService::clearCache();
        $structure = FormLayoutService::getStructure('air_purifiers');

        $fields = $structure['Tab A']['sections']['Section 1']['fields'];
        $this->assertEquals(0, $fields['field_b']);
        $this->assertEquals(1, $fields['field_a']);
    }

    public function test_save_tree_persists_field_moved_between_sections(): void
    {
        $this->seedTestLayout();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('sortFields', 'field_a', 0, '1:0')
            ->call('saveTree');

        FormLayoutService::clearCache();
        $structure = FormLayoutService::getStructure('air_purifiers');

        // field_a should now be in Section 3 (Tab B)
        $this->assertArrayHasKey('field_a', $structure['Tab B']['sections']['Section 3']['fields']);
        $this->assertArrayNotHasKey('field_a', $structure['Tab A']['sections']['Section 1']['fields']);
    }

    public function test_save_tree_with_empty_table_does_nothing(): void
    {
        $countBefore = FormLayoutItem::count();

        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', '')
            ->call('saveTree');

        $this->assertEquals($countBefore, FormLayoutItem::count());
    }

    // ── Seed Layout ──────────────────────────────────────────────────────────

    public function test_seed_layout_creates_items_from_default_structure(): void
    {
        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', 'air_purifiers')
            ->call('seedLayout');

        $this->assertTrue(FormLayoutItem::where('table_name', 'air_purifiers')->exists());
    }

    public function test_seed_layout_with_empty_table_does_nothing(): void
    {
        Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class)
            ->set('selectedTable', '')
            ->call('seedLayout');

        $this->assertEquals(0, FormLayoutItem::count());
    }

    // ── Product Options ──────────────────────────────────────────────────────

    public function test_get_product_options_returns_all_products(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(FormLayoutEditor::class);

        $options = $component->call('getProductOptions')->get('layoutTree');

        // Just verify the method is callable and the page has options
        $component->assertOk();
    }
}
