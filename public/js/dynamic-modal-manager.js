/**
 * Dynamic Modal Manager
 * Handles display and tracking of dynamic modals from admin panel
 */

class DynamicModalManager {
    constructor() {
        this.modalQueue = [];
        this.currentModal = null;
        this.modalContainer = null;
        this.init();
    }

    /**
     * Initialize the modal manager
     */
    init() {
        this.createModalContainer();
        this.loadModals();
        this.bindEvents();
    }

    /**
     * Create modal container in DOM
     */
    createModalContainer() {
        if (document.getElementById('dynamic-modal-container')) {
            return;
        }

        this.modalContainer = document.createElement('div');
        this.modalContainer.id = 'dynamic-modal-container';
        this.modalContainer.className = 'dynamic-modal-container';
        document.body.appendChild(this.modalContainer);
    }

    /**
     * Load modals from server
     */
    async loadModals() {
        try {
            const response = await fetch('/api/modals/get-modals', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                this.modalQueue = data.data;
                this.showNextModal();
            }
        } catch (error) {
            console.error('Error loading modals:', error);
        }
    }

    /**
     * Show the next modal in queue
     */
    showNextModal() {
        if (this.modalQueue.length === 0 || this.currentModal) {
            return;
        }

        const modal = this.modalQueue.shift();
        this.showModal(modal);
    }

    /**
     * Display a modal
     */
    showModal(modalData) {
        this.currentModal = modalData;
        
        // Apply delay if specified
        const delay = (modalData.delay_seconds || 0) * 1000;
        
        setTimeout(() => {
            this.renderModal(modalData);
            this.recordModalShow(modalData.modal_name);
        }, delay);
    }

    /**
     * Render modal HTML
     */
    renderModal(modalData) {
        const modalHtml = this.generateModalHtml(modalData);
        this.modalContainer.innerHTML = modalHtml;
        
        // Apply custom CSS if provided
        if (modalData.custom_css) {
            this.applyCustomCSS(modalData.custom_css);
        }
        
        // Execute custom JS if provided
        if (modalData.custom_js) {
            this.executeCustomJS(modalData.custom_js);
        }
        
        // Show modal with animation
        const modalElement = this.modalContainer.querySelector('.dynamic-modal');
        if (modalElement) {
            setTimeout(() => {
                modalElement.classList.add('show');
            }, 100);
        }
        
        // Bind modal-specific events
        this.bindModalEvents();
    }

    /**
     * Generate modal HTML based on data
     */
    generateModalHtml(modalData) {
        const { id, modal_name, title, subtitle, heading, description, settings } = modalData;
        
        const modalId = `dynamic-modal-${id}`;
        const size = settings?.size || 'medium';
        const theme = settings?.theme || 'default';
        const showCloseButton = settings?.show_close_button !== false;
        const backdrop = settings?.backdrop !== false;
        
        return `
            <div class="dynamic-modal modal-${theme} modal-${size}" id="${modalId}" data-modal-name="${modal_name}">
                <div class="dynamic-modal-backdrop ${backdrop ? '' : 'no-backdrop'}"></div>
                <div class="dynamic-modal-dialog">
                    <div class="dynamic-modal-content">
                        ${showCloseButton ? '<button type="button" class="dynamic-modal-close" aria-label="Close">&times;</button>' : ''}
                        
                        ${heading ? `<div class="dynamic-modal-header">
                            <h5 class="dynamic-modal-title">${this.escapeHtml(heading)}</h5>
                        </div>` : ''}
                        
                        <div class="dynamic-modal-body">
                            ${title ? `<h6 class="modal-subtitle">${this.escapeHtml(title)}</h6>` : ''}
                            ${subtitle ? `<p class="modal-subtitle">${this.escapeHtml(subtitle)}</p>` : ''}
                            ${description ? `<div class="modal-description">${description}</div>` : ''}
                        </div>
                        
                        ${settings?.show_footer !== false ? `<div class="dynamic-modal-footer">
                            ${settings?.primary_button_text ? `<button type="button" class="btn btn-primary modal-primary-btn">${this.escapeHtml(settings.primary_button_text)}</button>` : ''}
                            ${settings?.secondary_button_text ? `<button type="button" class="btn btn-secondary modal-secondary-btn">${this.escapeHtml(settings.secondary_button_text)}</button>` : ''}
                            ${!settings?.primary_button_text && !settings?.secondary_button_text ? '<button type="button" class="btn btn-primary modal-close-btn">OK</button>' : ''}
                        </div>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Apply custom CSS
     */
    applyCustomCSS(css) {
        let styleElement = document.getElementById('dynamic-modal-custom-styles');
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.id = 'dynamic-modal-custom-styles';
            document.head.appendChild(styleElement);
        }
        styleElement.textContent += css;
    }

