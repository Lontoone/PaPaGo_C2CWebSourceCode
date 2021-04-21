<?php
$serverName = "localhost";
$username = "root";
$password = "才不告訴你wwww";
$dbname = "papago";
$conn = mysqli_connect($serverName, $username, $password, $dbname) or die(mysqli_connect_error($conn));

mysqli_query($conn, 'SET NAMES utf8');
?>
