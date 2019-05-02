<?php

###########################################################
#                       DIRECTORIES                       #
###########################################################

define("MODULES_CATALOG", "modules/");
define("PAGES_CATALOG", "pages/");
define("VIEW_CATALOG", "view/");
define("VIEW_SITE", "site/");
define("VIEW_AP", "admin/");
define("NEWS_IMAGES_CATALOG", VIEW_CATALOG.VIEW_SITE."news_images/");

###########################################################
#                   CONNECTED MODULES                     #
###########################################################

require_once(MODULES_CATALOG."database.php");
require_once(MODULES_CATALOG."template_engine.php");
require_once(MODULES_CATALOG."news.php");
require_once(MODULES_CATALOG."comments.php");
require_once(MODULES_CATALOG."widgets.php");
require_once(MODULES_CATALOG."pages.php");
require_once(MODULES_CATALOG."page_builder.php");
require_once(MODULES_CATALOG."SEF.php");
require_once(MODULES_CATALOG."search.php");
require_once(MODULES_CATALOG."categories.php");
require_once(MODULES_CATALOG."news_editor.php");
require_once(MODULES_CATALOG."widgets_editor.php");
require_once(MODULES_CATALOG."pages_editor.php");
require_once(MODULES_CATALOG."categories_editor.php");
require_once(MODULES_CATALOG."user.php");
use MysqliAdapter\DataBase;
use News\NewsChannel;
use Widgets\WidgetsChannel;
use Comments\CommentsChannel;
use Categories\CategoriesChannel;
use Pages\PagesChannel;
use PageBuilder\Page;
use SEF\SEF;
use NewsEditor\NewsEditor;
use WidgetsEditor\WidgetsEditor;
use PagesEditor\PagesEditor;
use CategoriesEditor\CategoriesEditor;
use User\Auth;

###########################################################
#                   CONFIG FOR DATABASE                   #
###########################################################

// Данные для связи с БД
 define("DB_SETTINGS", array(
  "host" => "127.0.0.1",
  "user" => "root",
  "pass" => "",
  "name" => "vov_site",
));

// Таблицы в БД
define("DB_TABLES", array(
  "users" => "users",
  "categories" => "news_categories",
  "news" => "news",
  "comments" => "comments",
  "pages" => "pages",
  "widgets" => "widgets",
));

// Поля таблиц в БД
define("DBT_USERS", array(
  "id" => "id",
  "login" => "login",
  "password" => "password",
  "name" => "name",
  "permissions" => "permissions",
  "reg_date" => "registration_date",
  "tmp" => "tmp",
));
define("DBT_NEWS_CATEGORIES", array(
  "id" => "id",
  "name" => "name",
));
define("DBT_NEWS", array(
  "id" => "id",
  "category_id" => "category_id",
  "image" => "image",
  "title" => "title",
  "description" => "description",
  "content" => "content",
  "author_id" => "author_id",
  "creation_date" => "creation_date",
  "count_of_views" => "count_of_views",
));
define("DBT_COMMENTS", array(
  "id" => "id",
  "post_id" => "post_id",
  "text" => "text",
  "author" => "author_name",
  "upload_date" => "upload_date",
));
define("DBT_PAGES", array(
  "id" => "id",
  "link" => "link",
  "title" => "title",
  "content" => "content",
));
define("DBT_WIDGETS", array(
  "id" => "id",
  "title" => "title",
  "content" => "content",
));

###########################################################
#                        CONSTANTS                        #
###########################################################

define("SITE_TITLE", "Все обо всем");
define("SITE_CHARSET", "utf-8");
define("ME_NO_NEWS", "<p>Новостная лента пуста...</p>");
define("ME_404", "<p>Страницы не существует...</p>");
define("ME_IN_DEVELOPING", "<p>Данная страница еще в разработке...</p>");
define("ME_SEARCH_FAILED", "<p>Новости по заданному запросу отсутствуют!</p>");
define("ME_NO_COMMENTS", "<p class=\"message-error\">Пока что еще никто не оставлял комментариев...</p>");
define("ME_UNKNOWN", "<p>Упс.. Что-то сломалось...</p>");

###########################################################
#                    GLOBAL VARIABLES                     #
###########################################################

$db = new DataBase(DB_SETTINGS);
$templateEngine = new PlainHTMLView(VIEW_CATALOG);
$newsChannel = new NewsChannel($db, new PlainHTMLView(VIEW_CATALOG));
$widgetsChannel = new WidgetsChannel($db, new PlainHTMLView(VIEW_CATALOG));
$commentsChannel = new CommentsChannel($db, new PlainHTMLView(VIEW_CATALOG));
$pagesChannel = new PagesChannel($db, new PlainHTMLView(VIEW_CATALOG));
$categoriesChannel = new CategoriesChannel($db, new PlainHTMLView(VIEW_CATALOG));
$page = new Page($db, $templateEngine, $widgetsChannel);
$SEF = new SEF($db);
$search = new Search($db, $newsChannel);
$newsEditor = new NewsEditor($db, new PlainHTMLView(VIEW_CATALOG));
$widgetsEditor = new WidgetsEditor($db, new PlainHTMLView(VIEW_CATALOG));
$pagesEditor = new PagesEditor($db, new PlainHTMLView(VIEW_CATALOG));
$categoriesEditor = new CategoriesEditor($db, new PlainHTMLView(VIEW_CATALOG));
$auth = new Auth($db);

?>
