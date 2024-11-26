<?php
require 'vendor/autoload.php';

use App\Models\Uom;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Testing that database configuration work well
// $db = Database::get_instance()->get_connection();
// $query = 'INSERT INTO users (name) VALUES ("test user")';
// $db->exec($query);
// $uom = new Uom();
// $uom = $uom->insert([
//     'name' => 'pcs1',
//     'description' => 'for countable items'
// ]);
// Uom::insert([
//     'name' => 'n1',
//     'description' => 'n2',
// ]);
// var_dump(Uom::find(7));
// var_dump(Uom::all());
// $uom = $uom->update(3, ['name' => 'pice', 'description' => 'new']);
