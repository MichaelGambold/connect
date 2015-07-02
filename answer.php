<?php
   require 'php/connect.php';

   // display variables
   echo 'wine name: ' . $_GET['wineName'] . '<br>';
   echo 'winery name: ' . $_GET['wineryName'] . '<br>';
   echo 'region: ' . $_GET['region'] . '<br>';
   echo 'grape variety: ' . $_GET['grapeVariety'] . '<br>';
   //echo 'min year: ' . $_GET['minYear'] . '<br>';
   //echo 'max year: ' . $_GET['maxYear'] . '<br>';
   echo 'min wine in stock: ' . $_GET['minWineInStock'] . '<br>';
   echo 'min wines ordered: ' . $_GET['minWinesOrdered'] . '<br>';
   echo 'min cost: ' . $_GET['minCost'] . '<br>';
   echo 'max cost: ' . $_GET['maxCost'] . '<br>';

   // connect to database
   $db = db_connect();

   // perform basic server side validation
   try {
      // check years not less or greater than years in database
   
      // check min wine in stock value is not negative
      if (!empty($_GET['minWineInStock']) && $_GET['minWinInStock'] < 0) {
         throw new Exception('Min wine in stock cannot be negative');
      }

      // check min wines ordered is not negative
      if (!empty($_GET['minWinesOrdered']) && $_GET['minWinesOrdered'] < 0) {
         throw new Exception('Min wines ordered cannot be negative');
      }

      // assignment spec mentions about only having either min or max for cost check for only one
      if (!empty($_GET['minCost']) && !empty($_GET['maxCost'])) {
         throw new Exception('Cannot enter both min and max years');
      }

      // check min cost is not negative
      if (!empty($_GET['minCost']) && $_GET['minCost'] < 0) {
         throw new Exception('Min cost cannot be negative');
      }

      // check max cost is not negative
      if (!empty($GET['maxCost']) && $_GET['maxCost'] < 0) {
         throw new Exception('Max Cost cannot be negative');
      }
   } catch (Exception $e) {
      // close database connection
      $db = null;
   }

   // create database statment based on inputs available
   echo '<br>testing for matches to wine name<br><br>';
   
   $stmt = $db->prepare("SELECT w.wine_name, wt.wine_type, w.year, wy.winery_name, r.region_name, gv.grape_blend, inv.on_hand, ord.ordered 
                         FROM wine w 
                         JOIN wine_type wt ON wt.wine_type_id = w.wine_type 
                         JOIN winery wy ON wy.winery_id = w.winery_id 
                         JOIN region r ON r.region_id = wy.region_id 
                         JOIN (SELECT wv.wine_id, GROUP_CONCAT(gv.variety SEPARATOR ' ') AS grape_blend 
                               FROM wine_variety wv 
                               JOIN grape_variety gv ON gv.variety_id = wv.variety_id 
                               GROUP BY wv.wine_id 
                               ORDER BY wv.wine_id, wv.id DESC) AS gv ON gv.wine_id = w.wine_id 
                         JOIN inventory inv ON inv.wine_id = w.wine_id 
                         JOIN (SELECT wine_id, SUM(qty) AS ordered 
                               FROM items 
                               GROUP BY wine_id) AS ord ON ord.wine_id = w.wine_id;");
      //$stmt = $db->prepare("SELECT w.wine_name, wt.wine_type, w.year, wy.winery_name
                           // FROM wine w");
                           // JOIN wine_type wt
                           // ON wt.wine_type = w.wine_type
                           // JOIN winery wy
                           // ON wy.winery_id = w.winery_id");
                           // WHERE w.wine_name LIKE '%" . $_GET['wineName'] . "%'");
   
   $stmt->execute();

   echo '<table><tr>';
   echo '<th>wine name</th>';
   echo '<th>wine type</th>';
   echo '<th>year</th>';
   echo '<th>winery_name</th>';
   echo '<th>region</th>';
   echo '<th>grape variety</th>';
   echo '<th>num in stock</th>';
   echo '<th>orders</th>';
   echo '</tr>';
 
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo '<tr><td>' . $row['wine_name'] . '</td>';
      echo '<td>' . $row['wine_type'] . '</td>';
      echo '<td>' . $row['year'] . '</td>';
      echo '<td>' . $row['winery_name'] . '</td>';
      echo '<td>' . $row['region_name'] . '</td>';
      echo '<td>' . $row['grape_blend'] . '</td>';
      echo '<td>' . $row['on_hand'] . '</td>';
      echo '<td>' . $row['ordered'] . '</td>';
      

      echo '</tr>';
   }
   //$stmt = $db->prepare('');

   // close database connection
    $db = null;
?>
