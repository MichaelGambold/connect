<?php require_once 'php/header.php'; ?>
<!-- open databae connection -->


<!-- form  for user search criteria -->
<form name="searchForm" action="answer.php" method="get">
   <label for="wineName">Wine Name:</label>
   <input type="text" id="wineName" name="wineName"><br>
   
   <label for="wineryName">Winery Name:</label>
   <input type="text" id="wineryName" name="wineryName"><br>

   <label for="region">Region:</label>
   <?php  echo '<select id="region" name="region"></select>'; ?><br>

   <label for="grapeVariety">Grape Variety:</label>
   <?php echo '<select id="grapeVariety" name="grapeVariety"></select>'; ?><br>

   <label for="years">Years:</label>
   <?php echo 'TODO: YEAR RANGE, inc ID & NAME'; ?><br>

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

<!-- close database connection here -->

<?php require_once 'php/footer.php'; ?>
