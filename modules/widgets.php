<?php

namespace Widgets;

/* Widgets Channel */
class Widget {
  public static function getHTML($templateEngine, $id, $title, $content) {
    $templateEngine->assign("widget_id", $id);
    $templateEngine->assign("widget_title", $title);
    $templateEngine->assign("widget_content", $content);
    return $templateEngine->fetch(VIEW_SITE."widget.html");
  }
}

class WidgetsTable {
  public static function getElementHTML($templateEngine, $id, $title) {
    $templateEngine->assign("widget_id", $id);
    $templateEngine->assign("widget_title", $title);
    return $templateEngine->fetch(VIEW_AP."widgets_table_element.html");
  }
  public static function getHTML($templateEngine, $widgetsList) {
    $tableContent = "";
    for ($i = 0; $i < count($widgetsList); $i++) {
      $tableContent .= self::getElementHTML($templateEngine,
                                            $widgetsList[$i][ DBT_WIDGETS["id"] ],
                                            $widgetsList[$i][ DBT_WIDGETS["title"] ]);
    }
    $templateEngine->assign("table_content", $tableContent ? $tableContent : "<td colspan=\"3\" align=\"center\">Виджеты отсутствуют</td>");
    return $templateEngine->fetch(VIEW_AP."widgets_content.html");
  }
}

class WidgetsChannel {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getAllWidgets() {
    $widgetsList = array();
    $widgets = $this->db->select(DB_TABLES["widgets"], array("*"));
    if (!$widgets) return null;
    for ($i = 0; $i < count($widgets); $i++) {
      $widgetsList[$i] = Widget::getHTML($this->templateEngine,
                                         $widgets[$i][ DBT_WIDGETS["id"] ],
                                         $widgets[$i][ DBT_WIDGETS["title"] ],
                                         $widgets[$i][ DBT_WIDGETS["content"] ]);
    }
    return $widgetsList;
  }
  /* Admin functions */
  public function getWidgetsTable() {
    $widgets = $this->db->select(DB_TABLES["widgets"], array("*"));
    if (!$widgets) $widgets = array();
    return WidgetsTable::getHTML($this->templateEngine, $widgets);
  }
  public function add($title, $content) {
    return $this->db->insert(DB_TABLES["widgets"], array(DBT_WIDGETS["title"], DBT_WIDGETS["content"]),
                                                  array($title, $content));
  }
  public function edit($widgetId, $title, $content) {
    return $this->db->update(DB_TABLES["widgets"], array(DBT_WIDGETS["title"], DBT_WIDGETS["content"]),
                                                  array($title, $content),
                                                  DBT_WIDGETS["id"]."=".$widgetId);
  }
  public function delete($widgetId) {
    if (gettype($widgetId) == "integer") $condition = DBT_WIDGETS["id"]."='".$widgetId."'";
    else if (gettype($widgetId) == "array") $condition = DBT_WIDGETS["id"]." IN (".join($widgetId, ", ").")";
    return $this->db->delete(DB_TABLES["widgets"], $condition);
  }
  public function clear() {
    return $this->db->clear(DB_TABLES["widgets"]);
  }
}

?>
