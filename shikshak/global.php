<?php 
$servername = "localhost";
$username = "iitd-market";
$password = "BP2Q7DteACHuSu5j";
$dbname = "teacher transfer";
$site=$_SERVER["SERVER_NAME"];
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

