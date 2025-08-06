/**
 * Advanced Popup System
 * Handles various types of popups and modals for the application
 * Production Version - Clean and optimized
 */

class AdvancedPopupSystem {
    constructor() {
        this.popups = new Map();
        this.defaultSettings = {
            backdrop: true,
            keyboard: true,
            focus: true,
            show: true,
            animation: true,
            autoClose: false,
            autoCloseDelay: 5000
        };
        
        this.init();
    }

    init() {
        // Initialize popup system
        this.createPopupContainer();
        this.bindGlobalEvents();
    }

    createPopupContainer() {
        if (!document.getElementById('popup-container')) {
            const container = document.createElement('div');
            container.id = 'popup-container';
            container.className = 'popup-container';
            document.body.appendChild(container);
        }
    }

    bindGlobalEvents() {
        // Handle escape key for all popups
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeTopMostPopup();
            }
        });

        // Handle backdrop clicks
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('popup-backdrop')) {
                const popupId = e.target.getAttribute('data-popup-id');
                if (popupId) {
                    this.close(popupId);
                }
            }
        });
    }

    show(options = {}) {
        const settings = { ...this.defaultSettings, ...options };
        const popupId = settings.id || 'popup-' + Date.now();

        // Create popup HTML
        const popupHTML = this.createPopupHTML(popupId, settings);
        
        // Add to container
        const container = document.getElementById('popup-container');
        container.insertAdjacentHTML('beforeend', popupHTML);

        // Store popup reference
        this.popups.set(popupId, {
            settings: settings,
            element: document.getElementById(popupId)
        });

        // Show popup with animation
        setTimeout(() => {
            const popup = document.getElementById(popupId);
            if (popup) {
                popup.classList.add('show');
                
                // Auto-close if specified
                if (settings.autoClose && settings.autoCloseDelay > 0) {
                    setTimeout(() => {
                        this.close(popupId);
                    }, settings.autoCloseDelay);
                }
            }
        }, 10);

        return popupId;
    }

    close(popupId) {
        const popup = this.popups.get(popupId);
        if (!popup) return;

        const element = popup.element;
        if (element) {
            element.classList.remove('show');
            
            // Remove after animation
            setTimeout(() => {
                element.remove();
                this.popups.delete(popupId);
            }, 300);
        }
    }

    closeAll() {
        this.popups.forEach((popup, popupId) => {
            this.close(popupId);
        });
    }

    closeTopMostPopup() {
        if (this.popups.size > 0) {
            const lastPopupId = Array.from(this.popups.keys()).pop();
            this.close(lastPopupId);
        }
    }

    createPopupHTML(popupId, settings) {
        const {
            title = 'Notification',
            message = '',
            type = 'info',
            confirmText = 'OK',
            cancelText = 'Cancel',
            showConfirm = true,
            showCancel = false,
            customButtons = [],
            size = 'medium',
            backdrop = true
        } = settings;

        const sizeClass = {
            small: 'popup-sm',
            medium: 'popup-md',
            large: 'popup-lg',
            fullscreen: 'popup-fullscreen'
        }[size] || 'popup-md';

        const typeIcon = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'alert-triangle',
            info: 'info',
            question: 'help-circle'
        }[type] || 'info';

        let buttonsHTML = '';
        
        if (customButtons.length > 0) {
            buttonsHTML = customButtons.map(btn => 
                `<button type="button" class="popup-btn popup-btn-${btn.type || 'secondary'}" 
                         onclick="window.advancedPopupSystem.handleButtonClick('${popupId}', '${btn.action || 'close'}')"
                         ${btn.attributes || ''}>
                    ${btn.text}
                </button>`
            ).join('');
        } else {
            if (showCancel) {
                buttonsHTML += `<button type="button" class="popup-btn popup-btn-secondary" 
                                       onclick="window.advancedPopupSystem.close('${popupId}')">
                                   ${cancelText}
                               </button>`;
            }
            if (showConfirm) {
                buttonsHTML += `<button type="button" class="popup-btn popup-btn-primary" 
                                       onclick="window.advancedPopupSystem.handleConfirm('${popupId}')">
                                   ${confirmText}
                               </button>`;
            }
        }

        return `
            <div class="popup-backdrop ${backdrop ? 'popup-backdrop-visible' : ''}" 
                 data-popup-id="${popupId}">
                <div class="popup-modal ${sizeClass}" id="${popupId}">
                    <div class="popup-header">
                        <div class="popup-title">
                            <i class="popup-icon popup-icon-${type}">
                                <svg class="popup-svg">
                                    <use href="#icon-${typeIcon}"></use>
                                </svg>
                            </i>
                            <h4>${title}</h4>
                        </div>
                        <button type="button" class="popup-close" 
                                onclick="window.advancedPopupSystem.close('${popupId}')">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="popup-body">
                        <div class="popup-message">${message}</div>
                    </div>
                    ${buttonsHTML ? `<div class="popup-footer">${buttonsHTML}</div>` : ''}
                </div>
            </div>
        `;
    }

    handleConfirm(popupId) {
        const popup = this.popups.get(popupId);
        if (popup && popup.settings.onConfirm) {
            popup.settings.onConfirm(popupId);
        }
        this.close(popupId);
    }

    handleButtonClick(popupId, action) {
        const popup = this.popups.get(popupId);
        if (popup && popup.settings.onButtonClick) {
            popup.settings.onButtonClick(action, popupId);
        }
        
        if (action === 'close') {
            this.close(popupId);
        }
    }

    // Convenience methods
    success(message, title = 'Success') {
        return this.show({
            type: 'success',
            title: title,
            message: message,
            confirmText: 'OK',
            showCancel: false
        });
    }

    error(message, title = 'Error') {
        return this.show({
            type: 'error',
            title: title,
            message: message,
            confirmText: 'OK',
            showCancel: false
        });
    }

    warning(message, title = 'Warning') {
        return this.show({
            type: 'warning',
            title: title,
            message: message,
            confirmText: 'OK',
            showCancel: false
        });
    }

    info(message, title = 'Information') {
        return this.show({
            type: 'info',
            title: title,
            message: message,
            confirmText: 'OK',
            showCancel: false
        });
    }

    confirm(message, title = 'Confirm', onConfirm = null, onCancel = null) {
        return this.show({
            type: 'question',
            title: title,
            message: message,
            confirmText: 'Yes',
            cancelText: 'No',
            showConfirm: true,
            showCancel: true,
            onConfirm: onConfirm,
            onCancel: onCancel
        });
    }
}

