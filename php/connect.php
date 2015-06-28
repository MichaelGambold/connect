<?php
// import database settings
require_once('db.php');

try {
   // create PDO database handler
   $dbh = new PDO(DB_ENGINE . ':host=' . DB_HOST . ';dbname=winestore;', DB_USER, DB_PW);
} catch (PDOException $e) {
   echo 'Error: ' . $e->getMessage() . '<br />';
}
?>
