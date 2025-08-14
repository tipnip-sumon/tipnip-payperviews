<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MarkdownFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MarkdownFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of markdown files
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            Log::info('DataTables AJAX request received', $request->all());
            
            $markdownFiles = MarkdownFile::with(['author', 'creator', 'updater'])
                ->select(['id', 'title', 'slug', 'category', 'status', 'is_published', 
                         'published_at', 'author_id', 'view_count', 'created_at', 'updated_at']);

            Log::info('Query count: ' . $markdownFiles->count());

            return DataTables::of($markdownFiles)
                ->addColumn('action', function ($file) {
                    return view('admin.markdown.partials.action-buttons', compact('file'))->render();
                })
                ->addColumn('author_name', function ($file) {
                    return $file->author ? $file->author->name : 'N/A';
                })
                ->addColumn('status_badge', function ($file) {
                    $statusClass = $file->status === 'active' ? 'success' : 
                                  ($file->status === 'inactive' ? 'danger' : 'warning');
                    return '<span class="badge bg-' . $statusClass . '">' . ucfirst($file->status) . '</span>';
                })
                ->addColumn('published_badge', function ($file) {
                    if ($file->is_published) {
                        return '<span class="badge bg-success">Published</span>';
                    }
                    return '<span class="badge bg-secondary">Draft</span>';
                })
                ->editColumn('created_at', function ($file) {
                    return $file->created_at->format('M d, Y h:i A');
                })
                ->rawColumns(['action', 'status_badge', 'published_badge'])
                ->make(true);
        }

        return view('admin.markdown.index');
    }

    /**
     * Show the form for creating a new markdown file
     */
    public function create()
    {
        $categories = $this->getCategories();
        return view('admin.markdown.create', compact('categories'));
    }

    /**
     * Store a newly created markdown file
     */
    public function store(Request $request)
    {
        Log::info('Store method called with data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:markdown_files,slug',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'status' => 'required|in:active,inactive,draft',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'save_to_file' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->all();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Map keywords to meta_keywords for database
            if (!empty($data['keywords'])) {
                $data['meta_keywords'] = $data['keywords'];
                unset($data['keywords']);
            }

            // Handle checkboxes (they won't be in request if unchecked)
            $data['is_published'] = $request->has('is_published') ? 1 : 0;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
            $data['save_to_file'] = $request->has('save_to_file') ? 1 : 0;

            // Set author and creator
            $data['author_id'] = Auth::guard('admin')->id();
            $data['created_by'] = Auth::guard('admin')->id();

            // Handle published status
            if ($data['is_published'] && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $markdownFile = MarkdownFile::create($data);
            Log::info('Markdown file created successfully', ['id' => $markdownFile->id]);

            // Save to file if requested
            if ($data['save_to_file']) {
                $markdownFile->saveToFile();
            }

            return redirect()->route('admin.markdown.index')
                           ->with('success', 'Markdown file created successfully!');

        } catch (\Exception $e) {
            Log::error('Markdown creation error: ' . $e->getMessage());
            return back()->with('error', 'Error creating markdown file: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified markdown file
     */
    public function show($id)
    {
        $markdownFile = MarkdownFile::with(['author', 'creator', 'updater'])->findOrFail($id);
        $markdownFile->incrementViewCount();
        
        return view('admin.markdown.show', compact('markdownFile'));
    }

    /**
     * Show the form for editing the specified markdown file
     */
    public function edit($id)
    {
        $markdownFile = MarkdownFile::findOrFail($id);
        $categories = $this->getCategories();
        
        return view('admin.markdown.edit', compact('markdownFile', 'categories'));
    }

    /**
     * Update the specified markdown file
     */
    public function update(Request $request, $id)
    {
        Log::info('Markdown update started', [
            'id' => $id,
            'request_data' => $request->all(),
            'admin_id' => Auth::guard('admin')->id()
        ]);

        $markdownFile = MarkdownFile::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:markdown_files,slug,' . $id,
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'save_to_file' => 'boolean'
        ]);

        if ($validator->fails()) {
            Log::warning('Markdown update validation failed', [
                'id' => $id,
                'errors' => $validator->errors()->toArray()
            ]);
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->all();
            
            // Handle checkbox values (they don't send false values)
            $data['is_published'] = $request->boolean('is_published');
            $data['is_featured'] = $request->boolean('is_featured');
            $data['save_to_file'] = $request->boolean('save_to_file');
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Process tags
            if (!empty($data['tags'])) {
                $data['tags'] = array_map('trim', explode(',', $data['tags']));
            }

            // Set updater
            $data['updated_by'] = Auth::guard('admin')->id();

            // Handle published status
            if ($data['is_published'] && empty($markdownFile->published_at)) {
                $data['published_at'] = now();
            }

            $markdownFile->update($data);

            Log::info('Markdown file updated successfully', [
                'id' => $markdownFile->id,
                'title' => $markdownFile->title,
                'admin_id' => Auth::guard('admin')->id()
            ]);

            // Save to file if requested
            if ($request->boolean('save_to_file')) {
                $markdownFile->saveToFile();
            }

            return redirect()->route('admin.markdown.index')
                           ->with('success', 'Markdown file "' . $markdownFile->title . '" updated successfully!');

        } catch (\Exception $e) {
            Log::error('Markdown update error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::guard('admin')->id()
            ]);
            return back()->with('error', 'Error updating markdown file: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified markdown file
     */
    public function destroy($id)
    {
        Log::info('Markdown delete attempt', [
            'id' => $id,
            'admin_id' => Auth::guard('admin')->id()
        ]);

        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            
            Log::info('Markdown file found for deletion', [
                'id' => $markdownFile->id,
                'title' => $markdownFile->title
            ]);
            
            // Delete associated file
            $markdownFile->deleteFile();
            
            // Soft delete the record
            $markdownFile->delete();

            Log::info('Markdown file deleted successfully', [
                'id' => $markdownFile->id,
                'title' => $markdownFile->title
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Markdown file "' . $markdownFile->title . '" deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Markdown delete error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::guard('admin')->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting markdown file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle published status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            $markdownFile->is_published = !$markdownFile->is_published;
            
            if ($markdownFile->is_published && !$markdownFile->published_at) {
                $markdownFile->published_at = now();
            }
            
            $markdownFile->updated_by = Auth::guard('admin')->id();
            $markdownFile->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'is_published' => $markdownFile->is_published
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish/unpublish markdown file
     */
    public function publish(Request $request, $id)
    {
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            $markdownFile->is_published = !$markdownFile->is_published;
            $markdownFile->published_at = $markdownFile->is_published ? now() : null;
            $markdownFile->updated_by = Auth::guard('admin')->id();
            $markdownFile->save();

            Log::info('Markdown file publication status changed', [
                'id' => $markdownFile->id,
                'title' => $markdownFile->title,
                'is_published' => $markdownFile->is_published,
                'admin_id' => Auth::guard('admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $markdownFile->is_published ? 'File published successfully!' : 'File unpublished successfully!',
                'is_published' => $markdownFile->is_published,
                'published_at' => $markdownFile->published_at ? $markdownFile->published_at->format('M d, Y h:i A') : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating publication status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle feature status of markdown file
     */
    public function toggleFeature(Request $request, $id)
    {
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            $markdownFile->is_featured = !$markdownFile->is_featured;
            $markdownFile->updated_by = Auth::guard('admin')->id();
            $markdownFile->save();

            Log::info('Markdown file feature status changed', [
                'id' => $markdownFile->id,
                'title' => $markdownFile->title,
                'is_featured' => $markdownFile->is_featured,
                'admin_id' => Auth::guard('admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $markdownFile->is_featured ? 'File featured successfully!' : 'File unfeatured successfully!',
                'is_featured' => $markdownFile->is_featured
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating feature status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate markdown file
     */
    public function duplicate($id)
    {
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            $duplicate = $markdownFile->duplicate();
            
            return redirect()->route('admin.markdown.edit', $duplicate->id)
                           ->with('success', 'Markdown file duplicated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error duplicating markdown file: ' . $e->getMessage());
        }
    }

    /**
     * Export markdown file
     */
    public function export($id)
    {
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            $content = $markdownFile->generateMarkdownContent();
            
            $filename = Str::slug($markdownFile->title) . '.md';
            
            return response($content)
                ->header('Content-Type', 'text/markdown')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting markdown file: ' . $e->getMessage());
        }
    }

    /**
     * Import markdown files from directory
     */
    public function importFromDirectory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'directory' => 'required|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $directory = $request->input('directory');
            $files = MarkdownFile::scanDirectory($directory);
            
            return back()->with('success', "Successfully imported {$files->count()} markdown files!");

        } catch (\Exception $e) {
            return back()->with('error', 'Error importing files: ' . $e->getMessage());
        }
    }

    /**
     * Preview markdown file
     */
    public function preview(Request $request, $id)
    {
        $markdownFile = MarkdownFile::findOrFail($id);
        
        if ($request->ajax()) {
            return response()->json([
                'html' => $markdownFile->html_content,
                'title' => $markdownFile->title
            ]);
        }
        
        return view('admin.markdown.preview', compact('markdownFile'));
    }

    /**
     * Get available categories
     */
    private function getCategories()
    {
        return [
            'general' => 'General',
            'documentation' => 'Documentation',
            'help' => 'Help & Support',
            'tutorial' => 'Tutorial',
            'announcement' => 'Announcement',
            'policy' => 'Policy',
            'terms' => 'Terms & Conditions',
            'privacy' => 'Privacy Policy',
            'faq' => 'FAQ',
            'guide' => 'User Guide',
            'api' => 'API Documentation',
            'changelog' => 'Changelog',
            'release-notes' => 'Release Notes'
        ];
    }

    /**
     * Get markdown statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => MarkdownFile::count(),
            'published' => MarkdownFile::where('is_published', true)->count(),
            'draft' => MarkdownFile::where('is_published', false)->count(),
            'active' => MarkdownFile::where('status', 'active')->count(),
            'inactive' => MarkdownFile::where('status', 'inactive')->count(),
            'by_category' => MarkdownFile::selectRaw('category, COUNT(*) as count')
                                       ->groupBy('category')
                                       ->pluck('count', 'category'),
            'total_views' => MarkdownFile::sum('view_count'),
            'recent' => MarkdownFile::orderBy('created_at', 'desc')->limit(5)->get()
        ];

        return view('admin.markdown.statistics', compact('stats'));
    }

    /**
     * Show categories management page or return categories as JSON
     */
    public function categories(Request $request)
    {
        $categories = MarkdownFile::selectRaw('category, COUNT(*) as count')
                                  ->groupBy('category')
                                  ->orderBy('category')
                                  ->get();

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'categories' => $categories->map(function($cat) {
                    return [
                        'value' => $cat->category,
                        'label' => ucfirst(str_replace('-', ' ', $cat->category)),
                        'count' => $cat->count
                    ];
                })
            ]);
        }

        // Otherwise return view (though the view doesn't exist yet)
        return response()->json([
            'success' => true,
            'categories' => $categories->map(function($cat) {
                return [
                    'value' => $cat->category,
                    'label' => ucfirst(str_replace('-', ' ', $cat->category)),
                    'count' => $cat->count
                ];
            })
        ]);
    }

    /**
     * Show stats page (alias for statistics)
     */
    public function stats()
    {
        return $this->statistics();
    }

    /**
     * Show export all files page
     */
    public function exportAll()
    {
        return view('admin.markdown.export');
    }

    /**
     * Show import form page
     */
    public function importForm()
    {
        return view('admin.markdown.import');
    }
}
