$(document).ready(function () {

    $(".authLinks").click(function (e) {

        e.stopPropagation();
        e.stopImmediatePropagation();

        /* ALWAYS SHOW REGISTER TEXT */
        $("#registerText").css('display', 'block');

        /* ALWAYS HIDE THE ERROR/SUCCESS MESSAGES */
        $(".alert-close").parent().remove();

        /* RESET ALL FORM VALUES */
        $('form').each(function () {
            this.reset();
        });

        // initially remove all error messages
        $('div.error_msg').removeClass('error_msg');

        switch (this.id) {
            case 'forgotlink':
                /* RESET LOGIN AND REGISTRATION FORM VALIDATION AND HIDE THEM */
                userLoginObj.resetForm();
                userRegObj.resetForm();
                $("#login-div").css('display', 'none');
                $("#register-div").css('display', 'none');
                /* SHOW FORGOT PASSWORD FORM */
                $("#forgot-div").css('display', 'block');
                document.title = "Forgot Password | Finny";
                break;
            case 'loginlink':
                /* RESET FORGOT PASSWORD AND REGISTRATION FORM AND HIDE THEM */
                forgotPassObj.resetForm();
                userRegObj.resetForm();
                $("#forgot-div").css('display', 'none');
                $("#register-div").css('display', 'none');
                /* SHOW LOGIN FORM */
                $("#login-div").css('display', 'block');
                document.title = "Login | Finny";
                break;
            case 'registerlink':
                /* RESET LOGIN AND FORGOT PASSWORD FORM AND HIDE THEM */
                userLoginObj.resetForm();
                forgotPassObj.resetForm();
                $("#forgot-div").css('display', 'none');
                $("#login-div").css('display', 'none');
                /* HIDE REGISTER TEXT */
                $("#registerText").css('display', 'none');
                /* SHOW REGISTER FORM */
                $("#register-div").css('display', 'block');
                document.title = "Registration | Finny";
                break;
        }

    });

    /**
     * COMMON FUNCTION TO CLOSE THE MESSAGES ALERT
     */
    $(".alert-close").click(function (e) {

        e.stopPropagation();
        e.stopImmediatePropagation();
        //$(this).parent().remove();
        $(this).parent().slideUp("normal", function () {
            $(this).remove();
        });
    });

    /* SCRIPT TO CHANGE BACKGROUND IMAGES IN LOGIN TEMPLATE */
    var Image = ["login-bg1.jpg", "login-bg2.jpg", 'login-bg3.jpg'];
    var img = Image[Math.floor(Math.random() * Image.length)];
    //$('.loginBody').css('background-image', 'url("' + SITE_URL + "images/" + img + '")');
    //$('.loginBody').show("slow");



});

function showForgotForm() {

    /* RESET ALL FORM VALUES */
    $('form').each(function () {
        this.reset();
    });

    // initially remove all error messages
    $('div.error_msg').removeClass('error_msg');
    $("#login-div").css('display', 'none');
    $("#register-div").css('display', 'none');
    /* SHOW FORGOT PASSWORD FORM */
    $("#forgot-div").css('display', 'block');

}