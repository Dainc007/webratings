<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Product selector --}}
        <div class="flex items-center gap-4">
            <div class="w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produkt</label>
                <select
                    wire:model.live="selectedTable"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    @foreach($this->getProductOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 pt-6">
                <button
                    wire:click="seedLayout"
                    wire:confirm="Załadować domyślny układ? Istniejące pozycje zostaną zaktualizowane."
                    class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition"
                >
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Załaduj domyślny układ
                </button>
            </div>
        </div>

        @if(empty($layoutTree))
            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
                <x-heroicon-o-squares-2x2 class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Brak układu</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kliknij "Załaduj domyślny układ" aby zainicjalizować strukturę formularza.
                </p>
            </div>
        @else
            {{-- Tree editor --}}
            <div
                x-data="formLayoutEditor(@js($layoutTree))"
                class="space-y-4"
            >
                {{-- Save button --}}
                <div class="flex justify-end">
                    <button
                        @click="save()"
                        class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg bg-success-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-success-500 transition"
                    >
                        <x-heroicon-o-check class="w-4 h-4" />
                        Zapisz układ
                    </button>
                </div>

                {{-- Tabs level --}}
                <div x-ref="tabsContainer" class="space-y-3">
                    <template x-for="(tab, tabIndex) in tree" :key="tab.key">
                        <div class="rounded-xl border border-primary-300 dark:border-primary-700 bg-primary-50 dark:bg-primary-900/20 overflow-hidden">
                            {{-- Tab header --}}
                            <div
                                class="flex items-center gap-2 px-4 py-3 bg-primary-100 dark:bg-primary-800/40 cursor-grab"
                                :data-tab-index="tabIndex"
                            >
                                <x-heroicon-o-bars-3 class="w-4 h-4 text-primary-400 handle-tab" />
                                <span class="inline-flex items-center rounded-md bg-primary-100 dark:bg-primary-800 px-2 py-1 text-xs font-medium text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
                                    Zakładka
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white" x-text="tab.key"></span>
                                <span class="ml-auto text-xs text-gray-500" x-text="'#' + tabIndex"></span>
                            </div>

                            {{-- Sections inside this tab --}}
                            <div class="p-3 space-y-2 sections-container" :data-tab-index="tabIndex">
                                <template x-for="(section, sectionIndex) in tab.sections" :key="section.key">
                                    <div class="rounded-lg border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 overflow-hidden section-item">
                                        {{-- Section header --}}
                                        <div class="flex items-center gap-2 px-3 py-2 bg-amber-100 dark:bg-amber-800/40 cursor-grab">
                                            <x-heroicon-o-bars-3 class="w-4 h-4 text-amber-400 handle-section" />
                                            <span class="inline-flex items-center rounded-md bg-amber-100 dark:bg-amber-800 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20">
                                                Sekcja
                                            </span>
                                            <span class="font-medium text-gray-800 dark:text-gray-200 text-sm" x-text="section.key"></span>
                                            <span class="ml-auto text-xs text-gray-500" x-text="'#' + sectionIndex"></span>
                                        </div>

                                        {{-- Fields inside this section --}}
                                        <div class="p-2 space-y-1 fields-container min-h-[2rem]" :data-tab-index="tabIndex" :data-section-index="sectionIndex">
                                            <template x-for="(field, fieldIndex) in section.fields" :key="field.key">
                                                <div class="flex items-center gap-2 px-3 py-1.5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm cursor-grab field-item">
                                                    <x-heroicon-o-bars-3 class="w-3 h-3 text-gray-400 handle-field" />
                                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600">
                                                        Pole
                                                    </span>
                                                    <code class="text-xs text-gray-700 dark:text-gray-300" x-text="field.key"></code>
                                                    <span class="ml-auto text-xs text-gray-400" x-text="'#' + fieldIndex"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formLayoutEditor', (initialTree) => ({
                tree: initialTree,

                init() {
                    this.$nextTick(() => this.initSortables());
                },

                initSortables() {
                    // Tab-level sorting
                    const tabsContainer = this.$refs.tabsContainer;
                    if (tabsContainer) {
                        new Sortable(tabsContainer, {
                            animation: 200,
                            handle: '.handle-tab',
                            onEnd: (evt) => {
                                const item = this.tree.splice(evt.oldIndex, 1)[0];
                                this.tree.splice(evt.newIndex, 0, item);
                            },
                        });
                    }

                    // Section-level sorting (within and between tabs)
                    this.$el.querySelectorAll('.sections-container').forEach((container) => {
                        new Sortable(container, {
                            animation: 200,
                            handle: '.handle-section',
                            group: 'sections',
                            draggable: '.section-item',
                            onEnd: (evt) => {
                                const fromTabIndex = parseInt(evt.from.dataset.tabIndex);
                                const toTabIndex = parseInt(evt.to.dataset.tabIndex);
                                const item = this.tree[fromTabIndex].sections.splice(evt.oldIndex, 1)[0];
                                this.tree[toTabIndex].sections.splice(evt.newIndex, 0, item);
                            },
                        });
                    });

                    // Field-level sorting (within and between sections)
                    this.$el.querySelectorAll('.fields-container').forEach((container) => {
                        new Sortable(container, {
                            animation: 200,
                            handle: '.handle-field',
                            group: 'fields',
                            draggable: '.field-item',
                            onEnd: (evt) => {
                                const fromTabIdx = parseInt(evt.from.dataset.tabIndex);
                                const fromSecIdx = parseInt(evt.from.dataset.sectionIndex);
                                const toTabIdx = parseInt(evt.to.dataset.tabIndex);
                                const toSecIdx = parseInt(evt.to.dataset.sectionIndex);

                                const item = this.tree[fromTabIdx].sections[fromSecIdx].fields.splice(evt.oldIndex, 1)[0];
                                this.tree[toTabIdx].sections[toSecIdx].fields.splice(evt.newIndex, 0, item);
                            },
                        });
                    });
                },

                save() {
                    this.$wire.saveTree(this.tree);
                },
            }));
        });
    </script>
    @endpush
</x-filament-panels::page>
