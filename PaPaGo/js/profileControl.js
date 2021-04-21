
//進入時，檢查有沒有預設要開得tag
$(document).ready(
    function () {
        var page = GetURLParameter("page");
        if (typeof page === "undefined") {
            openTagPage("accData");
        }
        else {
            console.log(page);
            getTagData(page);
            //openTagPage(page);
        }

        //更新購物車數量
        $.ajax({
            url: "../PHP/getProfileData.php",
            type: "post",
            data: {
                'dataType': 'ordercount'
            },
            success: function (result) {
                var datas = JSON.parse(result);
                console.log(datas);

                //購物車數量:
                $("#cart-count").html(datas[0]["cart_count"]);

                //更新訂單數量:
                $("#order-count").html(datas[1]["order_count"]);

            },
            error: function (err) {
                reject(err);// Reject the promise and go to catch()
            }
        });

    },
);

//左邊Nav展開/關閉
$(".title-big").click(function () {
    if ($(this).next().hasClass("hidden")) {
        $(this).next().removeClass("hidden");
    }
    else {
        $(this).next().addClass("hidden");
    }
});

//[購物車:]確定購買
$(document).on("click", ".confirmOrder-btn", function () {
    var orderid = $(this).parent().attr("orderid");
    var this_btnParent = $(this).parent();
    $.ajax({
        url: "../PHP/common.php",
        type: "post",
        data: {
            'func_name': 'updateData',
            'tb_name_post': 'orderrecord',
            'row_name_post': 'state',
            'val_post': 1,
            'where_post': 'recordNO',
            'key_post': orderid
        },
        success: function () {
            //更新介面
            $(this_btnParent).find(".confirmOrder-btn").remove();
        },
        error: function (error) {
            alert(err);
        }
    });
});

//確認訂單:
$(document).on("click", ".orderAccept-btn", function () {
    var orderid = $(this).parent().attr("orderid");
    var this_btnParent = $(this).parent();
    $.ajax({
        url: "../PHP/common.php",
        type: "post",
        data: {
            'func_name': 'updateData',
            'tb_name_post': 'orderrecord',
            'row_name_post': 'state',
            'val_post': 2,
            'where_post': 'recordNO',
            'key_post': orderid
        },
        success: function () {
            //更新介面
            $(this_btnParent).html("已接受");
        },
        error: function (error) {
            alert(err);
        }
    });
});

//取消訂單
$(document).on("click", ".cancel-btn", function () {
    var orderid = $(this).parent().attr("orderid");
    var this_btnParent = $(this).parent();
    console.log(orderid);
    //TODO[安全]改用別的php方法處理，動作前需判斷狀態，避免inject改值
    $.ajax({
        url: "../PHP/common.php",
        type: "post",
        data: {
            'func_name': 'removeData',
            'tb_name_post': 'orderrecord',
            'where_post': 'recordNO',
            'key_post': orderid
        },
        success: function (result) {
            //更新介面
            $(this_btnParent).html("已取消");
            console.log(result);
        },
        error: function (error) {
            alert(err);
        }
    });
});

//完成訂單[遊客]
$(document).on("click", '.productDone-btn', function () {
    var this_btnParent = $(this).parent();
    var orderid = $(this).parent().attr("orderid");
    var pid = $(this).parent().attr("productid");
    var rate = $(this_btnParent).parent().find(".star-container").attr("star");
    var comment = $(this_btnParent).parent().find(".comment").val();

    $.ajax({
        url: "../PHP/setProfileData.php",
        type: "post",
        data: {
            'dataType': 'productDone-review',
            'oid': orderid,
            'pid': pid,
            'rate': rate,
            'comment': comment
        },
        success: function (result) {
            console.log(result);
            //更新介面
            $(this_btnParent).html("已完成");
        },
        error: function (error) {
            alert(err);
        }
    });
});

//完成訂單[嚮導]
$(document).on("click", '.orderDone-btn', function () {
    var this_btnParent = $(this).parent();
    var orderid = $(this).parent().attr("orderid");
    var pid = $(this).parent().attr("productid");
    var rate = $(this_btnParent).parent().find(".star-container").attr("star");
    var comment = $(this_btnParent).parent().find(".comment").val();

    $.ajax({
        url: "../PHP/setProfileData.php",
        type: "post",
        data: {
            'dataType': 'orderDone-review',
            'oid': orderid,
            'pid': pid,
            'rate': rate,
            'comment': comment
        },
        success: function (result) {
            console.log(result);
            //更新介面
            $(this_btnParent).html("已完成");
        },
        error: function (error) {
            alert(err);
        }
    });
});

