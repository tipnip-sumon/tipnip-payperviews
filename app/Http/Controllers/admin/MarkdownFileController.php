<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MarkdownFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $markdownFiles = MarkdownFile::with(['author', 'creator', 'updater'])
                ->select(['id', 'title', 'slug', 'category', 'status', 'is_published', 
                         'published_at', 'author_id', 'view_count', 'created_at', 'updated_at']);

            return DataTables::of($markdownFiles)
                ->addColumn('action', function ($file) {
                    return view('admin.markdown.partials.action-buttons', compact('file'));
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:markdown_files,slug',
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
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->all();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Process tags
            if (!empty($data['tags'])) {
                $data['tags'] = array_map('trim', explode(',', $data['tags']));
            }

            // Set author and creator
            $data['author_id'] = Auth::guard('admin')->id();
            $data['created_by'] = Auth::guard('admin')->id();

            // Handle published status
            if ($data['is_published'] && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $markdownFile = MarkdownFile::create($data);

            // Save to file if requested
            if ($request->boolean('save_to_file')) {
                $markdownFile->saveToFile();
            }

            return redirect()->route('admin.markdown.index')
                           ->with('success', 'Markdown file created successfully!');

        } catch (\Exception $e) {
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
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->all();
            
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

            // Save to file if requested
            if ($request->boolean('save_to_file')) {
                $markdownFile->saveToFile();
            }

            return redirect()->route('admin.markdown.index')
                           ->with('success', 'Markdown file updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating markdown file: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified markdown file
     */
    public function destroy($id)
    {
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            
            // Delete associated file
            $markdownFile->deleteFile();
            
            // Soft delete the record
            $markdownFile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Markdown file deleted successfully!'
            ]);

        } catch (\Exception $e) {
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
}
