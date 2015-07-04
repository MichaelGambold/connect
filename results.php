<?php
   // start session
   session_start();

   // add required files
   require_once 'php/header.php';

   // save results to session variable
   // $_SESSION['results'] = $stmt->fetchAll();
?>
<h1>Search Results</h1>
<?php
   // check if results are not empty. display error if session variable does not exists
   if (!empty($_SESSION['results'])) {
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
 
      
      echo count($_SESSION['results']);
      //print_r($_SESSION['results'][0]['wine_name']);
      //while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
         echo '</tr>';
      }

      echo '</table>';
   } else {
      // display error that no data is returned
      echo 'no data to display';
   }

   require_once 'php/footer.php';
?>
