<?php
class Template {

    var $vars;

    function __construct($file = null) {
        $this->file = $file;
    }

    function set($name, $value) {
        $this->vars[$name] = is_object($value) ? $value->fetch() : $value;
    }

    function display($file = null) {
        if(!$file) $file = $this->file;
        extract($this->vars);
        ob_start();
        include($file);
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
    }
}