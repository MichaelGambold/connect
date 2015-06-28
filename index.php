<?php require_once 'php/header.php'; ?>
<h1>Test output for database</h1>
<br>
<table>
   <tr>
      <th>Customer ID</th>
      <th>Surname</th>
      <th>Firstname</th>
      <th>Initial</th>
      <th>Title ID</th>
      <th>Address</th>
   </tr>
   
   <?php
      # use connect.php to connect to database
      require_once('php/connect.php');

      # generate query and query database connection
      $query = $dbh->prepare("SELECT * FROM customer");

      # output result
      if ($query->execute(array())) {
         while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $row['cust_id'] . '</td>';
            echo '<td>' . $row['surname'] . '</td>';
            echo '<td>' . $row['firstname'] . '</td>';
            echo '<td>' . $row['initial'] . '</td>';
            echo '<td>' . $row['title_id'] . '</td>';
            echo '<td>' . $row['address'] . '</td>';
            echo '</tr>';
         }
      }

      # close database connection
      $dbh = null;
   ?>
   </table>

<? require_once 'php/footer.php'; ?>
