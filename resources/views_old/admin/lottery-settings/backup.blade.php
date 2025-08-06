<x-layout>
    @section('top_title', 'Lottery Settings Backup & Restore')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Lottery Settings Backup & Restore')
            
            <!-- Statistics Cards -->
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Available Backups</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ count($backups) }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-primary my-auto float-end">
                                    <i class="fe fe-archive"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Last Backup</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ count($backups) > 0 ? $backups[0]['created_at']->diffForHumans() : 'Never' }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-success my-auto float-end">
                                    <i class="fe fe-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Total Size</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ number_format(array_sum(array_column($backups, 'size')) / 1024, 2) }} KB</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-info my-auto float-end">
                                    <i class="fe fe-hard-drive"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create New Backup</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Create a backup of current lottery settings configuration.</p>
                        <form method="POST" action="{{ route('admin.lottery-settings.backup.create') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Backup Name</label>
                                <input type="text" name="backup_name" class="form-control" 
                                       value="Backup_{{ now()->format('Y-m-d_H-i-s') }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-download me-2"></i>Create Backup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Import Settings</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Import settings from a backup file.</p>
                        <form method="POST" action="{{ route('admin.lottery-settings.import') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Backup File</label>
                                <input type="file" name="backup_file" class="form-control" accept=".json" required>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fe fe-upload me-2"></i>Import Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Available Backups -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Available Backups</h4>
                    </div>
                    <div class="card-body">
                        @if(count($backups) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Backup Name</th>
                                            <th>Size</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($backups as $backup)
                                            <tr>
                                                <td>{{ $backup['name'] }}</td>
                                                <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                                <td>{{ $backup['created_at']->format('M d, Y H:i A') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.lottery-settings.export') }}?backup={{ $backup['name'] }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fe fe-download me-1"></i>Download
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fe fe-archive display-1 text-muted"></i>
                                <h4 class="mt-3 text-muted">No Backups Available</h4>
                                <p class="text-muted">Create your first backup using the form above.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.lottery-settings.index') }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left me-2"></i>Back to Lottery Settings
                </a>
            </div>
        </div>
    @endsection
</x-layout>