//刪除商品
$(document).on("click", ".allProduct-cell-delete_btn", function () {
    if (confirm("真的要刪除嗎?")) {
        var pid = $(this).parent().parent().attr("id");
        $(this).parent().parent().remove();
        //刪除:
        $.ajax({
            url: "../PHP/setProfileData.php",
            type: "post",
            data: {
                'dataType': 'removeProduct',
                'pid': pid
            },
            success: function (result) {
                //更新介面
                //$(this).parent().parent().remove();
                console.log(result);
            },
            error: function (error) {
                alert(err);
            }
        });
    }
});

//商品上傳多個圖片瀏覽:
function readMutilpalPhoto(input, insertElement) {
    console.log($(insertElement));
    if (input.files) {
        //TODO:限制上傳數量 和 大小
        var fileAmout = input.files.length;

        for (i = 0; i < fileAmout; i++) {

            var reader = new FileReader();
            reader.onload = function (event) {

                //創建圖片物件
                var pic = document.createElement("img");
                pic.src = event.target.result;
                //pic.setAttribute("id", "pic" + i);
                pic.classList.add("max-200px");
                //pic.classList.add("putCenter");
                $(insertElement).append(pic);

                //隱藏預設圖片
                $(insertElement).find(".defult").addClass("hidden");

            }
            reader.readAsDataURL(input.files[i]);

        }
    }


}

//靠id找對應的右欄div開啟
function openTagPage(tag_id) {
    //其他先關閉
    $(".title-small").each(function (index) {
        var div_id = ($(this).attr('id') || "").split('-');
        if ($("#" + div_id[0]).length
            && !$("#" + div_id[0]).hasClass("hidden")) { $("#" + div_id[0]).addClass("hidden"); }
    });

    $("#" + tag_id).removeClass("hidden");

    //TODO:以後改用這種方法? =>各page還原方法可免
    //  還原預設[makeNewProduct]預設page:
    if (tag_id == "makeNewProduct") {
        $("#makeNewProduct").empty();
        var _page = $($("#newProductForm-template").html()).clone();
        $(_page).removeClass("hidden");
        $("#makeNewProduct").append(_page);

        //刷新:
        refreshSelect("#county-select", county_tw);
    }


    //整理網址: 只留下userID: //更新:不靠網址id互動
    //var uid = GetURLParameter("id");
    //var pageURL = $(location).attr("href").split("?")[0];
    //pageURL += "?id=" + uid;
    //window.history.replaceState('', '', pageURL);
    var href = new URL($(location).attr("href"));
    href.searchParams.set('page', tag_id);
    window.history.replaceState('', '', href);
}

//登出:
$("#logout-btn").click(function () {
    console.log("bye");
    ajax_single_common_request("logout");
    window.location.href = "../";

});

//點左欄標題，靠id自動找對應的右欄div開啟
$(".title-small").click(
    function () {
        var id = $(this).attr('id');
        id = id.split('-')[0];

        getTagData(id);
    }
);

