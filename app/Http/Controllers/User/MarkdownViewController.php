<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MarkdownFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MarkdownViewController extends Controller
{
    /**
     * Display the documentation/help center index
     */
    public function index(Request $request)
    {
        // Redirect authenticated users to dashboard
        if (Auth::check()) {
            return redirect()->route('user.dashboard')->with('info', 'You already have access to the dashboard. Documentation is for guests.');
        }
        
        // Cache categories for 1 hour
        $categories = Cache::remember('markdown_categories', 3600, function () {
            return MarkdownFile::published()
                ->selectRaw('category, COUNT(*) as count, MIN(created_at) as created_at')
                ->groupBy('category')
                ->orderBy('created_at')
                ->get()
                ->map(function ($item) {
                    return [
                        'category' => $item->category,
                        'count' => $item->count,
                        'title' => $this->getCategoryTitle($item->category),
                        'description' => $this->getCategoryDescription($item->category),
                        'icon' => $this->getCategoryIcon($item->category)
                    ];
                });
        });

        // Get featured/popular documents
        $featured = MarkdownFile::published()
            ->orderByDesc('view_count')
            ->limit(6)
            ->get();

        // Get recent documents
        $recent = MarkdownFile::published()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('user.docs.index', compact('categories', 'featured', 'recent'));
    }

    /**
     * Display documents in a specific category
     */
    public function category($category, Request $request)
    {
        // Redirect authenticated users to dashboard
        if (Auth::check()) {
            return redirect()->route('user.dashboard')->with('info', 'You already have access to the dashboard. Documentation is for guests.');
        }
        
        $documents = MarkdownFile::published()
            ->byCategory($category)
            ->orderBy('title')
            ->paginate(12);

        if ($documents->isEmpty()) {
            abort(404, 'Category not found or no documents available.');
        }

        $categoryInfo = [
            'name' => $category,
            'title' => $this->getCategoryTitle($category),
            'description' => $this->getCategoryDescription($category),
            'icon' => $this->getCategoryIcon($category)
        ];

        return view('user.docs.category', compact('documents', 'categoryInfo'));
    }

    /**
     * Display a specific markdown document
     */
    public function show($category, $slug, Request $request)
    {
        // Redirect authenticated users to dashboard
        if (Auth::check()) {
            return redirect()->route('user.dashboard')->with('info', 'You already have access to the dashboard. Documentation is for guests.');
        }
        
        $document = MarkdownFile::published()
            ->where('slug', $slug)
            ->where('category', $category)
            ->firstOrFail();

        // Record view (async to avoid slowing page load)
        $this->recordViewAsync($document->id);

        // Get related documents
        $related = MarkdownFile::published()
            ->where('category', $category)
            ->where('id', '!=', $document->id)
            ->limit(5)
            ->get();

        // Generate table of contents from headers
        $tableOfContents = $this->generateTableOfContents($document->content);

        return view('user.docs.show', compact('document', 'related', 'tableOfContents'));
    }

    /**
     * Search for documents
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $category = $request->get('category');
        $limit = min($request->get('limit', 10), 50);

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ]);
        }

        $results = MarkdownFile::published()
            ->search($query)
            ->when($category, function ($q) use ($category) {
                return $q->byCategory($category);
            })
            ->limit($limit)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'slug' => $doc->slug,
                    'category' => $doc->category,
                    'excerpt' => $doc->excerpt,
                    'url' => route('docs.show', [$doc->category, $doc->slug])
                ];
            });

        return response()->json([
            'success' => true,
            'results' => $results,
            'count' => $results->count()
        ]);
    }

    /**
     * Get list of documents for API
     */
    public function list(Request $request)
    {
        $category = $request->get('category');
        $limit = min($request->get('limit', 20), 100);

        $documents = MarkdownFile::published()
            ->when($category, function ($q) use ($category) {
                return $q->byCategory($category);
            })
            ->orderBy('title')
            ->limit($limit)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'slug' => $doc->slug,
                    'category' => $doc->category,
                    'excerpt' => $doc->excerpt,
                    'reading_time' => $doc->reading_time,
                    'view_count' => $doc->view_count,
                    'url' => route('docs.show', [$doc->category, $doc->slug]),
                    'published_at' => $doc->published_at->format('M d, Y')
                ];
            });

        return response()->json([
            'success' => true,
            'documents' => $documents,
            'count' => $documents->count()
        ]);
    }

    /**
     * Record document view via API
     */
    public function recordView($id, Request $request)
    {
        try {
            $document = MarkdownFile::findOrFail($id);
            $this->recordViewAsync($document->id);

            return response()->json([
                'success' => true,
                'view_count' => $document->fresh()->view_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }
    }

    /**
     * Record view asynchronously
     */
    private function recordViewAsync($documentId)
    {
        // Use a job or simple increment to avoid blocking the response
        dispatch(function () use ($documentId) {
            try {
                MarkdownFile::find($documentId)?->incrementViewCount();
            } catch (\Exception $e) {
                Log::error("Failed to record view for document {$documentId}: " . $e->getMessage());
            }
        })->afterResponse();
    }

    /**
     * Generate table of contents from markdown headers
     */
    private function generateTableOfContents($content)
    {
        $toc = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (preg_match('/^(#{1,6})\s+(.+)$/', trim($line), $matches)) {
                $level = strlen($matches[1]);
                $title = trim($matches[2]);
                $anchor = Str::slug($title);

                $toc[] = [
                    'level' => $level,
                    'title' => $title,
                    'anchor' => $anchor
                ];
            }
        }

        return $toc;
    }

    /**
     * Get category title
     */
    private function getCategoryTitle($category)
    {
        $titles = [
            'general' => 'General Information',
            'documentation' => 'Documentation',
            'help' => 'Help & Support',
            'tutorial' => 'Tutorials',
            'announcement' => 'Announcements',
            'policy' => 'Policies',
            'terms' => 'Terms & Conditions',
            'privacy' => 'Privacy Policy',
            'faq' => 'Frequently Asked Questions',
            'guide' => 'User Guides',
            'api' => 'API Documentation',
            'changelog' => 'Changelog',
            'release-notes' => 'Release Notes'
        ];

        return $titles[$category] ?? ucfirst(str_replace('-', ' ', $category));
    }

    /**
     * Get category description
     */
    private function getCategoryDescription($category)
    {
        $descriptions = [
            'general' => 'General information and overview',
            'documentation' => 'Complete platform documentation',
            'help' => 'Get help and support for common issues',
            'tutorial' => 'Step-by-step tutorials and guides',
            'announcement' => 'Latest news and announcements',
            'policy' => 'Platform policies and guidelines',
            'terms' => 'Terms of service and legal information',
            'privacy' => 'Privacy policy and data protection',
            'faq' => 'Answers to commonly asked questions',
            'guide' => 'Comprehensive user guides',
            'api' => 'Developer documentation and API reference',
            'changelog' => 'Platform updates and changes',
            'release-notes' => 'Release notes and version history'
        ];

        return $descriptions[$category] ?? 'Documents in this category';
    }

    /**
     * Get category icon
     */
    private function getCategoryIcon($category)
    {
        $icons = [
            'general' => 'fas fa-info-circle',
            'documentation' => 'fas fa-book',
            'help' => 'fas fa-question-circle',
            'tutorial' => 'fas fa-graduation-cap',
            'announcement' => 'fas fa-bullhorn',
            'policy' => 'fas fa-shield-alt',
            'terms' => 'fas fa-file-contract',
            'privacy' => 'fas fa-user-shield',
            'faq' => 'fas fa-comments',
            'guide' => 'fas fa-map',
            'api' => 'fas fa-code',
            'changelog' => 'fas fa-list-alt',
            'release-notes' => 'fas fa-tag'
        ];

        return $icons[$category] ?? 'fas fa-file-text';
    }

    /**
     * Show privacy policy page
     */
    public function showPrivacy(Request $request)
    {
        return $this->show('policy', 'privacy-policy', $request);
    }

    /**
     * Show terms and conditions page
     */
    public function showTerms(Request $request)
    {
        return $this->show('terms', 'terms-and-conditions', $request);
    }

    /**
     * Show FAQ page
     */
    public function showFaq(Request $request)
    {
        return $this->show('faq', 'frequently-asked-questions', $request);
    }
}
