<?php

//商品狀態代號:
$o_state_done=3; //商品完成
$o_state_reply_done=4; //賣家回復完成

//用ajax呼叫功能:
$func_name = @$_POST['func_name'];

$tb_name_post = @$_POST['tb_name_post'];
$row_name_post = @$_POST['row_name_post'];
$where_post = @$_POST['where_post'];
$key_post = @$_POST['key_post'];

//$limit_start=@$_POST["limit_start"];
//$limit_n=@$_POST["limit_n"];
//$limit_start=mysqli_real_escape_string($conn,$_POST["limit_start"]);
//$limit_n=mysqli_real_escape_string($conn,$_POST["limit_n"]);


//$order_by_post = @$_POST['order_by_post'];

//func:取得資料:
if ($func_name == "getData") {
    $split_by_post = @$_POST['split_by_post'];
    if (empty($order_by_post)) {
        getData($tb_name_post, $row_name_post, $where_post, $key_post, $split_by_post);
    } else {
        getData($tb_name_post, $row_name_post, $where_post, $key_post, $split_by_post, $order_by_post);
    }
} else if ($func_name == "getData_inRow") {
    $split_data_by_post = @$_POST['split_data_by_post'];
    $split_row_by_post = @$_POST['split_row_by_post'];
    getData_inRow($tb_name_post, $row_name_post, $where_post, $key_post, $split_data_by_post, $split_row_by_post);
} else if ($func_name == "getData_inRow_json") {
    getData_inRow_json($tb_name_post, $row_name_post, $where_post, $key_post);
} else if ($func_name == "countData") {
    countData($tb_name_post, $row_name_post, $where_post, $key_post);
} else if ($func_name == "updateData") {
    $val_post = @$_POST['val_post'];
    updateData($tb_name_post, $row_name_post, $val_post, $where_post, $key_post);
} else if ($func_name == "updateData_autoInsert") {
    $val_post = @$_POST['val_post'];
    updateData_autoInsert($tb_name_post, $row_name_post, $val_post, $where_post, $key_post);
} else if ($func_name == "removeData") {
    removeData($tb_name_post, $where_post, $key_post);
}

else if($func_name=="logout"){
    session_start();
    session_unset();
    session_destroy();
    header('Location:../');
}


//常用變數/路徑:
$userAva_FolderPath = $_SERVER['DOCUMENT_ROOT'] . "/papago/upload/avatars/";
$product_img_FolderPath = $_SERVER['DOCUMENT_ROOT'] . "/papago/upload/productIMG/";
$product_thumb_FolderPath = $_SERVER['DOCUMENT_ROOT'] . "/papago/upload/productThumbIMG/";

//常用函示庫
function getUniqID($prfix)
{
    return uniqid($prfix);
}

/**
 * 上傳圖片 最大2MB
 * 注意input的name屬性名稱一定要加[]，不然會出錯
 * @param string[] $filePOST 集體照片POST
 * @param string $uploadFolder 照片存放位置
 * @param string $picName_prefix 檔案開頭 格式 [$picName_prefix]_[$counter].png;
 * @param string $updateTable 上傳後，更新連接該照片的資料表名稱
 * @param string $updateRow 上傳後，更新連接該照片的表格欄位
 * @param string $updateWhere 用來搜尋更新資料列的欄位名，通常是ID
 * @param string $updateKey 用來搜尋更新資料列的比較資料，例如:$_SESSION['id']
 * @param bool $checkOverWrite 是否用圖片檔名檢查覆寫? 
 */
