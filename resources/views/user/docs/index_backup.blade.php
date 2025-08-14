<x-adaptive_layout>
    <x-slot name="title">Documentation & Help Center</x-slot>

<script>
// Define global functions immediately
window.performSearch = function() {
    var input = document.getElementById('docSearch');
    var results = document.getElementById('searchResults');
    
    if (!input) {
        alert('Search input not found!');
        return;
    }
    
    var query = input.value.trim();
    console.log('Search called with: "' + query + '"');
    
    if (query.length < 2) {
        if (results) results.style.display = 'none';
        return;
    }
    
    if (results) {
        results.innerHTML = '<div class="search-loading"><i class="ri-loader-4-line"></i> Searching...</div>';
        results.style.display = 'block';
    }
    
    // Make real API call
    fetch('/api/docs/search?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            console.log('Search response:', data);
            if (data.success && data.results && data.results.length > 0) {
                displaySearchResults(data.results);
            } else {
                displayNoResults();
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            displaySearchError(error.message);
        });
};

function displaySearchResults(results) {
    var searchResults = document.getElementById('searchResults');
    if (!searchResults) return;
    
    var html = results.map(function(result) {
        return '<div class="search-result-item" onclick="window.location=\'' + result.url + '\'">' +
               '<div>' +
               '<h6>' + result.title + '</h6>' +
               '<p>' + result.excerpt + '</p>' +
               '<small>' + result.category + '</small>' +
               '</div>' +
               '</div>';
    }).join('');

    searchResults.innerHTML = html;
    searchResults.style.display = 'block';
}

function displayNoResults() {
    var searchResults = document.getElementById('searchResults');
    if (!searchResults) return;
    
    searchResults.innerHTML = '<div class="search-result-item text-center">' +
                              '<i class="ri-search-line"></i>' +
                              '<p class="mb-0">No results found</p>' +
                              '<small class="text-muted">Try different keywords</small>' +
                              '</div>';
    searchResults.style.display = 'block';
}

function displaySearchError(errorMessage) {
    var searchResults = document.getElementById('searchResults');
    if (!searchResults) return;
    
    searchResults.innerHTML = '<div class="search-result-item text-center text-danger">' +
                              '<i class="ri-error-warning-line"></i>' +
                              '<p class="mb-0">Search error: ' + errorMessage + '</p>' +
                              '<small>Please try again later</small>' +
                              '</div>';
    searchResults.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    // Set up search functionality
    var searchInput = document.getElementById('docSearch');
    var searchResults = document.getElementById('searchResults');
    var searchTimeout;
    
    if (searchInput) {
        // Search on typing
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                window.performSearch();
            }, 300);
        });
        
        // Search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                window.performSearch();
            }
        });
    }
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (searchResults && searchInput && 
            !searchInput.contains(e.target) && 
            !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
</script>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center py-5 bg-gradient-primary text-white rounded-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                <h1 class="display-4 fw-bold mb-3 text-white">
                    <i class="ri-book-open-line me-3"></i>
                    Help Center
                </h1>
                <p class="lead mb-4 text-white">Find answers, tutorials, and documentation for PayPerViews</p>
                
                <!-- Search Box -->
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" id="docSearch" placeholder="Search documentation..." autocomplete="off">
                            <button class="btn btn-light" type="button" id="searchBtn" onclick="performSearch()">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                        <div id="searchResults" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="h3 mb-4" style="color: #212529;">Browse by Category</h2>
            <div class="row g-4">
                @foreach($categories as $category)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-shape icon-lg bg-primary text-white rounded-circle me-3">
                                    <i class="{{ $category['icon'] }}"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1">{{ $category['title'] }}</h5>
                                    <small class="text-muted">{{ $category['count'] }} {{ Str::plural('document', $category['count']) }}</small>
                                </div>
                            </div>
                            <p class="card-text text-muted mb-3">{{ $category['description'] }}</p>
                            <a href="{{ route('docs.category', $category['category']) }}" class="btn btn-outline-primary btn-sm">
                                View Documents <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Featured Documents -->
        <div class="col-lg-8">
            <h2 class="h3 mb-4" style="color: #212529;">Popular Documents</h2>
            <div class="row g-3">
                @foreach($featured as $doc)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary-soft text-primary">{{ ucfirst($doc->category) }}</span>
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>{{ $doc->view_count }}
                                </small>
                            </div>
                            <h6 class="card-title">
                                <a href="{{ route('docs.show', [$doc->category, $doc->slug]) }}" class="text-decoration-none">
                                    {{ $doc->title }}
                                </a>
                            </h6>
                            <p class="card-text text-muted small">{{ $doc->excerpt }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $doc->reading_time }} min read
                                </small>
                                <a href="{{ route('docs.show', [$doc->category, $doc->slug]) }}" class="btn btn-sm btn-outline-primary">
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Documents -->
        <div class="col-lg-4">
            <h2 class="h3 mb-4" style="color: #212529;">Recent Updates</h2>
            <div class="list-group list-group-flush">
                @foreach($recent as $doc)
                <div class="list-group-item border-0 px-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-2">
                            <h6 class="mb-1">
                                <a href="{{ route('docs.show', [$doc->category, $doc->slug]) }}" class="text-decoration-none">
                                    {{ Str::limit($doc->title, 40) }}
                                </a>
                            </h6>
                            <p class="mb-1 text-muted small">{{ Str::limit($doc->excerpt, 60) }}</p>
                            <small class="text-muted">{{ $doc->published_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-light text-dark">{{ ucfirst($doc->category) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="bg-light rounded-3 p-4">
                <h3 class="h4 mb-3">Quick Links</h3>
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('faq') }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-question-circle me-2"></i>FAQ
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('privacy-policy') }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-shield-alt me-2"></i>Privacy Policy
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('terms-and-conditions') }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-file-contract me-2"></i>Terms
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('user.support.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-headset me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-adaptive_layout>

@push('styles')
<style>
.hover-lift {
    transition: transform 0.2s ease-in-out;
}
.hover-lift:hover {
    transform: translateY(-2px);
}
.icon-shape {
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-primary-soft {
    background-color: rgba(13, 110, 253, 0.1);
}
#searchResults {
    background: #ffffff;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    max-height: 400px;
    overflow-y: auto;
    border: 2px solid #e9ecef;
    margin-top: 0.5rem;
    z-index: 1050;
    position: relative;
}
.search-result-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    background-color: #ffffff;
    color: #212529 !important;
}
.search-result-item:hover {
    background-color: #f8f9fa !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transform: translateY(-1px);
}
.search-result-item:last-child {
    border-bottom: none;
    border-radius: 0 0 0.5rem 0.5rem;
}
.search-result-item:first-child {
    border-radius: 0.5rem 0.5rem 0 0;
}
.search-result-item h6 {
    color: #212529 !important;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}
.search-result-item p {
    color: #495057 !important;
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}
.search-result-item small {
    color: #0d6efd !important;
    font-weight: 500;
    background-color: rgba(13, 110, 253, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}
.search-result-item .text-muted {
    color: #6c757d !important;
}
.search-result-item .text-danger {
    color: #dc3545 !important;
    font-weight: 500;
}
.search-result-item .text-center {
    text-align: center;
    padding: 2rem 1rem;
}
.search-result-item .text-center i {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}
/* Loading state styling */
.search-loading {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    text-align: center;
    padding: 1.5rem;
    border-radius: 0.5rem;
}
</style>
@endpush

@push('script')
<script>
// Simple supplemental script - main functionality is in the header
console.log('🔍 Supplemental search script loaded');
</script>
    
    console.log('Performing search for:', query);
    
    // Show loading state
    if (searchResults) {
        searchResults.innerHTML = '<div class="text-center p-3"><i class="ri-loader-4-line"></i> Searching...</div>';
        searchResults.style.display = 'block';
    }

    // Use fetch for the search
    const searchUrl = `{{ route('api.docs.search') }}?q=${encodeURIComponent(query)}`;
    console.log('Making fetch request to:', searchUrl);
    
    fetch(searchUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Search response:', data);
        if (data.success && data.results && data.results.length > 0) {
            displaySearchResults(data.results);
        } else {
            displayNoResults();
        }
    })
    .catch(error => {
        console.error('Fetch search error:', error);
        displaySearchError(error.message);
    });
}

