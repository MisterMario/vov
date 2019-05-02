<?php

require_once("config.php");

session_start();
$user = $auth->createUser();

if ( !empty($_POST) ) require_once(MODULES_CATALOG."post_handler.php"); // Обработка POST-а

/* Content section */
if (!isset($contentInfo)) $contentInfo = $SEF->getPageInfo($_SERVER["REQUEST_URI"]);
if ($contentInfo == null) {
  echo $page->getHTML("", "Ошибка 404", ME_404);
} else if ($contentInfo["type"] == 0) {
  $news = $newsChannel->getAllNews();
  echo $page->getHTML("", $contentInfo["name"], ($news ? join($news, "") : ME_NO_NEWS));
} else if ($contentInfo["type"] == 1) {
  $news = $newsChannel->getAllNews($contentInfo["id"]);
  echo $page->getHTML("", $contentInfo["name"], ($news ? join($news, "") : ME_NO_NEWS));
} else if ($contentInfo["type"] == 2) {
  $newsPost = $newsChannel->getNewsPost($contentInfo["id"]);
  echo $page->getHTML("", $contentInfo["name"], ($newsPost ? $newsPost : ME_NO_NEWS));
} else if ($contentInfo["type"] == 3) {
  $dynamicPage = $pagesChannel->getPage($contentInfo["id"]);
  echo $page->getHTML("", $contentInfo["name"], $dynamicPage ? $dynamicPage : ME_UNKNOWN);
} else if ($contentInfo["type"] == 4) {
  echo $page->getHTML("", $contentInfo["name"], $contentInfo["content"]);
} else if (($contentInfo["type"] == 5 || $contentInfo["type"] == 6 || $contentInfo["type"] == 7
            || $contentInfo["type"] == 8 || $contentInfo["type"] == 9) && !$user) { // Проверка авторизованности пользователя
  echo $page->getAdminLoginHTML();
} else if ($contentInfo["type"] == 5) {
  if ($contentInfo["id"] == 0) echo $page->getAdminHTML($user->getName(), "", "Новостной контент", $newsChannel->getNewsTable());
  else if ($contentInfo["id"] == 1) echo $page->getAdminHTML($user->getName(), "", "Управление виджетами", $widgetsChannel->getWidgetsTable());
  else if ($contentInfo["id"] == 2) echo $page->getAdminHTML($user->getName(), "", "Динамические страницы", $pagesChannel->getPagesTable());
  else if ($contentInfo["id"] == 3) echo $page->getAdminHTML($user->getName(), "", "Новостные категории", $categoriesChannel->getCategoriesTable());
} else if ($contentInfo["type"] == 6) {
  echo $page->getAdminHTML($user->getName(), "", "Редактор новостей", $contentInfo["id"] != 0 ? $newsEditor->getHTMLForPost( $contentInfo["id"])
                                                                                              : $newsEditor->getHTMLForNewPost() );
} else if ($contentInfo["type"] == 7) {
  echo $page->getAdminHTML($user->getName(), "", "Редактор виджетов", $contentInfo["id"] != 0 ? $widgetsEditor->getHTMLForWidget( $contentInfo["id"])
                                                                                              : $widgetsEditor->getHTMLForNewWidget() );
} else if ($contentInfo["type"] == 8) {
  echo $page->getAdminHTML($user->getName(), "", "Редактор динамических страниц", $contentInfo["id"] != 0 ? $pagesEditor->getHTMLForPage( $contentInfo["id"])
                                                                                                          : $pagesEditor->getHTMLForNewPage() );
} else if ($contentInfo["type"] == 9) {
  echo $page->getAdminHTML($user->getName(), "", "Редактор новостных категорий", $contentInfo["id"] != 0 ? $categoriesEditor->getHTMLForCategory($contentInfo["id"])
                                                                                                         : $categoriesEditor->getHTMLForNewCategory() );
}

?>
