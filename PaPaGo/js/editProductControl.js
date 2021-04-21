
//編輯商品: 報廢?
function do_edit_submit(pid,formdata) {
    //呼叫TEST
    formdata.append("pid", pid);
    formdata.append("action","edit");
    $.ajax({
        //url: '../PHP/editProduct.php',
        url: '../PHP/createNewProduct.php',
        type: 'POST',
        data: formdata,
        async: false,
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function (response) {
            console.log(response);
            alert(response);
        }
    });

}

//按下"編輯商品"=>自動加載資料:
$(document).on("click", ".allProduct-cell-edit_btn a", function () {
    var pid = $(this).attr("pid");

    //進入編輯page
    openTagPage("makeNewProduct");
    //在網址上加資訊
    var pageURL = $(location).attr("href");
    window.history.replaceState('', '', pageURL + "&pid=" + pid);

    //page標題:新增=>修改
    $("#makeNewProduct-title").html("修改行程");

    //載入該productID資料:
    getProductData(pid).then(function (datas) {
        //console.log(datas);
        //  標題
        $("#input-producttitle").val(datas[0]["title"]);

        //  人數限制
        $("#input-people").val(datas[0]["people"]);

        //  開放星期
        $(".week-selected").each(function (index, element) {
            //先全關掉
            $(element).removeClass("week-selected");
            $(element).addClass("week-unselected");
        });
        var weekSelect = datas[0]["weekSelect"].split(",");
        for (var i = 0; i < weekSelect.length; i++) {
            var week = weekSelect[i];
            $("#week-" + week).removeClass("week-unselected");
            $("#week-" + week).addClass("week-selected");
        }

        //  日程
        var days = datas[0]["days"]
        $("#input-duration").val(days);
        duration_input_change(days);

        //  介紹
        $("#input-productinfo").val(datas[0]["info"]);

        //  其他聲明
        $("#input-declear").val(datas[0]["other"]);

        //價錢
        $("#total-number").html(datas[0]["price"]);
    });

    //  縮圖
    get_productThumb_data(pid).then(function (datas) {
        var imgSrc = upload_thumpPhoto + datas[0]["photo"];
        console.log("縮圖 " + imgSrc);
        $(".thumbnail img").attr("src", imgSrc);
    });

    //  圖片群
    get_productPhotos(pid).then(function (data) {

        var gallery = $(".gallery");
        //清除之前資料:
        //gallery.empty(); 切換page就處理好了


        //移除defult
        $(gallery).find(".defult").addClass("hidden");

        for (var i = 0; i < data.length; i++) {
            if (typeof data[i]["photo"] === "undefined") { continue; }
            //clone圖片
            var pic = $($("#gallery-photobox-template").html()).clone();
            var imgSrc = "../upload/productIMG/" + data[i]["photo"];
            $(pic).find("img").attr("src", imgSrc);
            $(pic).attr("id", data[i]["photo"]);
            $(gallery).append(pic);
        }
    });


    //  區域
    get_product_region(pid).then(function (datas) {
        $("#country-select").val(datas[0]["country"]);
        checkCountry(datas[0]["country"]);
        $("#county-select").val(datas[0]["county"]);
    });


    //  日程 
    get_product_dayContent(pid).then(function (datas) {
        for (var i = 0; i < datas.length; i++) {
            var agneda = $("#agenda-d" + (i + 1));
            $(agneda).find(".productTitle").val(datas[i]["title"]);
        }
    });

    //  每日程活動
    get_product_dayActivity(pid).then(function (datas) {
        console.log("活動:" + datas + " " + datas.length);
        console.log(datas);
        for (var i = 0; i < datas.length; i++) {

            var agneda = $("#agenda-d" + datas[i]["day"]);

            //清空預設空欄位:
            if (datas[i]["sequence"] == 1) {
                agneda.find(".wrap").empty();
            }

            //產生項目
            var content = addDayActivity(agneda);
            $(content).find(".agenda-itme").val(datas[i]["title"]);
            $(content).find(".subText").val(datas[i]["content"]);

        }
    });


    //估價單
    get_product_billcontent(pid).then(function (datas) {

        var table_tbody = $(".price-table tbody");
        //清除之前資料:
        table_tbody.empty();

        for (var i = 0; i < datas.length; i++) {
            var tr = $($("#pricing-tr-input-template").html()).clone();
            console.log(tr);
            $(tr).find(".day-select").val(datas[i]["day"]);
            $(tr).find(".billcontent").val(datas[i]["content"]);
            $(tr).find(".price-input").val(datas[i]["price"]);
            $(tr).find(".unit-input").val(datas[i]["quantity"]);

            table_tbody.append(tr);
        }
        //TODO:[注意]網速慢可能導致 天數資料晚出現
        updateDaySelect_opt_num($("#input-duration").val());

        //補上最上面一個待輸入欄位:
        var tr = $($("#pricing-tr-input-template").html()).clone();
        table_tbody.prepend(tr);
    });
});


//product資料表資料
function getProductData(pid) {
    return new Promise(function (resolve, reject) {
        var row = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'product',
                'row_name_post': row,
                'where_post': 'ID',
                'key_post': pid,
                'doEcho': true
            },
            success: function (result) {
                var datas = JSON.parse(result);
                console.log(datas);
                resolve(datas);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//縮圖
function get_productThumb_data(pid) {
    return new Promise(function (resolve, reject) {
        var row = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'productthumbnail',
                'row_name_post': row,
                'where_post': 'productID',
                'key_post': pid,
                'doEcho': true
            },
            success: function (result) {
                var datas = JSON.parse(result);
                console.log(datas);
                resolve(datas);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//圖片
function get_productPhotos(pid) {
    return new Promise(function (resolve, reject) {
        var row = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'productimage',
                'row_name_post': row,
                'where_post': 'productID',
                'key_post': pid,
                'doEcho': true
            },
            success: function (result) {
                var datas = JSON.parse(result);
                console.log(datas);
                resolve(datas);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//日程:
function get_product_dayContent(pid) {
    return new Promise(function (resolve, reject) {
        var row = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'daycontent',
                'row_name_post': row,
                'where_post': 'productID',
                'key_post': pid,
                'order_by_post': 'day',
                'doEcho': true
            },
            success: function (result) {
                var datas = JSON.parse(result);
                resolve(datas);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//日程活動
function get_product_dayActivity(pid) {
    return new Promise(function (resolve, reject) {
        var row = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'activity',
                'row_name_post': row,
                'where_post': 'productID',
                'key_post': pid,
                'order_by_post': 'day,sequence',
                'doEcho': true
            },
            success: function (result) {
                var datas = JSON.parse(result);
                resolve(datas);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//估價單:
function get_product_billcontent(pid) {
    return new Promise(function (resolve, reject) {
        var row = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'productbillcontent',
                'row_name_post': row,
                'where_post': 'productID',
                'key_post': pid,
                'order_by_post': 'day',
                'doEcho': true
            },
            success: function (result) {
                var datas = JSON.parse(result);
                resolve(datas);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//區域:
function get_product_region(pid) {
    return new Promise(function (resolve, reject) {
        var row = ["*"];
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': 'getData_inRow_json',
                'tb_name_post': 'productregion',
                'row_name_post': row,
                'where_post': 'productID',
                'key_post': pid,
                'doEcho': true
            },
            success: function (result) {
                var datas = JSON.parse(result);
                resolve(datas);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}