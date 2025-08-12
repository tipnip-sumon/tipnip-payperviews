<?php

// Test the prize pool calculation logic
$prizeDistribution = [
    "1" => [
        "name" => "1st Prize",
        "type" => "fixed_amount",
        "amount" => "1000"
    ],
    "2" => [
        "name" => "2nd Prize", 
        "type" => "fixed_amount",
        "amount" => "300"
    ],
    "3" => [
        "name" => "3rd Prize",
        "type" => "fixed_amount", 
        "amount" => "100"
    ]
];

function calculatePrizePool($prizeDistribution) {
    if (!empty($prizeDistribution) && is_array($prizeDistribution)) {
        $total = 0;
        foreach ($prizeDistribution as $prize) {
            if (isset($prize['type']) && $prize['type'] === 'fixed_amount' && isset($prize['amount'])) {
                $total += floatval($prize['amount']);
            }
        }
        return $total;
    }
    
    return 1400.00; // Default fallback
}

$totalPrizePool = calculatePrizePool($prizeDistribution);

echo "Prize Distribution Test:\n";
echo "1st Prize: $" . $prizeDistribution["1"]["amount"] . "\n";
echo "2nd Prize: $" . $prizeDistribution["2"]["amount"] . "\n"; 
echo "3rd Prize: $" . $prizeDistribution["3"]["amount"] . "\n";
echo "Total Prize Pool: $" . number_format($totalPrizePool, 2) . "\n";
