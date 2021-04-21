<?php
session_start();

//若存在，直接使用cookie
if (isset($_COOKIE['id'])) {
    $_SESSION['id'] = $_COOKIE['id'];
} elseif (isset($_SESSION['id'])) {
    $_COOKIE['id'] = $_SESSION['id'];
}


//取的指定欄位的值
function GetUserData($rowName)
{
    $tablename = "member";
    require("../PHP/connectDB.php");
    mysqli_query($conn, 'SET NAMES utf8');

    $sql = "SELECT ID,$rowName FROM $tablename WHERE
            ID='" . $_SESSION['id'] . "'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo $row[$rowName];
        }
    }
}

//回傳使用者大頭照，若無檔案則回傳預設
/*
function GetUserAvatar()
{
    $photo = "../upload/avatars/".GetUserData("photo");/*
    $photoLink = "../upload/avatars/" . $photo;
    if ($photo != "" && file_exists($photoLink)) {
        echo $photoLink;
    } else {
        echo "../img/example/headshot.jpg";
    }
}*/
