<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MarkdownFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'file_path',
        'content',
        'meta_description',
        'meta_keywords',
        'category',
        'tags',
        'status',
        'is_published',
        'published_at',
        'author_id',
        'view_count',
        'file_size',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('status', 'active')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('meta_description', 'like', "%{$search}%")
              ->orWhere('meta_keywords', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getExcerptAttribute($length = 150)
    {
        return Str::limit(strip_tags($this->getHtmlContentAttribute()), $length);
    }

    public function getHtmlContentAttribute()
    {
        return $this->parseMarkdownToHtml($this->content);
    }

    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return ceil($wordCount / 200); // Average reading speed 200 words per minute
    }

    public function getFileExistsAttribute()
    {
        return $this->file_path && Storage::exists($this->file_path);
    }

    // Methods
    public function parseMarkdownToHtml($content)
    {
        // Basic Markdown to HTML conversion
        $content = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $content);
        $content = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $content);
        $content = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $content);
        $content = preg_replace('/^#### (.*$)/m', '<h4>$1</h4>', $content);
        $content = preg_replace('/^##### (.*$)/m', '<h5>$1</h5>', $content);
        $content = preg_replace('/^###### (.*$)/m', '<h6>$1</h6>', $content);
        
        // Bold and Italic
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        
        // Links
        $content = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $content);
        
        // Images
        $content = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1" class="img-fluid">', $content);
        
        // Code blocks
        $content = preg_replace('/`([^`]+)`/', '<code>$1</code>', $content);
        
        // Line breaks
        $content = nl2br($content);
        
        return $content;
    }

    public function saveToFile()
    {
        if (!$this->file_path) {
            $this->file_path = 'markdown/' . $this->slug . '.md';
        }

        $markdownContent = $this->generateMarkdownContent();
        Storage::put($this->file_path, $markdownContent);
        
        $this->file_size = Storage::size($this->file_path);
        $this->save();

        return $this->file_path;
    }

    public function loadFromFile()
    {
        if (!$this->file_exists) {
            return false;
        }

        $content = Storage::get($this->file_path);
        $this->parseFileContent($content);
        
        return true;
    }

    public function deleteFile()
    {
        if ($this->file_exists) {
            Storage::delete($this->file_path);
        }
    }

    public function generateMarkdownContent()
    {
        $markdown = "---\n";
        $markdown .= "title: \"{$this->title}\"\n";
        $markdown .= "slug: \"{$this->slug}\"\n";
        $markdown .= "category: \"{$this->category}\"\n";
        $markdown .= "tags: [" . implode(', ', $this->tags ?? []) . "]\n";
        $markdown .= "meta_description: \"{$this->meta_description}\"\n";
        $markdown .= "meta_keywords: \"{$this->meta_keywords}\"\n";
        $markdown .= "status: \"{$this->status}\"\n";
        $markdown .= "is_published: " . ($this->is_published ? 'true' : 'false') . "\n";
        $markdown .= "published_at: \"{$this->published_at}\"\n";
        $markdown .= "created_at: \"{$this->created_at}\"\n";
        $markdown .= "---\n\n";
        $markdown .= $this->content;

        return $markdown;
    }

    private function parseFileContent($content)
    {
        // Parse frontmatter
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $content, $matches)) {
            $frontmatter = $matches[1];
            $this->content = trim($matches[2]);

            // Parse frontmatter fields
            $lines = explode("\n", $frontmatter);
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    [$key, $value] = explode(':', $line, 2);
                    $key = trim($key);
                    $value = trim($value, ' "');

                    switch ($key) {
                        case 'title':
                            $this->title = $value;
                            break;
                        case 'slug':
                            $this->slug = $value;
                            break;
                        case 'category':
                            $this->category = $value;
                            break;
                        case 'tags':
                            $this->tags = json_decode($value) ?: [];
                            break;
                        case 'meta_description':
                            $this->meta_description = $value;
                            break;
                        case 'meta_keywords':
                            $this->meta_keywords = $value;
                            break;
                        case 'status':
                            $this->status = $value;
                            break;
                        case 'is_published':
                            $this->is_published = $value === 'true';
                            break;
                        case 'published_at':
                            $this->published_at = $value;
                            break;
                    }
                }
            }
        } else {
            $this->content = $content;
        }
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function duplicate()
    {
        $duplicate = $this->replicate();
        $duplicate->title = $this->title . ' (Copy)';
        $duplicate->slug = $this->slug . '-copy';
        $duplicate->is_published = false;
        $duplicate->published_at = null;
        $duplicate->view_count = 0;
        $duplicate->file_path = null;
        $duplicate->save();

        return $duplicate;
    }

    // Static methods
    public static function createFromFile($filePath, $authorId = null)
    {
        if (!Storage::exists($filePath)) {
            throw new \Exception("File does not exist: {$filePath}");
        }

        $content = Storage::get($filePath);
        $instance = new static();
        $instance->file_path = $filePath;
        $instance->author_id = $authorId;
        $instance->file_size = Storage::size($filePath);
        $instance->parseFileContent($content);
        $instance->save();

        return $instance;
    }

    public static function scanDirectory($directory = 'markdown')
    {
        $files = Storage::files($directory);
        $markdownFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'md';
        });

        $results = [];
        foreach ($markdownFiles as $file) {
            $existing = static::where('file_path', $file)->first();
            if (!$existing) {
                try {
                    $results[] = static::createFromFile($file);
                } catch (\Exception $e) {
                    Log::error("Failed to create MarkdownFile from {$file}: " . $e->getMessage());
                }
            } else {
                $results[] = $existing;
            }
        }

        return collect($results);
    }
}
