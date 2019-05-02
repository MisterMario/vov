<?php

namespace WidgetsEditor;

class WidgetsEditor {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getHTMLForWidget($widgetId) {
    $widget = $this->db->select(DB_TABLES["widgets"], array("*"),
                                                            DBT_WIDGETS["id"]."=".$widgetId);
    if (!$widget) return null;
    $this->templateEngine->assign("widget_id", $widget[0][ DBT_WIDGETS["id"] ]);
    $this->templateEngine->assign("widget_title", $widget[0][ DBT_WIDGETS["title"] ]);
    $this->templateEngine->assign("widget_content", $widget[0][ DBT_WIDGETS["content"] ]);
    $this->templateEngine->assign("form_type", "editor-edit-widget");
    $this->templateEngine->assign("btn_value", "Сохранить изменения");
    return $this->templateEngine->fetch(VIEW_AP."widgets_editor.html");
  }
  public function getHTMLForNewWidget() {
    $this->templateEngine->assign("widget_id", "");
    $this->templateEngine->assign("widget_title", "");
    $this->templateEngine->assign("widget_content", "");
    $this->templateEngine->assign("form_type", "editor-add-widget");
    $this->templateEngine->assign("btn_value", "Добавить виджет");
    return $this->templateEngine->fetch(VIEW_AP."widgets_editor.html");
  }
}

?>
