<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MarkdownFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = MarkdownFile::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100|unique:markdown_files,category',
            'display_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500'
        ], [
            'category_name.unique' => 'This category already exists.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $categoryName = $request->input('category_name');
            $displayName = $request->input('display_name');
            $description = $request->input('description');

            // Create a placeholder markdown file to establish the category
            $markdownFile = MarkdownFile::create([
                'title' => 'Welcome to ' . $displayName,
                'slug' => Str::slug('welcome-to-' . $categoryName),
                'content' => $this->generateWelcomeContent($displayName, $categoryName, $description),
                'category' => $categoryName,
                'status' => 'draft',
                'is_published' => false,
                'is_featured' => false,
                'meta_description' => $description ?: "Welcome to the {$displayName} section",
                'author_id' => auth('admin')->id(),
                'created_by' => auth('admin')->id(),
            ]);

            return redirect()->route('admin.categories.index')
                           ->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating category: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Show the form for editing a category
     */
    public function edit($categoryName)
    {
        $category = MarkdownFile::where('category', $categoryName)->first();
        
        if (!$category) {
            return redirect()->route('admin.categories.index')
                           ->with('error', 'Category not found.');
        }

        $filesCount = MarkdownFile::where('category', $categoryName)->count();
        
        return view('admin.categories.edit', compact('category', 'categoryName', 'filesCount'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $categoryName)
    {
        $validator = Validator::make($request->all(), [
            'new_category_name' => 'required|string|max:100|unique:markdown_files,category,' . $categoryName . ',category',
            'display_name' => 'required|string|max:150'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $newCategoryName = $request->input('new_category_name');
            $displayName = $request->input('display_name');

            // Update all files with this category
            MarkdownFile::where('category', $categoryName)
                       ->update(['category' => $newCategoryName]);

            return redirect()->route('admin.categories.index')
                           ->with('success', 'Category updated successfully! All files have been moved to the new category.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating category: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($categoryName)
    {
        try {
            $filesCount = MarkdownFile::where('category', $categoryName)->count();
            
            if ($filesCount > 0) {
                return back()->with('error', "Cannot delete category '{$categoryName}' because it contains {$filesCount} files. Please move or delete the files first.");
            }

            return redirect()->route('admin.categories.index')
                           ->with('success', 'Category deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }

    /**
     * Move files from one category to another
     */
    public function moveFiles(Request $request, $categoryName)
    {
        $validator = Validator::make($request->all(), [
            'target_category' => 'required|string|exists:markdown_files,category'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $targetCategory = $request->input('target_category');
            $filesCount = MarkdownFile::where('category', $categoryName)->count();
            
            MarkdownFile::where('category', $categoryName)
                       ->update(['category' => $targetCategory]);

            return back()->with('success', "Successfully moved {$filesCount} files from '{$categoryName}' to '{$targetCategory}'.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error moving files: ' . $e->getMessage());
        }
    }

    /**
     * Get categories as JSON for AJAX requests
     */
    public function getCategories()
    {
        try {
            $categories = MarkdownFile::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->orderBy('category')
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->category,
                        'label' => ucfirst(str_replace('-', ' ', $item->category)),
                        'count' => $item->count
                    ];
                });

            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate welcome content for new category
     */
    private function generateWelcomeContent($displayName, $categoryName, $description = null)
    {
        $content = "# Welcome to {$displayName}\n\n";
        
        if ($description) {
            $content .= "{$description}\n\n";
        } else {
            $content .= "This is the {$displayName} category.\n\n";
        }
        
        $content .= "This category has been created to organize content related to {$displayName}.\n\n";
        $content .= "## Getting Started\n\n";
        $content .= "You can now:\n";
        $content .= "- Create new files in this category\n";
        $content .= "- Organize your documentation\n";
        $content .= "- Maintain better content structure\n\n";
        $content .= "---\n\n";
        $content .= "*This welcome file was automatically created when the category was established.*";
        
        return $content;
    }
}
