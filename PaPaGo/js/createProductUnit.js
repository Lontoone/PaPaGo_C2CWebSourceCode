var current_page = 0;//目前頁數
const page_load_amount = 10; //一次讀的數量
var max_page = 0; //最大頁數
ajax_single_common_request("countData", "product", "ID", "", "").then(function (res) {
    max_page = res / page_load_amount;
    console.log("最大頁數" + max_page);
});

$(document).ready(function () {
    console.log(window.location.pathname);
    if (window.location.pathname.includes("index") ||
        window.location.pathname.includes("searchResult")) {
        createPorductUnit(current_page);
    }
});
//展示商品:
//createPorductUnit(current_page);

function createPorductUnit(limit_start) {
    console.log(limit_start + "~" + (page_load_amount + limit_start));
    //取的商品資料
    getProductData(limit_start, page_load_amount).then(fillFullData);
}

function getProductData(limit_start, limit_n, input = "", order_by = "") {
    return new Promise(function (resolve, reject) {
        if (typeof order_by === "undefined" || order_by == "") {
            var order_by = GetURLParameter("order");
        }
        if (typeof input === "undefined" || input == "") {
            input = GetURLParameter("search");
        }
        console.log(order_by);
        console.log(input);

        $.ajax({
            url: "../PHP/searchControl.php",
            type: "post",
            data: {
                'dataType': 'product-search',
                'limit_start': limit_start,
                'limit_n': limit_n,
                'order_by': order_by,
                'input': input
            },
            success: function (result) {
                console.log("產生首頁商品資料");
                //產生Unit:
                var data = JSON.parse(result);
                console.log(data);
                for (var i = 0; i < data.length; i++) {

                    var unit = $($("#productPreviewUnit-temlplate").html()).clone();
                    //設定
                    var href = "./productView.php?id=" + data[i]["ID"] + "&seller=" + data[i]["sellerID"];
                    $(unit).attr("href", href);
                    $(unit).attr("id", data[i]["ID"]);
                    $(unit).attr("sid", data[i]["sellerID"]);
                    $(unit).find(".productTitle").html(data[i]["title"]);
                    $(unit).find(".dercribe").html(data[i]["info"]);
                    $(unit).find(".price").html("$" + data[i]["price"]);

                    //$(".mainSection").append(unit);
                    $("#productDisplay").append(unit);
                }
                resolve(result);
            },
            error: function (err) {
                reject(err);
            }

        });
    })
}

//補齊資料
function fillFullData() {
    $(".productPreviewUnit").each(function () {
        var pid = $(this).attr("id");
        var sellerid = "";
        if (pid != "") {
            sellerid = pid.split("-")[1];
        }

        getProductThumbnail(pid);
        //取得收藏數量
        getCollectNum(pid);

        //取得交易與評價次數
        getOrderNumber(pid);
        getProductReviewNumber(pid);

        //取得評價星數
        getReviewRate(pid);
        getSellerReviewRate(pid, $(this).attr("sid"));

        //取得賣家name
        getSellerName(pid, sellerid);
    });
}
//讀取縮圖
function getProductThumbnail(pid) {
    //console.log(data);
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData',
                'tb_name_post': 'productthumbnail',
                'row_name_post': 'photo',
                'where_post': 'productID',
                'key_post': pid,
                'split_by_post': '',
            },
            success: function (result) {
                var imgSrc = "../upload/productThumbIMG/" + result;
                if (result != "") {
                    $("#" + pid + " .thumb").attr("src", imgSrc);
                }
            },

            error: function (err) {
                reject(err);// Reject the promise and go to catch()
            }
        });
    })
}


function getSellerName(pid, sellerID) {
    $.ajax({
        url: "../PHP/common.php",
        type: "post",
        data: {
            'func_name': 'getData',
            'tb_name_post': 'member',
            'row_name_post': 'name',
            'where_post': 'ID',
            'key_post': sellerID,
            'doEcho': true,
        },
        success: function (result) {
            $("#" + pid + " .sellerInfo p").html(result);
        },

        error: function (err) {
        }
    });
}
//取得評價數量
function getProductReviewNumber(pid) {
    $.ajax({
        url: "../PHP/common.php",
        type: "post",
        data: {
            'func_name': 'countData',
            'tb_name_post': 'productreview',
            'row_name_post': 'comment',
            'where_post': 'productID',
            'key_post': pid,
            'doEcho': true,
        },
        success: function (result) {
            $("#" + pid + " .commentnumber").html(result + "則評論");
        },

        error: function (err) {
        }
    });
}

//取得交易次數:
function getOrderNumber(pid) {
    $.ajax({
        url: "../PHP/common.php",
        type: "post",
        data: {
            'func_name': 'countData',
            'tb_name_post': 'orderrecord',
            'row_name_post': 'buyerID',
            'where_post': 'productID',
            'key_post': pid,
            'doEcho': true,
        },
        success: function (result) {
            $("#" + pid + " .ordernumber").html(result + "次成交");
        },

        error: function (err) {
        }
    });
}

//取得收藏
function getCollectNum(pid) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'countData',
                'tb_name_post': 'collect',
                'row_name_post': 'userID',
                'where_post': 'productID',
                'key_post': pid,
                'doEcho': true,
            },
            success: function (result) {
                //if (result == "") { result = 0; }
                $("#" + pid + " .saveInfo span").html(result + "人收藏");

            },

            error: function (err) {
            }
        });
    })
}


//換頁:
$("#prevpage-btn").click(function () {
    if (current_page > 0) {
        current_page -= 1;
        console.log(current_page * page_load_amount);
        $("#productDisplay").empty();
        createPorductUnit(current_page * page_load_amount);
    }
    else {
        alert("已經是第一頁了");
    }
});

$("#nextpage-btn").click(function () {
    if (current_page < max_page-1) {
        current_page += 1;
        console.log(current_page * page_load_amount);
        $("#productDisplay").empty();
        createPorductUnit(current_page * page_load_amount );
    }
    else {
        alert("已經是最後一頁了");
    }
});