function getTagData(id) {
    //var id =tid;
    //id = id.split('-')[0];

    //檢查是否存在才執行
    if ($("#" + id).length) {

        openTagPage(id);

        //ajax讀取各欄位資料
        switch (id) {
            //帳號基本資料
            case 'accData':
                //ajax_getProfileData("../PHP/userProfileQueryControl.php", id);
                break;

            //商品總覽:
            case 'allProduct':
                var sellerID = GetURLParameter("id");
                //清空原先內容:
                $("#allProduct table tbody").empty();
                console.log(sellerID);
                ajax_get_allProduct_data(sellerID).then(function (res) {
                    console.log(res);
                    //處理文字:
                    var products = res.split("@");
                    for (var i = 0; i < products.length - 1; i++) {
                        var datas = products[i].split("|");
                        //複製範本
                        var temp = document.getElementById("allProduct-cell-templet");
                        var content = temp.content.cloneNode(true);
                        //設定參數
                        var p_title = $(content).children().find(".allProduct-cell-title a");
                        $(p_title).html(datas[1]);
                        $(p_title).attr("href", "../page/productView.php?id=" + datas[0] + "&seller=" + sellerID); //連結=商品頁面.php?ID= &seller=

                        $(content).children().find(".allProduct-cell-day").html(datas[2]);
                        $(content).children().find(".allProduct-cell-price").html(datas[3]);
                        $(content).children().find(".allProduct-cell-state").html(datas[4]);//TODO:"published"轉成中文
                        $(content).children().find(".allProduct-cell-uploadDate").html(datas[5]);
                        $(content).children().attr("id", datas[0]);

                        //主要縮圖 
                        ajax_get_allProduct_pic(datas[0]);

                        //設定動作btn:
                        //  設定edit:
                        $(content).find(".allProduct-cell-edit_btn a").attr("pid", datas[0]);
                        console.log($(content).find(".allProduct-cell-edit_btn a"));

                        //最後:加上
                        $("#allProduct table tbody").append(content);
                    }
                })
                break;

            //賣家:進行中行程:
            case 'currentOrders':
                var tmp = $($("#orderCalender-template").html()).clone();
                $("#cal").remove();
                $("#currentOrders").empty();
                $("#currentOrders").append(tmp);
                setSellerCalender();
                break;

            //購物車:
            case 'cart':
                ajax_get_cart_Data();
                break;
            //訂單
            case 'orderRecord':
                ajax_get_orderRecord_Data();
                break;
            //遊客:已完成的訂單
            case 'doneProduct':
                ajax_get_doneProduct_Data().then(function (res) {
                    ajax_get_doneProduct_review_Data();
                });
                break;
            //嚮導:已完成的訂單
            case 'doneOrder':
                $("#doneOrder").empty();
                ajax_get_doneOrder_Data().then(function (res) {
                    //ajax_get_doneProduct_review_Data();
                });
                break;
            //遊客:進行中行程:
            case 'currentTrips':
                var tmp = $($("#orderCalender-template").html()).clone();
                $("#cal").remove();
                $("#currentTrips").empty();
                $("#currentTrips").append(tmp);
                setBuyerCalender();
                break;

            //收藏
            case 'collection':
                ajax_get_collect_data().then(function () {
                    //取得交易次數 成交次數 和 喜歡次數?
                    $(".boxUnit").each(function () {
                        var pid = $(this).attr("id");
                        getCountData("orderrecord", "productID", "productID", pid, $(this).find(".ordernumber"));
                        getCountData("productreview", "productID", "productID", pid, $(this).find(".reviewnumber"));
                        getCountData("collect", "productID", "productID", pid, $(this).find(".collectnumber"));
                        getReviewRate(pid).then(function(res){
                            var src='../img/icon/star'+res+'.png';
                            $("#"+pid).find(".ratingStar").attr("src",src);
                        });
                    })
                });

                break;

        }
    }
}
//聯絡買家btn
$(document).on("click", ".connectBuyer-btn", function () {
    var id = $(this).parent().attr("buyerid");
    location.href = "chatRoom.php?seller=" + id;
});
//聯絡賣家btn
$(document).on("click", ".connectSeller-btn", function () {
    var id = $(this).parent().attr("seller");
    location.href = "chatRoom.php?seller=" + id;
});


//商品總攬:
function ajax_get_allProduct_data(_key) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/getProfileData.php",
            type: "post",
            data: {
                'dataType': 'allProduct'
            },
            success: function (result) {
                //var r = result.split("|");
                resolve(result); // Resolve promise and go to then()

            },
            error: function (err) {
                reject(err);// Reject the promise and go to catch()
            }
        });
    })
}

function ajax_get_allProduct_pic(_key) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData',
                'tb_name_post': 'productthumbnail',
                'row_name_post': 'photo',
                'where_post': 'productID',
                'key_post': _key,
                'split_by_post': '',
            },
            success: function (result) {
                var imgSrc = "../upload/productThumbIMG/" + result;
                if (result != "") {
                    $("#" + _key).find(".allProduct-cell-thumbnail img").attr("src", imgSrc);
                }
            },
            error: function (err) {
                reject(err);// Reject the promise and go to catch()
            }
        });

    })
}

