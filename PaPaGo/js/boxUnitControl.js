$(document).on("mouseover", ".boxUnit", function () {
    console.log("hi");
    $(this).find(".info").removeClass("transparent");

});
$(document).on("mouseleave", ".boxUnit", function () {
    console.log("hi");
    $(this).find(".info").addClass("transparent");

});

$(document).on("click", ".collect-btn",(function () {
    //紀錄至資料庫
    if ($(this).attr('src') == "../img/icon/heart_off.png") {
        $(this).attr('src', "../img/icon/heart_on.png");
    }
    else {
        $(this).attr('src', "../img/icon/heart_off.png");
    }
    $.ajax({
        url: "../PHP/addtoCollect.php",
        type: "post",
        data: {
            'pid': $(this).attr('id')
        },
        success: function (result) {
            console.log(result);
        },
        error: function (err) {
        }
    });
}));