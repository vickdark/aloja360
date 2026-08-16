<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo str_pad('ORDER', 6) . ' | ' . str_pad('MODULE', 18) . ' | ' . str_pad('SLUG', 34) . ' | IS_MENU | IN_ADMIN' . PHP_EOL;
echo str_repeat('-', 120) . PHP_EOL;

$admin = \App\Models\Roles\Role::where('slug', 'admin')->first();
$adminPermIds = $admin ? $admin->permissions()->pluck('permission_id')->all() : [];

$items = \App\Models\Roles\Permission::orderBy('order')->orderBy('module')->get();
foreach ($items as $i) {
    $inAdmin = in_array($i->id, $adminPermIds);
    $slug = $i->slug;
    if (stripos($i->slug, 'ammenities') !== false || stripos($i->module, 'Alojam') !== false || $i->is_menu) {
        echo str_pad($i->order, 6) . ' | '
            . str_pad(($i->module ?? 'NULL'), 18) . ' | '
            . str_pad($i->slug, 34) . ' | '
            . str_pad(($i->is_menu ? '1' : '0'), 7) . ' | '
            . ($inAdmin ? 'YES' : 'NO')
            . PHP_EOL;
    }
}

echo PHP_EOL . 'Solo IS_MENU=1:' . PHP_EOL;
$itemsMenu = \App\Models\Roles\Permission::where('is_menu', 1)->orderBy('order')->get();
foreach ($itemsMenu as $i) {
    $inAdmin = in_array($i->id, $adminPermIds);
    echo str_pad($i->order, 6) . ' | '
        . str_pad(($i->module ?? 'NULL'), 18) . ' | '
        . str_pad($i->slug, 34) . ' | '
        . ($inAdmin ? 'YES' : 'NO')
        . PHP_EOL;
}
