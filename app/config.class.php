<?php

class config {

  function __construct() {

    $iniSettings = parse_ini_file(realpath(dirname(__FILE__)) . "/../config/config.ini");

    $this->title = $iniSettings['title'];
    $this->url = $iniSettings['url'];
    $this->topDir = $iniSettings['topDir'];

  }

}