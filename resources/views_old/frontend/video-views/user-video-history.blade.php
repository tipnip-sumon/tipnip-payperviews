<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('page_title', $pageTitle)
    @section('content')
        <div class="container py-4 my-4">
            <h2 class="mb-4 text-center fw-bold text-primary">Video Viewing History</h2>
            <div class="row mb-4 g-3">
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-dollar-sign fa-2x text-success"></i></div>
                            <div class="fw-bold fs-4 text-success">${{ number_format($totalEarnings, 4) }}</div>
                            <div class="small text-muted">Total Earnings</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-play-circle fa-2x text-primary"></i></div>
                            <div class="fw-bold fs-4">{{ $totalVideosWatched }}</div>
                            <div class="small text-muted">Total Watched</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-film fa-2x text-info"></i></div>
                            <div class="fw-bold fs-4">{{ $uniqueVideosWatched }}</div>
                            <div class="small text-muted">Unique Videos</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-chart-line fa-2x text-warning"></i></div>
                            <div class="fw-bold fs-4">${{ number_format($averagePerVideo, 4) }}</div>
                            <div class="small text-muted">Avg. Per Video</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list text-primary"></i> History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Video</th>
                                    <th>Watched At</th>
                                    <th>Earned</th>
                                    <th>IP Address</th>
                                    <th>Device Info</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($viewHistory as $i => $view)
                                    <tr>
                                        <td class="text-muted">{{ ($viewHistory->currentPage() - 1) * $viewHistory->perPage() + $i + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($view->videoLink && $view->videoLink->thumbnail)
                                                    <img src="{{ $view->videoLink->thumbnail }}" alt="thumb" class="rounded" style="width:40px;height:28px;object-fit:cover;">
                                                @else
                                                    <span class="bg-light rounded d-inline-flex align-items-center justify-content-center" style="width:40px;height:28px;"><i class="fas fa-video text-muted"></i></span>
                                                @endif
                                                <span class="fw-semibold">{{ $view->videoLink->title ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $view->viewed_at ? $view->viewed_at->format('Y-m-d H:i') : '-' }}</span></td>
                                        <td><span class="badge bg-success fs-6">${{ number_format($view->earned_amount, 4) }}</span></td>
                                        <td><span class="font-monospace small">{{ $view->ip_address }}</span></td>
                                        <td><span class="small text-muted">{{ Str::limit($view->device_info, 30) }}</span></td>
                                        <td>
                                            @if($view->videoLink)
                                                <a href="{{ route('video.show', $view->videoLink->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @else
                                                <span class="badge bg-warning">Video Unavailable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No video history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($viewHistory->hasPages())
                    <div class="card-footer bg-white border-top-0">
                        {{ $viewHistory->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endsection
</x-smart_layout>