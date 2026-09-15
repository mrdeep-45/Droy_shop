<!-- Gmail-Style Floating Compose FAB & Compose Modal Component -->

<!-- Floating Action Button -->
<button id="gmail-compose-fab" class="gmail-fab-btn" type="button" aria-label="Compose new message">
    <span class="gmail-fab-icon">
        <i class="fa-solid fa-pen"></i>
    </span>
    <span class="gmail-fab-text">Compose</span>
</button>

<!-- Gmail Compose Modal -->
<div id="gmail-compose-modal" class="gmail-compose-modal" role="dialog" aria-label="New Message" aria-hidden="true" tabindex="-1">
    <!-- Header Title Bar (Draggable Handle) -->
    <div id="gmail-compose-header" class="gmail-compose-header">
        <div class="gmail-compose-title" id="gmail-modal-title">New Message</div>
        <div class="gmail-header-actions">
            <button type="button" class="gmail-header-btn" id="gmail-compose-min" title="Minimize" aria-label="Minimize">
                <i class="fa-solid fa-minus"></i>
            </button>
            <button type="button" class="gmail-header-btn" id="gmail-compose-max" title="Full screen" aria-label="Full screen">
                <i class="fa-solid fa-up-right-and-down-left-from-center" id="gmail-max-icon"></i>
            </button>
            <button type="button" class="gmail-header-btn close-btn" id="gmail-compose-close" title="Save & close" aria-label="Save and close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Compose Form -->
    <form id="gmail-compose-form" autocomplete="off" onsubmit="return false;" style="display: flex; flex-direction: column; flex: 1; margin: 0;">
        <div class="gmail-compose-body">
            <!-- Recipients (To) -->
            <div class="gmail-field-row">
                <span class="gmail-field-label">To</span>
                <input type="text" id="gmail-compose-to" class="gmail-field-input" placeholder="Recipients" />
                <div class="gmail-cc-bcc-toggles">
                    <span id="gmail-toggle-cc" class="gmail-toggle-link" role="button">Cc</span>
                    <span id="gmail-toggle-bcc" class="gmail-toggle-link" role="button">Bcc</span>
                </div>
            </div>

            <!-- CC Recipient (Hidden by default) -->
            <div id="gmail-cc-row" class="gmail-field-row gmail-extra-field">
                <span class="gmail-field-label">Cc</span>
                <input type="text" id="gmail-compose-cc" class="gmail-field-input" placeholder="Cc Recipients" />
            </div>

            <!-- BCC Recipient (Hidden by default) -->
            <div id="gmail-bcc-row" class="gmail-field-row gmail-extra-field">
                <span class="gmail-field-label">Bcc</span>
                <input type="text" id="gmail-compose-bcc" class="gmail-field-input" placeholder="Bcc Recipients" />
            </div>

            <!-- Subject -->
            <div class="gmail-field-row">
                <input type="text" id="gmail-compose-subject" class="gmail-field-input" placeholder="Subject" />
            </div>

            <!-- Formatting Sub-Toolbar (Toggled by 'A' formatting button) -->
            <div id="gmail-fmt-subbar" class="gmail-fmt-subbar">
                <button type="button" class="gmail-fmt-btn" data-cmd="bold" title="Bold (Ctrl+B)">
                    <i class="fa-solid fa-bold"></i>
                </button>
                <button type="button" class="gmail-fmt-btn" data-cmd="italic" title="Italic (Ctrl+I)">
                    <i class="fa-solid fa-italic"></i>
                </button>
                <button type="button" class="gmail-fmt-btn" data-cmd="underline" title="Underline (Ctrl+U)">
                    <i class="fa-solid fa-underline"></i>
                </button>
                <button type="button" class="gmail-fmt-btn" data-cmd="strikethrough" title="Strikethrough">
                    <i class="fa-solid fa-strikethrough"></i>
                </button>
                <div class="gmail-fmt-divider"></div>
                <select id="gmail-fmt-block" class="gmail-fmt-select" title="Heading Level">
                    <option value="p">Normal text</option>
                    <option value="h1">Heading 1</option>
                    <option value="h2">Heading 2</option>
                    <option value="h3">Heading 3</option>
                </select>
                <div class="gmail-fmt-divider"></div>
                <button type="button" class="gmail-fmt-btn" data-cmd="insertUnorderedList" title="Bulleted list">
                    <i class="fa-solid fa-list-ul"></i>
                </button>
                <button type="button" class="gmail-fmt-btn" data-cmd="insertOrderedList" title="Numbered list">
                    <i class="fa-solid fa-list-ol"></i>
                </button>
                <div class="gmail-fmt-divider"></div>
                <button type="button" class="gmail-fmt-btn" data-cmd="justifyLeft" title="Align left">
                    <i class="fa-solid fa-align-left"></i>
                </button>
                <button type="button" class="gmail-fmt-btn" data-cmd="justifyCenter" title="Align center">
                    <i class="fa-solid fa-align-center"></i>
                </button>
                <button type="button" class="gmail-fmt-btn" data-cmd="justifyRight" title="Align right">
                    <i class="fa-solid fa-align-right"></i>
                </button>
                <div class="gmail-fmt-divider"></div>
                <button type="button" class="gmail-fmt-btn" data-cmd="removeFormat" title="Clear formatting">
                    <i class="fa-solid fa-eraser"></i>
                </button>
            </div>

            <!-- Attachments Preview Area -->
            <div id="gmail-attachments-preview" class="gmail-attachments-preview"></div>

            <!-- Message Body Editor -->
            <div class="gmail-editor-container">
                <div id="gmail-compose-editor" class="gmail-editor" contenteditable="true" placeholder="Write your message here..."></div>
            </div>
        </div>

        <!-- Hidden Attachment File Input -->
        <input type="file" id="gmail-compose-file-input" style="display: none;" multiple />

        <!-- Footer Action Bar -->
        <div class="gmail-compose-footer">
            <div class="gmail-footer-left">
                <button type="button" id="gmail-send-btn" class="gmail-send-btn">
                    <span>Send</span>
                    <i class="fa-solid fa-paper-plane" style="font-size: 0.8rem;"></i>
                </button>
                <div class="gmail-toolbar-icons">
                    <button type="button" class="gmail-tool-btn" id="gmail-tool-format" title="Formatting options"><i class="fa-solid fa-font"></i></button>
                    <button type="button" class="gmail-tool-btn" id="gmail-tool-attach" title="Attach files"><i class="fa-solid fa-paperclip"></i></button>
                    <button type="button" class="gmail-tool-btn" id="gmail-tool-link" title="Insert link"><i class="fa-solid fa-link"></i></button>
                </div>
            </div>
            <div class="gmail-footer-right">
                <button type="button" class="gmail-trash-btn" id="gmail-compose-discard" title="Discard draft">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Insert / Edit Link Modal Dialog -->