function displaySearchResults(results) {
    if (!searchResults) return;
    
    const html = results.map(result => `
        <div class="search-result-item" onclick="window.location='${result.url}'">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">${result.title}</h6>
                    <p class="mb-1 text-muted small">${result.excerpt}</p>
                    <small class="text-primary">${result.category}</small>
                </div>
            </div>
        </div>
    `).join('');

    searchResults.innerHTML = html;
    searchResults.style.display = 'block';
}

function displayNoResults() {
    if (!searchResults) return;
    
    searchResults.innerHTML = `
        <div class="search-result-item text-center text-muted">
            <i class="ri-search-line mb-2"></i>
            <p class="mb-0">No results found</p>
        </div>
    `;
    searchResults.style.display = 'block';
}

function displaySearchError(errorMessage) {
    if (!searchResults) return;
    
    searchResults.innerHTML = `
        <div class="search-result-item text-center text-danger">
            <i class="ri-error-warning-line mb-2"></i>
            <p class="mb-0">Search error: ${errorMessage}</p>
            <small>Please try again later</small>
        </div>
    `;
    searchResults.style.display = 'block';
}

function testSearchFunction() {
    console.log('� Test Search Function Called!');
    alert('Test Search Function Called!');
    if (searchInput) {
        searchInput.value = 'test';
        performSearch();
    } else {
        alert('Search input not found!');
    }
}

function testDirectAPI() {
    console.log('� Direct API test triggered');
    alert('Direct API test starting...');
    
    fetch('{{ route('api.docs.search') }}?q=test')
        .then(response => response.json())
        .then(data => {
            console.log('� Direct API test result:', data);
            alert('API Test Result: ' + JSON.stringify(data, null, 2));
        })
        .catch(error => {
            console.error('🔵 Direct API test error:', error);
            alert('API Test Error: ' + error.message);
        });
}

// Immediate execution to test if this script loads at all
console.log('🔍 DOCS INDEX SCRIPT STARTED - Loading docs index search script...');

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('� DOCS INDEX - Documentation search initialized');
    
    // Get elements
    searchInput = document.getElementById('docSearch');
    searchResults = document.getElementById('searchResults');
    searchBtn = document.getElementById('searchBtn');
    
    // Debug: Log all elements found
    console.log('🔍 ELEMENT CHECK:', {
        searchInput: !!searchInput,
        searchResults: !!searchResults,
        searchBtn: !!searchBtn
    });
    
    if (!searchInput || !searchResults || !searchBtn) {
        console.error('🚨 Critical: Required search elements not found');
        return;
    }
    
    console.log('✅ All search elements found successfully');
    
    // Set up event listeners
    searchInput.addEventListener('input', function() {
        console.log('📝 Search input detected:', this.value);
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    });
    
    searchInput.addEventListener('keypress', function(e) {
        console.log('⌨️ Key pressed in search:', e.key);
        if (e.key === 'Enter') {
            e.preventDefault();
            console.log('🟢 Enter key pressed, calling performSearch...');
            performSearch();
        }
    });
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (searchResults && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
    
    console.log('✅ All event listeners added successfully');
});
</script>
@endpush
