<?php

namespace News;

require_once(MODULES_CATALOG."comments.php");
use Comments\CommentsChannel;

/* Класс для формирования HTML каркаса новостных публикаций */
class NewsPost {
  public static function getSummaryHTML($templateEngine, $id, $image, $title, $description, $category, $categoryId, $countOfViews) {
    $templateEngine->assign("post_id", $id);
    $templateEngine->assign("post_image", $image);
    $templateEngine->assign("post_title", $title);
    $templateEngine->assign("post_countOfViews", $countOfViews);
    $templateEngine->assign("post_category", $category);
    $templateEngine->assign("category_link", "/category/".$categoryId."/");
    $templateEngine->assign("post_description", $description);
    $templateEngine->assign("post_link", "/post/".$id."/");
    return $templateEngine->fetch(VIEW_SITE."short_news_post.html");
  }
  public static function getFullHTML($templateEngine, $id, $image, $title, $content, $categoryId, $category, $author, $countOfViews, $commentsBlock) {
    $templateEngine->assign("post_id", $id);
    $templateEngine->assign("post_image", $image);
    $templateEngine->assign("post_title", $title);
    $templateEngine->assign("post_content", $content);
    $templateEngine->assign("post_countOfViews", $countOfViews);
    $templateEngine->assign("category_link", "/category/".$categoryId."/");
    $templateEngine->assign("post_category", $category);
    $templateEngine->assign("post_author", $author);
    $templateEngine->assign("comments_block", $commentsBlock);
    return $templateEngine->fetch(VIEW_SITE."full_news_post.html");
  }
}

/* Класс для формирования новостной таблицы в административной панели */
class NewsTable {
  public static function getElementHTML($templateEngine, $id, $category, $title, $author, $countOfViews) {
    $templateEngine->assign("post_id", $id);
    $templateEngine->assign("post_category", $category);
    $templateEngine->assign("post_title", $title);
    $templateEngine->assign("post_author", $author);
    $templateEngine->assign("post_countOfViews", $countOfViews);
    return $templateEngine->fetch(VIEW_AP."news_table_element.html");
  }
  public static function getHTML($templateEngine, $newsList) {
    $tableContent = "";
    for ($i = 0; $i < count($newsList); $i++) {
      $tableContent .= self::getElementHTML($templateEngine,
                                            $newsList[$i][ DBT_NEWS["id"] ],
                                            $newsList[$i][ "category" ],
                                            $newsList[$i][ DBT_NEWS["title"] ],
                                            $newsList[$i][ "author" ],
                                            $newsList[$i][ DBT_NEWS["count_of_views"] ]);
    }
    $templateEngine->assign("tableContent", $tableContent ? $tableContent : "<td colspan=\"6\" align=\"center\">Новостная лента пуста</td>");
    return $templateEngine->fetch(VIEW_AP."news_content.html");
  }
}

