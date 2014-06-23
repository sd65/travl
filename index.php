<?php
session_start();
	
// Config
include "app/config.class.php";
$conf = new config;

// Get Arguments (GET + POST ?+? FILES)
$REQUEST = $_REQUEST;
if (isset($_FILES))
  $REQUEST = array_merge($REQUEST, $_FILES);

// Parse the URI
$method  = "index";
if (isset($_SERVER['PATH_INFO'])) {
	if ($_SERVER['PATH_INFO'] != "/") {
		$method = str_replace('/', '', $_SERVER['PATH_INFO']);
	}
}

include 'app/app.class.php';
$app = new app($conf);
if (method_exists($app, $method))
  $app->$method($REQUEST);
else
  echo "La page " .$method . " n'existe pas.";
