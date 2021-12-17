<?php
session_start();
//("localhost","my_user","my_password","my_database")
$conn = mysqli_connect('localhost:8889', 'root', 'root', 'ecommunity');

if(!$conn){
  //kills the connection, then tells us and tells us exactly what happened
die( "Database Connection Error: " . mysqli_connect_error());
}
