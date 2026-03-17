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
    {{-- ── Tab list ────────────────────────────────────────────────────── --}}
    <div class="fle-tabs">

        @foreach($layoutTree as $tabIndex => $tab)
        <section class="fi-section fle-tab-card" wire:key="tab-{{ $tabIndex }}">

            {{-- Tab header --}}
            <div class="fle-row fle-tab-header">

                <div class="fle-btn-group">
                    <x-filament::icon-button
                        icon="heroicon-m-chevron-up"
                        color="gray" size="xs"
                        tooltip="Przesuń zakładkę wyżej"
                        wire:click="moveTab({{ $tabIndex }}, -1)"
                        :disabled="$tabIndex === 0"
                    />
                    <x-filament::icon-button
                        icon="heroicon-m-chevron-down"
                        color="gray" size="xs"
                        tooltip="Przesuń zakładkę niżej"
                        wire:click="moveTab({{ $tabIndex }}, 1)"
                        :disabled="$tabIndex === count($layoutTree) - 1"
                    />
                </div>

                <x-filament::badge color="info" size="xs">Zakładka</x-filament::badge>

                <div class="fle-name-wrap"
                     x-data="{ editing: false, val: @js($tab['display']) }"
                     x-init="$watch('val', v => v)"
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

                <span class="fle-index">#{{ $tabIndex }}</span>
            </div>

            {{-- Sections grid --}}
            <div class="fle-sections-grid">

                @foreach($tab['sections'] as $sectionIndex => $section)
                <div class="fle-section-card" wire:key="section-{{ $tabIndex }}-{{ $sectionIndex }}">

                    {{-- Section header --}}
                    <div class="fle-row fle-section-header">
                        <div class="fle-btn-group">
                            <x-filament::icon-button
                                icon="heroicon-m-chevron-up"
                                color="gray" size="xs"
                                tooltip="Przesuń sekcję wyżej"
                                wire:click="moveSection({{ $tabIndex }}, {{ $sectionIndex }}, -1)"
                                :disabled="$sectionIndex === 0"
                            />
                            <x-filament::icon-button
                                icon="heroicon-m-chevron-down"
                                color="gray" size="xs"
                                tooltip="Przesuń sekcję niżej"
                                wire:click="moveSection({{ $tabIndex }}, {{ $sectionIndex }}, 1)"
                                :disabled="$sectionIndex === count($tab['sections']) - 1"
                            />
                        </div>

                        <x-filament::badge color="warning" size="xs">Sekcja</x-filament::badge>

                        <div class="fle-name-wrap"
                             x-data="{ editing: false, val: @js($section['display']) }"
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

                        <span class="fle-index">#{{ $sectionIndex }}</span>
                    </div>

                    {{-- Fields drop zone: uses mouse Y to determine insert position ── --}}
                    <div class="fle-fields-list"
                         x-data="{ over: false, indicatorY: null }"
                         @dragover.prevent="
                             over = true;
                             const items = Array.from($el.querySelectorAll('.fle-field-item'));
                             let y = null;
                             for (let i = 0; i < items.length; i++) {
                                 const r = items[i].getBoundingClientRect();
                                 if ($event.clientY < r.top + r.height / 2) { y = r.top - $el.getBoundingClientRect().top; break; }
                             }
                             indicatorY = y;
                         "
                         @dragleave.self="over = false; indicatorY = null"
                         @drop.prevent="
                             over = false;
                             indicatorY = null;
                             const d = JSON.parse($event.dataTransfer.getData('application/fle-field'));
                             const items = Array.from($el.querySelectorAll('.fle-field-item'));
                             let insertIndex = items.length;
                             for (let i = 0; i < items.length; i++) {
                                 const r = items[i].getBoundingClientRect();
                                 if ($event.clientY < r.top + r.height / 2) { insertIndex = i; break; }
                             }
                             $wire.moveField(d.key, d.fromTab, d.fromSection, {{ $tabIndex }}, {{ $sectionIndex }}, insertIndex);
                         "
                         :class="{ 'fle-drop-target': over }"
                    >
                        {{-- Drop position indicator line --}}
                        <div class="fle-drop-indicator"
                             x-show="over && indicatorY !== null"
                             :style="'top:' + indicatorY + 'px'"
                             x-cloak
                        ></div>

                        @foreach($section['fields'] as $fieldIndex => $field)
                        <div class="fle-field-item"
                             wire:key="field-{{ $tabIndex }}-{{ $sectionIndex }}-{{ $fieldIndex }}"
                             draggable="true"
                             x-data="{ dragging: false }"
                             @dragstart="
                                 dragging = true;
                                 $event.dataTransfer.effectAllowed = 'move';
                                 $event.dataTransfer.setData('application/fle-field', JSON.stringify({
                                     key: '{{ $field['key'] }}',
                                     fromTab: {{ $tabIndex }},
                                     fromSection: {{ $sectionIndex }}
                                 }));
                             "
                             @dragend="dragging = false"
                             :class="{ 'fle-dragging': dragging }"
                        >
                            <x-filament::icon icon="heroicon-m-bars-2" class="fle-drag-handle" />
                            <code class="fle-field-code">{{ $field['key'] }}</code>
                            <span class="fle-index">{{ $fieldIndex }}</span>
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
