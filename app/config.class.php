<?php

class config {

  function __construct() {

    $iniSettings = parse_ini_file(realpath(dirname(__FILE__)) . "/../config/config.ini");

    foreach ($iniSettings as $key => $value) {
    	$this->$key = $value;
    }

  }

}