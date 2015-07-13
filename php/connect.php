<?php
// import database settings
require_once('db.php');

function db_connect() {
   try {
      // create PDO database handler
      $dbh = new PDO(DB_ENGINE . ':host=' . DB_HOST . ';dbname=winestore;', DB_USER, DB_PW);

      // enable exception raising for debug, comment the following line out when in production
      //$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) {
      echo 'Error: ' . $e->getMessage() . '<br />';
   }

   // return database handler
   return $dbh;
}
?>
