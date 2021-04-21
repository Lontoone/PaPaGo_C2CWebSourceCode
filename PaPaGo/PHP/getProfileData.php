<?php
//負責回傳profile資料
require("../PHP/connectDB.php");
require("../PHP/common.php");

if (!(isset($_SESSION))) {
    session_start();
    if(!isset( $_SESSION['id'])){
        die("die");
    }
}

//問哪種資料
$dataType = @$_POST["dataType"];

$userID = $_SESSION['id'];

//賣家商品總覽
if ($dataType == "allProduct") {
    $tableName = 'product';
    //$userID_post = @$_POST['userID'];


    //若無userID:錯誤:
    if ($userID == "") {
        die(mysqli_error($conn, "錯誤"));
    }
    $rows = array("ID", "title", "days", "price", "state", "uploadDate");
    echo getData_inRow($tableName, $rows, "sellerID", $userID, "|", "@");
}

//訂單資料:
else if ($dataType == "orderRecord") {
    $tableName = 'orderrecord';

    //$sellerID_post = mysqli_real_escape_string($conn, $_POST['sellerID']);
    //$sellerID_post = $_SESSION['id'];
    //$productID_post = mysqli_real_escape_string($conn, @$_POST['productID']);

    $sql = "SELECT 
        B.ID as productID,
        B.title,
        C.name as buyerName,
        C.ID as buyerID,
        A.recordNO,
        A.people,
        A.startDate,
        A.endDate,
        A.price,
        A.state,
        D.photo
    FROM 
    orderrecord as A,
    product as B,
    member as C,
    productthumbnail as D
    Where 
        A.sellerID='" . $userID . "' AND 
        A.productID=B.ID AND
        A.buyerID=C.ID AND
        D.productID=A.productID AND
        (A.state='1' OR A.state='2')
        ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $out_json[] = $row;
        }
        echo json_encode($out_json);
    } else {
    }
}

//購物車:
else if ($dataType == "cart") {
    $tableName = 'orderrecord';

    $sql = "SELECT 
        B.ID as productID,
        B.title,
        C.name as sellerName,
        C.ID as sellerID,
        A.recordNO,
        A.people,
        A.startDate,
        A.endDate,
        A.price,
        A.state,
        D.photo
    FROM 
    orderrecord as A,
    product as B,
    member as C,
    productthumbnail as D
    Where 
        A.buyerID='" . $userID . "' AND 
        A.productID=B.ID AND
        A.sellerID=C.ID AND
        D.productID=A.productID AND
        A.state<2
        ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $out_json[] = $row;
        }
        echo json_encode($out_json);
    } else {
    }
}
//購物車/訂單數量(等待接受中數量)
else if ($dataType == "ordercount") {
    //購物車數量
    $sql = "SELECT count(productID) as cart_count
        FROM orderrecord
        WHERE buyerID='" . $userID . "' AND state='0'; ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $out_json = array();
    $out_json[] =  $result->fetch_assoc();

    //訂單數量
    $sql = "SELECT count(productID) as order_count
        FROM orderrecord
        WHERE sellerID='" . $userID . "' AND state='1'; ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json[] =  $result->fetch_assoc();


    echo json_encode($out_json);
    //echo $result->fetch_array()[0];

}


//[遊客]已完成行程:
else if ($dataType == "doneProduct") {
    $sql = "SELECT 
        B.ID as productID,
        B.title,
        C.name as sellerName,
        C.ID as sellerID,
        A.recordNO,
        A.people,
        A.startDate,
        A.endDate,
        A.price,
        A.state,
        D.photo
    FROM 
    orderrecord as A,
    product as B,
    member as C,
    productthumbnail as D
    Where 
        A.buyerID='" . $userID . "' AND 
        A.productID=B.ID AND
        A.sellerID=C.ID AND
        D.productID=A.productID AND
        A.state >1 AND
        A.endDate <= NOW();
        ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $out_json[] = $row;
        }
        echo json_encode($out_json);
    } else {
    }
}

//[嚮導]已完成行程:
else if ($dataType == "doneOrder") {
    $sql = "SELECT 
        B.ID as productID,
        B.title,
        C.name as buyerName,
        C.ID as buyerID,
        A.recordNO,
        A.people,
        A.startDate,
        A.endDate,
        A.price,
        A.state,
        D.photo,
        pv.rate,
        pv.comment,
        pv.Reply,
        pv.Reply_rate
    FROM 
    orderrecord as A,
    product as B,
    member as C,
    productthumbnail as D,
    productreview as pv
    Where 
        A.sellerID='" . $userID . "' AND 
        A.productID=B.ID AND
        A.sellerID=C.ID AND
        D.productID=A.productID AND
        pv.recordNO=A.recordNO AND
        A.state >=3 AND
        A.endDate <= NOW();
        ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $out_json[] = $row;
        }
        echo json_encode($out_json);
    } else {
    }
}
//收藏資料
else if ($dataType == "collect") {
    //user-產品ID的資料
    //$datas = explode("|", getData($tableName, "productID", "userID", $userID, "|"));
    //for (@$i = 0; $i < count($datas)-1; $i++) {}

    $sql = "SELECT 
        p.ID as pid,
        p.sellerID as sellerID,
        p.price,
        p.info,
        p.title,
        pt.photo
    FROM
        collect AS c,
        product AS p,
        productthumbnail AS pt
    WHERE
        c.userID='" . $userID . "' AND
        c.productID=p.ID AND
        pt.productID=p.ID
    GROUP BY
        p.ID
    ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $out_json[] = $row;
        }
        echo json_encode($out_json);
    } else {
    }
}
//遊客:進行中行程
else if ($dataType == "currentTrips") {
    $month_post = mysqli_real_escape_string($conn, $_POST["month_post"]);
    $sql = "SELECT 
        o.recordNO as recordNO,
        p.ID as pid,
        p.sellerID as sellerID,
        p.price,
        p.people,
        p.title,
        o.startDate,
        o.endDate,
        pt.photo
    FROM
        orderrecord AS o,
        product AS p,
        productthumbnail AS pt
    WHERE
        o.buyerID='" . $userID . "' AND
        o.productID=p.ID AND
        (o.startDate LIKE '%-%" . $month_post . "-%' OR o.endDate LIKE '%-%" . $month_post . "-%') AND
        o.state='2' AND
        pt.productID=p.ID
    GROUP BY
        p.ID
    ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $out_json[] = $row;
        }
        echo json_encode($out_json);
    } else {
    }
}
//導遊:進行中行程
else if ($dataType == "currentOrders") {
    $month_post = mysqli_real_escape_string($conn, $_POST["month_post"]);
    $sql = "SELECT 
        o.recordNO as recordNO,
        p.ID as pid,
        o.buyerID as buyerID,
        p.price,
        p.people,
        p.title,
        o.startDate,
        o.endDate,
        pt.photo
    FROM
        orderrecord AS o,
        product AS p,
        productthumbnail AS pt
    WHERE
        o.sellerID='" . $userID . "' AND
        o.productID=p.ID AND
        (o.startDate LIKE '%-%" . $month_post . "-%' OR o.endDate LIKE '%-%" . $month_post . "-%') AND
        o.state='2' AND
        pt.productID=p.ID
    GROUP BY
        p.ID
    ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $out_json[] = $row;
        }
        echo json_encode($out_json);
    } else {
    }
}

