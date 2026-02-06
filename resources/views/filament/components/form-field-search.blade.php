<div
    wire:ignore
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-form-field-search relative'])
    }}
>
    <div x-data="formFieldSearch" x-on:click.outside="close()">
        {{-- Search Input - using Filament's native fi-input-wrp structure --}}
        <div class="fi-input-wrp">
            <div class="fi-input-wrp-prefix fi-input-wrp-prefix-has-content fi-inline">
                <svg class="fi-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <div class="fi-input-wrp-content-ctn">
                <input
                    x-ref="searchInput"
                    type="search"
                    autocomplete="off"
                    x-model="query"
                    x-on:input.debounce.200ms="search()"
                    x-on:focus="onFocus()"
                    x-on:keydown.arrow-down.prevent="moveDown()"
                    x-on:keydown.arrow-up.prevent="moveUp()"
                    x-on:keydown.enter.prevent="selectCurrent()"
                    x-on:keydown.escape="close(); $refs.searchInput.blur()"
                    placeholder="Search form fields..."
                    class="fi-input fi-input-has-inline-prefix"
                />
            </div>
            <div class="fi-input-wrp-suffix fi-inline">
                <span class="fi-input-wrp-label">/</span>
            </div>
        </div>

        {{-- Results Dropdown --}}
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            x-cloak
            class="absolute z-50 mt-1 w-full overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        >
            <ul x-ref="resultsList" class="max-h-64 overflow-y-auto py-1" role="listbox" style="margin: 0; padding: 0.25rem 0; list-style: none;">
                <template x-for="(result, index) in results" :key="result.id">
                    <li
                        role="option"
                        :aria-selected="selectedIndex === index"
                        x-on:click="navigateToResult(result)"
                        x-on:mouseenter="selectedIndex = index"
                        :class="selectedIndex === index
                            ? 'bg-gray-50 dark:bg-white/5'
                            : ''"
                        class="flex cursor-pointer items-center justify-between gap-3 px-3 py-2 text-sm transition-colors"
                    >
                        <span class="min-w-0 truncate text-gray-700 dark:text-gray-200" x-html="highlightMatch(result.label)"></span>
                        <div class="flex shrink-0 items-center gap-1.5">
                            <span
                                x-show="result.type === 'tab'"
                                class="rounded-md bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-600 dark:bg-primary-400/10 dark:text-primary-400"
                            >Tab</span>
                            <span
                                x-show="result.type === 'section'"
                                class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                            >Section</span>
                            <span
                                x-show="result.tab && result.type === 'field'"
                                x-text="result.tab"
                                class="rounded-md bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-600 dark:bg-primary-400/10 dark:text-primary-400"
                            ></span>
                        </div>
                    </li>
                </template>
            </ul>

            {{-- Footer --}}
            <div class="flex items-center gap-3 border-t border-gray-100 px-3 py-1.5 text-[11px] text-gray-400 dark:border-white/5 dark:text-gray-500">
                <span class="inline-flex items-center gap-1">
                    <kbd class="rounded border border-gray-300 px-1 font-mono dark:border-gray-600">&uarr;</kbd>
                    <kbd class="rounded border border-gray-300 px-1 font-mono dark:border-gray-600">&darr;</kbd>
                    navigate
                </span>
                <span class="inline-flex items-center gap-1">
                    <kbd class="rounded border border-gray-300 px-1 font-mono dark:border-gray-600">&crarr;</kbd>
                    select
                </span>
                <span class="inline-flex items-center gap-1">
                    <kbd class="rounded border border-gray-300 px-1 font-mono dark:border-gray-600">esc</kbd>
                    close
                </span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    if (window.__formFieldSearchRegistered) return;
    window.__formFieldSearchRegistered = true;

    function register() {
        Alpine.data('formFieldSearch', () => ({
            query: '',
            results: [],
            isOpen: false,
            selectedIndex: 0,

            init() {
                this._keyHandler = (e) => {
                    if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName) && !document.activeElement.isContentEditable) {
                        e.preventDefault();
                        this.$refs.searchInput.focus();
                    }
                };
                window.addEventListener('keydown', this._keyHandler);
            },

            destroy() {
                if (this._keyHandler) {
                    window.removeEventListener('keydown', this._keyHandler);
                }
            },

            onFocus() {
                if (this.query.length >= 2 && this.results.length > 0) {
                    this.isOpen = true;
                }
            },

            search() {
                if (!this.query || this.query.length < 2) {
                    this.results = [];
                    this.isOpen = false;
                    return;
                }

                var form = this.$root.closest('form') || document.querySelector('form');
                if (!form) return;

                // Clean previous markers
                var old = form.querySelectorAll('[data-ffs-id]');
                for (var m = 0; m < old.length; m++) {
                    old[m].removeAttribute('data-ffs-id');
                }

                var q = this.query.toLowerCase();
                var results = [];
                var id = 0;

                // ── 1. Search TAB names ──
                var tabButtons = form.querySelectorAll('.fi-tabs-item');
                for (var t = 0; t < tabButtons.length; t++) {
                    var tabBtn = tabButtons[t];
                    var labelSpan = tabBtn.querySelector('.fi-tabs-item-label');
                    var tabText = labelSpan ? labelSpan.textContent.trim() : tabBtn.textContent.trim();

                    if (!tabText || tabText.length < 2) continue;
                    if (!tabText.toLowerCase().includes(q)) continue;

                    tabBtn.setAttribute('data-ffs-id', String(id));

                    results.push({
                        id: id++,
                        label: tabText,
                        tab: '',
                        type: 'tab',
                    });
                }

                // ── 2. Search SECTION headings ──
                var headings = form.querySelectorAll('.fi-section-header-heading');
                for (var s = 0; s < headings.length; s++) {
                    var heading = headings[s];
                    var headingText = heading.textContent.trim();

                    if (!headingText || headingText.length < 2) continue;
                    if (!headingText.toLowerCase().includes(q)) continue;

                    var sectionEl = heading.closest('section') || heading.closest('.fi-sc-section');
                    if (!sectionEl) continue;

                    sectionEl.setAttribute('data-ffs-id', String(id));

                    // Find parent tab name
                    var sTabName = this._getTabName(sectionEl);

                    results.push({
                        id: id++,
                        label: headingText,
                        tab: sTabName,
                        type: 'section',
                    });
                }

                // ── 3. Search FIELD labels ──
                var labels = form.querySelectorAll('label');
                for (var i = 0; i < labels.length; i++) {
                    var label = labels[i];
                    var text = label.textContent.trim();

                    if (!text || text.length < 2) continue;
                    if (!text.toLowerCase().includes(q)) continue;

                    var wrapper = label.closest('[data-field-wrapper]') || label.closest('.fi-fo-field') || label.parentElement;
                    if (!wrapper) continue;

                    wrapper.setAttribute('data-ffs-id', String(id));

                    var tabName = this._getTabName(wrapper);

                    results.push({
                        id: id++,
                        label: text,
                        tab: tabName,
                        type: 'field',
                    });
                }

                this.results = results.slice(0, 15);
                this.isOpen = this.results.length > 0;
                this.selectedIndex = 0;
            },

            _getTabName(el) {
                var tabPanel = el.closest('[role="tabpanel"]');
                if (!tabPanel) return '';

                var tabsContainer = tabPanel.closest('.fi-sc-tabs');
                if (!tabsContainer) return '';

                var bindClass = tabPanel.getAttribute('x-bind:class') || tabPanel.getAttribute(':class') || '';
                var match = bindClass.match(/tab\s*===\s*'([^']+)'/);
                if (!match || !match[1]) return '';

                var tabBtn = tabsContainer.querySelector('[data-tab-key="' + match[1] + '"]');
                return tabBtn ? tabBtn.textContent.trim() : '';
            },

            navigateToResult(result) {
                var el = document.querySelector('[data-ffs-id="' + result.id + '"]');
                if (!el) return;

                if (result.type === 'tab') {
                    // For tabs: just click the tab button directly
                    el.click();

                    this.isOpen = false;
                    this.query = '';

                    // Scroll to the top of the tab content
                    setTimeout(function() {
                        var tabsContainer = el.closest('.fi-sc-tabs');
                        if (tabsContainer) {
                            tabsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 150);
                    return;
                }

                // For fields and sections:

                // 1. Switch to the correct tab
                var tabPanel = el.closest('[role="tabpanel"]');
                if (tabPanel) {
                    tabPanel.dispatchEvent(new CustomEvent('expand', { bubbles: false }));
                }

                // 2. Expand collapsed sections
                var current = el.parentElement;
                while (current) {
                    if (current.tagName === 'SECTION' && current.classList.contains('fi-collapsible')) {
                        current.dispatchEvent(new CustomEvent('expand', { bubbles: false }));
                    }
                    current = current.parentElement;
                }

                // 3. Close search
                this.isOpen = false;
                this.query = '';

                // 4. Scroll and highlight
                setTimeout(function() {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    el.style.transition = 'box-shadow 0.3s ease';
                    el.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.5)';
                    el.style.borderRadius = '8px';

                    setTimeout(function() {
                        el.style.boxShadow = '';
                        el.style.borderRadius = '';
                    }, 2000);

                    // Focus input for fields
                    if (result.type === 'field') {
                        var input = el.querySelector('input, select, textarea');
                        if (input && typeof input.focus === 'function') {
                            input.focus();
                        }
                    }
                }, 250);
            },

            moveDown() {
                if (this.selectedIndex < this.results.length - 1) {
                    this.selectedIndex++;
                    this.scrollResultIntoView();
                }
            },

            moveUp() {
                if (this.selectedIndex > 0) {
                    this.selectedIndex--;
                    this.scrollResultIntoView();
                }
            },

            scrollResultIntoView() {
                var self = this;
                this.$nextTick(function() {
                    var list = self.$refs.resultsList;
                    if (list && list.children[self.selectedIndex]) {
                        list.children[self.selectedIndex].scrollIntoView({ block: 'nearest' });
                    }
                });
            },

            selectCurrent() {
                if (this.results.length > 0 && this.results[this.selectedIndex]) {
                    this.navigateToResult(this.results[this.selectedIndex]);
                }
            },

            close() {
                this.isOpen = false;
            },

            highlightMatch(text) {
                if (!this.query) return text;
                var escaped = this.query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                var regex = new RegExp('(' + escaped + ')', 'gi');
                return text.replace(regex, '<mark class="bg-yellow-200 rounded px-0.5 dark:bg-yellow-500/20">$1</mark>');
            },
        }));
    }

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', register);
    }
})();
</script>
