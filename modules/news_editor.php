<?php

namespace NewsEditor;

class Text {
  public static function encode($text) {
    return str_replace("\n", "<br />", $text);
  }
  public static function decode($text) {
    return str_replace("<br />", "\n", $text);
  }
}

class CategoriesList {
  public static function getHTML($templateEngine, $categoriesList, $selectedCategory) {
    $list = "";
    for ($i = 0; $i < count($categoriesList); $i++) {
      $templateEngine->assign("category_id", $categoriesList[$i][ DBT_NEWS_CATEGORIES["id"] ]);
      $templateEngine->assign("category_name", $categoriesList[$i][ DBT_NEWS_CATEGORIES["name"] ]);
      $templateEngine->assign("category_isSelected", $i+1 == (int)$selectedCategory ? "selected" : "");
      $list .= $templateEngine->fetch(VIEW_AP."categories_option.html");
      $templateEngine->clear();
    }
    return $list;
  }
}

class NewsEditor {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getHTMLForPost($postId) {
    $newsPost = $this->db->select(DB_TABLES["news"], array(DBT_NEWS["id"],
                                                           DBT_NEWS["image"],
                                                           DBT_NEWS["title"],
                                                           DBT_NEWS["description"],
                                                           DBT_NEWS["content"],
                                                           DBT_NEWS["category_id"]),
                                                           DBT_NEWS["id"]."=".$postId);
    if (!$newsPost) return null;
    $categories = $this->db->select(DB_TABLES["categories"], array("*"));
    $categoriesList = CategoriesList::getHTML($this->templateEngine, $categories, $newsPost[0][ DBT_NEWS["category_id"] ]);
    $this->templateEngine->assign("post_id", $newsPost[0][ DBT_NEWS["id"] ]);
    $this->templateEngine->assign("post_title", $newsPost[0][ DBT_NEWS["title"] ]);
    $this->templateEngine->assign("post_image", $newsPost[0][ DBT_NEWS["image"] ]);
    $this->templateEngine->assign("categoriesList", $categoriesList);
    $this->templateEngine->assign("post_description", $newsPost[0][ DBT_NEWS["description"] ]);
    $this->templateEngine->assign("post_content", Text::decode( $newsPost[0][ DBT_NEWS["content"] ] ) );
    $this->templateEngine->assign("form_type", "editor-edit-post");
    $this->templateEngine->assign("btn_value", "Сохранить изменения");
    return $this->templateEngine->fetch(VIEW_AP."news_editor.html");
  }
  public function getHTMLForNewPost() {
    $categories = $this->db->select(DB_TABLES["categories"], array("*"));
    $categoriesList = CategoriesList::getHTML($this->templateEngine, $categories, 1);
    $this->templateEngine->assign("post_id", "");
    $this->templateEngine->assign("post_title", "");
    $this->templateEngine->assign("post_image", "");
    $this->templateEngine->assign("categoriesList", $categoriesList);
    $this->templateEngine->assign("post_description", "");
    $this->templateEngine->assign("post_content", "");
    $this->templateEngine->assign("form_type", "editor-add-post");
    $this->templateEngine->assign("btn_value", "Добавить публикацию");
    return $this->templateEngine->fetch(VIEW_AP."news_editor.html");
  }
}

?>
