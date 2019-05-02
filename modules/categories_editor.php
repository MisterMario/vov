<?php

namespace CategoriesEditor;

class CategoriesEditor {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getHTMLForCategory($categoryId) {
    $category = $this->db->select(DB_TABLES["categories"], array("*"), DBT_NEWS_CATEGORIES["id"]."=".$categoryId);
    if (!$category) return null;
    $this->templateEngine->assign("category_id", $category[0][ DBT_NEWS_CATEGORIES["id"] ]);
    $this->templateEngine->assign("category_name", $category[0][ DBT_NEWS_CATEGORIES["name"] ]);
    $this->templateEngine->assign("form_type", "editor-edit-category");
    $this->templateEngine->assign("btn_value", "Сохранить изменеия");
    return $this->templateEngine->fetch(VIEW_AP."categories_editor.html");
  }
  public function getHTMLForNewCategory() {
    $this->templateEngine->assign("category_id", "");
    $this->templateEngine->assign("category_name", "");
    $this->templateEngine->assign("form_type", "editor-add-category");
    $this->templateEngine->assign("btn_value", "Добавить категорию");
    return $this->templateEngine->fetch(VIEW_AP."categories_editor.html");
  }
}

?>
