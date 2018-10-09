<?php
    session_start();

   if($_SESSION['email']){
       echo "You are login" ;
   } else {
       header("location: connect.php");
   }
?>