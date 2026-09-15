/**
 * Gmail-Style Floating Compose FAB & Modal JavaScript
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // DOM Element References
        const fab = document.getElementById('gmail-compose-fab');
        const modal = document.getElementById('gmail-compose-modal');
        const header = document.getElementById('gmail-compose-header');
        const minBtn = document.getElementById('gmail-compose-min');
        const maxBtn = document.getElementById('gmail-compose-max');
        const closeBtn = document.getElementById('gmail-compose-close');
        const discardBtn = document.getElementById('gmail-compose-discard');
        const sendBtn = document.getElementById('gmail-send-btn');
        const title = document.getElementById('gmail-modal-title');
        const maxIcon = document.getElementById('gmail-max-icon');

        const toInput = document.getElementById('gmail-compose-to');
        const ccInput = document.getElementById('gmail-compose-cc');
        const bccInput = document.getElementById('gmail-compose-bcc');
        const subjectInput = document.getElementById('gmail-compose-subject');
        const editor = document.getElementById('gmail-compose-editor');

        const ccRow = document.getElementById('gmail-cc-row');
        const bccRow = document.getElementById('gmail-bcc-row');
        const toggleCc = document.getElementById('gmail-toggle-cc');
        const toggleBcc = document.getElementById('gmail-toggle-bcc');

        const fileInput = document.getElementById('gmail-compose-file-input');
        const attachBtn = document.getElementById('gmail-tool-attach');
        const attachmentsPreview = document.getElementById('gmail-attachments-preview');

        const confirmOverlay = document.getElementById('gmail-confirm-overlay');
        const confirmCancel = document.getElementById('gmail-confirm-cancel');
        const confirmDiscard = document.getElementById('gmail-confirm-discard');

        if (!fab || !modal) return;

        // Modal State Variables
        let isMinimized = false;
        let isMaximized = false;
        let attachedFiles = [];

        // Drag State Variables
        let isDragging = false;
        let startX = 0, startY = 0;
        let modalStartX = 0, modalStartY = 0;

        // ------------------------------------------
        // Dirty State Check
        // ------------------------------------------
        function isDirty() {
            const toVal = toInput ? toInput.value.trim() : '';
            const ccVal = ccInput ? ccInput.value.trim() : '';
            const bccVal = bccInput ? bccInput.value.trim() : '';
            const subjectVal = subjectInput ? subjectInput.value.trim() : '';
            const editorVal = editor ? editor.innerText.trim() : '';

            return (
                toVal.length > 0 ||
                ccVal.length > 0 ||
                bccVal.length > 0 ||
                subjectVal.length > 0 ||
                editorVal.length > 0 ||
                attachedFiles.length > 0
            );
        }

        // ------------------------------------------
        // Dynamic Title Update
        // ------------------------------------------
        function updateTitle() {
            if (!title) return;
            const subjectVal = subjectInput ? subjectInput.value.trim() : '';
            title.textContent = subjectVal ? subjectVal : 'New Message';
        }

        if (subjectInput) {
            subjectInput.addEventListener('input', updateTitle);
        }

        // ------------------------------------------
        // Open Modal
        // ------------------------------------------
        function openModal() {
            if (isMinimized) {
                restoreFromMinimize();
                return;
            }

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');

            if (toInput && !toInput.value) {
                setTimeout(() => toInput.focus(), 150);
            }
        }

        // ------------------------------------------
        // Restore From Minimized
        // ------------------------------------------
        function restoreFromMinimize() {
            isMinimized = false;
            modal.classList.remove('minimized');
            updateTitle();
            if (toInput) toInput.focus();
        }

        // ------------------------------------------
        // Toggle Minimize
        // ------------------------------------------
        function toggleMinimize() {
            if (isMaximized) {
                toggleMaximize(); // Exit max state if currently maximized
            }

            isMinimized = !isMinimized;
            if (isMinimized) {
                modal.classList.add('minimized');
            } else {
                modal.classList.remove('minimized');
                updateTitle();
            }
        }

        // ------------------------------------------
        // Toggle Maximize
        // ------------------------------------------
        function toggleMaximize() {
            if (isMinimized) {
                modal.classList.remove('minimized');
                isMinimized = false;
            }

            isMaximized = !isMaximized;
            if (isMaximized) {
                modal.classList.add('maximized');
                if (maxIcon) {
                    maxIcon.className = 'fa-solid fa-down-left-and-up-right-to-center';
                }
            } else {
                modal.classList.remove('maximized');
                if (maxIcon) {
                    maxIcon.className = 'fa-solid fa-up-right-and-down-left-from-center';
                }
            }
        }

        // ------------------------------------------
        // Close Modal & Confirmation Handling
        // ------------------------------------------
        function requestClose() {
            if (isDirty()) {
                openConfirmModal();
            } else {
                forceCloseModal();
            }
        }

        function forceCloseModal() {
            modal.classList.remove('active', 'minimized', 'maximized');
            modal.setAttribute('aria-hidden', 'true');
            isMinimized = false;
            isMaximized = false;

            // Reset inline styles from drag/maximize
            modal.style.left = '';
            modal.style.top = '';
            modal.style.right = '';
            modal.style.bottom = '';

            resetForm();
            closeConfirmModal();
        }

        function resetForm() {
            if (toInput) toInput.value = '';
            if (ccInput) ccInput.value = '';
            if (bccInput) bccInput.value = '';
            if (subjectInput) subjectInput.value = '';
            if (editor) editor.innerHTML = '';

            if (ccRow) ccRow.classList.remove('active');
            if (bccRow) bccRow.classList.remove('active');
            if (toggleCc) toggleCc.style.display = 'inline';
            if (toggleBcc) toggleBcc.style.display = 'inline';

            attachedFiles = [];
            renderAttachments();
            updateTitle();
        }

        function openConfirmModal() {
            if (confirmOverlay) {
                confirmOverlay.classList.add('active');
            }
        }

        function closeConfirmModal() {
            if (confirmOverlay) {
                confirmOverlay.classList.remove('active');
            }
        }

        // ------------------------------------------
        // CC / BCC Toggles
        // ------------------------------------------
        if (toggleCc && ccRow) {
            toggleCc.addEventListener('click', function () {
                ccRow.classList.add('active');
                toggleCc.style.display = 'none';
                if (ccInput) ccInput.focus();
            });
        }

        if (toggleBcc && bccRow) {
            toggleBcc.addEventListener('click', function () {
                bccRow.classList.add('active');
                toggleBcc.style.display = 'none';
                if (bccInput) bccInput.focus();
            });
        }

        // ------------------------------------------
        // Attachments Handling
        // ------------------------------------------
        if (attachBtn && fileInput) {
            attachBtn.addEventListener('click', function () {
                fileInput.click();
            });

            fileInput.addEventListener('change', function (e) {
                const files = Array.from(e.target.files);
                attachedFiles = attachedFiles.concat(files);
                renderAttachments();
                fileInput.value = '';
            });
        }

        function renderAttachments() {
            if (!attachmentsPreview) return;
            attachmentsPreview.innerHTML = '';

            attachedFiles.forEach((file, index) => {
                const chip = document.createElement('div');
                chip.className = 'gmail-attachment-chip';
                chip.innerHTML = `
                    <i class="fa-solid fa-paperclip" style="font-size:0.75rem;"></i>
                    <span>${escapeHtml(file.name)}</span>
                    <span class="remove-att" data-index="${index}"><i class="fa-solid fa-xmark"></i></span>
                `;
                attachmentsPreview.appendChild(chip);
            });

            // Delegate remove action
            attachmentsPreview.querySelectorAll('.remove-att').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.getAttribute('data-index'), 10);
                    attachedFiles.splice(idx, 1);
                    renderAttachments();
                });
            });
        }

        function escapeHtml(text) {
            return text.replace(/[&<>"']/g, function (m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[m];
            });
        }

        // ------------------------------------------
        // Formatting Sub-Toolbar & Rich Text Controls
        // ------------------------------------------
        const formatBtn = document.getElementById('gmail-tool-format');
        const fmtSubbar = document.getElementById('gmail-fmt-subbar');
        const fmtBlockSelect = document.getElementById('gmail-fmt-block');

        if (formatBtn && fmtSubbar) {
            formatBtn.addEventListener('click', function () {
                fmtSubbar.classList.toggle('active');
                formatBtn.classList.toggle('active');
            });
        }

        // Handle formatting sub-toolbar buttons
        if (fmtSubbar && editor) {
            fmtSubbar.querySelectorAll('.gmail-fmt-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const cmd = this.getAttribute('data-cmd');
                    if (cmd) {
                        editor.focus();
                        document.execCommand(cmd, false, null);
                        updateFmtBtnStates();
                    }
                });
            });

            if (fmtBlockSelect) {
                fmtBlockSelect.addEventListener('change', function () {
                    const tag = this.value;
                    editor.focus();
                    document.execCommand('formatBlock', false, tag);
                });
            }

            // Update formatting button active states on selection change inside editor
            editor.addEventListener('keyup', updateFmtBtnStates);
            editor.addEventListener('mouseup', updateFmtBtnStates);
            editor.addEventListener('click', updateFmtBtnStates);
        }

        function updateFmtBtnStates() {
            if (!fmtSubbar || !editor) return;

            const cmds = ['bold', 'italic', 'underline', 'strikethrough'];
            cmds.forEach(cmd => {
                const btn = fmtSubbar.querySelector(`.gmail-fmt-btn[data-cmd="${cmd}"]`);
                if (btn) {
                    try {
                        if (document.queryCommandState(cmd)) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    } catch (err) {
                        btn.classList.remove('active');
                    }
                }
            });
        }

        // ------------------------------------------
        // Insert / Edit Link Functionality
        // ------------------------------------------
        const linkBtn = document.getElementById('gmail-tool-link');
        const linkOverlay = document.getElementById('gmail-link-overlay');
        const linkTextInput = document.getElementById('gmail-link-text');
        const linkUrlInput = document.getElementById('gmail-link-url');
        const linkApplyBtn = document.getElementById('gmail-link-apply');
        const linkRemoveBtn = document.getElementById('gmail-link-remove');
        const linkCancelBtn = document.getElementById('gmail-link-cancel');

        let savedRange = null;
        let activeAnchorNode = null;

        function saveSelection() {
            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0) {
                return sel.getRangeAt(0);
            }
            return null;
        }

        function restoreSelection(range) {
            if (range) {
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }

        function findParentAnchor(node) {
            while (node && node !== editor) {
                if (node.nodeName === 'A') return node;
                node = node.parentNode;
            }
            return null;
        }

        if (linkBtn && linkOverlay && editor) {
            linkBtn.addEventListener('click', function () {
                editor.focus();
                savedRange = saveSelection();

                let selectedText = '';
                activeAnchorNode = null;

                const sel = window.getSelection();
                if (sel && sel.anchorNode) {
                    activeAnchorNode = findParentAnchor(sel.anchorNode);
                }

                if (activeAnchorNode) {
                    selectedText = activeAnchorNode.innerText;
                    if (linkUrlInput) linkUrlInput.value = activeAnchorNode.getAttribute('href') || '';
                    if (linkRemoveBtn) linkRemoveBtn.style.display = 'inline-block';
                } else {
                    if (sel) {
                        selectedText = sel.toString();
                    }
                    if (linkUrlInput) linkUrlInput.value = '';
                    if (linkRemoveBtn) linkRemoveBtn.style.display = 'none';
                }

                if (linkTextInput) linkTextInput.value = selectedText;
                linkOverlay.classList.add('active');
                setTimeout(() => {
                    if (linkUrlInput) linkUrlInput.focus();
                }, 150);
            });

            if (linkCancelBtn) {
                linkCancelBtn.addEventListener('click', function () {
                    linkOverlay.classList.remove('active');
                });
            }

            if (linkApplyBtn) {
                linkApplyBtn.addEventListener('click', function () {
                    let url = linkUrlInput ? linkUrlInput.value.trim() : '';
                    let text = linkTextInput ? linkTextInput.value.trim() : '';

                    if (!url) {
                        showToast('warning', 'URL Required', 'Please enter a web address (e.g. https://example.com).');
                        if (linkUrlInput) linkUrlInput.focus();
                        return;
                    }

                    if (!/^https?:\/\//i.test(url) && !/^mailto:/i.test(url)) {
                        url = 'https://' + url;
                    }

                    linkOverlay.classList.remove('active');
                    editor.focus();

                    if (activeAnchorNode) {
                        activeAnchorNode.setAttribute('href', url);
                        if (text) activeAnchorNode.innerText = text;
                    } else {
                        restoreSelection(savedRange);
                        if (!text) text = url;

                        const html = `<a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer">${escapeHtml(text)}</a>`;
                        document.execCommand('insertHTML', false, html);
                    }
                });
            }

            if (linkRemoveBtn) {
                linkRemoveBtn.addEventListener('click', function () {
                    linkOverlay.classList.remove('active');
                    editor.focus();
                    if (activeAnchorNode) {
                        const parent = activeAnchorNode.parentNode;
                        while (activeAnchorNode.firstChild) {
                            parent.insertBefore(activeAnchorNode.firstChild, activeAnchorNode);
                        }
                        parent.removeChild(activeAnchorNode);
                    } else {
                        restoreSelection(savedRange);
                        document.execCommand('unlink', false, null);
                    }
                });
            }
        }

        // ------------------------------------------
        // Toast Helper Function
        // ------------------------------------------
        function showToast(type, title, message) {
            if (window.iziToast && typeof window.iziToast[type] === 'function') {
                window.iziToast[type]({
                    title: title,
                    message: message,
                    position: 'topRight',
                    timeout: 5000
                });
            } else {
                alert(`${title}: ${message}`);
            }
        }

        // ------------------------------------------
        // Send Action (Real Backend Email Sending)
        // ------------------------------------------
        if (sendBtn) {
            sendBtn.addEventListener('click', function () {
                const toVal = toInput ? toInput.value.trim() : '';
                if (!toVal) {
                    showToast('warning', 'Recipient Required', 'Please specify at least one recipient in the "To" field.');
                    if (toInput) toInput.focus();
                    return;
                }

                // Construct FormData payload
                const formData = new FormData();
                formData.append('to', toVal);
                if (ccInput && ccInput.value.trim()) {
                    formData.append('cc', ccInput.value.trim());
                }
                if (bccInput && bccInput.value.trim()) {
                    formData.append('bcc', bccInput.value.trim());
                }
                if (subjectInput && subjectInput.value.trim()) {
                    formData.append('subject', subjectInput.value.trim());
                }

                const bodyText = editor ? editor.innerHTML.trim() : "";
                formData.append('body', bodyText);

                if (attachedFiles && attachedFiles.length > 0) {
                    attachedFiles.forEach(file => {
                        formData.append('attachments[]', file);
                    });
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                // UI Loading state
                const origSendHtml = sendBtn.innerHTML;
                sendBtn.disabled = true;
                sendBtn.innerHTML = `<span>Sending...</span> <i class="fa-solid fa-spinner fa-spin" style="font-size:0.8rem;"></i>`;

                const targetUrl = (window.routes && window.routes.composeEmailSend)
                    ? window.routes.composeEmailSend
                    : '/compose-email/send';

                // Send AJAX request
                $.ajax({
                    url: targetUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
                    success: function (response) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = origSendHtml;

                        if (response && response.status === true) {
                            showToast('success', 'Message Sent', response.message || 'Your email has been sent successfully.');
                            forceCloseModal();
                        } else {
                            const errorMsg = (response && response.message) ? response.message : 'Failed to send email.';
                            showToast('error', 'Sending Failed', errorMsg);
                        }
                    },
                    error: function (xhr) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = origSendHtml;

                        let errorMsg = 'Failed to send email. Please check your SMTP settings and try again.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                const errs = Object.values(xhr.responseJSON.errors).flat();
                                if (errs.length > 0) errorMsg = errs.join(' ');
                            }
                        }

                        showToast('error', 'Sending Failed', errorMsg);
                    }
                });
            });
        }

        // ------------------------------------------
        // Event Listeners (FAB & Header Controls)
        // ------------------------------------------
        fab.addEventListener('click', openModal);

        if (minBtn) minBtn.addEventListener('click', toggleMinimize);
        if (maxBtn) maxBtn.addEventListener('click', toggleMaximize);
        if (closeBtn) closeBtn.addEventListener('click', requestClose);
        if (discardBtn) discardBtn.addEventListener('click', requestClose);

        if (confirmCancel) confirmCancel.addEventListener('click', closeConfirmModal);
        if (confirmDiscard) confirmDiscard.addEventListener('click', forceCloseModal);

        // Click on Header (if minimized) expands window back
        if (header) {
            header.addEventListener('click', function (e) {
                // If minimized and user clicked header (not on control buttons), expand
                if (isMinimized && !e.target.closest('.gmail-header-btn')) {
                    restoreFromMinimize();
                }
            });
        }

        // ------------------------------------------
        // Keyboard Accessibility
        // ------------------------------------------
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (confirmOverlay && confirmOverlay.classList.contains('active')) {
                    closeConfirmModal();
                    e.preventDefault();
                } else if (modal && modal.classList.contains('active')) {
                    requestClose();
                    e.preventDefault();
                }
            }
        });

        // ------------------------------------------
        // Draggable Window Functionality
        // ------------------------------------------
        if (header) {
            header.addEventListener('mousedown', startDrag);
            header.addEventListener('touchstart', startDrag, { passive: false });
        }

        function startDrag(e) {
            // Ignore drag if clicking header action buttons or if window is maximized or minimized
            if (e.target.closest('.gmail-header-btn') || isMaximized || isMinimized) {
                return;
            }

            isDragging = true;
            const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
            const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;

            startX = clientX;
            startY = clientY;

            const rect = modal.getBoundingClientRect();
            modalStartX = rect.left;
            modalStartY = rect.top;

            document.addEventListener('mousemove', onDrag);
            document.addEventListener('touchmove', onDrag, { passive: false });
            document.addEventListener('mouseup', stopDrag);
            document.addEventListener('touchend', stopDrag);

            e.preventDefault();
        }

        function onDrag(e) {
            if (!isDragging) return;

            const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
            const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;

            const deltaX = clientX - startX;
            const deltaY = clientY - startY;

            let newLeft = modalStartX + deltaX;
            let newTop = modalStartY + deltaY;

            const modalRect = modal.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            // Keep within viewport boundaries
            if (newLeft < 0) newLeft = 0;
            if (newTop < 0) newTop = 0;
            if (newLeft + modalRect.width > viewportWidth) newLeft = viewportWidth - modalRect.width;
            if (newTop + modalRect.height > viewportHeight) newTop = viewportHeight - modalRect.height;

            modal.style.left = newLeft + 'px';
            modal.style.top = newTop + 'px';
            modal.style.right = 'auto';
            modal.style.bottom = 'auto';

            if (e.type === 'touchmove') e.preventDefault();
        }

        function stopDrag() {
            isDragging = false;
            document.removeEventListener('mousemove', onDrag);
            document.removeEventListener('touchmove', onDrag);
            document.removeEventListener('mouseup', stopDrag);
            document.removeEventListener('touchend', stopDrag);
        }

    });
})();
