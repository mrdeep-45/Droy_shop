/**
 * Global Search Keyword Highlighting & Auto-Scroll Utility
 * Hexagon CRM
 *
 * Automatically reads the `search` query parameter from the URL,
 * scans visible text content, inputs, textareas, and select elements,
 * highlights all occurrences with a bright yellow background,
 * auto-expands parent tabs/accordions if needed,
 * smoothly scrolls to the first match, and focuses the field if applicable.
 */

(function () {
    'use strict';

    // Helper to escape regex special characters
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Helper to inject required highlight CSS into document head if not already present
    function injectStyles() {
        if (document.getElementById('global-search-highlight-styles')) return;

        const style = document.createElement('style');
        style.id = 'global-search-highlight-styles';
        style.textContent = `
            mark.global-search-highlight, span.global-search-highlight {
                background-color: #ffeb3b !important;
                color: #111111 !important;
                padding: 1px 4px !important;
                border-radius: 3px !important;
                font-weight: 700 !important;
                box-shadow: 0 0 6px rgba(255, 235, 59, 0.9) !important;
                display: inline !important;
                margin: 0 !important;
            }

            .global-search-highlight-field {
                background-color: #fffde7 !important;
                border: 2px solid #fbc02d !important;
                box-shadow: 0 0 8px rgba(251, 192, 45, 0.6) !important;
                transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
            }

            .global-search-first-match {
                outline: 2px dashed #f57c00 !important;
                outline-offset: 2px !important;
                animation: globalSearchPulse 1.5s ease-in-out 2 !important;
            }

            @keyframes globalSearchPulse {
                0% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.8); }
                50% { box-shadow: 0 0 0 10px rgba(255, 152, 0, 0); }
                100% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0); }
            }

            #global-search-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(2px);
                z-index: 999999;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                transition: opacity 0.3s ease;
            }
            .global-search-spinner {
                border: 4px solid rgba(0, 0, 0, 0.1);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border-left-color: #f57c00;
                animation: globalSearchSpin 1s linear infinite;
                margin-bottom: 15px;
            }
            @keyframes globalSearchSpin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .global-search-text {
                font-size: 20px;
                font-weight: 600;
                color: #333;
            }
            .global-search-toast {
                position: fixed;
                bottom: 30px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #323232;
                color: #fff;
                padding: 12px 24px;
                border-radius: 30px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 999999;
                transition: opacity 0.3s ease;
                opacity: 0;
                font-size: 15px;
            }
        `;
        document.head.appendChild(style);
    }

    let overlayElement = null;
    let overlayTimeout = null;
    let hasPerformedInitialSearch = false;

    function showOverlay(keyword) {
        if (overlayElement) return;
        injectStyles();

        overlayElement = document.createElement('div');
        overlayElement.id = 'global-search-overlay';
        overlayElement.innerHTML = `
            <div class="global-search-spinner"></div>
            <div class="global-search-text">Searching for '${keyword}'...</div>
        `;
        document.body.appendChild(overlayElement);
        document.body.style.overflow = 'hidden';

        // Max timeout to hide overlay
        overlayTimeout = setTimeout(() => {
            hideOverlay();
        }, 5000);
    }

    function hideOverlay() {
        if (!overlayElement) return;
        clearTimeout(overlayTimeout);
        overlayElement.style.opacity = '0';
        setTimeout(() => {
            if (overlayElement && overlayElement.parentNode) {
                overlayElement.parentNode.removeChild(overlayElement);
            }
            overlayElement = null;
            document.body.style.overflow = '';
        }, 300);
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'global-search-toast';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => { toast.style.opacity = '1'; }, 10);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // Auto-expand hidden Bootstrap tabs or accordions containing an element
    function expandParentContainers(element) {
        if (!element || !element.closest) return;

        // Handle Bootstrap Tab panes
        const tabPane = element.closest('.tab-pane');
        if (tabPane && !tabPane.classList.contains('active')) {
            const tabId = tabPane.getAttribute('id');
            if (tabId) {
                const tabTrigger = document.querySelector(`[data-bs-target="#${tabId}"], [href="#${tabId}"], [data-target="#${tabId}"]`);
                if (tabTrigger) {
                    if (window.bootstrap && window.bootstrap.Tab) {
                        try {
                            const tabInstance = window.bootstrap.Tab.getOrCreateInstance(tabTrigger);
                            tabInstance.show();
                        } catch (e) {
                            tabTrigger.click();
                        }
                    } else if (window.jQuery && $(tabTrigger).tab) {
                        $(tabTrigger).tab('show');
                    } else {
                        tabTrigger.click();
                    }
                }
            }
        }

        // Handle Bootstrap Accordions / Collapsibles
        const collapseElem = element.closest('.collapse:not(.show)');
        if (collapseElem) {
            const collapseId = collapseElem.getAttribute('id');
            let collapseTrigger = null;
            if (collapseId) {
                collapseTrigger = document.querySelector(`[data-bs-target="#${collapseId}"], [href="#${collapseId}"], [data-target="#${collapseId}"]`);
            }
            if (window.bootstrap && window.bootstrap.Collapse) {
                try {
                    const collapseInstance = window.bootstrap.Collapse.getOrCreateInstance(collapseElem);
                    collapseInstance.show();
                } catch (e) {
                    if (collapseTrigger) collapseTrigger.click();
                    else collapseElem.classList.add('show');
                }
            } else if (collapseTrigger) {
                collapseTrigger.click();
            } else {
                collapseElem.classList.add('show');
            }
        }
    }

    // Main highlight & scroll procedure
    function performGlobalSearchHighlight() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        if (!searchQuery) return;

        const keyword = searchQuery.trim();
        if (keyword.length === 0) return;

        injectStyles();

        const lowerKeyword = keyword.toLowerCase();
        const matchedElements = [];

        // Main content area to scan (fallbacks if .main-content not found)
        const mainContent = document.querySelector('.main-content') ||
                            document.querySelector('.app-content') ||
                            document.querySelector('.page-main') ||
                            document.body;

        // Forbidden tags and containers
        const isForbidden = (elem) => {
            if (!elem || !elem.tagName) return true;
            const tag = elem.tagName.toUpperCase();
            if (['SCRIPT', 'STYLE', 'NOSCRIPT', 'SVG', 'CANVAS', 'IFRAME', 'MARK', 'HEAD'].includes(tag)) {
                return true;
            }
            if (elem.closest && elem.closest('#headersearch, #typehead, #sidebar, .app-header, header, nav.navbar')) {
                return true;
            }
            return false;
        };

        // 1. Scan Form Inputs, Textareas, and Selects
        const formFields = mainContent.querySelectorAll(
            'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="checkbox"]):not([type="radio"]):not([type="image"]):not([type="file"]), textarea, select'
        );

        formFields.forEach((field) => {
            if (isForbidden(field)) return;

            let isMatched = false;
            const tag = field.tagName.toUpperCase();

            if (tag === 'INPUT' || tag === 'TEXTAREA') {
                if (field.value && field.value.toLowerCase().includes(lowerKeyword)) {
                    isMatched = true;
                }
            } else if (tag === 'SELECT') {
                // Check options text or value
                for (let i = 0; i < field.options.length; i++) {
                    const opt = field.options[i];
                    if ((opt.text && opt.text.toLowerCase().includes(lowerKeyword)) ||
                        (opt.value && opt.value.toLowerCase().includes(lowerKeyword))) {
                        isMatched = true;
                        break;
                    }
                }
            }

            if (isMatched) {
                field.classList.add('global-search-highlight-field');

                // If Select2 container exists, also highlight the Select2 wrapper
                if (tag === 'SELECT' && window.jQuery) {
                    const select2Container = $(field).next('.select2-container');
                    if (select2Container.length) {
                        select2Container.find('.select2-selection').addClass('global-search-highlight-field');
                    }
                }

                matchedElements.push(field);
            }
        });

        // 2. Scan Text Nodes safely using TreeWalker
        const nodesToProcess = [];
        const walker = document.createTreeWalker(
            mainContent,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function (node) {
                    if (!node || !node.parentNode) return NodeFilter.FILTER_REJECT;
                    const parent = node.parentNode;

                    if (isForbidden(parent)) return NodeFilter.FILTER_REJECT;

                    const tag = parent.tagName ? parent.tagName.toUpperCase() : '';
                    if (['INPUT', 'TEXTAREA', 'SELECT', 'OPTION', 'BUTTON'].includes(tag)) {
                        return NodeFilter.FILTER_REJECT;
                    }

                    if (!node.nodeValue || !node.nodeValue.trim()) {
                        return NodeFilter.FILTER_SKIP;
                    }

                    if (node.nodeValue.toLowerCase().includes(lowerKeyword)) {
                        return NodeFilter.FILTER_ACCEPT;
                    }

                    return NodeFilter.FILTER_SKIP;
                }
            }
        );

        while (walker.nextNode()) {
            nodesToProcess.push(walker.currentNode);
        }

        // Replace matching text nodes with highlight marks
        const regex = new RegExp('(' + escapeRegExp(keyword) + ')', 'gi');

        nodesToProcess.forEach((node) => {
            const text = node.nodeValue;
            const parent = node.parentNode;
            if (!parent) return;

            const fragment = document.createDocumentFragment();
            let lastIdx = 0;
            let match;

            // Reset regex state
            regex.lastIndex = 0;

            while ((match = regex.exec(text)) !== null) {
                const matchIndex = match.index;
                const matchedText = match[0];

                if (matchIndex > lastIdx) {
                    fragment.appendChild(document.createTextNode(text.substring(lastIdx, matchIndex)));
                }

                const mark = document.createElement('mark');
                mark.className = 'global-search-highlight';
                mark.textContent = matchedText;
                fragment.appendChild(mark);

                matchedElements.push(mark);

                lastIdx = regex.lastIndex;
            }

            if (lastIdx < text.length) {
                fragment.appendChild(document.createTextNode(text.substring(lastIdx)));
            }

            parent.replaceChild(fragment, node);
        });

        // 3. Sort matched elements in document order
        if (matchedElements.length === 0) {
            if (!hasPerformedInitialSearch) {
                showToast(`No matches found for '${keyword}'`);
                hasPerformedInitialSearch = true;
            }
            hideOverlay();
            return;
        }

        matchedElements.sort((a, b) => {
            if (a === b) return 0;
            const position = a.compareDocumentPosition(b);
            if (position & NodeFilter.DOCUMENT_POSITION_FOLLOWING) {
                return -1;
            } else if (position & NodeFilter.DOCUMENT_POSITION_PRECEDING) {
                return 1;
            }
            return 0;
        });

        // 4. Handle first match: Auto-expand tab/accordion, Scroll & Focus
        const firstMatch = matchedElements[0];
        firstMatch.classList.add('global-search-first-match');

        expandParentContainers(firstMatch);

        // Allow layout to recalculate after potential tab expansion
        setTimeout(() => {
            const rect = firstMatch.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const headerOffset = 120; // Accounts for sticky navbar header
            const targetY = rect.top + scrollTop - headerOffset;

            window.scrollTo({
                top: Math.max(0, targetY),
                behavior: 'smooth'
            });

            // Focus if first match is an input/textarea/select field
            const tag = firstMatch.tagName ? firstMatch.tagName.toUpperCase() : '';
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(tag)) {
                try {
                    firstMatch.focus({ preventScroll: true });
                } catch (e) {
                    firstMatch.focus();
                }
            } else {
                // If text inside a label associated with an input, focus that input
                const label = firstMatch.closest('label');
                if (label && label.htmlFor) {
                    const targetField = document.getElementById(label.htmlFor);
                    if (targetField && ['INPUT', 'TEXTAREA', 'SELECT'].includes(targetField.tagName.toUpperCase())) {
                        try {
                            targetField.focus({ preventScroll: true });
                        } catch (e) {
                            targetField.focus();
                        }
                    }
                }
            }
            hideOverlay();
            hasPerformedInitialSearch = true;
        }, 250);
    }

    // Execute on page load & re-check for AJAX updates
    function init() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        if (searchQuery && searchQuery.trim().length > 0) {
            const keyword = searchQuery.trim();
            if (document.body) {
                showOverlay(keyword);
            } else {
                document.addEventListener('DOMContentLoaded', () => showOverlay(keyword));
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', performGlobalSearchHighlight);
        } else {
            performGlobalSearchHighlight();
        }

        // Secondary check after window load for late-rendering plugins (e.g., DataTables)
        window.addEventListener('load', function () {
            setTimeout(performGlobalSearchHighlight, 300);
        });

        // Re-run on jQuery AJAX completions if present
        if (window.jQuery) {
            let ajaxDebounce;
            $(document).ajaxComplete(function () {
                clearTimeout(ajaxDebounce);
                ajaxDebounce = setTimeout(performGlobalSearchHighlight, 400);
            });
        }
    }

    init();
})();
