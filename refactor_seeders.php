<?php

$files = [
    __DIR__ . '/database/seeders/BusinessDataSeeder.php',
    __DIR__ . '/database/seeders/DemoDataSeeder.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $original = $content;

        // Remove 'business_id' => $business->id,
        $content = preg_replace("/'business_id'\s*=>\s*\\\$business->id,\s*/", "", $content);
        $content = preg_replace("/'business_id'\s*=>\s*1,\s*/", "", $content);
        $content = preg_replace("/'business_id'\s*=>\s*\\\$demoBusiness->id,\s*/", "", $content);
        
        // Save
        if ($original !== $content) {
            file_put_contents($file, $content);
            echo "Updated: " . basename($file) . "\n";
        }
    }
}
echo "Done\n";
