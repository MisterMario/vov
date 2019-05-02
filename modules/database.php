<?php

namespace MysqliAdapter;

class DataBase {
  private $mysqli;

  public function __construct($db_settings) {
    $this->mysqli = new \mysqli($db_settings["host"], $db_settings["user"], $db_settings["pass"], $db_settings["name"]);
    $this->mysqli->set_charset("utf8");
    if ($this->mysqli->connect_errno) die("Ошибка соединения [".$this->mysqli->connect_errno."]: ".$this->mysqli->connect_error);
  }
  /* Входные параметры: имя таблицы, выбираемые поля, условие */
  public function select($table_name, $fields, $condition = "", $modifier = "") {
    $query = "SELECT " . join(", ", $fields) . " FROM " . $table_name;
    $query .= $condition ? " WHERE " . $condition : "";
    $query .= $modifier ? " ".$modifier : "";
    $query_result = $this->mysqli->query($query, MYSQLI_STORE_RESULT);
    if ($query_result) {
      $res = [];
      for ($i = 0; $i < $query_result->num_rows; $i++) {
        $res[$i]= $query_result->fetch_assoc();
        $query_result->data_seek($i+1);
      }
      return $res;
    }
    return false;
  }
  /* Входные параметры: имя таблицы, поля, значения полей */
  public function insert($table_name, $fields, $values) {
    $query = "INSERT INTO " . $table_name . "(" . join(", ", $fields) . ") VALUES(";
    for ($i = 0; $i < count($values); $i++) {
      $query .= "'" . $values[$i] . "'";
      if ($i != count($values)-1) $query .= ",";
    }
    $query .= ")";
    $query_result = $this->mysqli->query($query);
    return $query_result;
  }
  /* Входные параметры: имя таблицы, поля, значения полей, условие */
  public function update($table_name, $fields, $values, $condition = "") {
    $query = "UPDATE " . $table_name . " SET ";
    for ($i = 0; $i < count($fields); $i++) {
      $query .= $fields[$i] . "='" . $values[$i] . ($i < count($fields)-1 ? "', " : "' ");
    }
    $query .= ( strlen($condition) != 0 ) ? " WHERE " . $condition : "";
    $query_result = $this->mysqli->query($query);
    return $query_result;
  }
  public function delete($table_name, $condition) {
    $query = "DELETE FROM ".$table_name." WHERE ".$condition;
    $query_result = $this->mysqli->query($query);
    return $query_result;
  }
  public function clear($table_name) {
    $query = "TRUNCATE TABLE ".$table_name;
    $query_result = $this->mysqli->query($query);
    return $query_result;
  }
}

?>
