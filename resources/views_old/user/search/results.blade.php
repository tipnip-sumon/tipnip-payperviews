<x-smart_layout>

@section('title', 'Search Results')

@section('content')

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Search Results</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Search Results</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Search Header -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-1">Search Results for: <span class="text-primary">"{{ $query ?? '' }}"</span></h4>
                            <p class="text-muted mb-0">Found {{ $totalResults ?? 0 }} results</p>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- Search Filters -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fe fe-filter me-1"></i>Filter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-filter="all">All Results</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="videos">Videos</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="users">Users</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="transactions">Transactions</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="investments">Investments</a></li>
                                </ul>
                            </div>
                            
                            <!-- Sort Options -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fe fe-sort-desc me-1"></i>Sort
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-sort="relevance">Relevance</a></li>
                                    <li><a class="dropdown-item" href="#" data-sort="date">Date</a></li>
                                    <li><a class="dropdown-item" href="#" data-sort="title">Title</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Search Bar -->
                    <form method="GET" action="{{ route('search.results') }}" class="mb-4">
                        <div class="input-group search-input-group">
                            <input type="text" 
                                   class="form-control search-input" 
                                   name="q" 
                                   value="{{ $query ?? '' }}" 
                                   placeholder="Search videos, users, transactions, investments..."
                                   autocomplete="off">
                            <button class="btn btn-primary search-submit-btn" type="submit">
                                <i class="fe fe-search me-1"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($results) && count($results) > 0)
        <!-- Search Results -->
        <div class="row" id="searchResults">
            @foreach($results as $category => $items)
                @if(count($items) > 0)
                    <div class="col-12 mb-4">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="fe fe-{{ $category === 'videos' ? 'play-circle' : ($category === 'users' ? 'user' : ($category === 'transactions' ? 'credit-card' : 'star')) }} me-2"></i>
                                    {{ ucfirst($category) }} ({{ count($items) }})
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($items as $item)
                                        <div class="col-lg-6 col-xl-4 mb-3">
                                            <div class="search-result-item" data-category="{{ $category }}">
                                                @if($category === 'videos')
                                                    <div class="d-flex align-items-start">
                                                        <div class="avatar avatar-lg me-3 bg-primary-transparent">
                                                            <i class="fe fe-play-circle fs-18"></i>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <h6 class="mb-1 fw-semibold">{{ $item['title'] ?? 'Video Title' }}</h6>
                                                            <p class="text-muted mb-1 fs-12">{{ $item['category'] ?? 'Category' }}</p>
                                                            <p class="text-muted mb-2 fs-11">{{ $item['description'] ?? 'Video description...' }}</p>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-primary-transparent">{{ $item['duration'] ?? '5:30' }}</span>
                                                                <span class="badge bg-success-transparent">{{ $item['views'] ?? '1.2K' }} views</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($category === 'users')
                                                    <div class="d-flex align-items-start">
                                                        <div class="avatar avatar-md me-3">
                                                            <img src="{{ $item['avatar'] ?? asset('assets/images/users/16.jpg') }}" alt="User" class="rounded-circle">
                                                        </div>
                                                        <div class="flex-fill">
                                                            <h6 class="mb-1 fw-semibold">{{ $item['name'] ?? 'User Name' }}</h6>
                                                            <p class="text-muted mb-1 fs-12">{{ $item['role'] ?? 'Member' }}</p>
                                                            <p class="text-muted mb-2 fs-11">{{ $item['email'] ?? 'user@example.com' }}</p>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-success-transparent">{{ $item['status'] ?? 'Active' }}</span>
                                                                <span class="badge bg-info-transparent">Joined {{ $item['joined'] ?? '2024' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($category === 'transactions')
                                                    <div class="d-flex align-items-start">
                                                        <div class="avatar avatar-lg me-3 bg-success-transparent">
                                                            <i class="fe fe-credit-card fs-18"></i>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <h6 class="mb-1 fw-semibold">{{ $item['description'] ?? 'Transaction' }}</h6>
                                                            <p class="text-muted mb-1 fs-12">{{ $item['type'] ?? 'Deposit' }}</p>
                                                            <p class="text-muted mb-2 fs-11">{{ $item['date'] ?? date('Y-m-d H:i') }}</p>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-{{ $item['status'] === 'completed' ? 'success' : 'warning' }}-transparent">
                                                                    {{ $item['amount'] ?? '$0.00' }}
                                                                </span>
                                                                <span class="badge bg-info-transparent">{{ $item['status'] ?? 'Completed' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($category === 'investments')
                                                    <div class="d-flex align-items-start">
                                                        <div class="avatar avatar-lg me-3 bg-warning-transparent">
                                                            <i class="fe fe-star fs-18"></i>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <h6 class="mb-1 fw-semibold">{{ $item['name'] ?? 'Investment Plan' }}</h6>
                                                            <p class="text-muted mb-1 fs-12">{{ $item['return'] ?? '5% Daily' }}</p>
                                                            <p class="text-muted mb-2 fs-11">{{ $item['description'] ?? 'Investment description...' }}</p>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-warning-transparent">{{ $item['min_amount'] ?? '$100' }} min</span>
                                                                <span class="badge bg-success-transparent">{{ $item['duration'] ?? '30 days' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Pagination -->
        @if(isset($pagination) && $pagination['total_pages'] > 1)
            <div class="row">
                <div class="col-12">
                    <nav aria-label="Search results pagination">
                        <ul class="pagination justify-content-center">
                            @if($pagination['current_page'] > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('search.results', ['q' => $query, 'page' => $pagination['current_page'] - 1]) }}">
                                        <i class="fe fe-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            @for($i = 1; $i <= $pagination['total_pages']; $i++)
                                <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                    <a class="page-link" href="{{ route('search.results', ['q' => $query, 'page' => $i]) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            @if($pagination['current_page'] < $pagination['total_pages'])
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('search.results', ['q' => $query, 'page' => $pagination['current_page'] + 1]) }}">
                                        <i class="fe fe-chevron-right"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        @endif
    @else
        <!-- No Results Found -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fe fe-search fs-40 text-muted"></i>
                        </div>
                        <h4 class="mb-3">No Results Found</h4>
                        <p class="text-muted mb-4">We couldn't find any results for "<strong>{{ $query ?? '' }}</strong>"</p>
                        
                        <div class="mb-4">
                            <p class="text-muted mb-2">Try searching for:</p>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <span class="badge bg-primary-transparent search-suggestion" data-suggestion="videos">Videos</span>
                                <span class="badge bg-success-transparent search-suggestion" data-suggestion="users">Users</span>
                                <span class="badge bg-info-transparent search-suggestion" data-suggestion="transactions">Transactions</span>
                                <span class="badge bg-warning-transparent search-suggestion" data-suggestion="investments">Investment Plans</span>
                            </div>
                        </div>

                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fe fe-home me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Custom CSS for Search Results -->
<style>
.search-input-group {
    max-width: 600px;
    margin: 0 auto;
}

.search-input {
    border-radius: 25px 0 0 25px !important;
    border-right: none !important;
    padding: 12px 20px !important;
    font-size: 14px !important;
}

.search-submit-btn {
    border-radius: 0 25px 25px 0 !important;
    padding: 12px 20px !important;
    border-left: none !important;
}

.search-result-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
}

.search-result-item:hover {
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    border-color: #007bff;
}

.search-suggestion {
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-suggestion:hover {
    transform: scale(1.05);
}

.pagination .page-link {
    border-radius: 8px !important;
    margin: 0 2px;
    border: 1px solid #dee2e6;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(45deg, #007bff, #6f42c1);
    border-color: transparent;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
}

.avatar.bg-primary-transparent {
    background: rgba(13, 110, 253, 0.1) !important;
    color: #0d6efd !important;
}

.avatar.bg-success-transparent {
    background: rgba(25, 135, 84, 0.1) !important;
    color: #198754 !important;
}

.avatar.bg-warning-transparent {
    background: rgba(255, 193, 7, 0.1) !important;
    color: #ffc107 !important;
}

@media (max-width: 768px) {
    .search-result-item {
        margin-bottom: 15px;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 10px !important;
    }
}
</style>

<!-- JavaScript for Search Results -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('[data-filter]');
    const searchResults = document.querySelectorAll('.search-result-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;
            
            searchResults.forEach(result => {
                if (filter === 'all' || result.dataset.category === filter) {
                    result.closest('.col-lg-6').style.display = 'block';
                } else {
                    result.closest('.col-lg-6').style.display = 'none';
                }
            });
            
            // Update button states
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Search suggestions
    const suggestions = document.querySelectorAll('.search-suggestion');
    suggestions.forEach(suggestion => {
        suggestion.addEventListener('click', function() {
            const searchInput = document.querySelector('.search-input');
            searchInput.value = this.dataset.suggestion;
            searchInput.form.submit();
        });
    });
    
    // Sort functionality
    const sortButtons = document.querySelectorAll('[data-sort]');
    sortButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const sortBy = this.dataset.sort;
            
            // Add sorting logic here
            console.log('Sort by:', sortBy);
            
            // You can implement actual sorting logic based on your needs
        });
    });
});
</script>

@endsection
</x-smart_layout>
