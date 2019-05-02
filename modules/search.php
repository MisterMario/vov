<?php

class Search {
  private $db;
  private $newsChannel;

  public function __construct($db, $newsChannel) {
    $this->db = $db;
    $this->newsChannel = $newsChannel;
  }
  public function getResults($searchTitle) {
    $contentInfo = array("type" => 4, "name" => "Результаты поиска");
    $res = $this->newsChannel->getNewsByTitle($searchTitle);
    if (!$res) $contentInfo["content"] = ME_SEARCH_FAILED;
    else $contentInfo["content"] = join($res, "<br />");
    return $contentInfo;
   }
}

?>
