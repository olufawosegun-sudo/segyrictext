<?php
$file = __DIR__ . '/../resources/views/components/purchase-number-section.blade.php';
$content = file_get_contents($file);

// Extract JS block
if (preg_match('/<script>(.*?)<\/script>/s', $content, $m)) {
    $js = $m[1];
    echo "JS block found: " . strlen($js) . " chars\n";
    
    // Check brace balance
    $open = substr_count($js, '{');
    $close = substr_count($js, '}');
    echo "Braces: open=$open close=$close " . ($open === $close ? "✓ BALANCED" : "✗ UNBALANCED") . "\n";
    
    // Check parens balance
    $po = substr_count($js, '(');
    $pc = substr_count($js, ')');
    echo "Parens: open=$po close=$pc " . ($po === $pc ? "✓ BALANCED" : "✗ UNBALANCED") . "\n";
    
    // Check for key functions
    $functions = ['pvSelectCountry', 'pvSelectDuration', 'pvToggleAllServices', 'pvHandleCtaClick', 
                  'pvOpenWebsiteSheet', 'pvCloseWebsiteSheet', 'pvExecutePurchase', 'pvShowActiveStage',
                  'pvReturnToPurchaseStage', 'pvSelectAllServicesCountry'];
    
    echo "\nFunction definitions:\n";
    foreach ($functions as $fn) {
        $found = strpos($js, "window.$fn = function") !== false || strpos($js, "function $fn") !== false;
        echo "  $fn: " . ($found ? "✓ DEFINED" : "✗ MISSING") . "\n";
    }
    
    // Check for 'updateAllServicesState' call (should NOT exist anymore)
    $hasOldCall = strpos($js, 'updateAllServicesState') !== false;
    echo "\nupdateAllServicesState reference: " . ($hasOldCall ? "✗ STILL PRESENT" : "✓ REMOVED") . "\n";
    
    // Check for duplicate activeStage.style.display orphan
    $orphanCount = substr_count($js, "activeStage.style.display = 'block'");
    echo "activeStage.style.display orphan occurrences: $orphanCount " . ($orphanCount <= 1 ? "✓ OK" : "✗ DUPLICATE") . "\n";
    
} else {
    echo "No script block found!\n";
}
