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
      $query = "SELECT * FROM customer";
      $result = mysql_query($query);

      # check if query returned is blank and display error
      if (!$result) {
         $message = 'Invalid query: ' . mysql_error() . "\n";
         $message .= 'Whole query: ' . $query;
         die($message);
      }
      else {
         echo '<p>data returned</p>';
      }

      # output result
      while ($row = mysql_fetch_assoc($result)) {
         echo '<tr>';
         echo '<td>' . $row['cust_id'] . '</td>';
         echo '<td>' . $row['surname'] . '</td>';
         echo '<td>' . $row['firstname'] . '</td>';
         echo '<td>' . $row['initial'] . '</td>';
         echo '<td>' . $row['title_id'] . '</td>';
         echo '<td>' . $row['address'] . '</td>';
         echo '</tr>';
      }

      # free resources associated with the result set
      # close database connection
      mysql_free_result($result);
      mysql_close($dbconn);
   ?>
   </table>

<? require_once 'php/footer.php'; ?>
