# Category Selector Component

A reusable Blade component for selecting categories in Laravel forms with dynamic loading and custom category creation.

## Usage

### Basic Usage
```blade
<x-category-selector :categories="$categories" />
```

### Full Configuration
```blade
<x-category-selector 
    :categories="$categories" 
    :selected="$selectedCategory" 
    name="category" 
    id="categorySelect" 
    :required="true" 
    :allowCustom="true" 
    :showRefresh="true" />
```

## Parameters

- **categories** (array) - Array of existing categories (key => label format)
- **selected** (string, optional) - Pre-selected category value
- **name** (string, default: 'category') - Form field name
- **id** (string, default: 'categorySelect') - HTML element ID
- **required** (boolean, default: true) - Whether the field is required
- **allowCustom** (boolean, default: true) - Allow creating new categories
- **showRefresh** (boolean, default: true) - Show refresh button for reloading categories

## Features

1. **Dynamic Category Loading**: Fetches categories from the API endpoint
2. **URL Parameter Support**: Automatically selects category from URL parameters
3. **Custom Category Creation**: Allows users to create new categories inline
4. **Validation**: Built-in pattern validation for category slugs
5. **Refresh Functionality**: Reload categories without page refresh
6. **Error Handling**: Displays Laravel validation errors
7. **Responsive Design**: Works with Bootstrap components

## JavaScript Functions

The component provides these JavaScript functions:

- `handleCategoryChange(selectId)` - Handles dropdown changes
- `refreshCategories(selectId)` - Reloads categories from server
- `initializeCategoryFromUrl(selectId)` - Sets category from URL parameters

## Generated HTML Structure

```html
<div class="mb-3">
    <label for="categorySelect" class="form-label">Category *</label>
    <div class="input-group">
        <select id="categorySelect" class="form-select">...</select>
        <button type="button" class="btn btn-outline-secondary">...</button>
    </div>
    <input type="text" id="categorySelect_custom" style="display: none;">
    <input type="hidden" id="categorySelect_final" name="category">
</div>
```

## Examples

### Create Form
```blade
<x-category-selector 
    :categories="$categories" 
    :selected="request('category')" 
    name="category" 
    id="categorySelect" 
    :required="true" 
    :allowCustom="true" 
    :showRefresh="true" />
```

### Edit Form
```blade
<x-category-selector 
    :categories="$categories" 
    :selected="$model->category" 
    name="category" 
    id="categoryEditSelect" 
    :required="true" 
    :allowCustom="false" 
    :showRefresh="true" />
```

### Simple Selection (No Custom Categories)
```blade
<x-category-selector 
    :categories="$categories" 
    :allowCustom="false" 
    :showRefresh="false" />
```

## Controller Requirements

Your controller should pass the categories array:

```php
public function create()
{
    $categories = $this->getCategories(); // Returns array like ['slug' => 'Display Name']
    return view('admin.markdown.create', compact('categories'));
}
```

## API Endpoint

The component expects a JSON endpoint at `admin.markdown.categories` that returns:

```json
{
    "success": true,
    "categories": [
        {
            "value": "category-slug",
            "label": "Category Display Name",
            "count": 5
        }
    ]
}
```
