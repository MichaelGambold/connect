<?php
   // start session
   session_start();

   // add required files
   require 'php/connect.php';

   // perform basic server side validation
   try {
      // check inputs are alphanumeric
      $alphaNumRegex = "/^[a-z0-9 ']*$/i";
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
      $_SESSION['errorMsg'] = $e->getMessage();
      header("Location: error.php");
     die(); 
   }

   // assign get paramaters to variables
   $wineName = filter_var($_GET['wineName'], FILTER_SANITIZE_MAGIC_QUOTES);
   $wineryName = filter_var($_GET['wineryName'], FILTER_SANITIZE_MAGIC_QUOTES);
   $region = filter_var($_GET['region'], FILTER_SANITIZE_MAGIC_QUOTES);
   $grapeVariety = filter_var($_GET['grapeVariety'], FILTER_SANITIZE_MAGIC_QUOTES);
   $minYear = filter_var($_GET['minYear'], FILTER_SANITIZE_NUMBER_INT);
   $maxYear = filter_var($_GET['maxYear'], FILTER_SANITIZE_NUMBER_INT);;
   $minWinesInStock = filter_var($_GET['minWinesInStock'], FILTER_SANITIZE_NUMBER_INT);
   $minWinesOrdered = filter_var($_GET['minWinesOrdered'], FILTER_SANITIZE_NUMBER_INT);
   $minCost = filter_var($_GET['minCost'], FILTER_SANITIZE_NUMBER_FLOAT);
   $maxCost = filter_var($_GET['maxCost'], FILTER_SANITIZE_NUMBER_FLOAT);
   
   // connect to database
   $db = db_connect();
   
   // create array of where statments
   $where = array();
   $where[] = "w.year >= :minYear";
   $where[] = "w.year <=:maxYear";
   $where[] = "inv.sum_on_hand >= :numInStock";
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
   
   try {
   // create database statment based on inputs available   
   $stmt = $db->prepare("SELECT w.wine_name, w.year, wy.winery_name, r.region_name, gv.grape_blend, inv.avg_cost, inv.sum_on_hand, ord.ordered, ord.salesRev, w.wine_id
                         FROM wine w
                         JOIN winery wy ON wy.winery_id = w.winery_id 
                         JOIN region r ON r.region_id = wy.region_id 
                         JOIN (SELECT wv.wine_id, GROUP_CONCAT(gv.variety SEPARATOR ' ') AS grape_blend 
                               FROM wine_variety wv 
                               JOIN grape_variety gv ON gv.variety_id = wv.variety_id 
                               GROUP BY wv.wine_id 
                               ORDER BY wv.wine_id, wv.id DESC) AS gv ON gv.wine_id = w.wine_id 
                         JOIN (SELECT wine_id, ROUND(SUM(on_hand*cost)/SUM(on_hand),2) AS avg_cost, SUM(on_hand) AS sum_on_hand
                               FROM inventory
                               GROUP BY wine_id) AS inv ON inv.wine_id = w.wine_id
                         JOIN (SELECT wine_id, SUM(qty) AS ordered, sum(price) AS salesRev
                               FROM items
                               GROUP BY wine_id) AS ord ON ord.wine_id = w.wine_id
                         WHERE " . implode(' AND ', $where) . "
                         ORDER BY w.wine_name, gv.grape_blend, w.year");

   // bind standard parameters
   $stmt->bindParam(':minYear', $minYear, PDO::PARAM_INT);
   $stmt->bindParam(':maxYear', $maxYear, PDO::PARAM_INT);
   $stmt->bindParam(':numInStock', $minWinesInStock, PDO::PARAM_INT);
   $stmt->bindParam(':numOrdered', $minWinesOrdered, PDO::PARAM_INT);

   // bind optional parameters if required
   if (!empty($_GET['wineName']))
      $stmt->bindParam(':wineName', $wineName, PDO::PARAM_STR);

   if (!empty($_GET['wineryName']))
      $stmt->bindParam(':wineryName', $wineryName, PDO::PARAM_STR);
   
   if (!empty($_GET['region']) && $region != 'All')
      $stmt->bindParam(':region', $_GET['region'], PDO::PARAM_STR);

   if (!empty($_GET['grapeVariety']) && $grapeVariety != 'All')
      $stmt->bindParam(':grapeVariety', $_GET['grapeVariety'], PDO::PARAM_STR);
   
   if (!empty($_GET['minCost']))
      $stmt->bindParam(':minCost', $minCost, PDO::PARAM_STR);

   if (!empty($_GET['maxCost']))
      $stmt->bindParam(':maxCost', $maxCost, PDO::PARAM_STR);
   
   
      $stmt->execute();
   } catch (Exception $e) {
      $_SESSION['errorMsg'] = $e->getMessage();
      header("Location: error.php");
      die(); 
   }
   
    // save results to session variable
    $_SESSION['results'] = $stmt->fetchAll();

   // close database connection
   $db = null;

   // redirect to results page
   $resultsPage = 'results.php';
   header("Location: $resultsPage");
?>

