<x-filament-panels::page>
<div class="fle-editor" @if($hasPendingFields) wire:poll.3s="checkPendingFields" @endif>

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

        @if($hasPendingFields)
            <div class="mt-3 flex items-center gap-2 text-sm text-warning-600 dark:text-warning-400">
                <x-filament::loading-indicator class="h-4 w-4" />
                Trwa przetwarzanie migracji...
            </div>
        @endif
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

                        {{-- Add field button --}}
                        <button
                            class="fle-add-field-btn"
                            wire:click="openAddFieldModal({{ $tabIndex }}, {{ $sectionIndex }})"
                            wire:sort:ignore
                            title="Dodaj nowe pole do tej sekcji"
                        >
                            <x-filament::icon icon="heroicon-m-plus" class="fle-add-field-icon" />
                        </button>

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
                        <div class="fle-field-item {{ ($field['is_custom'] ?? false) ? 'fle-field-custom' : '' }}"
                             wire:key="field-{{ $field['key'] }}"
                             wire:sort:item="{{ $field['key'] }}"
                        >
                            <x-filament::icon
                                icon="heroicon-m-bars-2"
                                class="fle-drag-handle"
                                wire:sort:handle
                            />
                            <code class="fle-field-code" wire:sort:ignore>{{ $field['key'] }}</code>

                            @if(($field['display'] ?? null) && $field['display'] !== $field['key'])
                                <span class="fle-field-label" wire:sort:ignore>{{ $field['display'] }}</span>
                            @endif

                            @if($field['is_custom'] ?? false)
                                <x-filament::badge color="success" size="xs" wire:sort:ignore>Własne</x-filament::badge>

                                <button
                                    class="fle-delete-field-btn"
                                    wire:click="deleteCustomField('{{ $field['key'] }}')"
                                    wire:confirm="Usunąć pole „{{ $field['key'] }}"? Kolumna zostanie usunięta z bazy danych."
                                    wire:sort:ignore
                                    title="Usuń pole"
                                >
                                    <x-filament::icon icon="heroicon-m-trash" class="fle-delete-field-icon" />
                                </button>
                            @endif

                            <span class="fle-index" wire:sort:ignore>{{ $fieldIndex }}</span>
                        </div>
                        @endforeach

                        @if(empty($section['fields']))
                        <p class="fle-fields-empty">Przeciągnij pola tutaj</p>
                        @endif
                    </div>

                </div>
                @endforeach

                {{-- Add Section button --}}
                <button
                    class="fle-add-card-btn"
                    wire:click="addSection({{ $tabIndex }})"
                    title="Dodaj nową sekcję do tej zakładki"
                >
                    <x-filament::icon icon="heroicon-m-plus" class="fle-add-card-icon" />
                    <span>Dodaj sekcję</span>
                </button>

            </div>

        </section>
        @endforeach

    </div>

    {{-- Add Tab button --}}
    <button
        class="fle-add-card-btn fle-add-tab-btn"
        wire:click="addTab"
        title="Dodaj nową zakładkę"
    >
        <x-filament::icon icon="heroicon-m-plus" class="fle-add-card-icon" />
        <span>Dodaj zakładkę</span>
    </button>
    @endif

    {{-- ── Add Custom Field Modal ───────────────────────────────────────── --}}
    @if($showAddFieldModal)
    <div class="fle-modal-backdrop" x-data x-on:keydown.escape.window="$wire.set('showAddFieldModal', false)">
        <div class="fle-modal">
            <h3 class="fle-modal-title">Dodaj nowe pole</h3>

            @if(!empty($layoutTree))
                <p class="fle-modal-context">
                    Sekcja: <strong>{{ $layoutTree[$targetTabIndex]['sections'][$targetSectionIndex]['display'] ?? '—' }}</strong>
                    w zakładce: <strong>{{ $layoutTree[$targetTabIndex]['display'] ?? '—' }}</strong>
                </p>
            @endif

            <div class="fle-modal-fields">
                <div class="fle-modal-field">
                    <label class="fle-modal-label">Nazwa kolumny (ang., małe litery)</label>
                    <input
                        type="text"
                        wire:model="newFieldColumnName"
                        class="fle-modal-input"
                        placeholder="np. energy_class"
                    />
                </div>

                <div class="fle-modal-field">
                    <label class="fle-modal-label">Wyświetlana nazwa</label>
                    <input
                        type="text"
                        wire:model="newFieldDisplayName"
                        class="fle-modal-input"
                        placeholder="np. Klasa energetyczna"
                    />
                </div>

                <div class="fle-modal-field">
                    <label class="fle-modal-label">Typ pola</label>
                    <select wire:model="newFieldType" class="fle-modal-input">
                        <option value="string">Tekst (do 255 znaków)</option>
                        <option value="integer">Liczba</option>
                        <option value="boolean">TAK/NIE</option>
                    </select>
                </div>
            </div>

            <div class="fle-modal-actions">
                <x-filament::button
                    wire:click="addCustomField"
                    color="success"
                    size="sm"
                    icon="heroicon-m-plus"
                >
                    Dodaj pole
                </x-filament::button>

                <x-filament::button
                    wire:click="$set('showAddFieldModal', false)"
                    color="gray"
                    size="sm"
                >
                    Anuluj
                </x-filament::button>
            </div>
        </div>
    </div>
    @endif

</div>
</x-filament-panels::page>
