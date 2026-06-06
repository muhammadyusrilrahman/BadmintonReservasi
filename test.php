<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = app(App\Http\Controllers\Admin\CourtController::class);
$court = App\Models\Court::first();
if (!$court) {
    echo "No court found\n";
    exit;
}
echo "Court ID: " . $court->id . "\n";
try {
    $c->destroy($court);
    echo "Deleted!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
