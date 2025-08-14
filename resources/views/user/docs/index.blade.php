<x-adaptive_layout>
    <x-slot name="title">Documentation & Help Center</x-slot>

<script>
// Define global search function
window.performSearch = function() {
    var input = document.getElementById('docSearch');
    var results = document.getElementById('searchResults');
    
    if (!input) return;
    
    var query = input.value.trim();
    
    if (query.length < 2) {
        if (results) results.style.display = 'none';
        return;
    }
    
    if (results) {
        results.innerHTML = '<div class="search-loading"><i class="ri-loader-4-line"></i> Searching...</div>';
        results.style.display = 'block';
    }
    
    // Make API call
    fetch('/api/docs/search?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.results && data.results.length > 0) {
                displaySearchResults(data.results);
            } else {
                displayNoResults();
            }
        })
        .catch(error => {
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
                <div class="hero-section text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 1rem;">
                    <h1 class="display-4 fw-bold text-white mb-3">
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
                                    <div class="icon-wrapper me-3">
                                        <i class="ri-folder-2-line fs-2" style="color: #667eea;"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1" style="color: #212529;">{{ $category['category'] }}</h5>
                                        <small class="text-muted">{{ $category['count'] }} {{ Str::plural('article', $category['count']) }}</small>
                                    </div>
                                </div>
                                <p class="card-text text-muted mb-3">{{ $category['description'] ?? 'Browse articles in this category' }}</p>
                                <a href="{{ route('docs.category', $category['category']) }}" class="btn btn-outline-primary btn-sm">
                                    Browse Articles <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Articles Section -->
        @if($recent->count() > 0)
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="h3 mb-4" style="color: #212529;">Recent Articles</h2>
                <div class="row g-4">
                    @foreach($recent as $doc)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-lift">
                            <div class="card-body p-4">
                                <a href="{{ route('docs.show', [$doc->category, $doc->slug]) }}" class="text-decoration-none">
                                    <h5 class="card-title" style="color: #212529;">{{ $doc->title }}</h5>
                                </a>
                                <p class="card-text text-muted mb-3">{{ Str::limit($doc->excerpt ?? $doc->content, 120) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="ri-calendar-line me-1"></i>{{ $doc->created_at->format('M j, Y') }}
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
        </div>
        @endif

        <!-- Popular Articles Section -->
        @if($featured->count() > 0)
        <div class="row">
            <div class="col-12">
                <h2 class="h3 mb-4" style="color: #212529;">
                    <i class="ri-fire-line me-2" style="color: #ff6b35;"></i>Popular Articles
                </h2>
                <div class="row g-4">
                    @foreach($featured as $doc)
                    <div class="col-lg-6 col-md-12">
                        <div class="card h-100 border-0 shadow-sm hover-lift popular-article">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start">
                                    <div class="popular-icon me-3">
                                        <i class="ri-article-line"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="{{ route('docs.show', [$doc->category, $doc->slug]) }}" class="text-decoration-none">
                                            <h5 class="card-title mb-2" style="color: #212529;">{{ $doc->title }}</h5>
                                        </a>
                                        <p class="card-text text-muted mb-3">{{ Str::limit($doc->excerpt ?? $doc->content, 120) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="article-stats">
                                                <span class="badge bg-primary-soft text-primary me-2">
                                                    <i class="ri-eye-line me-1"></i>{{ $doc->view_count ?? 0 }} views
                                                </span>
                                                <span class="badge bg-secondary-soft text-secondary">
                                                    <i class="ri-folder-2-line me-1"></i>{{ $doc->category }}
                                                </span>
                                            </div>
                                            <a href="{{ route('docs.show', [$doc->category, $doc->slug]) }}" class="btn btn-sm btn-outline-primary">
                                                Read Now <i class="ri-arrow-right-line ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

@push('styles')
<style>
.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.1"/><circle cx="10" cy="50" r="1" fill="white" opacity="0.1"/><circle cx="90" cy="30" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.icon-wrapper {
    width: 50px;
    height: 50px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#searchResults {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    box-shadow: 0 4px 25px rgba(0,0,0,0.15);
    max-height: 400px;
    overflow-y: auto;
    position: relative;
    z-index: 1000;
}

.search-result-item {
    padding: 1rem;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: all 0.2s ease;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.search-result-item h6 {
    color: #212529 !important;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.search-result-item p {
    color: #495057 !important;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    line-height: 1.4;
}

.search-result-item small {
    background: #e9ecef;
    color: #6c757d !important;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-weight: 500;
}

.search-loading {
    text-align: center;
    padding: 1.5rem;
    color: #6c757d;
}

.search-loading i {
    animation: spin 1s linear infinite;
    margin-right: 0.5rem;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.search-result-item.text-center {
    text-align: center;
    padding: 1.5rem;
    border-radius: 0.5rem;
}

/* Popular Articles Enhanced Styles */
.popular-article {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-left: 4px solid #ff6b35;
    transition: all 0.3s ease;
}

.popular-article:hover {
    border-left-color: #667eea;
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.popular-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.popular-article .card-title {
    font-weight: 600;
    line-height: 1.3;
    transition: color 0.3s ease;
}

.popular-article:hover .card-title {
    color: #667eea !important;
}

.article-stats .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
    border-radius: 20px;
}

.bg-primary-soft {
    background-color: rgba(102, 126, 234, 0.1) !important;
}

.bg-secondary-soft {
    background-color: rgba(108, 117, 125, 0.1) !important;
}

.text-secondary {
    color: #6c757d !important;
}

/* Responsive adjustments for Popular Articles */
@media (max-width: 768px) {
    .popular-article .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .popular-icon {
        align-self: center;
        margin-bottom: 1rem;
    }
    
    .article-stats {
        justify-content: center;
        margin-bottom: 1rem;
    }
}
</style>
@endpush

</x-adaptive_layout>
