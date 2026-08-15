<?php

$files = [
    __DIR__ . '/database/factories/AccommodationFactory.php',
    __DIR__ . '/database/factories/GuestFactory.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $original = $content;

        // Remove 'business_id' => Business::factory(),
        $content = preg_replace("/'business_id'\s*=>\s*Business::factory\(\),\s*/", "", $content);
        
        // Save
        if ($original !== $content) {
            file_put_contents($file, $content);
            echo "Updated: " . basename($file) . "\n";
        }
    }
}
echo "Done\n";
