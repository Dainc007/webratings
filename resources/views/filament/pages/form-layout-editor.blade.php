<x-filament-panels::page>
<div class="fle-editor">

    {{-- ── Toolbar ────────────────────────────────────────────────────── --}}
    <x-filament::section>
        <div class="fle-toolbar">
            <x-filament::input.wrapper class="fle-toolbar-select">
                <x-filament::input.select wire:model.live="selectedTable">
                    @foreach($this->getProductOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <x-filament::button
                wire:click="seedLayout"
                wire:confirm="Załadować domyślny układ? Istniejące pozycje zostaną zaktualizowane."
                color="warning"
                size="sm"
                icon="heroicon-m-arrow-down-on-square"
            >
                Załaduj domyślny układ
            </x-filament::button>

            @if(!empty($layoutTree))
                <x-filament::button
                    wire:click="saveTree"
                    color="success"
                    size="sm"
                    icon="heroicon-m-check"
                    class="fle-save-btn"
                >
                    Zapisz kolejność
                </x-filament::button>
            @endif
        </div>
    </x-filament::section>

    {{-- ── Empty state ────────────────────────────────────────────────── --}}
    @if(empty($layoutTree))
        <x-filament::section>
            <div class="fle-empty">
                <div class="fle-empty-icon-wrap">
                    <x-filament::icon icon="heroicon-o-squares-2x2" class="fle-empty-icon" />
                </div>
                <p class="fle-empty-title">Brak układu</p>
                <p class="fle-empty-desc">Wybierz produkt i kliknij „Załaduj domyślny układ".</p>
            </div>
        </x-filament::section>

    @else
    {{-- ── Tab list — wire:sort for tab reordering ─────────────────────── --}}
    <div class="fle-tabs" wire:sort="sortTabs">

        @foreach($layoutTree as $tabIndex => $tab)
        <section class="fi-section fle-tab-card"
                 wire:key="tab-{{ $tab['key'] }}"
                 wire:sort:item="{{ $tab['key'] }}"
                 x-data="{ open: true }"
        >
            {{-- Tab header — entire row is the drag handle --}}
            <div class="fle-row fle-tab-header" wire:sort:handle>

                <x-filament::icon icon="heroicon-m-bars-3" class="fle-drag-handle" />

                <x-filament::badge color="info" size="xs" wire:sort:ignore>Zakładka</x-filament::badge>

                <div class="fle-name-wrap"
                     x-data="{ editing: false, val: @js($tab['display']) }"
                     x-init="$watch('val', v => v)"
                     wire:sort:ignore
                >
                    <span
                        x-show="!editing"
                        x-text="val"
                        @dblclick="editing = true; $nextTick(() => $refs.inp.select())"
                        class="fle-tab-name"
                        title="Kliknij dwukrotnie aby zmienić nazwę"
                    ></span>
                    <input
                        x-ref="inp"
                        x-show="editing"
                        x-cloak
                        x-model="val"
                        @keydown.enter.prevent="editing = false; $wire.renameTab({{ $tabIndex }}, val)"
                        @keydown.escape.prevent="editing = false; val = @js($tab['display'])"
                        @blur="editing = false; $wire.renameTab({{ $tabIndex }}, val)"
                        class="fle-rename-input"
                    />
                </div>

                <span class="fle-index" wire:sort:ignore>#{{ $tabIndex }}</span>

                {{-- Collapse toggle --}}
                <button class="fle-collapse-btn" @click.stop="open = !open" wire:sort:ignore :title="open ? 'Zwiń' : 'Rozwiń'">
                    <x-filament::icon icon="heroicon-m-chevron-up" class="fle-collapse-icon" x-show="open" />
                    <x-filament::icon icon="heroicon-m-chevron-down" class="fle-collapse-icon" x-show="!open" />
                </button>
            </div>

            {{-- Sections grid — collapsible --}}
            <div class="fle-sections-grid" wire:sort="sortSections" x-show="open" x-collapse>

                @foreach($tab['sections'] as $sectionIndex => $section)
                <div class="fle-section-card"
                     wire:key="section-{{ $tab['key'] }}-{{ $section['key'] }}"
                     wire:sort:item="{{ $tabIndex }}:{{ $section['key'] }}"
                     x-data="{ open: true }"
                >
                    {{-- Section header — drag handle for this section --}}
                    <div class="fle-row fle-section-header" wire:sort:handle>

                        <x-filament::icon icon="heroicon-m-bars-3" class="fle-drag-handle" />

                        <x-filament::badge color="warning" size="xs" wire:sort:ignore>Sekcja</x-filament::badge>

                        <div class="fle-name-wrap"
                             x-data="{ editing: false, val: @js($section['display']) }"
                             wire:sort:ignore
                        >
                            <span
                                x-show="!editing"
                                x-text="val"
                                @dblclick="editing = true; $nextTick(() => $refs.sinp.select())"
                                class="fle-section-name"
                                title="Kliknij dwukrotnie aby zmienić nazwę"
                            ></span>
                            <input
                                x-ref="sinp"
                                x-show="editing"
                                x-cloak
                                x-model="val"
                                @keydown.enter.prevent="editing = false; $wire.renameSection({{ $tabIndex }}, {{ $sectionIndex }}, val)"
                                @keydown.escape.prevent="editing = false; val = @js($section['display'])"
                                @blur="editing = false; $wire.renameSection({{ $tabIndex }}, {{ $sectionIndex }}, val)"
                                class="fle-rename-input fle-rename-input-sm"
                            />
                        </div>

                        <span class="fle-index" wire:sort:ignore>#{{ $sectionIndex }}</span>

                        {{-- Collapse toggle --}}
                        <button class="fle-collapse-btn" @click.stop="open = !open" wire:sort:ignore :title="open ? 'Zwiń' : 'Rozwiń'">
                            <x-filament::icon icon="heroicon-m-chevron-up" class="fle-collapse-icon" x-show="open" />
                            <x-filament::icon icon="heroicon-m-chevron-down" class="fle-collapse-icon" x-show="!open" />
                        </button>
                    </div>

                    {{-- Fields list — collapsible, wire:sort:group for cross-section dragging --}}
                    <div class="fle-fields-list"
                         wire:sort="sortFields"
                         wire:sort:group="fle-fields"
                         wire:sort:group-id="{{ $tabIndex }}:{{ $sectionIndex }}"
                         x-show="open"
                         x-collapse
                    >
                        @foreach($section['fields'] as $fieldIndex => $field)
                        <div class="fle-field-item"
                             wire:key="field-{{ $field['key'] }}"
                             wire:sort:item="{{ $field['key'] }}"
                        >
                            <x-filament::icon
                                icon="heroicon-m-bars-2"
                                class="fle-drag-handle"
                                wire:sort:handle
                            />
                            <code class="fle-field-code" wire:sort:ignore>{{ $field['key'] }}</code>
                            <span class="fle-index" wire:sort:ignore>{{ $fieldIndex }}</span>
                        </div>
                        @endforeach

                        @if(empty($section['fields']))
                        <p class="fle-fields-empty">Przeciągnij pola tutaj</p>
                        @endif
                    </div>

                </div>
                @endforeach

            </div>

        </section>
        @endforeach

    </div>
    @endif

</div>
</x-filament-panels::page>
