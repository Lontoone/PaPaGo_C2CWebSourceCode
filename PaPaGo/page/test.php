<?php
echo "HI";

require_once("../PHP/connectDB.php");


$sql = "SELECT * FROM member 
    WHERE Email = '123'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
echo $result->num_rows;
