<?php
//搜尋
require("../PHP/connectDB.php");
require("../PHP/common.php");

if (!(isset($_SESSION))) {
    session_start();
    $userID =@$_SESSION['id'];
}
//問哪種資料
$dataType = @$_POST["dataType"];

if ($dataType == "like-user") {
    $input = mysqli_real_escape_string($conn, $_POST["input"]);

    //防止清空時取得全部用戶
    if (empty($input)) {
        die("");
    }
    $sql = "SELECT
        ID,name,photo
    FROM
        member
    WHERE
        name like '%" . $input . "%'
    ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }

    echo json_encode($out);
}
//商品搜尋
else if ($dataType == "product-search") {
    $limit_start = mysqli_real_escape_string($conn, @$_POST["limit_start"]);
    $limit_n = mysqli_real_escape_string($conn, @$_POST["limit_n"]);

    $input = mysqli_real_escape_string($conn, @$_POST["input"]);

    $order_by = mysqli_real_escape_string($conn, @$_POST["order_by"]);
    if (!empty($order_by)) {
        if ($order_by == "newest") {
            $order_by = "uploadDate DESC";
        } elseif ($order_by == "cheapest") {
            $order_by = "price ASC";
        } elseif ($order_by == "hotest") {
            $order_by = " (SELECT COUNT(productID) FROM orderrecord WHERE productID=id GROUP BY productID ) DESC ";
        }
    } else {
        $order_by = "uploadDate DESC";
    }

    $sql = "SELECT
            p.ID as ID,
            p.sellerID as sellerID,
            p.price as price,
            p.info as info,
            p.title as title,
            pt.photo
        FROM
            product as p,
            productthumbnail as pt,
            productregion as pr

        WHERE
            p.ID=pt.productID AND
            p.ID=pt.productID AND
            pr.productID =p.ID AND
            p.state='published' AND
            (
                p.title LIKE '%" . $input . "%' OR
                p.info LIKE '%" . $input . "%' OR
                pr.county LIKE '%" . $input . "%'
            ) 
        Group BY p.ID
        ORDER BY
            $order_by
        LIMIT
            $limit_start,$limit_n
    ";
    $result = mysqli_query($conn, $sql)or die(mysqli_error($conn));
    $out = array();
    //echo $sql;
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode($out);
}
