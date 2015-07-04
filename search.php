<?php 
   require_once 'php/header.php';
   require_once 'php/connect.php';

   // create database connection
   $db = db_connect();
?>

<!-- form  for user search criteria -->
<form name="searchForm" action="answer.php" method="get">
   <label for="wineName">Wine Name:</label>
   <input type="text" id="wineName" name="wineName"><br>
   
   <label for="wineryName">Winery Name:</label>
   <input type="text" id="wineryName" name="wineryName"><br>

   <label for="region">Region:</label>
   <select id="region" name="region">
      <option value="">Please select</option>
      <?php
         // get regions from database connection
         $stmt = $db->prepare('SELECT region_name FROM region');
         if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
               echo '<option value="' . $row['region_name'] . '">' . $row['region_name'] . '</option>';
            }
         }
      ?>
   </select><br>

   <label for="grapeVariety">Grape Variety:</label>
   <select id="grapeVariety" name="grapeVariety">
      <option value="">Please Select</option>
      <option value="All">All</option>
      <?php
         // get grape varieties from database connection
         $stmt = $db->prepare('SELECT variety FROM grape_variety');
         if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
               echo '<option value="' . $row['variety'] . '">' . $row['variety'] . '</option>';
            }
         }
      ?>
   </select><br>

   <label for="years">Years:</label>
   <?php 
      $stmt = $db->prepare('SELECT MIN(year) AS minYear, MAX(year) AS maxYear FROM wine');
      if ($stmt->execute()) {
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
         $minYear = $row['minYear'];
         $maxYear = $row['maxYear'];

         echo '<label for="minYear">Min Year: </label>';
         echo '<input type="number" id ="minYear" name="minYear" value="' . $minYear . '" min="' . $minYear . '" max="' . $maxYear . '">';
         echo '<label for="maxYear">Max Year: </label>';
         echo '<input type="number" id ="maxYear" name="maxYear" value="' . $maxYear . '" min="' . $minYear . '" max="' . $maxYear . '">';
      }
   ?><br>

   <label for="minWinesInStock">Minimum No. Wines In Stock(per wine):</label>
   <input type="number" id="minWinesInStock" name="minWinesInStock" value="0" min="0"><br>

   <label for="minWinesOrdered">Minumum No. Wines Orders(per wine):</label>
   <input type="number" id="minWinesOrdered" name="minWinesOrdered" value="0" min="0"><br>

   <label for="minCost">Min Cost:</label>
   <input type="text" id="minCost" name="minCost">
   <label for="maxCost"> Max Cost:</label>
   <input type="text" id="maxCost" name="maxCost"><br>

   <input type="submit" value="Search" />
</form>

<?php 
   // close database connection
   $db = null;

   // generate footer
   require_once 'php/footer.php';
?>