//訂單資料
function ajax_get_orderRecord_Data() {
    $("#orderRecord").empty();
    $.ajax({
        url: "../PHP/getProfileData.php",
        type: "post",
        data: {
            'dataType': 'orderRecord'
        },
        success: function (result) {
            console.log(result);
            var datas = JSON.parse(result);
            console.log(datas.length);
            console.log(datas);

            var sellerID = GetURLParameter("id");
            //產生資料:
            for (var i = 0; i < datas.length; i++) {
                var unit = $($("#orderRecord-templet").html()).clone();
                //設定
                var href = "./productView.php?id=" + datas[i]["productID"] + "&seller=" + sellerID;
                //$(unit).attr("pid", data[0]);
                $(unit).attr("id", datas[i]["recordNO"]);
                $(unit).find(".productTitle").attr("href", href);
                $(unit).find(".productTitle").html(datas[i]["title"]);
                $(unit).find(".orderID").html(datas[i]["recordNO"]);
                $(unit).find(".buyer").attr("id", datas[i]["buyerID"]);
                $(unit).find(".buyer").html(datas[i]["buyerName"]);
                $(unit).find(".people").html(datas[i]["people"]);

                var playDate = datas[i]["startDate"] + " ~ " + datas[i]["endDate"];

                console.log(datas[i]['buyerID']);

                $(unit).find(".playDate").html(playDate);
                $(unit).find(".price").html(datas[i]["price"]);

                //控制按鈕
                var control_btns = $(unit).find(".orderControl-btn");
                $(control_btns).attr("orderID", datas[i]["recordNO"]);
                $(control_btns).attr("buyerID", datas[i]["buyerID"]);
                //檢查過期
                var start_date = new Date(datas[i]["startDate"]);
                if (start_date < Date.now()) {
                    var accept_btn = $(unit).find(".orderAccept-btn");
                    $(accept_btn).addClass("btn-c");
                    $(accept_btn).removeClass("orderAccept-btn");
                    $(accept_btn).html("過期");
                }
                //已接受
                if (datas[i]["state"] == 2) {
                    $(control_btns).html("已接受");
                    //$(control_btns).removeClass("orderAccept-btn");
                    //$(unit).find(".cancel-btn").addClass("hidden");
                }

                //縮圖:
                var img_src = "../upload/productThumbIMG/" + datas[i]["photo"];
                $(unit).find(".thumb").attr("src", img_src);

                console.log(unit);
                $("#orderRecord").append(unit);
            }
        },
        error: function (err) {

        }
    });
}

//購物車:
function ajax_get_cart_Data() {
    $("#cart-container").empty();
    $.ajax({
        url: "../PHP/getProfileData.php",
        type: "post",
        data: {
            'dataType': 'cart'
        },
        success: function (result) {
            var datas = JSON.parse(result);
            console.log(datas);

            //var sellerID = GetURLParameter("id");

            //產生資料:
            for (var i = 0; i < datas.length; i++) {
                var unit = $($("#cart-templet").html()).clone();
                //設定
                var href = "./productView.php?id=" + datas[i]["productID"] + "&seller=" + datas[i]["sellerID"];
                //$(unit).attr("pid", data[0]);
                $(unit).attr("id", datas[i]["recordNO"]);
                $(unit).find(".productTitle").attr("href", href);
                $(unit).find(".productTitle").html(datas[i]["title"]);
                $(unit).find(".orderID").html(datas[i]["recordNO"]);
                $(unit).find(".seller").attr("id", datas[i]["sellerID"]);
                $(unit).find(".seller").html(datas[i]["sellerName"]);
                $(unit).find(".people").html(datas[i]["people"]);

                var playDate = datas[i]["startDate"] + " ~ " + datas[i]["endDate"];

                $(unit).find(".playDate").html(playDate);
                $(unit).find(".price").html(datas[i]["price"]);

                //控制按鈕
                var control_btns = $(unit).find(".orderControl-btn");
                $(control_btns).attr("orderID", datas[i]["recordNO"]);
                $(control_btns).attr("buyerID", datas[i]["buyerID"]);
                $(control_btns).attr("sellerID", datas[i]["sellerID"]);
                //檢查過期
                var start_date = new Date(datas[i]["startDate"]);
                if (start_date < Date.now()) {
                    //var accept_btn = $(unit).find(".orderAccept-btn");
                    //$(accept_btn).addClass("btn-c");
                    //$(accept_btn).removeClass("orderAccept-btn");
                    $(unit).find(".state").html("已過期");
                }

                //告知狀態:
                console.log(datas[i]["state"]);
                if (datas[i]["state"] == 1) {
                    $(unit).find(".state").html("等待賣家回應");
                    $(unit).find(".confirmOrder-btn").remove();
                    console.log($(unit).find(".confirmOrder-btn"));

                } else if (datas[i]["state"] == 2) {
                    $(unit).find(".state").html("已接受");
                    $(control_btns).html("已接受");
                }

                //縮圖:
                var img_src = "../upload/productThumbIMG/" + datas[i]["photo"];
                $(unit).find(".thumb").attr("src", img_src);

                $("#cart-container").append(unit);
            }
        },
        error: function (err) {

        }
    });
}

