<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::getSchemaBuilder()->getColumnListing('tbl_product');
echo "COLUMNS OF tbl_product:\n";
print_r($columns);

$units = DB::table('tbl_unit')->where('status', 0)->get();
echo "\nUNITS:\n";
print_r($units);
