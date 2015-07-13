<?php require_once 'php/header.php'; ?>
<div id="main-content" class="wrapper">
   <h1>An Errror Occured</h1>
   <p>We're sorry but an error occured. Please try again.</p>
   <p>If this continues please contact us</p>
   <br><p>The error that occured was: 
   <?php echo $_SESSION['errorMsg']; ?>
   </p>
</div>
<? require_once 'php/footer.php'; ?>