//遊客:已完成的行程
function ajax_get_doneProduct_Data() {
    return new Promise(function (reslove, reject) {
        $("#doneProduct").empty();
        $.ajax({
            url: "../PHP/getProfileData.php",
            type: "post",
            data: {
                'dataType': 'doneProduct'
            },
            success: function (result) {
                console.log(result);
                var datas = JSON.parse(result);
                console.log(datas.length);
                console.log(datas);

                //產生資料:
                for (var i = 0; i < datas.length; i++) {
                    var unit = $($("#doneProduct-templet").html()).clone();
                    //設定
                    var href = "./productView.php?id=" + datas[i]["productID"] + "&seller=" + datas[i]["sellerID"];
                    //$(unit).attr("pid", data[0]);
                    $(unit).attr("id", datas[i]["recordNO"]);
                    $(unit).find(".productTitle").attr("href", href);
                    $(unit).find(".productTitle").html(datas[i]["title"]);
                    $(unit).find(".orderiD").html(datas[i]["recordNO"]);
                    $(unit).find(".seller").attr("id", datas[i]["sellerID"]);
                    $(unit).find(".seller").html(datas[i]["sellerName"]);
                    $(unit).find(".people").html(datas[i]["people"]);

                    var playDate = datas[i]["startDate"] + " ~ " + datas[i]["endDate"];

                    $(unit).find(".playDate").html(playDate);
                    $(unit).find(".price").html(datas[i]["price"]);

                    var control_btns = $(unit).find(".orderControl-btn");
                    if (datas[i]["state"] == 2) {// 2=賣家已接受
                        //控制按鈕
                        $(control_btns).attr("orderid", datas[i]["recordNO"]);
                        $(control_btns).attr("buyerID", datas[i]["buyerID"]);
                        $(control_btns).attr("productid", datas[i]["productID"]);
                    }
                    else {
                        $(control_btns).html("已完成");
                        var comment_t = $(unit).find(".comment");
                        $(comment_t).prop('readonly', true);
                        $(comment_t).html(" ");

                        //移除可控制星數:
                        $(unit).find(".star-container").remove();
                    }
                    /*
                    //告知狀態:
                    if (datas[i]["state"] == 0) {
                        $(unit).find(".state").html("等待賣家回應");
                    } else if (datas[i]["state"] == 1) {
                        $(unit).find(".state").html("已接受");
                        $(control_btns).html("已接受");
                    }*/

                    //縮圖:
                    var img_src = "../upload/productThumbIMG/" + datas[i]["photo"];
                    $(unit).find(".thumb").attr("src", img_src);

                    $("#doneProduct").append(unit);
                }
                reslove();

            },
            error: function (err) {

            }
        });
    });
}

