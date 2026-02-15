<div
    wire:ignore
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-form-field-search'])
    }}
>
    <div class="ffs-wrapper" x-data="formFieldSearch" x-on:click.outside="close()">
        {{-- Search Input --}}
        <div class="ffs-input-box">
            <div class="ffs-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <div class="ffs-input-ctn">
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
                    placeholder="Wpisz nazwę pola którego szukasz..."
                    class="ffs-input"
                />
            </div>
            <div class="ffs-kbd-hint">
                <span>/</span>
            </div>
        </div>

        {{-- Results Dropdown --}}
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="ffs-dropdown"
        >
            <ul x-ref="resultsList" class="ffs-results" role="listbox">
                <template x-for="(result, index) in results" :key="result.id">
                    <li
                        role="option"
                        :aria-selected="selectedIndex === index"
                        x-on:click="navigateToResult(result)"
                        x-on:mouseenter="selectedIndex = index"
                        :class="selectedIndex === index ? 'ffs-result-item ffs-selected' : 'ffs-result-item'"
                    >
                        <span class="ffs-result-label" x-html="highlightMatch(result.label)"></span>
                        <div class="ffs-badge">
                            <span
                                x-show="result.type === 'tab'"
                                class="ffs-badge-tab"
                            >Tab</span>
                            <span
                                x-show="result.type === 'section'"
                                class="ffs-badge-section"
                            >Section</span>
                            <span
                                x-show="result.tab && result.type === 'field'"
                                x-text="result.tab"
                                class="ffs-badge-tab"
                            ></span>
                        </div>
                    </li>
                </template>
            </ul>

            {{-- Footer --}}
            <div class="ffs-footer">
                <span>
                    <kbd>&uarr;</kbd>
                    <kbd>&darr;</kbd>
                    navigate
                </span>
                <span>
                    <kbd>&crarr;</kbd>
                    select
                </span>
                <span>
                    <kbd>esc</kbd>
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
                    el.click();

                    this.isOpen = false;
                    this.query = '';

                    setTimeout(function() {
                        var tabsContainer = el.closest('.fi-sc-tabs');
                        if (tabsContainer) {
                            tabsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 150);
                    return;
                }

                // For fields and sections:
                var tabPanel = el.closest('[role="tabpanel"]');
                if (tabPanel) {
                    tabPanel.dispatchEvent(new CustomEvent('expand', { bubbles: false }));
                }

                var current = el.parentElement;
                while (current) {
                    if (current.tagName === 'SECTION' && current.classList.contains('fi-collapsible')) {
                        current.dispatchEvent(new CustomEvent('expand', { bubbles: false }));
                    }
                    current = current.parentElement;
                }

                this.isOpen = false;
                this.query = '';

                setTimeout(function() {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    el.style.transition = 'box-shadow 0.3s ease';
                    el.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.5)';
                    el.style.borderRadius = '8px';

                    setTimeout(function() {
                        el.style.boxShadow = '';
                        el.style.borderRadius = '';
                    }, 2000);

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
                return text.replace(regex, '<span class="ffs-highlight">$1</span>');
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
