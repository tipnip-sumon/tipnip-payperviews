<?php
// PowerShell-based unused command analyzer for Windows

echo "=== UNUSED COMMAND ANALYZER (Windows) ===" . PHP_EOL . PHP_EOL;

// Get all command files
$commandFiles = glob('app/Console/Commands/*.php');

// Get scheduled commands
$consoleContent = file_get_contents('routes/console.php');
preg_match_all("/Schedule::command\('([^']+)'/", $consoleContent, $scheduledMatches);
$scheduledCommands = $scheduledMatches[1];

// Extract command signatures and check usage
$unusedCommands = [];
$usedCommands = [];

foreach ($commandFiles as $file) {
    $content = file_get_contents($file);
    $className = basename($file, '.php');
    
    // Extract command signature
    if (preg_match('/protected\s+\$signature\s*=\s*[\'"]([^\'\"]+)[\'"]/', $content, $matches)) {
        $signature = trim(explode(' ', $matches[1])[0]);
        
        // Check if scheduled
        $isScheduled = false;
        foreach ($scheduledCommands as $scheduledCmd) {
            if (strpos($scheduledCmd, $signature) === 0) {
                $isScheduled = true;
                break;
            }
        }
        
        if ($isScheduled) {
            $usedCommands[] = [
                'signature' => $signature,
                'file' => $file,
                'class' => $className,
                'usage' => 'scheduled'
            ];
        } else {
            // Check for usage in other files using PowerShell
            $searchCommand = "Get-ChildItem -Path app,routes -Include *.php -Recurse | Select-String -Pattern \"$signature\" | Where-Object { \$_.Filename -ne \"$className.php\" }";
            $result = shell_exec("powershell -Command \"$searchCommand\"");
            
            if (empty(trim($result))) {
                $unusedCommands[] = [
                    'signature' => $signature,
                    'file' => $file,
                    'class' => $className,
                    'reason' => 'No usage found'
                ];
            } else {
                $usedCommands[] = [
                    'signature' => $signature,
                    'file' => $file,
                    'class' => $className,
                    'usage' => 'referenced in code'
                ];
            }
        }
    } else {
        echo "⚠️ Could not extract signature from: $file" . PHP_EOL;
    }
}

echo "✅ USED COMMANDS (" . count($usedCommands) . "):" . PHP_EOL;
foreach ($usedCommands as $cmd) {
    echo "  ✓ {$cmd['signature']} - {$cmd['usage']}" . PHP_EOL;
}

echo PHP_EOL . "❌ UNUSED COMMANDS (" . count($unusedCommands) . "):" . PHP_EOL;
foreach ($unusedCommands as $cmd) {
    echo "  🗑️ {$cmd['signature']} ({$cmd['class']}.php)" . PHP_EOL;
}

if (!empty($unusedCommands)) {
    echo PHP_EOL . "💡 COMMANDS TO DELETE:" . PHP_EOL;
    foreach ($unusedCommands as $cmd) {
        echo "Remove-Item \"{$cmd['file']}\"" . PHP_EOL;
    }
}
