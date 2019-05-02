<?php

namespace PageBuilder;

class NavigationElement {
  public static function getHTML($templateEngine, $id, $name) {
    $templateEngine->assign("id", $id);
    $templateEngine->assign("name", $name);
    $templateEngine->assign("link", "/category/".$id."/");
    return $templateEngine->fetch(VIEW_SITE."navigation_bar_element.html");
  }
}

class NavigationBar {
  public static function getHTML($db, $templateEngine) {
    $navBar = "";
    $categories = $db->select(DB_TABLES["categories"], array("*"));
    if (!$categories) return null;
    for ($i = 0; $i < count($categories); $i++) {
      $navBar .= NavigationElement::getHTML($templateEngine,
                                            $categories[$i][ DBT_NEWS_CATEGORIES["id"] ],
                                            $categories[$i][ DBT_NEWS_CATEGORIES["name"] ]);
    }
    return $navBar;
  }
}

class Page {
  public $title;
  public $charset;
  private $navigationBar;
  private $rightBlock;
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine, $widgetsChannel) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
    $this->title = SITE_TITLE;
    $this->charset = SITE_CHARSET;
    $this->navigationBar = NavigationBar::getHTML($db, $templateEngine);
    $allWidgets = $widgetsChannel->getAllWidgets();
    $this->rightBlock = ($allWidgets ? join($allWidgets, "") : "");
  }
  public function getHTML($pageTitle, $sectionName, $content) {
    $this->templateEngine->assign("siteTitle", $pageTitle ? $pageTitle : $this->title);
    $this->templateEngine->assign("siteCharset", $this->charset);
    $this->templateEngine->assign("navigationBar", $this->navigationBar);
    $this->templateEngine->assign("sectionName", $sectionName);
    $this->templateEngine->assign("leftBlock", $content);
    $this->templateEngine->assign("rightBlock", $this->rightBlock);
    return $this->templateEngine->fetch(VIEW_SITE."index.html");
  }
  /* Admin function */
  public function getAdminHTML($adminName, $pageTitle, $sectionName, $content) {
    $this->templateEngine->assign("siteTitle", $pageTitle ? $pageTitle : $this->title);
    $this->templateEngine->assign("siteCharset", $this->charset);
    $this->templateEngine->assign("sectionName", $sectionName);
    $this->templateEngine->assign("rightBlock", $content);
    $this->templateEngine->assign("userName", $adminName);
    return $this->templateEngine->fetch(VIEW_AP."index.html");
  }
  public function getAdminLoginHTML() {
    return $this->templateEngine->fetch(VIEW_AP."login.html");
  }
}

?>
