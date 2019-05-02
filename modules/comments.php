<?php

namespace Comments;

/* Comments Channel */
class Comment {
  public static function getHTML($templateEngine, $author, $uploadDate, $text) {
    $templateEngine->assign("comment_author", $author);
    $templateEngine->assign("comment_uploadDate", $uploadDate);
    $templateEngine->assign("comment_text", $text);
    return $templateEngine->fetch(VIEW_SITE."comment.html");
  }
}

class CommentsChannel {
  private $db;
  private $templateEngine;

  public function __construct($db, $templateEngine) {
    $this->db = $db;
    $this->templateEngine = $templateEngine;
  }
  public function getAllComments($postId) {
    $commentsList = array();
    $comments = $this->db->select(DB_TABLES["comments"], array(DBT_COMMENTS["text"],
                                                               DBT_COMMENTS["author"],
                                                               DBT_COMMENTS["upload_date"]),
                                                         DBT_COMMENTS["post_id"]."=".$postId,
                                                         "ORDER BY ".DBT_COMMENTS["id"]." DESC");
    if (!$comments) return null;
    for ($i = 0; $i < count($comments); $i++) {
      $commentsList[$i] = Comment::getHTML($this->templateEngine,
                                            $comments[$i][ DBT_COMMENTS["author"] ],
                                            $comments[$i][ DBT_COMMENTS["upload_date"] ],
                                            $comments[$i][ DBT_COMMENTS["text"] ]);
    }
    return $commentsList;
  }
  public function add($postId, $text, $author) {
    $text = strip_tags($text);
    $author = trim($author);
    $author = strip_tags($author);
    //if ( !preg_match("/[a-zA-Zа-яА-Я0-9_-!,. ]{1,}/" $text) ) return false;
    if ( !preg_match("/[a-zA-Z_]{1,}/", $author) ) return false;
    else if ( strlen($text) == 0 ) return false;
    return $this->db->insert(DB_TABLES["comments"], array(DBT_COMMENTS["post_id"],
                                                          DBT_COMMENTS["text"],
                                                          DBT_COMMENTS["author"],
                                                          DBT_COMMENTS["upload_date"]),
                                                    array($postId, $text, $author, date("Y-m-d")));
  }
  public function edit($commentId, $text, $author) {
    return $this->update(DB_TABLES["news"], array(DBT_COMMENTS["text"], DBT_COMMENTS["author"]),
                                            array($text, $author),
                                            DBT_COMMENTS["id"]."=".$commentId);
  }
  public function delete($commentId) {
    if (gettype($commentId) == "integer") $condition = DBT_COMMENTS["id"]."='".$commentId."'";
    else if (gettype($commentId) == "array") $condition = DBT_COMMENTS["id"]." IN (".join($commentId, ", ").")";
    return $this->db->delete(DB_TABLES["comments"], $condition);
  }
  public function clear() {
    return $this->db->clear(DB_TABLES["comments"]);
  }
}

?>
