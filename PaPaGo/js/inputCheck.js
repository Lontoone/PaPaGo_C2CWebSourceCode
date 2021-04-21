//檢查輸入
//檢查密碼
$(document).on('submit', "#signup_from", function () {
    var first_pw = $("#passwd_input").val();
    var sec_pw = $("#passwd_input_2").val();
    var email = $("#mail_input").val();
    if (!email.length) {
        alert("請輸入信箱!");
        return false;
    }
    if (first_pw != sec_pw) {
        alert("密碼不同!");
        return false;
    }
    if (first_pw.length < 7) {
        alert("密碼長度過短!");
        return false;
    }

    $.ajax({
        url: "../PHP/singup.php",
        type: "post",
        data: {
            'mail_input': email,
            'passwd_input':first_pw
        },
        success: function (result) {
           if(result=="1"){
               alert("信箱格式錯誤");
           }
           else if(result=="2"){
               alert("信箱重複");
           }
           else{
               //成功:
               alert("註冊成功");
               location.href='../index.php';
           }

        },
        error: function (err) {
        }
    });

    return false;
});