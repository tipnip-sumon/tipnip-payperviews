@props([
    'categories' => [],
    'selected' => null,
    'name' => 'category',
    'id' => 'categorySelect',
    'required' => true,
    'allowCustom' => true,
    'showRefresh' => true
])

<div class="mb-3">
    <label for="{{ $id }}" class="form-label">
        Category 
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    <div class="input-group">
        <select class="form-select @error($name) is-invalid @enderror" 
                id="{{ $id }}" 
                onchange="handleCategoryChange('{{ $id }}')"
                @if($required) required @endif>
            <option value="">Select Existing Category</option>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ (old($name, $selected) == $key) ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
            @if($allowCustom)
                <option value="custom">+ Create New Category</option>
            @endif
        </select>
        
        @if($showRefresh)
            <button class="btn btn-outline-secondary" 
                    type="button" 
                    id="refreshCategoriesBtn" 
                    onclick="refreshCategories('{{ $id }}')" 
                    title="Refresh categories">
                <i class="fe fe-refresh-cw"></i>
            </button>
        @endif
    </div>
    
    @if($allowCustom)
        <input type="text" 
               class="form-control mt-2 @error($name) is-invalid @enderror" 
               id="{{ $id }}_custom" 
               name="{{ $name }}" 
               style="display: none;" 
               placeholder="Enter new category (e.g., user-guides)" 
               pattern="[a-z0-9-]+" 
               title="Only lowercase letters, numbers, and hyphens allowed"
               value="{{ old($name, $selected) }}">
    @endif
    
    <input type="hidden" 
           id="{{ $id }}_final" 
           name="{{ $name }}" 
           value="{{ old($name, $selected) }}">
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    <div class="form-text">
        @if($allowCustom)
            Choose an existing category or create a new one. Use lowercase letters, numbers, and hyphens for new categories.
        @else
            Select a category from the available options.
        @endif
    </div>
</div>

@push('script')
<script>
// Category handling functions
function handleCategoryChange(selectId) {
    const select = document.getElementById(selectId);
    const customInput = document.getElementById(selectId + '_custom');
    const finalInput = document.getElementById(selectId + '_final');
    
    if (select.value === 'custom') {
        if (customInput) {
            customInput.style.display = 'block';
            customInput.required = true;
            finalInput.value = '';
            customInput.focus();
        }
    } else {
        if (customInput) {
            customInput.style.display = 'none';
            customInput.required = false;
        }
        finalInput.value = select.value;
    }
}

// Update final category when custom input changes
document.addEventListener('DOMContentLoaded', function() {
    const customInputs = document.querySelectorAll('[id$="_custom"]');
    customInputs.forEach(function(input) {
        input.addEventListener('input', function(e) {
            const selectId = e.target.id.replace('_custom', '');
            const finalInput = document.getElementById(selectId + '_final');
            if (finalInput) {
                finalInput.value = e.target.value;
            }
        });
    });
});

function refreshCategories(selectId) {
    fetch('{{ route("admin.categories.api") }}', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.categories) {
            const select = document.getElementById(selectId);
            const currentValue = select.value;
            
            // Clear existing options except the first and last
            const options = select.querySelectorAll('option');
            for (let i = 1; i < options.length - 1; i++) {
                options[i].remove();
            }
            
            // Add existing categories
            data.categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.value;
                option.textContent = `${category.label} (${category.count} files)`;
                select.insertBefore(option, select.lastElementChild);
            });
            
            // Restore selection if it still exists
            if (currentValue) {
                select.value = currentValue;
            }
            
            // Initialize form with URL parameters after refresh
            initializeCategoryFromUrl(selectId);
            
            console.log('Categories refreshed successfully');
        }
    })
    .catch(error => {
        console.error('Error loading categories:', error);
    });
}

function initializeCategoryFromUrl(selectId) {
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');
    
    if (categoryParam) {
        const select = document.getElementById(selectId);
        const finalInput = document.getElementById(selectId + '_final');
        const customInput = document.getElementById(selectId + '_custom');
        
        // Try to find the category in existing options
        let categoryFound = false;
        for (let option of select.options) {
            if (option.value === categoryParam) {
                option.selected = true;
                finalInput.value = categoryParam;
                categoryFound = true;
                break;
            }
        }
        
        // If category not found in dropdown, use custom input
        if (!categoryFound && customInput) {
            select.value = 'custom';
            customInput.style.display = 'block';
            customInput.required = true;
            customInput.value = categoryParam;
            finalInput.value = categoryParam;
        }
        
        // Trigger the change event to update the form state
        handleCategoryChange(selectId);
    }
}

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Find all category selectors and initialize them
    const selectors = document.querySelectorAll('[id$="Select"], [id*="category"]');
    selectors.forEach(function(select) {
        if (select.tagName === 'SELECT') {
            // Load categories first, then initialize
            refreshCategories(select.id);
        }
    });
});
</script>
@endpush
