<?php

namespace App\Console\Commands;

use App\Models\MarkdownFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarkdownManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'markdown:manage 
                           {action : The action to perform (list|create|update|delete|export|import|sync|stats)}
                           {--id= : The ID of the markdown file (for update/delete/export)}
                           {--title= : The title of the markdown file}
                           {--slug= : The slug of the markdown file}
                           {--content= : The content of the markdown file}
                           {--category= : The category of the markdown file}
                           {--status= : The status of the markdown file (active|inactive|draft)}
                           {--file= : The file path for import/export}
                           {--directory= : The directory to scan for markdown files}
                           {--published : Mark as published}
                           {--save-to-file : Save to physical file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage markdown files through CLI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                return $this->listMarkdownFiles();
            case 'create':
                return $this->createMarkdownFile();
            case 'update':
                return $this->updateMarkdownFile();
            case 'delete':
                return $this->deleteMarkdownFile();
            case 'export':
                return $this->exportMarkdownFile();
            case 'import':
                return $this->importMarkdownFile();
            case 'sync':
                return $this->syncFromDirectory();
            case 'stats':
                return $this->showStatistics();
            default:
                $this->error("Unknown action: {$action}");
                $this->line('Available actions: list, create, update, delete, export, import, sync, stats');
                return 1;
        }
    }

    private function listMarkdownFiles()
    {
        $files = MarkdownFile::with('author')->get();

        if ($files->isEmpty()) {
            $this->info('No markdown files found.');
            return 0;
        }

        $this->info('Markdown Files:');
        $this->line('');

        $headers = ['ID', 'Title', 'Slug', 'Category', 'Status', 'Published', 'Author', 'Views', 'Created'];
        $rows = [];

        foreach ($files as $file) {
            $rows[] = [
                $file->id,
                Str::limit($file->title, 30),
                Str::limit($file->slug, 25),
                $file->category,
                $file->status,
                $file->is_published ? 'Yes' : 'No',
                $file->author ? $file->author->name : 'N/A',
                $file->view_count,
                $file->created_at->format('M d, Y')
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    private function createMarkdownFile()
    {
        $title = $this->option('title') ?: $this->ask('Enter the title');
        $slug = $this->option('slug') ?: Str::slug($title);
        $content = $this->option('content') ?: $this->ask('Enter the content');
        $category = $this->option('category') ?: $this->choice('Select category', [
            'general', 'documentation', 'help', 'tutorial', 'announcement', 
            'policy', 'terms', 'privacy', 'faq', 'guide', 'api', 'changelog'
        ], 'general');
        $status = $this->option('status') ?: $this->choice('Select status', ['active', 'inactive', 'draft'], 'draft');
        $isPublished = $this->option('published') ?: $this->confirm('Publish immediately?', false);

        try {
            $markdownFile = MarkdownFile::create([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'category' => $category,
                'status' => $status,
                'is_published' => $isPublished,
                'published_at' => $isPublished ? now() : null,
                'author_id' => 1, // Default to first admin
                'created_by' => 1
            ]);

            if ($this->option('save-to-file') || $this->confirm('Save to physical file?', true)) {
                $markdownFile->saveToFile();
                $this->info("File saved to: {$markdownFile->file_path}");
            }

            $this->info("Markdown file created successfully! ID: {$markdownFile->id}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error creating markdown file: {$e->getMessage()}");
            return 1;
        }
    }

    private function updateMarkdownFile()
    {
        $id = $this->option('id') ?: $this->ask('Enter the markdown file ID');
        
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            
            $this->info("Current file: {$markdownFile->title}");
            
            $title = $this->option('title') ?: $this->ask('Enter new title', $markdownFile->title);
            $content = $this->option('content') ?: $this->ask('Enter new content', $markdownFile->content);
            $category = $this->option('category') ?: $this->choice('Select category', [
                'general', 'documentation', 'help', 'tutorial', 'announcement', 
                'policy', 'terms', 'privacy', 'faq', 'guide', 'api', 'changelog'
            ], $markdownFile->category);
            $status = $this->option('status') ?: $this->choice('Select status', ['active', 'inactive', 'draft'], $markdownFile->status);

            $markdownFile->update([
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'status' => $status,
                'updated_by' => 1
            ]);

            if ($this->option('save-to-file') || $this->confirm('Update physical file?', true)) {
                $markdownFile->saveToFile();
            }

            $this->info('Markdown file updated successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error("Error updating markdown file: {$e->getMessage()}");
            return 1;
        }
    }

    private function deleteMarkdownFile()
    {
        $id = $this->option('id') ?: $this->ask('Enter the markdown file ID');
        
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            $title = $markdownFile->title;
            
            if ($this->confirm("Are you sure you want to delete '{$title}'?", false)) {
                $markdownFile->deleteFile();
                $markdownFile->delete();
                
                $this->info('Markdown file deleted successfully!');
                return 0;
            } else {
                $this->info('Deletion cancelled.');
                return 0;
            }

        } catch (\Exception $e) {
            $this->error("Error deleting markdown file: {$e->getMessage()}");
            return 1;
        }
    }

    private function exportMarkdownFile()
    {
        $id = $this->option('id') ?: $this->ask('Enter the markdown file ID');
        $file = $this->option('file') ?: $this->ask('Enter the export file path');
        
        try {
            $markdownFile = MarkdownFile::findOrFail($id);
            $content = $markdownFile->generateMarkdownContent();
            
            file_put_contents($file, $content);
            
            $this->info("Markdown file exported to: {$file}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error exporting markdown file: {$e->getMessage()}");
            return 1;
        }
    }

    private function importMarkdownFile()
    {
        $file = $this->option('file') ?: $this->ask('Enter the markdown file path to import');
        
        if (!file_exists($file)) {
            $this->error("File does not exist: {$file}");
            return 1;
        }

        try {
            $content = file_get_contents($file);
            $filename = pathinfo($file, PATHINFO_FILENAME);
            
            // Store in storage and create record
            $storagePath = 'markdown/' . $filename . '.md';
            Storage::put($storagePath, $content);
            
            $markdownFile = MarkdownFile::createFromFile($storagePath, 1);
            
            $this->info("Markdown file imported successfully! ID: {$markdownFile->id}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error importing markdown file: {$e->getMessage()}");
            return 1;
        }
    }

    private function syncFromDirectory()
    {
        $directory = $this->option('directory') ?: $this->ask('Enter the directory to scan', 'markdown');
        
        try {
            $files = MarkdownFile::scanDirectory($directory);
            
            $this->info("Scanned directory: {$directory}");
            $this->info("Found/Updated {$files->count()} markdown files.");
            
            if ($files->isNotEmpty()) {
                $this->line('');
                $headers = ['ID', 'Title', 'File Path'];
                $rows = [];
                
                foreach ($files as $file) {
                    $rows[] = [$file->id, $file->title, $file->file_path];
                }
                
                $this->table($headers, $rows);
            }
            
            return 0;

        } catch (\Exception $e) {
            $this->error("Error syncing directory: {$e->getMessage()}");
            return 1;
        }
    }

    private function showStatistics()
    {
        $total = MarkdownFile::count();
        $published = MarkdownFile::where('is_published', true)->count();
        $draft = MarkdownFile::where('is_published', false)->count();
        $active = MarkdownFile::where('status', 'active')->count();
        $totalViews = MarkdownFile::sum('view_count');
        
        $byCategory = MarkdownFile::selectRaw('category, COUNT(*) as count')
                                 ->groupBy('category')
                                 ->pluck('count', 'category');

        $this->info('Markdown Files Statistics:');
        $this->line('');
        
        $this->line("Total Files: {$total}");
        $this->line("Published: {$published}");
        $this->line("Draft: {$draft}");
        $this->line("Active: {$active}");
        $this->line("Total Views: {$totalViews}");
        $this->line('');
        
        if ($byCategory->isNotEmpty()) {
            $this->info('Files by Category:');
            foreach ($byCategory as $category => $count) {
                $this->line("  {$category}: {$count}");
            }
        }
        
        return 0;
    }
}