//嚮導:已完成的行程
function ajax_get_doneOrder_Data() {
    return new Promise(function (reslove, reject) {
        $("#doneProduct").empty();
        $.ajax({
            url: "../PHP/getProfileData.php",
            type: "post",
            data: {
                'dataType': 'doneOrder'
            },
            success: function (result) {
                console.log(result);
                var datas = JSON.parse(result);
                console.log(datas.length);
                console.log(datas);

                //產生資料:
                for (var i = 0; i < datas.length; i++) {
                    var unit = $($("#doneOrder-templet").html()).clone();
                    //設定
                    var href = "./productView.php?id=" + datas[i]["productID"] + "&seller=" + datas[i]["buyerID"];
                    //$(unit).attr("pid", data[0]);
                    $(unit).attr("id", datas[i]["recordNO"]);
                    $(unit).find(".productTitle").attr("href", href);
                    $(unit).find(".productTitle").html(datas[i]["title"]);
                    $(unit).find(".orderiD").html(datas[i]["recordNO"]);
                    $(unit).find(".seller").attr("id", datas[i]["buyerID"]);
                    $(unit).find(".seller").html(datas[i]["buyerName"]);
                    $(unit).find(".people").html(datas[i]["people"]);

                    var playDate = datas[i]["startDate"] + " ~ " + datas[i]["endDate"];

                    $(unit).find(".playDate").html(playDate);
                    $(unit).find(".price").html(datas[i]["price"]);

                    //買家評價資訊
                    $(unit).find(".buyerComment").val(datas[i]["comment"]);
                    var b_s_src = "../img/icon/star" + Math.floor(datas[i]["rate"]) + ".png"
                    $(unit).find(".buyer-ratingStar").attr("src", b_s_src);

                    var control_btns = $(unit).find(".orderControl-btn");
                    if (datas[i]["state"] == 3) {// 3=買家已完成留言

                        $(unit).find(".ratingStar").remove();
                        //控制按鈕
                        $(control_btns).attr("orderid", datas[i]["recordNO"]);
                        $(control_btns).attr("buyerID", datas[i]["buyerID"]);
                        $(control_btns).attr("productid", datas[i]["productID"]);
                    }
                    else {
                        $(control_btns).html("已完成");
                        var comment_t = $(unit).find(".comment");
                        $(comment_t).prop('readonly', true);
                        $(comment_t).html(datas[i]["Reply"]);
                        $(unit).find(".ratingStar").attr("src", "../img/icon/star" + datas[i]["Reply_rate"] + ".png");

                        //移除可控制星數:
                        $(unit).find(".star-container").remove();
                    }
                    //縮圖:
                    var img_src = "../upload/productThumbIMG/" + datas[i]["photo"];
                    $(unit).find(".thumb").attr("src", img_src);

                    $("#doneOrder").append(unit);
                }
                reslove();

            },
            error: function (err) {

            }
        });
    });
}

//遊客:取得產品星級+評論
function ajax_get_doneProduct_review_Data() {
    $("#doneProduct .infoWhiteBoard").each(function () {
        var container = $(this);
        var order_no = $(this).attr("id");
        var rows = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'productreview',
                'row_name_post': rows,
                'where_post': 'recordNO',
                'key_post': order_no,
                'doEcho': true,
            },
            success: function (result) {
                console.log(result);
                var datas = JSON.parse(result);
                console.log(datas);
                //已完成:
                if (datas.length > 0) {
                    $(container).find(".comment").html(datas[0]["comment"]);
                    var rate_src = "../img/icon/star" + datas[0]["rate"] + ".png";
                    $(container).find(".ratingStar").attr("src", rate_src);
                }
                //未完成:
                else {
                    $(container).find(".ratingStar").remove();
                }
            }
        });
    });
}

//收藏資料
function ajax_get_collect_data() {
    return new Promise(function (resolve, reject) {
        $(".boxUnitContainer").empty();
        $.ajax({
            url: "../PHP/getProfileData.php",
            type: "post",
            data: {
                'dataType': 'collect'
            },
            success: function (result) {
                console.log(result);
                resolve(result);
                var datas = JSON.parse(result);

                for (var i = 0; i < datas.length; i++) {
                    var unit = $($("#boxUnit-Product-templet").html()).clone();

                    var imgSrc = upload_thumpPhoto + datas[i]["photo"];
                    $(unit).attr("id", datas[i]['pid']);
                    var href="./productView.php?id="+datas[i]["pid"]+"&seller="+datas[i]['sellerID'];
                    $(unit).attr("href",href);
                    unit.find(".thumb").attr("src", imgSrc);
                    unit.find(".boxTitle").html(datas[i]["title"]);
                    //unit.find(".ordernumber").html(datas[i]["ordernumber"]);
                    //unit.find(".reviewnumber").html(datas[i]["reviewnumber"]);
                    unit.find(".info").html(datas[i]["info"]);
                    unit.find(".price").html("$" + datas[i]["price"]);
                    unit.find(".collect-btn").attr("id", datas[i]['pid']);

                    $(".boxUnitContainer").append(unit);
                    console.log(unit);
                }

            },
            error: function (err) {
                reject(err);
            }
        });
    });
}


//取的資料筆數:
function getCountData(tbname, rowname, where, key, element) {
    $.ajax({
        url: "../PHP/getProfileData.php",
        type: "post",
        data: {
            'func_name': 'countData',
            'tb_name_post': tbname,
            'row_name_post': rowname,
            'where_post': where,
            'key_post': key
        },
        success: function (result) {
            element.html(result);
        },
        error: function () { }
    });

}