// Initialize the popup system
window.advancedPopupSystem = new AdvancedPopupSystem();
window.AdvancedPopupSystem = AdvancedPopupSystem;

// Add required CSS
const popupCSS = `
.popup-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    pointer-events: none;
}

.popup-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.popup-backdrop.popup-backdrop-visible {
    pointer-events: all;
}

.popup-backdrop.show {
    opacity: 1;
}

.popup-modal {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transform: scale(0.7);
    transition: transform 0.3s ease;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.popup-backdrop.show .popup-modal {
    transform: scale(1);
}

.popup-sm {
    width: 300px;
    max-width: 90vw;
}

.popup-md {
    width: 500px;
    max-width: 90vw;
}

.popup-lg {
    width: 800px;
    max-width: 95vw;
}

.popup-fullscreen {
    width: 95vw;
    height: 95vh;
}

.popup-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.popup-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.popup-title h4 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.popup-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.popup-icon-success {
    color: #28a745;
}

.popup-icon-error {
    color: #dc3545;
}

.popup-icon-warning {
    color: #ffc107;
}

.popup-icon-info {
    color: #17a2b8;
}

.popup-icon-question {
    color: #6c757d;
}

.popup-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    transition: color 0.2s;
}

.popup-close:hover {
    color: #333;
}

.popup-body {
    padding: 20px;
    flex: 1;
    overflow-y: auto;
}

.popup-message {
    font-size: 1rem;
    line-height: 1.5;
    color: #333;
}

.popup-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.popup-btn {
    padding: 8px 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
}

.popup-btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.popup-btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.popup-btn-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.popup-btn-secondary:hover {
    background: #545b62;
    border-color: #545b62;
}

.popup-btn-success {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.popup-btn-danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

/* SVG Icons */
.popup-svg {
    width: 100%;
    height: 100%;
    fill: currentColor;
}

@media (max-width: 576px) {
    .popup-modal {
        margin: 20px;
    }
    
    .popup-sm,
    .popup-md,
    .popup-lg {
        width: calc(100vw - 40px);
    }
}
`;

// Add CSS to document
const styleElement = document.createElement('style');
styleElement.textContent = popupCSS;
document.head.appendChild(styleElement);

// Add SVG icons
const svgIcons = `
<svg style="display: none;">
    <defs>
        <symbol id="icon-check-circle" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="9,11 12,14 22,4"/>
        </symbol>
        <symbol id="icon-x-circle" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
        </symbol>
        <symbol id="icon-alert-triangle" viewBox="0 0 24 24">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </symbol>
        <symbol id="icon-info" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
        </symbol>
        <symbol id="icon-help-circle" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </symbol>
    </defs>
</svg>
`;

document.body.insertAdjacentHTML('afterbegin', svgIcons);
