<?php  
 
 
$servername = "localhost"; 
$username = "codmshopbd"; // Enter your user name 
$dbname = "codmshopbd"; // Enter your database name 
$password = "Vyuq6M0LKQIYacqnW3ZX"; // Enter your password 




 $con = mysqli_connect($servername, $username, $password, $dbname );    $conn = mysqli_connect($servername, $username, $password, $dbname );       $coon = mysqli_connect($servername, $username, $password, $dbname );
 

 
 if (mysqli_connect_errno()){  
   echo "Connection Faild <br> " . mysqli_connect_error();  
 } else {  
 // echo "Database Connected";  
 }  
 ?>  