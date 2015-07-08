<?php
   // start session
   session_start();

   // add required files
   require_once 'php/header.php';

   // save results to session variable
   // $_SESSION['results'] = $stmt->fetchAll();
?>
<div id="main-content" class="wrapper">
   <h1>Search Results</h1>
   <?php
      echo '<p>Number of wines found: ' . count($_SESSION['results']) . '</p>';

      // check if results are not empty. display error if session variable does not exists
      if (!empty($_SESSION['results'])) {
         echo '<table><tr>';
         echo '<th id="tbl-wine-name">Wine Name</th>';
         echo '<th id="tbl-wine-type">Wine Type</th>';
         echo '<th id="tbl-year">Year</th>';
         echo '<th id="tbl-winery-name">Winery Name</th>';
         echo '<th id="tbl-region">region</th>';
         echo '<th id="tbl-grape-variety">Grape Variety</th>';
         echo '<th id="tbl-cost">Cost</th>';
         echo '<th id="tbl-in-stock">In Stock</th>';
         echo '<th id="tbl-ordered">Ord</th>';
         echo '<th id="tbl-sales-revinue">Sales Revinue</th>';
         echo '</tr>';

         foreach ($_SESSION['results'] as $row) {
            echo '<tr><td>' . $row['wine_name'] . '</td>';
            echo '<td>' . $row['wine_type'] . '</td>';
            echo '<td>' . $row['year'] . '</td>';
            echo '<td>' . $row['winery_name'] . '</td>';
            echo '<td>' . $row['region_name'] . '</td>';
            echo '<td>' . $row['grape_blend'] . '</td>';
            echo '<td>' . $row['cost'] . '</td>';
            echo '<td>' . $row['on_hand'] . '</td>';
            echo '<td>' . $row['ordered'] . '</td>';
            echo '<td>' . $row['salesRev'] . '</td>';
            echo '</tr>';
         }

         echo '</table>';
      } else {
         // display error that no data is returned
         echo 'no data to display';
      }
   ?>
</div>
<?php
   require_once 'php/footer.php';
?>
