<x-adaptive_layout>
    <x-slot name="title">{{ $categoryInfo['title'] }}</x-slot>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('docs.index') }}">
                            <i class="fas fa-home me-1"></i>Documentation
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $categoryInfo['title'] }}</li>
                </ol>
            </nav>
            
            <div class="d-flex align-items-center mb-3">
                <div class="icon-shape icon-lg bg-primary text-white rounded-circle me-3">
                    <i class="{{ $categoryInfo['icon'] }}"></i>
                </div>
                <div>
                    <h1 class="h2 mb-1">{{ $categoryInfo['title'] }}</h1>
                    <p class="text-muted mb-0">{{ $categoryInfo['description'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="row">
        @forelse($documents as $document)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body p-4">
                    <!-- Document Meta -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-primary-soft text-primary">{{ ucfirst($document->category) }}</span>
                        <small class="text-muted">
                            <i class="fas fa-eye me-1"></i>{{ $document->view_count }}
                        </small>
                    </div>

                    <!-- Title -->
                    <h5 class="card-title mb-3">
                        <a href="{{ route('docs.show', [$document->category, $document->slug]) }}" 
                           class="text-decoration-none stretched-link">
                            {{ $document->title }}
                        </a>
                    </h5>

                    <!-- Excerpt -->
                    <p class="card-text text-muted mb-3">{{ $document->excerpt }}</p>

                    <!-- Meta Info -->
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>{{ $document->reading_time }} min read
                        </small>
                        <small class="text-muted">
                            {{ $document->published_at->format('M d, Y') }}
                        </small>
                    </div>

                    <!-- Tags -->
                    @if($document->tags && count($document->tags) > 0)
                    <div class="mt-3">
                        @foreach(array_slice($document->tags, 0, 3) as $tag)
                        <span class="badge bg-light text-dark me-1">{{ $tag }}</span>
                        @endforeach
                        @if(count($document->tags) > 3)
                        <span class="badge bg-light text-muted">+{{ count($document->tags) - 3 }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-file-alt text-muted" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-muted">No Documents Found</h4>
                <p class="text-muted mb-4">There are no documents available in this category yet.</p>
                <a href="{{ route('docs.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Documentation
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($documents->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Back to Categories -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="text-center">
                <a href="{{ route('docs.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Browse All Categories
                </a>
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

.card {
    position: relative;
}

.stretched-link::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
    content: "";
}
</style>
@endpush
