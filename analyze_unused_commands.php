<?php
// Script to identify unused console commands

$commandsDir = 'app/Console/Commands';
$commandFiles = glob($commandsDir . '/*.php');

echo "=== CONSOLE COMMAND USAGE ANALYSIS ===" . PHP_EOL . PHP_EOL;

// Get all scheduled commands from console.php
$consoleContent = file_get_contents('routes/console.php');
preg_match_all("/Schedule::command\('([^']+)'/", $consoleContent, $scheduledMatches);
$scheduledCommands = $scheduledMatches[1];

echo "📅 SCHEDULED COMMANDS (" . count($scheduledCommands) . "):" . PHP_EOL;
foreach ($scheduledCommands as $cmd) {
    echo "  ✓ {$cmd}" . PHP_EOL;
}
echo PHP_EOL;

// Get signature from each command file
$commandSignatures = [];
$potentiallyUnused = [];

foreach ($commandFiles as $file) {
    $content = file_get_contents($file);
    $className = basename($file, '.php');
    
    // Extract command signature
    if (preg_match('/protected\s+\$signature\s*=\s*[\'"]([^\'\"]+)[\'"]/', $content, $matches)) {
        $signature = trim(explode(' ', $matches[1])[0]); // Get just the command name part
        $commandSignatures[$signature] = [
            'file' => $file,
            'class' => $className,
            'full_signature' => $matches[1]
        ];
        
        // Check if this signature is used in scheduled tasks
        $isScheduled = false;
        foreach ($scheduledCommands as $scheduledCmd) {
            if (strpos($scheduledCmd, $signature) === 0) {
                $isScheduled = true;
                break;
            }
        }
        
        if (!$isScheduled) {
            $potentiallyUnused[] = [
                'signature' => $signature,
                'file' => $file,
                'class' => $className
            ];
        }
    }
}

echo "🔍 POTENTIALLY UNUSED COMMANDS (" . count($potentiallyUnused) . "):" . PHP_EOL;
foreach ($potentiallyUnused as $cmd) {
    echo "  ⚠️  {$cmd['signature']} ({$cmd['class']}.php)" . PHP_EOL;
}
echo PHP_EOL;

// Check for commands that might be used elsewhere (in controllers, jobs, etc.)
echo "🔎 CHECKING OTHER USAGE..." . PHP_EOL;
$definitelyUnused = [];

foreach ($potentiallyUnused as $cmd) {
    $signature = $cmd['signature'];
    
    // Search for usage in PHP files
    $grepCommand = "grep -r --include=\"*.php\" \"{$signature}\" app/ routes/ || echo \"NOT_FOUND\"";
    $grepResult = shell_exec($grepCommand);
    
    // If only found in the command file itself, it's likely unused
    $lines = explode("\n", trim($grepResult));
    $usageCount = 0;
    $usageFiles = [];
    
    foreach ($lines as $line) {
        if (!empty($line) && $line !== "NOT_FOUND" && !strpos($line, $cmd['file'])) {
            $usageCount++;
            $usageFiles[] = $line;
        }
    }
    
    if ($usageCount === 0) {
        $definitelyUnused[] = $cmd;
        echo "  ❌ {$signature} - NO EXTERNAL USAGE FOUND" . PHP_EOL;
    } else {
        echo "  ⚠️  {$signature} - Found {$usageCount} usage(s)" . PHP_EOL;
        foreach (array_slice($usageFiles, 0, 2) as $usage) {
            echo "     └─ " . trim($usage) . PHP_EOL;
        }
    }
}

echo PHP_EOL;
echo "📊 SUMMARY:" . PHP_EOL;
echo "  Total command files: " . count($commandFiles) . PHP_EOL;
echo "  Scheduled commands: " . count($scheduledCommands) . PHP_EOL;
echo "  Potentially unused: " . count($potentiallyUnused) . PHP_EOL;
echo "  Definitely unused: " . count($definitelyUnused) . PHP_EOL;

if (!empty($definitelyUnused)) {
    echo PHP_EOL . "💥 COMMANDS SAFE TO DELETE:" . PHP_EOL;
    foreach ($definitelyUnused as $cmd) {
        echo "  🗑️  rm {$cmd['file']}" . PHP_EOL;
    }
}
