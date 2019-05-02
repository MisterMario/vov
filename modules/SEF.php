<?php

namespace SEF;

################################################
# Content Types:                               #
# null - ошибка                                #
# 0    - главная страница сайта                #
# 1    - новостная категория                   #
# 2    - новостная публикация                  #
# 3    - динамическая страница                 #
# 4    - отсутствует (является поиском)        #
# 5    - панель администратора                 #
# 6    - редактор новостей                     #
# 7    - редактор виджетов                     #
# 8    - редактор динамических страниц         #
# 9    - редактор новостных категорий          #
################################################

class SEF {
  public $db;
  const PATTERN_FOR_CATEGORIES = "/^\/category\/[0-9]{1,}\/{0,1}$/";
  const PATTERN_FOR_POSTS = "/^\/post\/[0-9]{1,}\/{0,1}$/";
  const PATTERN_FOR_PAGES = "/^\/page\/[0-9]{1,}\/{0,1}$/";
  const PATTERN_FOR_NUMBERS = "/[^0-9]{1,}/";
  const PATTERN_FOR_AP = "/^\/admin\/(news|widgets|pages|categories|)\/{0,1}$/";
  const PATTERN_FOR_AP_NEWS_EDITOR = "/^\/admin\/news\/(edit)\/[0-9]{1,}\/{0,1}$/";
  const PATTERN_FOR_AP_WIDGETS_EDITOR = "/^\/admin\/widgets\/(edit)\/[0-9]{1,}\/{0,1}$/";
  const PATTERN_FOR_AP_PAGES_EDITOR = "/^\/admin\/pages\/(edit)\/[0-9]{1,}\/{0,1}$/";
  const PATTERN_FOR_AP_CATEGORIES_EDITOR = "/^\/admin\/categories\/(edit)\/[0-9]{1,}\/{0,1}$/";

  public function __construct($db) {
    $this->db = $db;
  }
  public function getPageInfo($uri) { // Метод следует назвать как-нибудь корректнее
    $uri = strtok($uri, "?");
    if ($uri == "/") {
      $contentInfo = array("type" => 0, "id" => 0, "name" => "Все новости");
      return $contentInfo;
    } else if ( preg_match(self::PATTERN_FOR_CATEGORIES, $uri) ) {
      $contentInfo = array("type" => 1, "id" => (int)preg_replace(self::PATTERN_FOR_NUMBERS, "", $uri));
      $res = $this->db->select(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                               DBT_NEWS_CATEGORIES["id"]."=".$contentInfo["id"], "LIMIT 1");
      if ($res) {
        $contentInfo["name"] = $res[0][ DBT_NEWS_CATEGORIES["name"] ];
        return $contentInfo;
      }
    } else if ( preg_match(self::PATTERN_FOR_POSTS, $uri) ) {
      $contentInfo = array("type" => 2, "id" => (int)preg_replace(self::PATTERN_FOR_NUMBERS, "", $uri));
      $res = $this->db->select(DB_TABLES["news"], array(DBT_NEWS["category_id"]),
                               DBT_NEWS["id"]."=".$contentInfo["id"], "LIMIT 1");
      if ($res) {
        $category = $this->db->select(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                                      DBT_NEWS_CATEGORIES["id"]."=".$res[0][ DBT_NEWS["category_id"] ]);
        $contentInfo["name"] = $category ? $category[0][ DBT_NEWS_CATEGORIES["name"] ] : "Неизвестная категория";
        return $contentInfo;
      }
    } else if ( preg_match(self::PATTERN_FOR_PAGES, $uri) ) {
      $contentInfo = array("type" => 3, "id" => (int)preg_replace(self::PATTERN_FOR_NUMBERS, "", $uri));
      $res = $this->db->select(DB_TABLES["pages"], array(DBT_PAGES["title"]),
                               DBT_PAGES["id"]."=".$contentInfo["id"], "LIMIT 1");
      if ($res) {
        $contentInfo["name"] = $res[0][ DBT_PAGES["title"] ];
        return $contentInfo;
      }
    } else if ( preg_match(self::PATTERN_FOR_AP, $uri) ) {
      $contentInfo = array("type" => 5, "name" => "");
      if ($uri == "/admin/" || $uri == "/admin/news/") {
        $contentInfo["id"] = 0;
        return $contentInfo;
      } else if ($uri == "/admin/widgets/") {
        $contentInfo["id"] = 1;
        return $contentInfo;
      } else if ($uri == "/admin/pages/") {
        $contentInfo["id"] = 2;
        return $contentInfo;
      } else if ($uri == "/admin/categories/") {
        $contentInfo["id"] = 3;
        return $contentInfo;
      }
    } else if ( preg_match(self::PATTERN_FOR_AP_NEWS_EDITOR, $uri) ) {
      $contentInfo = array("type" => 6, "name"=>"Редактирование публикации",
                                        "id" => (int)preg_replace(self::PATTERN_FOR_NUMBERS, "", $uri));
      return $contentInfo;
    } else if ( preg_match(self::PATTERN_FOR_AP_WIDGETS_EDITOR, $uri) ) {
      $contentInfo = array("type" => 7, "name"=>"Редактирование виджета",
                                        "id" => (int)preg_replace(self::PATTERN_FOR_NUMBERS, "", $uri));
      return $contentInfo;
    } else if ( preg_match(self::PATTERN_FOR_AP_PAGES_EDITOR, $uri) ) {
      $contentInfo = array("type" => 8, "name"=>"Редактирование динамической страницы",
                                        "id" => (int)preg_replace(self::PATTERN_FOR_NUMBERS, "", $uri));
      return $contentInfo;
    } else if ( preg_match(self::PATTERN_FOR_AP_CATEGORIES_EDITOR, $uri) ) {
      $contentInfo = array("type" => 9, "name"=>"Редактирование категории",
                                        "id" => (int)preg_replace(self::PATTERN_FOR_NUMBERS, "", $uri));
      return $contentInfo;
    }
    return null;
  }
}

?>
