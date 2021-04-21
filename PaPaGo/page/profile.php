<!DOCTYPE html>
<html>

<head>
    <meta charset='UTF-8'>
    <title>個人頁面</title>
    <link rel="shortcut icon" href="../img/icon/logo1.png" type="image/png">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Noto+Sans+TC&display=swap&subset=chinese-traditional' rel='stylesheet'>

    <script src='http://code.jquery.com/jquery-1.11.3.min.js'></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="../js/common.js"></script>

    <link rel='stylesheet' href='../css/reset.css'>
    <link rel='stylesheet' href='../css/common.css'>
    <link rel='stylesheet' href='../css/main.css'>
    <link rel='stylesheet' href='../css/boxUnit.css'>

    <link rel='stylesheet' href='../css/productView.css'>
    <link rel='stylesheet' href='../css/profile.css'>
    <link rel='stylesheet' href='../css/forForm.css'>
</head>

<body>
    <?php require("../PHP/autoLogin.php"); 
    if(!isset($_SESSION["id"])){
        echo "<script>alert('請先登入')</script>";
        echo "<script>location.href='../index.php'</script>";
    }
    ?>

    <!--HEADER-->
    <div class='header'>
        <div class='logo putCenter'><a href="../">PAPAGO</a></div>
        <!--SEARCH BAR-->
        <form class='putCenter'>
          
        </form>
        <!--大頭貼 或 '登入|註冊'按鈕-->
        <div class='putCenter'>

            <?php if (!@$_SESSION['id']) { ?>
                <div class="sign-log-in">
                    <a href="../page/login.html">登入</a>
                    <a href="../page/singup.html">註冊</a>
                </div>

            <?php } else { ?>
                <a href='../page/profile.php?id=<?php GetUserData("ID") ?>'>
                    <img class='user-headShot' src='../upload/avatars/<?php GetUserData("photo") ?>'>
                </a>
            <?php } ?>

        </div>
    </div>
    <!--主要內容-->
    <div class='mainContent putCenter'>
        <!--左欄選單-->
        <div class='selection'>
            <p class='title-big'>我的帳戶</p>
            <div class='expandSelction hidden'>
                <p class='title-small' id='accData-btn'>基本資料</p>
                <p class='title-small' id='logout-btn'>登出</p>
            </div>

            <p class='title-big'>我是嚮導</p>
            <div class='expandSelction hidden'>
                <p class='title-small' id='allProduct-btn'>商品行程總覽</p>
                <p class='title-small' id='makeNewProduct-btn'>發布新行程</p>
                <p class='title-small' id='currentOrders-btn'>進行中行程</p>
                <p class='title-small' id="orderRecord-btn">訂單
                    <span>(</span>
                    <span id="order-count">0</span>
                    <span>)</span>
                </p>
                <p class='title-small' id="doneOrder-btn">已完成的行程</p>
            </div>

            <p class='title-big'>我是遊客</p>
            <div class='expandSelction hidden'>
                <p class='title-small' id="cart-btn">購物車
                    <span>(</span>
                    <span id="cart-count">0</span>
                    <span>)</span>
                </p>
                <p class='title-small' id="currentTrips-btn">進行中的行程</p>
                <p class='title-small' id="doneProduct-btn">已完成的行程</p>
                <p class='title-small' id="collection-btn">收藏清單</p>
            </div>
        </div>

        <!--右邊選項-->
        <div class='contentContainer shadow-m'>
            <!--帳號基本資料-->
            <div class='accData putCenter' id='accData'>
                <form action='../PHP/updateUserData.php' method="POST" class='user-form' enctype="multipart/form-data">
                    <div class='putCenter'>
                        <p>姓名</p>
                        <input type='text' name='name' class='fillinBar' value='<?php GetUserData('name') ?>'>
                        <br>
                        <!--//TODO:增加 修改手機與信箱之動作-->
                        <p>Email</p>
                        <input type='text' name='mail' class='fillinBar' value='<?php GetUserData('Email') ?>' readonly>
                        <br>
                        <p>手機</p>
                        <input type='text' name='phone' class='fillinBar' value='<?php GetUserData('phone') ?>'>
                    </div>

                    <div>
                        <p>大頭照</p>
                        <input type='file' name='headShot_file[]' id='headShot-file' accept='image/*' onchange='readURL(this,".headShot-preview");'>
                        <img src='../upload/avatars/<?php GetUserData("photo") ?>' class='headShot-preview'>
                        <label for='headShot-file' class='picUpload_btn'>上傳大頭照</label>
                    </div>

                    <input type='submit' value='儲存' class='roundBtn'>
                </form>
            </div>

            <!--我是嚮導：行程總覽-->
            <div id='allProduct' class='hidden'>
                <table class='putCenter accData infoTable'>
                    <thead>
                        <tr>
                            <th>商品</th>
                            <th>標題</th>
                            <th>日程</th>
                            <th>價格</th>
                            <th>狀態</th>
                            <th>上傳時間</th>
                            <th>動作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--EXAMPLE
                            <tr>
                                <td class="allProduct-cell-thumbnail"><img src="../img/example/headshot.jpg"
                                    class="max-70px"></td>
                                    <td class="allProduct-cell-title"><a href='#'>鎮瀾宮一日遊</a></td>
                                    <td class="allProduct-cell-day">1天</td>
                                    <td class="allProduct-cell-price">NT.650</td>
                                    <td class="allProduct-cell-state">公開</td>
                                    <td class="allProduct-cell-uploadDate">2020-05-01</td>
                                    <td class='actionGroup'>
                                        <p class="allProduct-cell-edit_btn"><a href=''>編輯</a></p>
                                        <p class="allProduct-cell-exclusive_btn"><a href=''>開專屬</a></p>
                                        <p class="allProduct-cell-delete_btn"><a href=''>刪除</a></p>
                                    </td>
                                </tr>
                            -->
                    </tbody>
                </table>
            </div>

            <!--我是嚮導:發布新行程-->
            <div id='makeNewProduct' class="hidden">
                <div class='productTitle' id="makeNewProduct-title">新增行程</div>
                <form id='newProductForm' class='newProductForm accData' action="../PHP/createNewProduct.php" method="POST" enctype="multipart/form-data">
                    <div class="productHead">

                        <!--上傳縮圖-->
                        <div class="thumbnail">
                            <p>縮圖</p>
                            <input type="file" name="product_thumb_pic[]" id="product_thumb_pic_upload" accept="image/*" onchange='readURL(this, ".thumbnail img");'>
                            <label for="product_thumb_pic_upload">
                                <img src="../img/icon/uploadPics_icon.png" alt="" class="max-200px putCenter defult  upload-s-dotted-b">
                            </label>
                        </div>
                        <!--標題-->
                        <div>
                            <span>標題</span>
                            <input type='text' class='fillinBar-ul' name='input-producttitle' id='input-producttitle' placeholder='想個好玩的標題吧'>
                        </div>


                        <!--區域-->
                        <div class=''>
                            <span>區域</span>
                            <select class='dropDown country-select' name='country' id='country-select'>
                                <option value='tw'>台灣</option>
                                <option value='jp'>日本</option>
                            </select>

                            <span>縣市</span>
                            <select class='dropDown county-select' name='county' id='county-select'>
                                <option value='台北'>台北</option>
                                <option value='台中'>台中</option>
                                <option value='高雄'>高雄</option>
                                <option value='屏東'>屏東</option>
                            </select>
                        </div>

                        <!--數量限制-->
                        <div>
                            <span>人數限制</span>
                            <input type='number' class='fillinBar-s input-people' name='input-people' id='input-people' min='1' max='999' value='1'>
                            <span>人</span>
                        </div>
                        <!--星期限定-->
                        <div>
                            <span>開放星期</span>
                            <div id="week-select">
                                <span class="week-selected putCenter" id="week-1">一</span>
                                <span class="week-selected putCenter" id="week-2">二</span>
                                <span class="week-selected putCenter" id="week-3">三</span>
                                <span class="week-selected putCenter" id="week-4">四</span>
                                <span class="week-selected putCenter" id="week-5">五</span>
                                <span class="week-selected putCenter" id="week-6">六</span>
                                <span class="week-selected putCenter" id="week-7">日</span>
                            </div>

                        </div>
                        <!--日程-->
                        <div>
                            <span>日程</span>
                            <input type='number' class='fillinBar-s' name='input-duration' id='input-duration' min='1' max='999' value='1'>
                            <span>天</span>
                        </div>
                    </div>


                    <!--介紹-->
                    <div>
                        <p>介紹</p>
                        <textarea name='input-productinfo' id='input-productinfo' cols='30' rows='10' maxlength='600' class='textarea-b '></textarea>
                    </div>

                    <!--上傳圖片-->
                    <div>
                        <p>圖片</p>
                        <!--教學:https://stackoverflow.com/questions/1175347/how-can-i-select-and-upload-multiple-files-with-html-and-php-using-http-post-->
                        <input type="file" name="product_pics[]" id="product_pics_upload" size="6" multiple accept="image/*">
                        <!--onchange='readGalleryPhoto(this, ".gallery");'>-->
                        <label for="product_pics_upload" id="gallery_upload_lable">

                            <div class="gallery upload-s-dotted-b">
                                <!--DEFULT-->
                                <div class="gallery-photobox defult putCenter">
                                    <img src="../img/icon/uploadPics_icon.png" alt="" class="max-200px">
                                    <span class="hidden">X</span>
                                </div>

                            </div>
                        </label>

                    </div>

                    <!--行程規劃-->
                    <div id="agenda-wrapper">
                        <p>行程安排</p>

                        <div id="agenda-d1">
                            <div class='agenda putCenter'>
                                <!--EXAMPLE-->
                                <div class='pointCircle-big putCenter day'>Day 1</div>
                                <input type='text' class='productTitle putCenter fillinBar-ul' placeholder='標題' maxlength='15'>
                                <p></p>
                                <!--小項目-->
                                <div class='wrap'>
                                    <div class='agenda-content'>
                                        <p class='pointCircle-small putCenter'></p>
                                        <input type='text' class='agenda-itme fillinBar' placeholder='活動名稱' maxlength='15'>
                                        <p></p>
                                        <input type='text' class='subText fillinBar-ul' placeholder='活動簡述' maxlength='30'>
                                    </div>
                                </div>

                            </div>
                            <div class='btn-sq-m btn1 putCenter addevent-btn'>+</div>
                        </div>


                    </div>

                    <!--估價-->
                    <div id="priceing">
                        <p>估價單</p>
                        <div class='price-grid putCenter'>
                            <table class='price-table'>
                                <thead>
                                    <tr>
                                        <th class='cell-l'>日程</th>
                                        <th>摘要</th>
                                        <th class='cell-l'>費用</th>
                                        <th class='cell-l'>數量</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--EXAMPLE-->
                                    <tr>
                                        <td class='cell-l'>
                                            <select class="day-select">
                                                <option value="0">全程</option>
                                                <option value="1">1</option>
                                            </select>
                                        </td>
                                        <td class='text-indent'>
                                            <input type="text" class="input-none billcontent" name="" id="" maxlength="50" placeholder="摘要"></td>
                                        </td>
                                        <td class='cell-l'>
                                            <input type="number" class="input-none  price-input" name="" id="" min="1" placeholder="1"></td>
                                        <td class='cell-l'>
                                            <input type="number" class="input-none unit-input" name="" id="" min="1" placeholder="1"></td>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>

                            <div class='price-total'>
                                <p></p>
                                <p id='total'>Total</p>
                                <p id='total-number'>0/人</p>
                            </div>


                        </div>
                    </div>

                    <!--其他聲明-->
                    <div>
                        <p>其他聲明</p>
                        <textarea name='input-declear' id='input-declear' cols='30' rows='10' maxlength='600' class='textarea-b '></textarea>
                    </div>

                    <!--送出-->
                    <div class="putCenter">
                        <input type="submit" value="送出" tabIndex="-1" class="roundBtn">
                    </div>

                </form>
                <script src='../js/profile_newProduct_control.js'></script>
            </div>

            <!--我是嚮導:進行中行程-->
            <div id="currentOrders" class="hidden">
                <div class="productTitle">進行中訂單</div>

            </div>

            <!--我是嚮導:已完成行程-->
            <div id="doneOrder" class="hidden">

            </div>

            <!--我是嚮導:訂單:-->
            <div id="orderRecord" class="hidden">
                <!--EXAMPLE-->
                <div class="infoWhiteBoard putCenter">
                    <!--圖-->
                    <img src="../img/example/headshot.jpg" class="thumb putCenter">
                    <!--資訊-->
                    <div class="info">
                        <a class="productTitle" href="#">鎮瀾宮一日遊</a>
                        <div class="col2-100-auto">
                            <p>訂單編號:</p>
                            <p class="orderID">o123456789</a></p>
                        </div>
                        <div class="col2-100-auto">
                            <p>買家:</p>
                            <p><a href="#">林小姐</a></p>
                        </div>
                        <div class="col2-100-auto">
                            <p>人數:</p>
                            <p>1</p>
                        </div>
                        <div class="col2-100-auto">
                            <p>預定時間:</p>
                            <p>2020-05-14 ~ 2020-05-15</p>
                        </div>
                        <div class="col2-100-auto">
                            <p>購買金額:</p>
                            <p>2500</p>
                        </div>

                    </div>
                    <!--操作按鈕-->
                    <div class="putCenter orderControl-btn">
                        <div class="block-btn btn2 orderAccept-btn">接受訂單</div>
                        <div class="block-btn btn1 connectBuyer-btn">聯絡買家</div>
                        <div class="block-btn btn-c cancel-btn">取消訂單</div>
                    </div>

                </div>
            </div>


            <!--我是遊客:進行中的行程-->
            <div id="currentTrips" class="hidden">
                <div class="productTitle">進行中行程</div>

            </div>

            <!--我是遊客:已完成的行程-->
            <div id="doneProduct" class="hidden">

            </div>

            <!--我是遊客:收藏清單-->
            <div id="collection" class="hidden">
                <!--格子型排列方式-->
                <div class="boxUnitContainer putCenter">
                    <!--EXAMPLE-->
                    <a class="boxUnit shadow-m">
                        <img src="../img/example/headshot.jpg" alt="" class="thumb">
                        <h1 class="boxTitle">鎮瀾宮一日遊鎮瀾宮一日遊鎮瀾宮一日遊</h1>
                        <img src="../img/icon/star1.png" alt="" class="ratingStar">
                        <div>
                            <div class="col2-30-50">
                                <p class="ordernumber">5</p>
                                <p>次成交</p>
                            </div>
                            <div class="col2-30-50">
                                <p class="ordernumber">5</p>
                                <p>則評論</p>
                            </div>
                        </div>
                        <p class="info">全台最好玩
                            全台最好玩全台最好玩全台最好玩全台最好玩全台最好玩全台最好玩
                        </p>
                        <p class="price">$1499</p>

                    </a>
                </div>
            </div>

            <!--我是遊客:購物車-->
            <div id="cart" class="hidden">
                <p class="productTitle">購物車</p>
                <div id="cart-container"></div>
            </div>

        </div>

    </div>

    <script src="../js/common.js"></script>
    <script src='../js/profileControl.js'></script>
    <script src='../js/boxUnitControl.js'></script>
    <script src='../js/editProductControl.js'></script>
    <script src='../js/starControl.js'></script>
    <script src='../js/profileOrderCalender.js'></script>


    </div>
    </div>



    <!--PAGE:TEMPLATE-->
    <template id="newProductForm-template">
        <div class='productTitle' id="makeNewProduct-title">新增行程</div>
        <form id='newProductForm' class='newProductForm accData' action="../PHP/createNewProduct.php" method="POST" enctype="multipart/form-data">
            <div class="productHead">

                <!--上傳縮圖-->
                <div class="thumbnail">
                    <p>縮圖</p>
                    <input type="file" name="product_thumb_pic[]" id="product_thumb_pic_upload" accept="image/*" onchange='readURL(this, ".thumbnail img");'>
                    <label for="product_thumb_pic_upload">
                        <img src="../img/icon/uploadPics_icon.png" alt="" class="max-200px putCenter defult  upload-s-dotted-b">
                    </label>
                </div>
                <!--標題-->
                <div>
                    <span>標題</span>
                    <input type='text' class='fillinBar-ul' name='input-producttitle' id='input-producttitle' placeholder='想個好玩的標題吧'>
                </div>


                <!--區域-->
                <div class=''>
                    <span>區域</span>
                    <select class='dropDown country-select' name='country' id='country-select'>
                        <option value='tw'>台灣</option>
                        <option value='jp'>日本</option>
                    </select>

                    <span>縣市</span>
                    <select class='dropDown county-select' name='county' id='county-select'>
                        <option value='台北'>台北</option>
                        <option value='台中'>台中</option>
                        <option value='高雄'>高雄</option>
                        <option value='屏東'>屏東</option>
                    </select>
                </div>

                <!--數量限制-->
                <div>
                    <span>人數限制</span>
                    <input type='number' class='fillinBar-s input-people' name='input-people' id='input-people' min='1' max='999' value='1'>
                    <span>人</span>
                </div>
                <!--星期限定-->
                <div>
                    <span>開放星期</span>
                    <div id="week-select">
                        <span class="week-selected putCenter" id="week-1">一</span>
                        <span class="week-selected putCenter" id="week-2">二</span>
                        <span class="week-selected putCenter" id="week-3">三</span>
                        <span class="week-selected putCenter" id="week-4">四</span>
                        <span class="week-selected putCenter" id="week-5">五</span>
                        <span class="week-selected putCenter" id="week-6">六</span>
                        <span class="week-selected putCenter" id="week-7">日</span>
                    </div>

                </div>
                <!--日程-->
                <div>
                    <span>日程</span>
                    <input type='number' class='fillinBar-s' name='input-duration' id='input-duration' min='1' max='999' value='1'>
                    <span>天</span>
                </div>
            </div>


            <!--介紹-->
            <div>
                <p>介紹</p>
                <textarea name='input-productinfo' id='input-productinfo' cols='30' rows='10' maxlength='600' class='textarea-b '></textarea>
            </div>

            <!--上傳圖片-->
            <div>
                <p>圖片</p>
                <!--教學:https://stackoverflow.com/questions/1175347/how-can-i-select-and-upload-multiple-files-with-html-and-php-using-http-post-->
                <input type="file" name="product_pics[]" id="product_pics_upload" size="6" multiple accept="image/*">
                <!--onchange='readGalleryPhoto(this, ".gallery");'>-->
                <label for="product_pics_upload" id="gallery_upload_lable">

                    <div class="gallery upload-s-dotted-b">
                        <!--DEFULT-->
                        <div class="gallery-photobox defult putCenter">
                            <img src="../img/icon/uploadPics_icon.png" alt="" class="max-200px">
                            <span class="hidden">X</span>
                        </div>

                    </div>
                </label>

            </div>

            <!--行程規劃-->
            <div id="agenda-wrapper">
                <p>行程安排</p>

                <div id="agenda-d1">
                    <div class='agenda putCenter'>
                        <!--EXAMPLE-->
                        <div class='pointCircle-big putCenter day'>Day 1</div>
                        <input type='text' class='productTitle putCenter fillinBar-ul' placeholder='標題' maxlength='15'>
                        <p></p>
                        <!--小項目-->
                        <div class='wrap'>
                            <div class='agenda-content'>
                                <p class='pointCircle-small putCenter'></p>
                                <input type='text' class='agenda-itme fillinBar' placeholder='活動名稱' maxlength='15'>
                                <p></p>
                                <input type='text' class='subText fillinBar-ul' placeholder='活動簡述' maxlength='30'>
                            </div>
                        </div>

                    </div>
                    <div class='btn-sq-m btn1 putCenter addevent-btn'>+</div>
                </div>


            </div>

            <!--估價-->
            <div id="priceing">
                <p>估價單</p>
                <div class='price-grid'>
                    <table class='price-table'>
                        <thead>
                            <tr>
                                <th class='cell-l'>日程</th>
                                <th>摘要</th>
                                <th class='cell-l'>費用</th>
                                <th class='cell-l'>數量</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--EXAMPLE-->
                            <tr>
                                <td class='cell-l'>
                                    <select class="day-select">
                                        <option value="0">全程</option>
                                        <option value="1">1</option>
                                    </select>
                                </td>
                                <td class='text-indent'>
                                    <input type="text" class="input-none billcontent" name="" id="" maxlength="50" placeholder="摘要"></td>
                                </td>
                                <td class='cell-l'>
                                    <input type="number" class="input-none  price-input" name="" id="" min="1" placeholder="1"></td>
                                <td class='cell-l'>
                                    <input type="number" class="input-none unit-input" name="" id="" min="1" placeholder="1"></td>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                    <div class='price-total'>
                        <p></p>
                        <p id='total'>Total</p>
                        <p id='total-number'>0/人</p>
                    </div>


                </div>
            </div>

            <!--其他聲明-->
            <div>
                <p>其他聲明</p>
                <textarea name='input-declear' id='input-declear' cols='30' rows='10' maxlength='600' class='textarea-b '></textarea>
            </div>

            <!--送出-->
            <div class="">
                <input type="submit" value="送出" tabIndex="-1" class="roundBtn">
            </div>

        </form>
    </template>

    <!--[進行中行程]日歷:-->
    <!--TEMPLATE-->
    <template id="orderCalender-template">
        <div class="col2-auto-auto" id="cal">
            <!--選擇日期欄-->
            <div id='dateGrid-seller' class='putCenter'>
                <div class='calendarContainer putCenter'>
                    <p class='calendarTitle putCenter monthAndYear' id='monthAndYear'>2020 4月</p>
                    <table id='calendar-tabel'>
                        <thead>
                            <tr>
                                <th>日</th>
                                <th>一</th>
                                <th>二</th>
                                <th>三</th>
                                <th>四</th>
                                <th>五</th>
                                <th>六</th>
                            </tr>
                        </thead>
                        <tbody id='calendarBody'>

                        </tbody>
                    </table>
                    <!--切換上下月按鈕-->
                    <div class='putCenter'>
                        <button class='block-btn btn1' id='preMon-btn' type='button' onclick=previous()>上個月</button>
                        <button class='block-btn btn2' id='nextMon-btn' type='button' onclick=next()>下個月</button>
                    </div>
                </div>

            </div>
            <!--資訊欄位-->
            <div class="verticalWhiteBoard shadow-m putCenter">
                <div class="calender-info">
                    <!--EXAMPLE
                            <p class="board-title">5/30</p>-->

                </div>
            </div>

        </div>
    </template>

    <!--[進行中行程]:右欄位-->
    <!--TEMPLATE-->
    <template id="currentTrips-eachorder-template">
        <div class="infoWhiteBoard putCenter">
            <!--圖-->
            <img src="../img/example/headshot.jpg" class="thumb putCenter">
            <!--資訊-->
            <div class="info">
                <a class="productTitle" href="#">鎮瀾宮一日遊</a>
                <div class="col2-100-auto">
                    <p>訂單編號:</p>
                    <p class="orderID">o123456789</p>
                </div>
                <div class="col2-100-auto">
                    <p>買家:</p>
                    <p><a class="buyer" href="#">林小姐</a></p>
                </div>
                <div class="col2-100-auto">
                    <p>人數:</p>
                    <p class="people">1</p>
                </div>
                <div class="col2-100-auto">
                    <p>預定時間:</p>
                    <p class="playDate">2020-05-14 ~ 2020-05-15</p>
                </div>
                <div class="col2-100-auto">
                    <p>購買金額:</p>
                    <p class="price">2500</p>
                </div>

            </div>
        </div>
    </template>

    <!--行程安排-->
    <!--TEMPLATE-->
    <template id='agenda-template'>
        <div>
            <div class='agenda putCenter'>
                <!--EXAMPLE-->
                <div class='pointCircle-big putCenter day'>Day 1</div>
                <input type='text' class='productTitle putCenter fillinBar-ul' placeholder='標題' maxlength='15'>
                <p></p>
                <!--小項目-->
                <div class='wrap'>
                    <div class='agenda-content'>
                        <p class='pointCircle-small putCenter'></p>
                        <input type='text' class='agenda-itme fillinBar' placeholder='活動名稱' maxlength='15'>
                        <p></p>
                        <input type='text' class='subText fillinBar-ul' placeholder='活動簡述' maxlength='30'>
                    </div>
                </div>

            </div>
            <div class='btn-sq-m btn1 putCenter addevent-btn'>+</div>
        </div>
    </template>
    <!--TEMLPATE-->
    <template id="agenda-content-template">
        <!--<div class='wrap'>-->
        <div class='agenda-content'>
            <p class='pointCircle-small putCenter'></p>
            <input type='text' class='agenda-itme fillinBar' placeholder='活動名稱' maxlength='15'>
            <p></p>
            <input type='text' class='subText fillinBar-ul' placeholder='活動簡述' maxlength='30'>
        </div>
        <!--</div>-->
    </template>

    <!--估價-->
    <!--TEMLPATE-->
    <template id="pricing-tr-input-template">
        <tr>
            <td class='cell-l'>
                <select name="" id="" class="day-select">
                    <option value="0">全程</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
            <td class='text-indent'>
                <input type="text" class="input-none billcontent" name="" id="" maxlength="50" placeholder="摘要"></td>
            </td>
            <td class='cell-l'>
                <input type="number" class="input-none  price-input" name="" id="" min="1" placeholder="1"></td>
            <td class='cell-l'>
                <input type="number" class="input-none  unit-input" name="" id="" min="1" placeholder="1"></td>
            </td>
        </tr>
    </template>

    <!--Gallery圖片-->
    <!--TEMPLATE-->
    <template id="gallery-photobox-template">
        <div class="gallery-photobox">
            <img src="../img/icon/uploadPics_icon.png" alt="" class="max-200px putCenter">
            <span class="hidden gallery-photobox-remove">X</span>
        </div>
    </template>

    <!--行程總覽:tr-->
    <template id="allProduct-cell-templet">
        <tr>
            <td class="allProduct-cell-thumbnail"><img src="../img/example/headshot.jpg" class="max-70px"></td>
            <td class="allProduct-cell-title"><a href='#'>鎮瀾宮一日遊</a></td>
            <td class="allProduct-cell-day">1天</td>
            <td class="allProduct-cell-price">NT.650</td>
            <td class="allProduct-cell-state">公開</td>
            <td class="allProduct-cell-uploadDate">2020-05-01</td>
            <td class='actionGroup'>
                <p class="allProduct-cell-edit_btn"><a>編輯</a></p>
                <!--<p class="allProduct-cell-exclusive_btn"><a>開專屬</a></p>-->
                <p class="allProduct-cell-delete_btn"><a>刪除</a></p>
            </td>
        </tr>
    </template>

    <!--訂單-->
    <!--TEMPLATE-->
    <template id="orderRecord-templet">
        <div class="infoWhiteBoard putCenter">
            <!--圖-->
            <img src="../img/example/headshot.jpg" class="thumb putCenter">
            <!--資訊-->
            <div class="info">
                <a class="productTitle" href="#">鎮瀾宮一日遊</a>
                <div class="col2-100-auto">
                    <p>訂單編號:</p>
                    <p class="orderID">o123456789</p>
                </div>
                <div class="col2-100-auto">
                    <p>買家:</p>
                    <p><a class="buyer" href="#">林小姐</a></p>
                </div>
                <div class="col2-100-auto">
                    <p>人數:</p>
                    <p class="people">1</p>
                </div>
                <div class="col2-100-auto">
                    <p>預定時間:</p>
                    <p class="playDate">2020-05-14 ~ 2020-05-15</p>
                </div>
                <div class="col2-100-auto">
                    <p>購買金額:</p>
                    <p class="price">2500</p>
                </div>

            </div>
            <!--操作按鈕-->
            <div class="putCenter  orderControl-btn">
                <div class="block-btn btn2 orderAccept-btn">接受訂單</div>
                <div class="block-btn btn1 connectBuyer-btn">聯絡買家</div>
                <div class="block-btn btn-c cancel-btn">取消訂單</div>

            </div>

        </div>
    </template>

    <!--訂單[購物車]-->
    <!--TEMPLATE-->
    <template id="cart-templet">
        <div class="infoWhiteBoard putCenter">
            <!--圖-->
            <img src="../img/example/headshot.jpg" class="thumb putCenter">
            <!--資訊-->
            <div class="info">
                <a class="productTitle" href="#">鎮瀾宮一日遊</a>
                <div class="col2-100-auto">
                    <p>訂單編號:</p>
                    <p class="orderID">o123456789</p>
                </div>
                <div class="col2-100-auto">
                    <p>狀態:</p>
                    <p class="state">等待確認購買</p>
                </div>
                <div class="col2-100-auto">
                    <p>賣家:</p>
                    <p><a class="seller" href="#">林小姐</a></p>
                </div>
                <div class="col2-100-auto">
                    <p>人數:</p>
                    <p class="people">1</p>
                </div>
                <div class="col2-100-auto">
                    <p>預定時間:</p>
                    <p class="playDate">2020-05-14 ~ 2020-05-15</p>
                </div>
                <div class="col2-100-auto">
                    <p>購買金額:</p>
                    <p class="price">2500</p>
                </div>

            </div>
            <!--操作按鈕-->
            <div class="putCenter  orderControl-btn">
                <div class="block-btn btn1 connectSeller-btn">聯絡賣家</div>
                <div class="block-btn btn2 confirmOrder-btn">確定購買</div>
                <div class="block-btn btn-c cancel-btn">取消訂單</div>

            </div>

        </div>
    </template>

    <!--訂單:遊客[已完成的行程]-->
    <!--TEMPLATE-->
    <template id="doneProduct-templet">
        <div class="infoWhiteBoard putCenter">
            <!--圖-->
            <img src="../img/example/headshot.jpg" class="thumb putCenter">
            <!--資訊-->
            <div class="info">
                <a class="productTitle" href="#">鎮瀾宮一日遊</a>
                <div class="col2-100-auto">
                    <p>訂單編號:</p>
                    <p class="orderiD">o123456789</p>
                </div>
                <div class="col2-100-auto">
                    <p>賣家:</p>
                    <p><a class="seller" href="#">林小姐</a></p>
                </div>
                <div class="col2-100-auto">
                    <p>人數:</p>
                    <p class="people">1</p>
                </div>
                <div class="col2-100-auto">
                    <p>預定時間:</p>
                    <p class="playDate">2020-05-14 ~ 2020-05-15</p>
                </div>
                <div class="col2-100-auto">
                    <p>購買金額:</p>
                    <p class="price">2500</p>
                </div>
                <div class="star-container" star=5>
                    <img src="../img/icon/star.png" o=1>
                    <img src="../img/icon/star.png" o=2>
                    <img src="../img/icon/star.png" o=3>
                    <img src="../img/icon/star.png" o=4>
                    <img src="../img/icon/star.png" o=5>
                </div>
                <img src="../img/icon/star5.png" class="ratingStar">
                <textarea placeholder="留下評語(送出後不可修改)" class="textarea-b comment" rows="3" maxlength="300"></textarea>

            </div>
            <!--操作按鈕-->
            <div class="putCenter  orderControl-btn">
                <!--<div class="block-btn btn1 connectBuyer-btn">聯絡賣家</div>-->
                <div class="block-btn btn2 productDone-btn">確認完成</div>

            </div>

        </div>
    </template>


    <!--訂單:嚮導[已完成的行程]-->
    <!--TEMPLATE-->
    <template id="doneOrder-templet">
        <div class="infoWhiteBoard putCenter">
            <!--圖-->
            <img src="../img/example/headshot.jpg" class="thumb putCenter">
            <!--資訊-->
            <div class="info">
                <a class="productTitle" href="#">鎮瀾宮一日遊</a>
                <div class="col2-100-auto">
                    <p>訂單編號:</p>
                    <p class="orderiD">o123456789</p>
                </div>
                <div class="col2-100-auto">
                    <p>買家:</p>
                    <p><a class="seller" href="#">林小姐</a></p>
                </div>
                <div class="col2-100-auto">
                    <p>人數:</p>
                    <p class="people">1</p>
                </div>
                <div class="col2-100-auto">
                    <p>預定時間:</p>
                    <p class="playDate">2020-05-14 ~ 2020-05-15</p>
                </div>
                <div class="col2-100-auto">
                    <p>購買金額:</p>
                    <p class="price">2500</p>
                </div>
                <div></div>
                <!--買家的評價-->
                <div>
                    <img src="../img/icon/star5.png" class="buyer-ratingStar">
                    <textarea class="textarea-b buyerComment" readonly placeholder="買家沒有留言"></textarea>
                </div>
                <!--賣家留言操作-->
                <div class="star-container" star=5>
                    <img src="../img/icon/star.png" o=1>
                    <img src="../img/icon/star.png" o=2>
                    <img src="../img/icon/star.png" o=3>
                    <img src="../img/icon/star.png" o=4>
                    <img src="../img/icon/star.png" o=5>
                </div>
                <img src="../img/icon/star5.png" class="ratingStar">
                <textarea placeholder="留下評語(送出後不可修改)" class="textarea-b comment" rows="3" maxlength="300"></textarea>

            </div>
            <!--操作按鈕-->
            <div class="putCenter  orderControl-btn">
                <!--<div class="block-btn btn1 connectBuyer-btn">聯絡賣家</div>-->
                <div class="block-btn btn2 orderDone-btn">確認完成</div>

            </div>

        </div>
    </template>

    <!--收藏單位-->
    <!--TEMPLATE-->
    <template id="boxUnit-Product-templet">
        <a class="boxUnit shadow-m">
            <img src="../img/example/headshot.jpg" alt="" class="thumb">
            <h1 class="boxTitle">鎮瀾宮一日遊鎮瀾宮一日遊鎮瀾宮一日遊</h1>
            <img src="../img/icon/star1.png" alt="" class="ratingStar">
            <div>
                <div class="">
                    <span class="ordernumber subText">5</span>
                    <span class="subText">次成交</span>
                </div>
                <div class="">
                    <span class="reviewnumber subText">5</span>
                    <span class="subText">則評論</span>
                </div>
            </div>
            <p class="info">全台最好玩
                全台最好玩全台最好玩全台最好玩全台最好玩全台最好玩全台最好玩
            </p>
            <div class="col2-auto-auto">
                <div class='col2-30-50 putCenter'>
                    <img src='../img/icon/heart_on.png' alt='' class="collect-btn">
                    <p class="collectnumber">5</p>
                </div>
                <p class="price">$1499</p>
            </div>

        </a>
    </template>
</body>

</html>