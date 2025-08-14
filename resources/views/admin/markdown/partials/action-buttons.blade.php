<div class="btn-group" role="group">
    <!-- View Button -->
    <a href="{{ route('admin.markdown.show', $file->id) }}" 
       class="btn btn-info btn-sm" 
       title="View">
        <i class="ri-eye-line"></i>
    </a>
    
    <!-- Edit Button -->
    <a href="{{ route('admin.markdown.edit', $file->id) }}" 
       class="btn btn-warning btn-sm" 
       title="Edit">
        <i class="ri-edit-line"></i>
    </a>
    
    <!-- Toggle Status -->
    <a href="{{ route('admin.markdown.toggle-status', $file->id) }}" 
       class="btn btn-{{ $file->status === 'active' ? 'danger' : 'success' }} btn-sm toggle-status" 
       title="{{ $file->status === 'active' ? 'Deactivate' : 'Activate' }}">
        <i class="ri-{{ $file->status === 'active' ? 'pause' : 'play' }}-line"></i>
    </a>
    
    <!-- Export Button -->
    <a href="{{ route('admin.markdown.export', $file->id) }}" 
       class="btn btn-secondary btn-sm" 
       title="Export">
        <i class="ri-download-line"></i>
    </a>
    
    <!-- Publish/Unpublish Button -->
    <form method="POST" action="{{ route('admin.markdown.publish', $file->id) }}" style="display: inline;">
        @csrf
        <button type="submit" 
                class="btn btn-{{ $file->is_published ? 'warning' : 'success' }} btn-sm publish-btn" 
                data-id="{{ $file->id }}"
                title="{{ $file->is_published ? 'Unpublish' : 'Publish' }}">
            <i class="ri-{{ $file->is_published ? 'eye-off' : 'eye' }}-line"></i>
        </button>
    </form>
    
    <!-- Feature/Unfeature Button -->
    <form method="POST" action="{{ route('admin.markdown.feature', $file->id) }}" style="display: inline;">
        @csrf
        <button type="submit" 
                class="btn btn-{{ $file->is_featured ? 'dark' : 'primary' }} btn-sm feature-btn" 
                data-id="{{ $file->id }}"
                title="{{ $file->is_featured ? 'Remove from Featured' : 'Add to Featured' }}">
            <i class="ri-{{ $file->is_featured ? 'star-fill' : 'star' }}-line"></i>
        </button>
    </form>
    
    <!-- Duplicate Button -->
    <form method="POST" action="{{ route('admin.markdown.duplicate', $file->id) }}" style="display: inline;">
        @csrf
        <button type="submit" 
                class="btn btn-info btn-sm" 
                title="Duplicate">
            <i class="ri-file-copy-line"></i>
        </button>
    </form>
    
    <!-- Delete Button -->
    <form method="POST" action="{{ route('admin.markdown.destroy', $file->id) }}" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="btn btn-danger btn-sm delete-btn" 
                data-title="{{ $file->title }}"
                title="Delete">
            <i class="ri-delete-bin-line"></i>
        </button>
    </form>
</div>