    /**
     * Execute custom JavaScript
     */
    executeCustomJS(js) {
        try {
            // Create a function to safely execute the custom JS
            const func = new Function('modal', 'modalData', js);
            func(this.modalContainer.querySelector('.dynamic-modal'), this.currentModal);
        } catch (error) {
            console.error('Error executing custom JS:', error);
        }
    }

    /**
     * Bind modal events
     */
    bindModalEvents() {
        const modal = this.modalContainer.querySelector('.dynamic-modal');
        if (!modal) return;

        // Close button events
        const closeButtons = modal.querySelectorAll('.dynamic-modal-close, .modal-close-btn');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => this.closeModal());
        });

        // Backdrop click to close
        const backdrop = modal.querySelector('.dynamic-modal-backdrop');
        if (backdrop && !backdrop.classList.contains('no-backdrop')) {
            backdrop.addEventListener('click', () => this.closeModal());
        }

        // Primary button event
        const primaryBtn = modal.querySelector('.modal-primary-btn');
        if (primaryBtn) {
            primaryBtn.addEventListener('click', () => {
                this.recordModalClick(this.currentModal.modal_name);
                this.handlePrimaryButtonClick();
            });
        }

        // Secondary button event
        const secondaryBtn = modal.querySelector('.modal-secondary-btn');
        if (secondaryBtn) {
            secondaryBtn.addEventListener('click', () => {
                this.handleSecondaryButtonClick();
            });
        }

        // ESC key to close
        document.addEventListener('keydown', this.handleKeyDown.bind(this));
    }

    /**
     * Handle primary button click
     */
    handlePrimaryButtonClick() {
        const settings = this.currentModal.settings || {};
        
        if (settings.primary_button_action === 'redirect' && settings.primary_button_url) {
            window.location.href = settings.primary_button_url;
        } else if (settings.primary_button_action === 'download' && settings.download_url) {
            const link = document.createElement('a');
            link.href = settings.download_url;
            link.download = settings.download_filename || 'download';
            link.click();
        }
        
        this.closeModal();
    }

    /**
     * Handle secondary button click
     */
    handleSecondaryButtonClick() {
        const settings = this.currentModal.settings || {};
        
        if (settings.secondary_button_action === 'redirect' && settings.secondary_button_url) {
            window.location.href = settings.secondary_button_url;
        }
        
        this.closeModal();
    }

    /**
     * Handle keyboard events
     */
    handleKeyDown(event) {
        if (event.key === 'Escape' && this.currentModal) {
            this.closeModal();
        }
    }

    /**
     * Close current modal
     */
    closeModal() {
        if (!this.currentModal) return;

        const modal = this.modalContainer.querySelector('.dynamic-modal');
        if (modal) {
            modal.classList.remove('show');
            
            // Record dismiss
            this.recordModalDismiss(this.currentModal.modal_name);
            
            setTimeout(() => {
                this.modalContainer.innerHTML = '';
                this.currentModal = null;
                
                // Show next modal if any
                this.showNextModal();
            }, 300);
        }
    }

    /**
     * Record modal show event
     */
    async recordModalShow(modalName) {
        try {
            await fetch('/api/modals/record-show', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ modal_name: modalName })
            });
        } catch (error) {
            console.error('Error recording modal show:', error);
        }
    }

    /**
     * Record modal click event
     */
    async recordModalClick(modalName) {
        try {
            await fetch('/api/modals/record-click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ modal_name: modalName })
            });
        } catch (error) {
            console.error('Error recording modal click:', error);
        }
    }

    /**
     * Record modal dismiss event
     */
    async recordModalDismiss(modalName) {
        try {
            await fetch('/api/modals/record-dismiss', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ modal_name: modalName })
            });
        } catch (error) {
            console.error('Error recording modal dismiss:', error);
        }
    }

    /**
     * Bind global events
     */
    bindEvents() {
        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            document.removeEventListener('keydown', this.handleKeyDown.bind(this));
        });
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize on non-admin pages
    if (!window.location.pathname.includes('/admin')) {
        window.dynamicModalManager = new DynamicModalManager();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DynamicModalManager;
}