<div id="gmail-link-overlay" class="gmail-confirm-overlay">
    <div class="gmail-confirm-box" style="width: 400px;">
        <div class="gmail-confirm-title"><i class="fa-solid fa-link me-2" style="color:#0b57d0;"></i> Edit Link</div>
        <div style="margin-bottom: 14px;">
            <label style="font-size: 0.8rem; font-weight: 600; color: #444746; display: block; margin-bottom: 4px;">Text to display:</label>
            <input type="text" id="gmail-link-text" class="gmail-field-input" style="width:100%; border:1px solid #dadce0; border-radius:6px; padding:6px 10px; background:#fff;" placeholder="Text" />
        </div>
        <div style="margin-bottom: 18px;">
            <label style="font-size: 0.8rem; font-weight: 600; color: #444746; display: block; margin-bottom: 4px;">To what URL should this link go?</label>
            <input type="url" id="gmail-link-url" class="gmail-field-input" style="width:100%; border:1px solid #dadce0; border-radius:6px; padding:6px 10px; background:#fff;" placeholder="https://example.com" />
        </div>
        <div class="gmail-confirm-actions" style="justify-content: space-between;">
            <button type="button" id="gmail-link-remove" class="gmail-confirm-btn" style="background:#fce8e6; color:#d93025; display:none;">Remove link</button>
            <div style="display:flex; gap:8px; margin-left:auto;">
                <button type="button" id="gmail-link-cancel" class="gmail-confirm-btn gmail-btn-cancel">Cancel</button>
                <button type="button" id="gmail-link-apply" class="gmail-confirm-btn" style="background:#0b57d0; color:#fff;">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Unsaved Changes Confirmation Modal Dialog -->
<div id="gmail-confirm-overlay" class="gmail-confirm-overlay">
    <div class="gmail-confirm-box">
        <div class="gmail-confirm-title">Discard draft?</div>
        <div class="gmail-confirm-desc">You have unsaved changes in your message. Are you sure you want to discard this draft?</div>
        <div class="gmail-confirm-actions">
            <button type="button" id="gmail-confirm-cancel" class="gmail-confirm-btn gmail-btn-cancel">Cancel</button>
            <button type="button" id="gmail-confirm-discard" class="gmail-confirm-btn gmail-btn-discard">Discard</button>
        </div>
    </div>
</div>
