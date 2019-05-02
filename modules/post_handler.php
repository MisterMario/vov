<?php

if ( isset($_POST["form-type"]) ) {
  switch ($_POST["form-type"]) {
    #################################################################
    #    All - Запросы доступные неавторизированным пользователям   #
    #################################################################
    case "search-form":
      $contentInfo = $search->getResults($_POST["search-string"]);
      break;
    case "add-comment":
      $commentsChannel->add($_POST["post-id"], $_POST["comment-text"], $_POST["comment-author"]);
      break;
    case "login":
      $user = $auth->login($_POST["login"], $_POST["password"]);
      break;
  }
  // Необходимо сделать так, чтобы если выполняется первый swith, то второй игнорировался!!!!
  // Приведенное строкой ниже решение не лучший вариант - стоит подумать над этим!
  if ($_POST["form-type"] == "search-form" || $_POST["form-type"] == "add-comment"|| $_POST["form-type"] == "login") return;
  /* POST запросы доступные только авторизированному администратору */
  if (!$user) return die("НЕТ ПРАВ ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО ЗАПРОСА");
  switch ($_POST["form-type"]) {
    case "logout":
      $auth->logout();
      break;
    ############
    # Add POST #
    ############
    case "add-post":
      header("Location: /admin/news/edit/0/");
      break;
    case "add-widget":
      header("Location: /admin/widgets/edit/0/");
      break;
    case "add-page":
      header("Location: /admin/pages/edit/0/");
      break;
    case "add-category":
      header("Location: /admin/categories/edit/0/");
      break;
    #############
    # Edit POST #
    #############
    case "post-edit":
      header("Location: /admin/news/edit/".$_POST["item-id"]."/");
      break;
    case "widget-edit":
      header("Location: /admin/widgets/edit/".$_POST["item-id"]."/");
      break;
    case "page-edit":
      header("Location: /admin/pages/edit/".$_POST["item-id"]."/");
      break;
    case "category-edit":
      header("Location: /admin/categories/edit/".$_POST["item-id"]."/");
      break;
    ##############
    # Delte POST #
    ##############
    case "post-delete":
      $newsChannel->delete((int)$_POST["item-id"]);
      break;
    case "widget-delete":
      $widgetsChannel->delete((int)$_POST["item-id"]);
      break;
    case "page-delete":
      $pagesChannel->delete((int)$_POST["item-id"]);
      break;
    case "category-delete":
      $categoriesChannel->delete((int)$_POST["item-id"]);
      break;
    ##############
    # Clear POST #
    ##############
    case "clear-news-feed":
      $newsChannel->clear();
      break;
    case "clear-widgets":
      $widgetsChannel->clear();
      break;
    case "clear-pages":
      $pagesChannel->clear();
      break;
    case "clear-categories":
      $categoriesChannel->clear();
      break;
    ####################
    # Editor Add POST #
    ####################
    case "editor-add-post":
      $newsChannel->add($_POST["category"], $_POST["image"], $_POST["title"], $_POST["description"],
                        NewsEditor\Text::encode($_POST["content"]), $user->getName());
      break;
    case "editor-add-widget":
      $widgetsChannel->add($_POST["title"], $_POST["content"]);
      break;
    case "editor-add-page":
      $pagesChannel->add($_POST["title"], $_POST["content"]);
      break;
    case "editor-add-category":
      $categoriesChannel->add($_POST["name"]);
      break;
    ####################
    # Editor Edit POST #
    ####################
    case "editor-edit-post":
      $newsChannel->edit($_POST["item-id"], $_POST["category"], $_POST["image"],
                         $_POST["title"], $_POST["description"], NewsEditor\Text::encode($_POST["content"]), $user->getName());
      break;
    case "editor-edit-widget":
      $widgetsChannel->edit($_POST["item-id"], $_POST["title"], $_POST["content"]);
      break;
    case "editor-edit-page":
      $pagesChannel->edit($_POST["item-id"], $_POST["title"], $_POST["content"]);
      break;
    case "editor-edit-category":
      $categoriesChannel->edit($_POST["item-id"], $_POST["name"]);
      break;
  }
}


?>