function uploadImage($filePOST, $uploadFolder, $picName_prefix, $updateTable, $updateRow, $updateWhere, $updateKey, $checkOverWrite)
{
    //上傳圖片
    require("../php/connectDB.php");
    if (!isset($_SESSION)) {
        session_start();
    }

    mysqli_query($conn, 'SET NAMES utf8');

    $errors = array();
    $uploadedFiles = array();
    $extension = array("jpeg", "jpg", "png", "gif");
    $bytes = 1024;
    $KB = 2048;
    $totalBytes = $bytes * $KB; //2MB
    $UploadFolder = $uploadFolder;

    $counter = 0;
    foreach ($_FILES[$filePOST]["tmp_name"] as $key => $tmp_name) {
        $temp = $_FILES[$filePOST]["tmp_name"][$key];
        $name = $_FILES[$filePOST]["name"][$key];

        if (empty($temp)) {
            break;
        }

        $counter++;
        $UploadOk = true;

        if ($_FILES[$filePOST]["size"][$key] > $totalBytes) {
            $UploadOk = false;
            array_push($errors, $name . " 檔案大小超過2MB");
        }

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        if (in_array($ext, $extension) == false) {
            $UploadOk = false;
            array_push($errors, $name . " 請上傳圖檔.");
        }
        /*直接覆蓋
        if(file_exists($UploadFolder."/".$name) == true){
            $UploadOk = false;
            array_push($errors, $name." file is already exist.");
         }*/

        //TODO [重要]:刪除原先的檔案

        if ($UploadOk == true) {
            $new_pic_name = $picName_prefix . "_" .uniqid(). $counter . "." . $ext;
            $upload_fullPath = $UploadFolder . $new_pic_name;

            move_uploaded_file($temp, $upload_fullPath);
            echo $upload_fullPath;

            //檢查先前有無資料 (用照片檔名找) //TODO:編輯商品時檢查刪除的照片數量?//DEBUG??
            var_dump(checkHasData($updateTable, $updateRow, $updateRow, $new_pic_name));
            if (!$checkOverWrite || checkHasData($updateTable, $updateRow, $updateRow, $new_pic_name)) {
                //有=>更新路徑資料
                updateData($updateTable, $updateRow, $new_pic_name, $updateWhere, $updateKey);
            } else {
                //無=>新增路徑資料紀錄 (包括自動新曾一列)
                $d = array(
                    new Insert_DATA($updateWhere, $updateKey),
                    new Insert_DATA($updateRow, $new_pic_name),
                );
                Do_Insert($updateTable, $d);
            }


            //DEBUG用:
            array_push($uploadedFiles, $name);
        }
    }
    //echo "<script>location.href='" . $_SERVER['HTTP_REFERER'] . "'</script>";

    //DEBUG 時使用:

    if ($counter > 0) {
        if (count($errors) > 0) {
            echo "<b>Errors:</b>";
            echo "<br/><ul>";
            foreach ($errors as $error) {
                echo "<li>" . $error . "</li>";
            }
            echo "</ul><br/>";
        }

        if (count($uploadedFiles) > 0) {
            echo "<b>Uploaded Files:</b>";
            echo "<br/><ul>";
            foreach ($uploadedFiles as $fileName) {
                echo "<li>" . $fileName . "</li>";
            }
            echo "</ul><br/>";

            echo count($uploadedFiles) . " file(s) are successfully uploaded.";
        }
    } else {
        echo "Please, Select file(s) to upload.";
    }
}

function updateData($tableName, $rowname, $val, $_where, $_key)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    }
    mysqli_query($conn, 'SET NAMES utf8');

    $sql = "UPDATE $tableName
    SET 
        $rowname='" . $val . "'
    WHERE $_where='" . $_key . "'";

    //TODO:更完善?
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}
//更新陣列內的資料
function updateData_inrow($tableName, $Insert_DATAs, $_where, $_key)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };
    /*
    $sql = "
    UPDATE $tableName 
    SET
        row='val',
        row1='val2'
    Where ...
    ";*/
    $sql = "UPDATE $tableName SET ";

    for ($i = 0; $i < count($Insert_DATAs); $i++) {

        $sql = $sql . "" . $Insert_DATAs[$i]->rowname . "=" .
            "'" . $Insert_DATAs[$i]->val . "'";

        if ($i != count($Insert_DATAs) - 1) {
            $sql = $sql . ",";
        }
    }
    $sql = $sql . "WHERE " . $_where . "=" . "'" . $_key . "'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    return $sql;
}

function updateData_autoInsert($tableName, $rowname, $val, $_where, $_key)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    }
    mysqli_query($conn, 'SET NAMES utf8');

    if (checkHasData($tableName, $rowname, $_where, $_key)) {
        $sql = "UPDATE $tableName
        SET 
            $rowname='" . $val . "'
        WHERE $_where='" . $_key . "'";
    } else {
        $sql = "INSERT INTO $tableName($rowname,$_where)
            VALUES('" . $_key . "','" . $val . "')";
    }

    //TODO:更完善?
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

class Insert_DATA
{
    public $rowname;
    public $val;
    public function __construct($_rowname, $_val)
    {
        $this->rowname = $_rowname;
        $this->val = $_val;
    }
}

