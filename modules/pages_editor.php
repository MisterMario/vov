<?php

namespace PagesEditor;

class PagesEditor {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getHTMLForPage($pageId) {
    $page = $this->db->select(DB_TABLES["pages"], array("*"),
                                                            DBT_PAGES["id"]."=".$pageId);
    if (!$page) return null;
    $this->templateEngine->assign("page_id", $page[0][ DBT_PAGES["id"] ]);
    $this->templateEngine->assign("page_title", $page[0][ DBT_PAGES["title"] ]);
    $this->templateEngine->assign("page_content", $page[0][ DBT_PAGES["content"] ]);
    $this->templateEngine->assign("form_type", "editor-edit-page");
    $this->templateEngine->assign("btn_value", "Сохранить изменения");
    return $this->templateEngine->fetch(VIEW_AP."pages_editor.html");
  }
  public function getHTMLForNewPage() {
    $this->templateEngine->assign("page_id", "");
    $this->templateEngine->assign("page_title", "");
    $this->templateEngine->assign("page_content", "");
    $this->templateEngine->assign("form_type", "editor-add-page");
    $this->templateEngine->assign("btn_value", "Добавить страницу");
    return $this->templateEngine->fetch(VIEW_AP."pages_editor.html");
  }
}

?>
