<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popup Preview - {{ $popup->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Advanced Popup System CSS -->
    <script src="{{asset('assets/js/advanced-popup-system.js')}}"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .preview-container {
            padding: 50px 20px;
            text-align: center;
        }
        
        .preview-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .preview-controls {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .btn-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-preview:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .popup-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #212529;
            margin-top: 5px;
        }
        
        .badge-custom {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="container">
            <div class="preview-header">
                <h1 class="mb-3">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Popup Preview
                </h1>
                <p class="text-muted mb-4">Preview how your popup will appear to users</p>
                
                <div class="popup-info">
                    <div class="info-item">
                        <div class="info-label">Title</div>
                        <div class="info-value">{{ $popup->title }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Type</div>
                        <div class="info-value">
                            <span class="badge badge-custom bg-{{ $popup->type === 'promotion' ? 'success' : ($popup->type === 'warning' ? 'warning' : ($popup->type === 'announcement' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($popup->type) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Size</div>
                        <div class="info-value">{{ ucfirst($popup->size) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Animation</div>
                        <div class="info-value">{{ ucfirst(str_replace('-', ' ', $popup->animation)) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Position</div>
                        <div class="info-value">{{ ucfirst($popup->position) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="badge badge-custom bg-{{ $popup->is_active ? 'success' : 'danger' }}">
                                {{ $popup->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="preview-controls">
                <h4 class="mb-4">Preview Controls</h4>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-preview w-100" onclick="showPopupPreview()">
                            <i class="fas fa-play me-2"></i>
                            Show Popup
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="window.close()">
                            <i class="fas fa-times me-2"></i>
                            Close Preview
                        </button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.popups.edit', $popup) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit me-1"></i>
                        Edit Popup
                    </a>
                    <a href="{{ route('admin.popups.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-list me-1"></i>
                        Back to Popups
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showPopupPreview() {
            // Create preview popup with actual data
            const popupData = {
                id: {{ $popup->id }},
                title: @json($popup->title),
                content: @json($popup->content),
                type: @json($popup->type),
                display_type: @json($popup->display_type),
                image_url: @json($popup->image_url),
                button_text: @json($popup->button_text),
                button_url: @json($popup->button_url),
                button_color: @json($popup->button_color),
                background_color: @json($popup->background_color),
                text_color: @json($popup->text_color),
                overlay_color: @json($popup->overlay_color),
                size: @json($popup->size),
                position: @json($popup->position),
                animation: @json($popup->animation),
                delay: 500, // Quick preview
                auto_close: @json($popup->auto_close),
                closable: @json($popup->closable),
                backdrop_close: @json($popup->backdrop_close)
            };

            // Use the AdvancedPopupSystem to display the popup
            if (window.AdvancedPopupSystem) {
                window.AdvancedPopupSystem.displayPopup(popupData);
            } else {
                // Fallback simple popup
                showFallbackPopup(popupData);
            }
        }

        function showFallbackPopup(data) {
            // Remove existing preview
            const existing = document.getElementById('fallback-popup');
            if (existing) existing.remove();
            
            // Create fallback popup
            const popup = document.createElement('div');
            popup.id = 'fallback-popup';
            popup.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: ${data.overlay_color};
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.3s ease;
            `;
            
            const container = document.createElement('div');
            container.style.cssText = `
                background: ${data.background_color};
                color: ${data.text_color};
                padding: 30px;
                border-radius: 15px;
                max-width: ${data.size === 'small' ? '400px' : data.size === 'large' ? '800px' : data.size === 'fullscreen' ? '90vw' : '600px'};
                max-height: ${data.size === 'fullscreen' ? '90vh' : 'auto'};
                position: relative;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                animation: ${data.animation === 'zoom' ? 'zoomIn' : data.animation === 'bounce' ? 'bounceIn' : 'slideInUp'} 0.4s ease;
            `;
            
            let imageHtml = '';
            if (data.display_type !== 'text' && data.image_url) {
                imageHtml = `<img src="${data.image_url}" alt="${data.title}" style="width: 100%; border-radius: 10px; margin-bottom: 20px;">`;
            }
            
            let contentHtml = '';
            if (data.display_type !== 'image' && data.content) {
                contentHtml = `<div style="margin-bottom: 20px; line-height: 1.6;">${data.content}</div>`;
            }
            
            container.innerHTML = `
                ${data.closable ? `<button type="button" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: ${data.text_color};" onclick="document.getElementById('fallback-popup').remove()">×</button>` : ''}
                <h3 style="margin-bottom: 20px; color: ${data.text_color};">${data.title}</h3>
                ${imageHtml}
                ${contentHtml}
                <button type="button" style="background: ${data.button_color}; color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; font-weight: 500;" onclick="document.getElementById('fallback-popup').remove()">${data.button_text}</button>
                <div style="margin-top: 15px; font-size: 12px; opacity: 0.7;">
                    <i class="fas fa-info-circle me-1"></i>
                    Preview Mode - This popup won't be tracked
                </div>
            `;
            
            popup.appendChild(container);
            
            // Backdrop close
            if (data.backdrop_close) {
                popup.addEventListener('click', (e) => {
                    if (e.target === popup) {
                        popup.remove();
                    }
                });
            }
            
            document.body.appendChild(popup);
            
            // Auto close
            if (data.auto_close) {
                setTimeout(() => {
                    if (document.getElementById('fallback-popup')) {
                        popup.remove();
                    }
                }, data.auto_close * 1000);
            }
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes zoomIn {
                from { transform: scale(0.3); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            @keyframes bounceIn {
                0% { transform: scale(0.3); opacity: 0; }
                50% { transform: scale(1.1); }
                70% { transform: scale(0.9); }
                100% { transform: scale(1); opacity: 1; }
            }
            @keyframes slideInUp {
                from { transform: translateY(50px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);

        // Auto-show popup on page load for preview
        setTimeout(() => {
            showPopupPreview();
        }, 1000);
    </script>
</body>
</html>
