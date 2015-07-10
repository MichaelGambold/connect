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

   // perform basic server side validation
   try {
      // check inputs are alphanumeric
      $alphaNumRegex = "/^[a-z0-9 ]*$/i";
      $yearRegex = "/^[12][0-9]{3}$/";

      if (!empty($_GET['wineName']))
         if (!preg_match($alphaNumRegex, $_GET['wineName']))
            throw new Exception('Invalid wine name');

      if (!empty($_GET['wineryName']))
         if (!preg_match($alphaNumRegex, $_GET['wineryName']))
            throw new Exception('Invalid winery name');

      if (!empty($_GET['region']))
         if (!preg_match($alphaNumRegex, $_GET['region']))
            throw new Exception('Invalid region');

      if (!empty($_GET['grapeVariety']))
         if (!preg_match($alphaNumRegex, $_GET['grapeVariety']))
            throw new Exception('Invalid grape variety');

      // check years are valid
      if (!empty($_GET['minYear']))
         if (!preg_match($yearRegex, $_GET['minYear']))
            throw new Exception('Invalid min year');

      if (!empty($_GET['maxYear']))
         if (!preg_match($yearRegex, $_GET['maxYear']))
            throw new Exception('Invalid max year');

      // check that min year is not greater than max year if it exists
      if (!empty($_GET['minYear']) && !empty($_GET['maxYear']))
         if ($_GET['maxYear'] < $_GET['minYear'])
            throw new Exception('Max year cannot be less the min year');
   
      // check min wine in stock value is not negative
      if (!empty($_GET['minWinesInStock']))
         if ($_GET['minWinesInStock'] < 0)
            throw new Exception('Min wine in stock cannot be negative');

      // check min wines ordered is not negative
      if (!empty($_GET['minWinesOrdered']))
         if ($_GET['minWinesOrdered'] < 0)
            throw new Exception('Min wines ordered cannot be negative');

      // check min cost is not negative
      if (!empty($_GET['minCost']))
         if($_GET['minCost'] < 0)
            throw new Exception('Min cost cannot be negative');

      // check max cost is not negative
      if (!empty($_GET['maxCost']))
         if ($_GET['maxCost'] < 0)
            throw new Exception('Max Cost cannot be negative');

      // check max cost is not less than min cost if they both exists
      if (!empty($_GET['minCost']) && $_GET['maxCost'])
         if ($_GET['maxCost'] < $_GET['minCost'])
            throw new Exception('Max cost cannot be less than min cost');
      
   } catch (Exception $e) {
      // kill page with error
      die('Error: ' . $e->getMessage());
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
      $where[] = "r.region_name LIKE CONCAT('%', :region, '%')";

   if (!empty($_GET['grapeVariety']) && $_GET['grapeVariety'] != 'All')
      $where[] = "gv.grape_blend LIKE CONCAT('%', :grapeVariety, '%')";

   if (!empty($_GET['minCost']))
      $where[] = "inv.cost >= :minCost";

   if (!empty($_GET['maxCost']))
      $where[] = "inv.cost <= :maxCost";

//echo 'where statement: ' . implode(' AND ', $where);
   

   // create database statment based on inputs available   
   $stmt = $db->prepare("SELECT w.wine_name, wt.wine_type, w.year, wy.winery_name, r.region_name, gv.grape_blend, inv.cost, inv.on_hand, ord.ordered, ord.salesRev, w.wine_id
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
                         JOIN (SELECT wine_id, SUM(qty) AS ordered, sum(price) AS salesRev
                               FROM items
                               GROUP BY wine_id) AS ord ON ord.wine_id = w.wine_id
                         WHERE " . implode(' AND ', $where) . "
                         ORDER BY w.wine_id");

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
      $stmt->bindParam(':region', $_GET['region'], PDO::PARAM_STR);

   if (!empty($_GET['grapeVariety']) && $_GET['grapeVariety'] != 'All')
      $stmt->bindParam(':grapeVariety', $_GET['grapeVariety'], PDO::PARAM_STR);
   
   if (!empty($_GET['minCost']))
      $stmt->bindParam(':minCost', $_GET['minCost'], PDO::PARAM_STR);

   if (!empty($_GET['maxCost']))
      $stmt->bindParam(':maxCost', $_GET['maxCost'], PDO::PARAM_STR);
   
   $stmt->execute();
   
    // save results to session variable
    $_SESSION['results'] = $stmt->fetchAll();

   // close database connection
   $db = null;

   // redirect to results page
   $resultsPage = 'results.php';
   header("Location: $resultsPage");
?>

