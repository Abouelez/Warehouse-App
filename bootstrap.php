<?php
	require 'vendor/autoload.php';
	use Config\Database;

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$db = Database::get_instance()->get_connection();
