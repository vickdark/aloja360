<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/app/Models');
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;

        // Remove 'business_id', from fillable
        $content = preg_replace("/'business_id',\s*/", "", $content);
        
        // Remove business() relation
        $content = preg_replace("/\s*public function business\(\): BelongsTo\s*\{\s*return \\\$this->belongsTo\(Business::class\);\s*\}/", "", $content);

        // Save
        if ($original !== $content) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getFilename() . "\n";
        }
    }
}
echo "Done\n";
