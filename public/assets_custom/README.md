# Assets Custom Directory

This directory contains all custom assets for the application, organized for better maintainability.

## Directory Structure

```
assets_custom/
├── css/
│   ├── comprehensive-theme.css      # Main custom theme
│   ├── dataTables.bootstrap4.min.css # DataTables Bootstrap styling
│   ├── responsive.bootstrap4.min.css # DataTables responsive styling
│   ├── error-prevention.css         # Error prevention styles
│   ├── font-fallback.css           # Font fallback styles
│   ├── simple-theme-text.css       # Simple text theme
│   └── theme-text.css              # Text theme styles
├── js/
│   ├── jquery-3.7.1.min.js         # jQuery library
│   ├── jquery.dataTables.min.js    # DataTables core
│   ├── dataTables.bootstrap4.min.js # DataTables Bootstrap integration
│   ├── dataTables.responsive.min.js # DataTables responsive extension
│   ├── responsive.bootstrap4.min.js # Responsive Bootstrap support
│   ├── comprehensive-theme.js       # Custom theme JavaScript
│   ├── global-realtime-updates.js  # Real-time updates
│   ├── error-prevention-init.js    # Error prevention
│   └── [other custom JS files]
└── images/
    └── [custom images]
```

## Usage in Blade Templates

### CSS Assets
```php
<link rel="stylesheet" href="{{asset('assets_custom/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('assets_custom/css/responsive.bootstrap4.min.css')}}">
```

### JavaScript Assets
```php
<script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{asset('assets_custom/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets_custom/js/dataTables.bootstrap4.min.js')}}"></script>
```

## Benefits

1. **Better Organization**: All custom assets in one place
2. **Version Control**: Easier to track custom modifications
3. **Deployment**: Simpler to deploy custom assets separately
4. **Maintenance**: Easier to update and maintain custom code
5. **Conflict Prevention**: Avoid conflicts with vendor assets

## Asset Loading Priority

1. Load jQuery first
2. Load DataTables core
3. Load DataTables extensions (Bootstrap, Responsive)
4. Load custom JavaScript last

## Notes

- All DataTables related files are now in assets_custom for consistency
- CSS files include both core DataTables and responsive extensions
- JavaScript files are organized by dependency order
- Custom theme files are separated from vendor files
