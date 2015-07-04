<?php
   // start session
   session_start();

   // add required files
   require 'php/connect.php';
   
   // assign get paramaters to variables
   $wineName = $_GET['wineName'];
   $wineryName = $_GET['wineryName'];
   $region = $_GET['region'];
   $grapeVariety = $_GET['grapeVariety'];
   $minYear = $_GET['minYear'];
   $maxYear = $_GET['maxYear'];
   $minWinesInStock = $_GET['minWinesInStock'];
   $minWinesOrdered = $_GET['minWinesOrdered'];
   $minCost = $_GET['minCost'];
   $maxCost = $_GET['maxCost'];   

   // display variables
   echo 'wine name: ' . $wineName . '<br>';
   echo 'winery name: ' . $wineryName . '<br>';
   echo 'region: ' . $region . '<br>';
   echo 'grape variety: ' . $grapeVariety . '<br>';
   echo 'min year: ' . $minYear . '<br>';
   echo 'max year: ' . $maxYear . '<br>';
   echo 'min wine in stock: ' . $minWinesInStock . '<br>';
   echo 'min wines ordered: ' . $minWinesOrdered . '<br>';
   echo 'min cost: ' . $minCost . '<br>';
   echo 'max cost: ' . $maxCost . '<br>';

   // perform basic server side validation
   try {
      // check years not less or greater than years in database
   
      // check min wine in stock value is not negative
      if (!empty($_GET['minWinesInStock']) && $_GET['minWinesInStock'] < 0) {
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
      // kill page with error
      die('Error: Invalid search parameters');
   }

   // connect to database
   $db = db_connect();
   
   // create array of where statments
   $where = array();
   $where[] = "w.year >= :minYear";
   $where[] = "w.year <=:maxYear";
   $where[] = "inv.on_hand >= :numInStock";
   $where[] = "ord.ordered >= :numOrdered";

   if (!empty($_GET['wineName']))
      $where[] = "w.wine_name LIKE CONCAT('%', :wineName, '%')";
   
   if (!empty($_GET['wineryName']))
      $where[] = "wy.winery_name LIKE CONCAT('%', :wineryName, '%')";

   if (!empty($_GET['region']) && $_GET['region'] != 'All')
      $where[] = "r.region LIKE CONCAT('%', :region, '%')";

   if (!empty($_GET['grapeVariety']) && $_GET['grapeVariety'] != 'All')
      $where[] = "gv.grape_blend LIKE CONCAT('%', :grapeVariety, '%')";

   if (!empty($_GET['minCost']))
      $where[] = "inv.cost >= :minCost";

   if (!empty($_GET['maxCost']))
      $where[] = "inv.cost <= :maxCost";

//echo 'where statement: ' . implode(' AND ', $where);
   

   // create database statment based on inputs available
   echo '<br>testing for matches to wine name<br><br>';
   
   $stmt = $db->prepare("SELECT w.wine_name, wt.wine_type, w.year, wy.winery_name, r.region_name, gv.grape_blend, inv.cost, inv.on_hand, ord.ordered 
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
                               GROUP BY wine_id) AS ord ON ord.wine_id = w.wine_id
                         WHERE " . implode(' AND ', $where));
   
   // add '%' wild cards for sql parameters where required for LIKE WHERE clauses
   /*$wineName = '%' . $wineName . '%';
   $wineryName = '%' . $wineryName . '%';
   $region = '%' . $region . '%';
   $grapeVariety = '%' . $grapeVarity . '%';*/

   // bind standard parameters
   $stmt->bindParam(':minYear', $_GET['minYear'], PDO::PARAM_INT);
   $stmt->bindParam(':maxYear', $_GET['maxYear'], PDO::PARAM_INT);
   $stmt->bindParam(':numInStock', $_GET['minWinesInStock'], PDO::PARAM_INT);
   $stmt->bindParam(':numOrdered', $_GET['minWinesOrdered'], PDO::PARAM_INT);

   // bind optional parameters if required
   if (!empty($_GET['wineName']))
      $stmt->bindParam(':wineName', $_GET['wineName'], PDO::PARAM_STR);

   if (!empty($_GET['wineryName']))
      $stmt->bindParam(':wineryName', $wineryName, PDO::PARAM_STR);
   
   if (!empty($_GET['region']) && $_GET['region'] != 'All')
      $stmt->bindParam(':region', $region, PDO::PARAM_STR);

   if (!empty($_GET['grapeVariety']) && $_GET['grapeVariety'] != 'All')
      $stmt->bindParam(':grapeVariety', $_GET['grapeVariety'], PDO::PARAM_STR);
   
   if (!empty($_GET['minCost']))
      $stmt->bindParam(':minCost', $_GET['minCost'], PDO::PARAM_STR);

   if (!empty($_GET['maxCost']))
      $stmt->bindParam(':maxCost', $_GET['maxCost'], PDO::PARAM_STR);
   
   $stmt->execute();

   echo '<table><tr>';
   echo '<th>wine name</th>';
   echo '<th>wine type</th>';
   echo '<th>year</th>';
   echo '<th>winery_name</th>';
   echo '<th>region</th>';
   echo '<th>grape variety</th>';
   echo '<th>cost</th>';
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
      echo '<td>' . $row['cost'] . '</td>';
      echo '<td>' . $row['on_hand'] . '</td>';
      echo '<td>' . $row['ordered'] . '</td>'; 
      echo '</tr>';
   }
   
   // save results to session variable
   // $_SESSION['results'] = $stmt->fetchAll();

   // close database connection
    $db = null;

   // redirect to results page
   $resultsPage = 'results.php';
   //header("Location: $resultsPage");
?>