function Do_Insert($tableName, $datas)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };
    /*
    $sql = "INSERT INTO $tableName ('$rowname') 
    VALUES('$val')";*/
    $sql = "INSERT INTO $tableName (";
    for ($i = 0; $i < count($datas); $i++) {
        $sql = $sql . "" . $datas[$i]->rowname . "";
        if ($i != count($datas) - 1) {
            $sql = $sql . ",";
        }
    }
    $sql = $sql . ") VALUES (";
    for ($i = 0; $i < count($datas); $i++) {
        $sql = $sql . "'" . $datas[$i]->val . "'";
        if ($i != count($datas) - 1) {
            $sql = $sql . ",";
        }
    }
    $sql = $sql . ")";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    return $sql;
}

function getData($tableName, $rowName, $_where, $_key, $split_by, $order_by = null)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };
    mysqli_query($conn, 'SET NAMES utf8');

    $sql = "SELECT $rowName FROM $tableName
        WHERE $_where='" . $_key . "'";

    //排序
    if (isset($order_by)) {
        $sql = $sql . " ORDER BY " . @$_POST['order_by_post'];
    }
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if ($result->num_rows > 0) {
        $return_result = "";
        while ($row = $result->fetch_assoc()) {
            echo $row[$rowName] . $split_by;
            $return_result = $return_result . $row[$rowName] . $split_by;
            //return true; 只會回傳1個
        }
        return $return_result;
    } else {
        return false;
    }
}

function checkHasData($tableName, $rowName, $_where, $_key)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };
    //mysqli_query($conn, 'SET NAMES utf8');


    $sql = "SELECT $rowName FROM $tableName
        WHERE $_where='" . $_key . "'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}


//取得所有指定欄位的值，並用符號分隔
function getData_inRow($tbname, $rowsname, $_where, $_key, $split_data_by, $split_row_by, $order_by = null, $doEcho = null)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };

    $limit_start=mysqli_real_escape_string($conn,@$_POST["limit_start"]);
    $limit_n=mysqli_real_escape_string($conn,@$_POST["limit_n"]);

    $sql = "SELECT ";
    for ($i = 0; $i < count($rowsname); $i++) {
        $sql = $sql . "" . $rowsname[$i] . "";
        if ($i != count($rowsname) - 1) {
            $sql = $sql . ",";
        }
    }
    $sql = $sql . " FROM  $tbname WHERE ";
    $sql = $sql .  $_where . "=" . "'" . $_key . "'";

    //排序
    if (isset($_POST['order_by_post'])) {
        $sql = $sql . " ORDER BY " . $_POST['order_by_post'];
    }
    
    if(isset($limit_start) && $limit_start!=""){
        $sql=$sql." LIMIT ".$limit_start ." , ".$limit_n;
    }

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $_out = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $r = "";
        for ($i = 0; $i < count($rowsname); $i++) {
            $r = $r . $row[$rowsname[$i]];
            if ($i != count($rowsname) - 1) {
                $r = $r . $split_data_by;
            }
        }
        $_out = $_out . $r . $split_row_by;
    }
    if (isset($_POST[@"doEcho"])) {
        echo $_out;
    }
    return $_out;
}

function getData_inRow_json($tbname, $rowsname, $_where, $_key)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };

    $sql = "SELECT ";
    for ($i = 0; $i < count($rowsname); $i++) {
        $sql = $sql . "" . $rowsname[$i] . "";
        if ($i != count($rowsname) - 1) {
            $sql = $sql . ",";
        }
    }
    $sql = $sql . " FROM  $tbname WHERE ";
    $sql = $sql .  $_where . "=" . "'" . $_key . "'";

    //排序
    if (isset($order_by)) {
        $sql = $sql . " ORDER BY " . $_POST['order_by_post'];
    }

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $out_json = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $out_json[] = $row;
    }

    if (isset($_POST[@"doEcho"])) {
        echo json_encode($out_json);
    }
    //var_dump($out_json);
    return $out_json;
}



function countData($tableName, $rowName, $_where, $_key)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };
    mysqli_query($conn, 'SET NAMES utf8');

    $sql = "SELECT $rowName FROM $tableName ";
    if(isset($_where) && $_where!=""){
        $sql=$sql." WHERE $_where='" . $_key . "'";
    }

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    echo ($result->num_rows);
    return $result->num_rows;

    if (isset($_POST[@"doEcho"])) {
        echo (string) 0;
    }
}

function removeData($tableName, $_where, $_key)
{
    require("../PHP/connectDB.php");
    if (!(isset($_SESSION))) {
        session_start();
    };
    mysqli_query($conn, 'SET NAMES utf8');

    $sql = "DELETE FROM $tableName
        WHERE $_where='" . $_key . "'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    //echo $result." ".$sql;
}
