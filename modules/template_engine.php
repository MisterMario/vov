<?php

class PlainHTMLView {
  private $template_dir = "";
  private $Vars = array();

  public function __construct($template_dir = "") {
    $this->template_dir = $template_dir;
  }
  public function assign($var_name, $var_value) {
    $this->Vars[$var_name] = $var_value;
  }
  public function fetch($template) {
    ini_set('error_reporting', error_reporting(E_ALL & ~E_NOTICE));
    extract($this->Vars);
    ob_start();
    include $this->template_dir.$template;
    $this->Vars = array();
    return ob_get_clean();
  }
  public function get_template_vars($var) {
    return isset($this->Vars[$var]) ? $this->Vars[$var] : false;
  }
  public function clear() {
    $this->Vars = array();
  }
}

?>
