<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MarkdownFile;

echo "Updating markdown files to remove TipNip references...\n";

try {
    // Update Welcome document
    $welcome = MarkdownFile::where('slug', 'welcome-to-tipnip-payperviews')->first();
    if ($welcome) {
        $newContent = str_replace(['TipNip PayPerViews', 'TipNip'], ['PayPerViews', 'PayPerViews'], $welcome->content);
        $newMetaDescription = str_replace(['TipNip PayPerViews', 'TipNip'], ['PayPerViews', 'PayPerViews'], $welcome->meta_description);
        
        $welcome->update([
            'title' => 'Welcome to PayPerViews',
            'slug' => 'welcome-to-payperviews',
            'content' => $newContent,
            'meta_description' => $newMetaDescription
        ]);
        echo "✅ Updated: Welcome document\n";
    }

    // Update other documents
    $documents = MarkdownFile::whereIn('slug', [
        'understanding-kyc-verification',
        'deposit-and-withdrawal-guide', 
        'frequently-asked-questions',
        'privacy-policy',
        'terms-and-conditions',
        'api-documentation'
    ])->get();

    foreach ($documents as $doc) {
        $newContent = str_replace(['TipNip PayPerViews', 'TipNip'], ['PayPerViews', 'PayPerViews'], $doc->content);
        $newMetaDescription = str_replace(['TipNip PayPerViews', 'TipNip'], ['PayPerViews', 'PayPerViews'], $doc->meta_description ?? '');
        
        $doc->update([
            'content' => $newContent,
            'meta_description' => $newMetaDescription
        ]);
        echo "✅ Updated: {$doc->title}\n";
    }

    echo "\n🎉 All documents updated successfully!\n";
    echo "\nUpdated URLs:\n";
    echo "- Welcome: /docs/documentation/welcome-to-payperviews\n";
    echo "- Other documents remain the same\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
