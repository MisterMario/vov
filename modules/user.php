<?php

namespace User;

class User {
  private $id;
  private $name;
  public function __construct($id, $name) {
    $this->id = $id;
    $this->name = $name;
  }
  public function getId() { return $this->id; }
  public function getName() {return $this->name; }
}

class Auth {
  private $db;

  public function __construct($db) {
    $this->db = $db;
  }
  /* Функция для генерации случайной строки */
  public static function TmpGenerate($tmp_length = 32){
  	$allchars = "abcdefghijklmnopqrstuvwxyz0123456789";
  	$output = "";
    mt_srand( (double) microtime() * 1000000 );
  	for($i = 0; $i < $tmp_length; $i++){
  	   $output .= $allchars{ mt_rand(0, strlen($allchars)-1) };
  	}
  	return $output;
  }
  public function login($login, $password) {
    // выполняется при авторизации
    $userInfo = $this->db->select(DB_TABLES["users"], array(DBT_USERS["id"],
                                                      DBT_USERS["password"],
                                                      DBT_USERS["name"]),
                                                DBT_USERS["login"]."='".$login."'", "LIMIT 1");
    if ($userInfo && ($userInfo[0][ DBT_USERS["password"] ] == md5($password)) ) { // аутентификация пользователя
      $tmp = self::TmpGenerate();
      if ( !$this->db->update(DB_TABLES["users"], array(DBT_USERS["tmp"]),
                                           array($tmp),
                                           DBT_USERS["id"]."=".$userInfo[0][ DBT_USERS["id"] ]) ) return die("НЕ УДАЛОСЬ СОЗДАТЬ COOKIE!");
      $_SESSION["user"] = array("id" => $userInfo[0][ DBT_USERS["id"] ], "name" => $userInfo[0][ DBT_USERS["name"] ]);
      setcookie("vov-id", $userInfo[0][ DBT_USERS["id"] ], time()+3600*24*30, "/");
      setcookie("vov-tmp", $tmp, time()+3600*24*30, "/");
      return new User($userInfo[0][ DBT_USERS["id"] ], $userInfo[0][ DBT_USERS["name"] ]);
    }
  }
  public function createUser() {
    if ( !isset($_SESSION["user"]) && isset($_COOKIE["vov-id"]) && isset($_COOKIE["vov-tmp"]) ) { // Проверка существования и валидности кукисов
      $userInfo = $this->db->select(DB_TABLES["users"], array(DBT_USERS["id"],
                                                        DBT_USERS["name"]),
                                                  DBT_USERS["tmp"]."='".$_COOKIE["vov-tmp"]."' AND ".
                                                  DBT_USERS["id"]."='".$_COOKIE["vov-id"]."'", "LIMIT 1");
      if (!$userInfo) { // Информация из кукисов устарела или ложная
      	setcookie("vov-id", "", time()-3600, "/");
      	setcookie("vov-tmp", "", time()-3600, "/");
        return false;
      }
      $_SESSION["user"] = array("id" => $userInfo[0][ DBT_USERS["id"] ], "name" => $userInfo[0][ DBT_USERS["name"] ]);
      return new User($userInfo[0][ DBT_USERS["id"] ], $userInfo[0][ DBT_USERS["name"] ]);
    } else if ( isset($_SESSION["user"]) ) {
      return new User($_SESSION["user"]["id"], $_SESSION["user"]["name"]);
    }
    return false;
  }
  public function logout() {
    $this->db->update(DB_TABLES["users"], array(DBT_USERS["tmp"]),
                                         array(null),
                                         DBT_USERS["id"]."=".$userInfo["id"]);
  	session_unset();
  	session_destroy();
  	setcookie("vov-id", "", time()-3600, "/");
  	setcookie("vov-tmp", "", time()-3600, "/");
  	header("Location: http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
  }
}

 ?>