class NewsChannel {
  private $db;
  private $templateEngine;
  private $commentsChannel;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
    $this->commentsChannel = new CommentsChannel($db, $templateEngine);
  }
  public function getAllNews($categoryId = null) {
    $newsList = array();
    $news = $this->db->select(DB_TABLES["news"], array(DBT_NEWS["id"],
                                                       DBT_NEWS["image"],
                                                       DBT_NEWS["title"],
                                                       DBT_NEWS["description"],
                                                       DBT_NEWS["category_id"],
                                                       DBT_NEWS["count_of_views"]),
                                                       $categoryId ? DBT_NEWS["category_id"]."=".$categoryId : "");
    if (!$news) return null;
    for ($i = 0; $i < count($news); $i++) {
      $category = $this->db->select(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                                    DBT_NEWS_CATEGORIES["id"]."=".$news[$i][ DBT_NEWS["category_id"] ]);
      $newsList[$i]  = NewsPost::getSummaryHTML($this->templateEngine,
                                                $news[$i][ DBT_NEWS["id"] ],
                                                $news[$i][ DBT_NEWS["image"] ],
                                                $news[$i][ DBT_NEWS["title"] ],
                                                $news[$i][ DBT_NEWS["description"] ],
                                                $category ? $category[0][ DBT_NEWS_CATEGORIES["name"] ] : "Неизвестно",
                                                $news[$i][ DBT_NEWS["category_id"] ],
                                                $news[$i][ DBT_NEWS["count_of_views"] ]);
    }
    return $newsList;
  }
  public function getNewsPost($id) {
    $newsPost = $this->db->select(DB_TABLES["news"], array(DBT_NEWS["id"],
                                                           DBT_NEWS["image"],
                                                           DBT_NEWS["title"],
                                                           DBT_NEWS["content"],
                                                           DBT_NEWS["category_id"],
                                                           DBT_NEWS["author_id"],
                                                           DBT_NEWS["count_of_views"],
                                                           DBT_NEWS["creation_date"]),
                                                           DBT_NEWS["id"]."=".$id);
    if (!$newsPost) return null;
    $category = $this->db->select(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                                  DBT_NEWS_CATEGORIES["id"]."=".$newsPost[0][ DBT_NEWS["category_id"] ]);
    $author = $this->db->select(DB_TABLES["users"], array(DBT_USERS["name"] ),
                                DBT_USERS["id"]."=".$newsPost[0][ DBT_NEWS["author_id"] ]);
    $commentsBlock = $this->commentsChannel->getAllComments($newsPost[0][ DBT_NEWS["id"] ]);
    // +1 view
    $this->db->update(DB_TABLES["news"], array(DBT_NEWS["count_of_views"]),
                                         array($newsPost[0][DBT_NEWS["count_of_views"]  ] + 1),
                                         DBT_NEWS["id"]."=".$id);
    return NewsPost::getFullHTML($this->templateEngine,
                                 $newsPost[0][ DBT_NEWS["id"] ],
                                 $newsPost[0][ DBT_NEWS["image"] ],
                                 $newsPost[0][ DBT_NEWS["title"] ],
                                 $newsPost[0][ DBT_NEWS["content"] ],
                                 $newsPost[0][ DBT_NEWS["category_id"] ],
                                 $category ? $category[0][ DBT_NEWS_CATEGORIES["name"] ] : "Неизвестно",
                                 $author ? $author[0][ DBT_USERS["name"] ] : "Неизвестно",
                                 $newsPost[0][ DBT_NEWS["count_of_views"] ],
                                 $commentsBlock ? join($commentsBlock, "") : ME_NO_COMMENTS);
  }
  public function getNewsByTitle($title) {
    $newsList = array();
    $news = $this->db->select(DB_TABLES["news"], array(DBT_NEWS["id"],
                                                       DBT_NEWS["image"],
                                                       DBT_NEWS["title"],
                                                       DBT_NEWS["description"],
                                                       DBT_NEWS["category_id"],
                                                       DBT_NEWS["count_of_views"]),
                                                       DBT_NEWS["title"]." LIKE '%".$title."%'");
    if (!$news) return null;
    for ($i = 0; $i < count($news); $i++) {
      $category = $this->db->select(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                                    DBT_NEWS_CATEGORIES["id"]."=".$news[$i][ DBT_NEWS["category_id"] ]);
      if (!$category) $category = "Неизвестно";
      $newsList[$i]  = NewsPost::getSummaryHTML($this->templateEngine,
                                                $news[$i][ DBT_NEWS["id"] ],
                                                $news[$i][ DBT_NEWS["image"] ],
                                                $news[$i][ DBT_NEWS["title"] ],
                                                $news[$i][ DBT_NEWS["description"] ],
                                                $category[0][ DBT_NEWS_CATEGORIES["name"] ],
                                                $news[$i][ DBT_NEWS["category_id"] ],
                                                $news[$i][ DBT_NEWS["count_of_views"] ]);
    }
    return $newsList;
  }
  /* Admin functions */
  public function getNewsTable() {
    $news = $this->db->select(DB_TABLES["news"], array(DBT_NEWS["id"],
                                                       DBT_NEWS["category_id"],
                                                       DBT_NEWS["title"],
                                                       DBT_NEWS["author_id"],
                                                       DBT_NEWS["count_of_views"]));
    if (!$news) $news = array();
    for ($i = 0; $i < count($news); $i++) {
      $category = $this->db->select(DB_TABLES["categories"], array(DBT_NEWS_CATEGORIES["name"]),
                                    DBT_NEWS_CATEGORIES["id"]."=".$news[$i][ DBT_NEWS["category_id"] ]);
      $author = $this->db->select(DB_TABLES["users"], array(DBT_USERS["name"] ),
                                  DBT_USERS["id"]."=".$news[$i][ DBT_NEWS["author_id"] ]);
      $news[$i]["category"]  = $category ? $category[0][ DBT_NEWS_CATEGORIES["name"] ] : "Неизвестно";
      $news[$i]["author"]  = $author ? $author[0][ DBT_USERS["name"] ] : "Неизвестно";
    }
    return NewsTable::getHTML($this->templateEngine, $news);
  }
  public function add($categoryId, $image, $title, $description, $content, $author_id) {
    return $this->db->insert(DB_TABLES["news"], array(DBT_NEWS["category_id"],
                                                      DBT_NEWS["image"],
                                                      DBT_NEWS["title"],
                                                      DBT_NEWS["description"],
                                                      DBT_NEWS["content"],
                                                      DBT_NEWS["author_id"],
                                                      DBT_NEWS["creation_date"]),
                                                array($categoryId,
                                                      $image,
                                                      $title,
                                                      $description,
                                                      $content,
                                                      $author_id,
                                                      date("Y-m-d")));
  }
  public function edit($postId, $categoryId, $image, $title, $description, $content, $author_id) {
    return $this->db->update(DB_TABLES["news"], array(DBT_NEWS["category_id"],
                                                  DBT_NEWS["image"],
                                                  DBT_NEWS["title"],
                                                  DBT_NEWS["description"],
                                                  DBT_NEWS["content"],
                                                  DBT_NEWS["author_id"]),
                                            array($categoryId,
                                                  $image,
                                                  $title,
                                                  $description,
                                                  $content,
                                                  $author_id),
                                            DBT_NEWS["id"]."=".$postId);
  }
  public function delete($postId) {
    if (gettype($postId) == "integer") $condition = DBT_NEWS["id"]."='".$postId."'";
    else if (gettype($postId) == "array") $condition = DBT_NEWS["id"]." IN (".join($postId, ", ").")";
    $this->db->delete(DB_TABLES["comments"], $condition);
    return $this->db->delete(DB_TABLES["news"], $condition);
  }
  public function clear() {
    $this->db->clear(DB_TABLES["comments"]);
    return $this->db->clear(DB_TABLES["news"]);
  }
}

?>
