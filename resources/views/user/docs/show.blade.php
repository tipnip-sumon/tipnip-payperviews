<x-adaptive_layout>
    <x-slot name="title">{{ $document->title }}</x-slot>
    <x-slot name="meta_description">{{ $document->meta_description }}</x-slot>
    <x-slot name="meta_keywords">{{ $document->meta_keywords }}</x-slot>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('docs.index') }}">
                            <i class="fas fa-home me-1"></i>Documentation
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('docs.category', $document->category) }}">
                            {{ ucfirst($document->category) }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $document->title }}</li>
                </ol>
            </nav>

            <!-- Document Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($document->category) }}</span>
                        <div class="text-muted small">
                            <i class="fas fa-eye me-1"></i>{{ $document->view_count }} views
                        </div>
                    </div>
                    
                    <h1 class="display-6 fw-bold mb-3">{{ $document->title }}</h1>
                    
                    @if($document->meta_description)
                    <p class="lead text-muted mb-3">{{ $document->meta_description }}</p>
                    @endif
                    
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <span>
                            <i class="fas fa-clock me-1"></i>{{ $document->reading_time }} min read
                        </span>
                        <span>
                            <i class="fas fa-calendar me-1"></i>{{ $document->published_at->format('M d, Y') }}
                        </span>
                        @if($document->updated_at != $document->created_at)
                        <span>
                            <i class="fas fa-edit me-1"></i>Updated {{ $document->updated_at->diffForHumans() }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Document Content -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="markdown-content">
                        {!! $document->html_content !!}
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($document->tags && count($document->tags) > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="mb-3">Tags</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($document->tags as $tag)
                        <span class="badge bg-light text-dark border">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Related Documents -->
            @if($related->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-layer-group me-2"></i>Related Documents
                    </h5>
                    <div class="row g-3">
                        @foreach($related as $relatedDoc)
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-2">
                                    <a href="{{ route('docs.show', [$relatedDoc->category, $relatedDoc->slug]) }}" 
                                       class="text-decoration-none">
                                        {{ $relatedDoc->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-2">{{ $relatedDoc->excerpt }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $relatedDoc->reading_time }} min
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>{{ $relatedDoc->view_count }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Table of Contents -->
            @if(count($tableOfContents) > 0)
            <div class="card border-0 shadow-sm sticky-top mb-4" style="top: 20px;">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>Table of Contents
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0" id="tocList">
                        @foreach($tableOfContents as $item)
                        <li class="toc-item" style="margin-left: {{ ($item['level'] - 1) * 15 }}px;">
                            <a href="#{{ $item['anchor'] }}" class="text-decoration-none d-block py-1 toc-link" 
                               data-level="{{ $item['level'] }}">
                                {{ $item['title'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print Document
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard(window.location.href, this)">
                            <i class="fas fa-copy me-1"></i>Copy Link
                        </button>
                        <a href="{{ route('user.support.create') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-question-circle me-1"></i>Ask Question
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-compass me-2"></i>Navigation
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('docs.category', $document->category) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to {{ ucfirst($document->category) }}
                        </a>
                        <a href="{{ route('docs.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-home me-1"></i>Documentation Home
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
.markdown-content {
    line-height: 1.7;
}

.markdown-content h1,
.markdown-content h2,
.markdown-content h3,
.markdown-content h4,
.markdown-content h5,
.markdown-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #2d3748;
    position: relative;
}

.markdown-content h1:first-child,
.markdown-content h2:first-child,
.markdown-content h3:first-child {
    margin-top: 0;
}

.markdown-content h1 { border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; }
.markdown-content h2 { border-bottom: 1px solid #e2e8f0; padding-bottom: 0.3rem; }

.markdown-content p {
    margin-bottom: 1rem;
    color: #4a5568;
}

.markdown-content ul,
.markdown-content ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.markdown-content li {
    margin-bottom: 0.25rem;
}

.markdown-content blockquote {
    border-left: 4px solid #3182ce;
    background-color: #ebf8ff;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0.375rem;
}

.markdown-content code {
    background-color: #f7fafc;
    color: #e53e3e;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.markdown-content pre {
    background-color: #2d3748;
    color: #e2e8f0;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1rem 0;
}

.markdown-content pre code {
    background: none;
    color: inherit;
    padding: 0;
}

.markdown-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    overflow: hidden;
}

.markdown-content th,
.markdown-content td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.markdown-content th {
    background-color: #f7fafc;
    font-weight: 600;
    color: #2d3748;
}

.markdown-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    margin: 1rem 0;
}

.toc-link {
    color: #4a5568;
    font-size: 0.875rem;
    transition: color 0.15s ease-in-out;
}

.toc-link:hover,
.toc-link.active {
    color: #3182ce;
    text-decoration: underline !important;
}

@media print {
    .col-lg-4 {
        display: none;
    }
    .col-lg-8 {
        width: 100%;
    }
}
</style>
@endpush

@push('script')
<script>
// Record document view
fetch(`{{ route('api.docs.view', $document->id) }}`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
    }
}).catch(console.error);

// Table of Contents functionality
document.addEventListener('DOMContentLoaded', function() {
    const tocLinks = document.querySelectorAll('.toc-link');
    const headings = document.querySelectorAll('.markdown-content h1, .markdown-content h2, .markdown-content h3, .markdown-content h4, .markdown-content h5, .markdown-content h6');
    
    // Add IDs to headings based on TOC anchors
    headings.forEach((heading, index) => {
        if (tocLinks[index]) {
            const anchor = tocLinks[index].getAttribute('href').substring(1);
            heading.id = anchor;
        }
    });
    
    // Smooth scrolling for TOC links
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Highlight active TOC item on scroll
    window.addEventListener('scroll', function() {
        let current = '';
        headings.forEach(heading => {
            const rect = heading.getBoundingClientRect();
            if (rect.top <= 100) {
                current = heading.id;
            }
        });
        
        tocLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
});
</script>
@endpush
