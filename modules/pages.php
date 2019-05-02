<?php

namespace Pages;


class PagesTable {
  public static function getElementHTML($templateEngine, $id, $title) {
    $templateEngine->assign("page_id", $id);
    $templateEngine->assign("page_title", $title);
    return $templateEngine->fetch(VIEW_AP."pages_table_element.html");
  }
  public static function getHTML($templateEngine, $pagesList) {
    $tableContent = "";
    for ($i = 0; $i < count($pagesList); $i++) {
      $tableContent .= self::getElementHTML($templateEngine,
                                            $pagesList[$i][ DBT_PAGES["id"] ],
                                            $pagesList[$i][ DBT_PAGES["title"] ]);
    }
    $templateEngine->assign("table_content", $tableContent ? $tableContent : "<td colspan=\"3\" align=\"center\">Динамические страницы отсутствуют</td>");
    return $templateEngine->fetch(VIEW_AP."pages_content.html");
  }
}

class PagesChannel {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getPage($pageId) {
    $page = $this->db->select(DB_TABLES["pages"], array("*"), DBT_PAGES["id"]."=".$pageId);
    if (!$page) return null;
    $this->templateEngine->assign("page_content", $page[0][ DBT_PAGES["content"] ]);
    return $this->templateEngine->fetch(VIEW_SITE."page.html");
  }
  /* Admin functions */
  public function getPagesTable() {
    $pages = $this->db->select(DB_TABLES["pages"], array("*"));
    if (!$pages) $pages = array();
    return PagesTable::getHTML($this->templateEngine, $pages);
  }
  public function add($title, $content) {
    return $this->db->insert(DB_TABLES["pages"], array(DBT_PAGES["title"], DBT_PAGES["content"]),
                                                  array($title, $content));
  }
  public function edit($pageId, $title, $content) {
    return $this->db->update(DB_TABLES["pages"], array(DBT_PAGES["title"], DBT_PAGES["content"]),
                                                  array($title, $content),
                                                  DBT_PAGES["id"]."=".$pageId);
  }
  public function delete($pageId) {
    if (gettype($pageId) == "integer") $condition = DBT_PAGES["id"]."='".$pageId."'";
    else if (gettype($pageId) == "array") $condition = DBT_PAGES["id"]." IN (".join($pageId, ", ").")";
    return $this->db->delete(DB_TABLES["pages"], $condition);
  }
  public function clear() {
    return $this->db->clear(DB_TABLES["pages"]);
  }
}

?>
