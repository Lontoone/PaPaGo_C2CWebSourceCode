//評分星星:
$(document).on("mouseover", ".star-container img", function () {
    var container = $(this).parent();
    var order = $(this).attr("o"); //目前順序
    console.log(container);

    $(container).find("img").each(function (index) {
        if (index < order) {
            $(this).attr("src", "../img/icon/star.png");
        } else {
            $(this).attr("src", "../img/icon/star_off.png");
        }
    });

});

$(document).on("click", ".star-container img", function () {
    var container = $(this).parent();
    var order = $(this).attr("o"); //目前順序
    $(container).attr("star", order);
});


$(document).on("mouseleave", ".star-container img", function () {
    //復原
    
    var container = $(this).parent();
    var order = $(container).attr("star");
    console.log(container);

    $(container).find("img").each(function (index) {
        if (index < order) {
            $(this).attr("src", "../img/icon/star.png");
        } else {
            $(this).attr("src", "../img/icon/star_off.png");
        }
    });

});
