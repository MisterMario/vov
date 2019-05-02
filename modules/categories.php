<?php

namespace Categories;

class CategoriesTable {
  public static function getElementHTML($templateEngine, $id, $name) {
    $templateEngine->assign("category_id", $id);
    $templateEngine->assign("category_name", $name);
    return $templateEngine->fetch(VIEW_AP."categories_table_element.html");
  }
  public static function getHTML($templateEngine, $categoriesList) {
    $tableContent = "";
    for ($i = 0; $i < count($categoriesList); $i++) {
      $tableContent .= self::getElementHTML($templateEngine,
                                            $categoriesList[$i][ DBT_NEWS_CATEGORIES["id"] ],
                                            $categoriesList[$i][ DBT_NEWS_CATEGORIES["name"] ]);
    }
    $templateEngine->assign("table_content", $tableContent ? $tableContent : "<td colspan=\"3\" align=\"center\">Новостные категории отсутствуют</td>");
    return $templateEngine->fetch(VIEW_AP."categories_content.html");
  }
}

class CategoriesChannel {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getCategoriesTable() {
    $categories = $this->db->select(DB_TABLES["categories"], array("*"));
    if (!$categories) $categories = array();
    return CategoriesTable::getHTML($this->templateEngine, $categories);
  }
  public function add($name) {
    return $this->db->insert(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                                                      array($name));
  }
  public function edit($categoryId, $name) {
    return $this->db->update(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                                                      array($name),
                                                      DBT_NEWS_CATEGORIES["id"]."=".$categoryId);
  }
  public function delete($categoryId) {
    if (gettype($categoryId) == "integer") $condition = DBT_NEWS_CATEGORIES["id"]."='".$categoryId."'";
    else if (gettype($categoryId) == "array") $condition = DBT_NEWS_CATEGORIES["id"]." IN (".join($categoryId, ", ").")";
    return $this->db->delete(DB_TABLES["categories"], $condition);
  }
  public function clear() {
    return $this->db->clear(DB_TABLES["categories"]);
  }
}

?>
