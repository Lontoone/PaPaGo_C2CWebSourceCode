//常用folder PATH:
var upload_thumpPhoto = "../upload/productThumbIMG/";
var upload_productPic = "../upload/productIMG/";
var upload_avaPic = "../upload/avatars/";

//常用page
//var page_makeNewProduct="../page/productView.php";


//取的URL裡面的變數資料
function GetURLParameter(ParamName) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == ParamName) {
            return decodeURIComponent(sParameterName[1]);
        }
    }
}

//讀取上傳照片
function readURL(input, classname) {
    console.log(classname);
    if (input.files && input.files[0]) {

        var reader = new FileReader();

        reader.onload = function (e) {
            $(classname).attr('src', e.target.result);

            //$('.picUpload_btn').html(input.files[0].name);
        };

        reader.readAsDataURL(input.files[0]);

    } else {
        removeUpload();
    }
}

function urltoFile(url, filename, mimeType) {
    return (fetch(url)
        .then(function (res) { return res.arrayBuffer(); })
        .then(function (buf) { return new File([buf], filename, { type: mimeType }); })
    );
}

function ajax_single_common_request(func_name, tb, row, where, key, val, split_by, element) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/common.php",
            type: "post",
            data: {
                'func_name': func_name,
                'tb_name_post': tb,
                'row_name_post': row,
                'where_post': where,
                'key_post': key,
                'val_post': val,
                'split_by_post': split_by,
            },
            success: function (result) {
                console.log(result);
                /*
                if (typeof result !== "undefined") {
                    $(element).html(result);
                }*/
                resolve(result);
            },

            error: function (err) {
                reject(err);
            }
        });
    });
}

//捲動
$(document).ready(function(){
    $('a[href^="#"]').on('click',function (e) {
        e.preventDefault();

        var target = this.hash,
        $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 600, 'swing', function () {
            window.location.hash = target;
        });
    });
});


function replaceUrlParam(url, paramName, paramValue)
{
    if (paramValue == null) {
        paramValue = '';
    }
    var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
    if (url.search(pattern)>=0) {
        return url.replace(pattern,'$1' + paramValue + '$2');
    }
    url = url.replace(/[?#]$/,'');
    return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
}
//取得評價星數:
function getReviewRate(pid) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/getReviewRate.php",
            type: "post",
            data: {
                'dataType': 'productReviewRate',
                'pid': pid
            },
            success: function (result) {
                //console.log(result);
                result = Math.round(result);
                if (result == 0) { result = 5; }
                resolve(result);
                var src = "../img/icon/star" + result + ".png";
                $("#" + pid + " .ratingInfo img").attr("src", src);

            },

            error: function (err) {
            }
        });
    })
}
//取得賣家總體評價
function getSellerReviewRate(pid, sid) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: "../PHP/getReviewRate.php",
            type: "post",
            data: {
                'dataType': 'sellerReviewRate',
                'sid': sid
            },
            success: function (result) {
                console.log(result);
                result = Math.round(result);
                resolve(result);
                if (result == 0) { result = 5; }
                var src = "../img/icon/star" + result + ".png";
                $("#" + pid + " .sellerInfo img").attr("src", src);

            },

            error: function (err) {
            }
        });
    })
}