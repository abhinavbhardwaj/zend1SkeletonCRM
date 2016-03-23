$.validator.addMethod("emailValidate", function (value) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,3})$/;
    return reg.test(value);
}, 'Please enter a valid email address');

$.validator.addMethod("validateHtml", function (value) {
    return !value.match(/([\<])([^\>]{1,})*([\>])/i);
}, 'HTML tags are not allowed');

$.validator.addMethod("checkUserEmail", function (val, element) {
    var isSuccess = false;
    var idValues = element.id;
    $.ajax({
        type: "post",
        async: false,
        url: SITE_URL + 'auth/check-user-email',
        data: {emailid: val},
        dataType: 'json',
        success: function (response, textStatus, jqXHR) {
            //Assuming you return true in server reply
            if (response.status && idValues != 'emailIdForgot') {
                isSuccess = true;
            } else if (!response.status && idValues == 'emailIdForgot') {
                isSuccess = true;
            } else {
                if (idValues == 'emailIdForgot') {
                    $.validator.messages.checkUserEmail = 'Email address does not exist';
                } else {
                    $.validator.messages.checkUserEmail = 'Email address already exist';
                }
                //return response.message;
                //Invalid Username
                isSuccess = false;
            }
        }
    });
    return isSuccess;
}, 'Email address not valid');
/****************end *****************/

function pageUp() {
    $('html, body').animate({scrollTop: 0}, 'slow');
}
function randomBetween(min, max) {
    if (min < 0) {
        var num = Math.floor(min + Math.random() * (Math.abs(min) + max));
    } else {
        var num = Math.floor(min + Math.random() * max);
    }
    if (num.length == 1) {
        return '0' + num;
    } else {
        return num;
    }
}


$.validator.addMethod("kidNameValidate", function (value, element) {
    var reg = /^[a-zA-Z\.\ ]+$/;
    var isSuccess = false;
    var message;
    var error = false;
    if (!reg.test(value) && value != '') {
        message = "Only alphabets with . and space are allowed";
        error = true;
    }
    if (!error) {
        isSuccess = true;
        message = '';
    }
    $.validator.messages.kidNameValidate = message;
    return isSuccess;
}, "Only alphabets with . and space are allowed");

$.validator.addMethod("validateTwoSpaceName",
        function (value, element) {
            return !value.match(/\s{2,}/g, ' ');
        }, 'Two consecutive space are not allowed');

/**
 * @desc Function to validate email and password on login
 * @param 
 * @author suman khatri on October 10 2014
 * @return result
 */
function validateEmail(email) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,3})$/;
    return reg.test(email);
}

$.validator.addMethod("checkValidEmailPassword", function (val, element) {
    if ($('#emailIdLogin').val() == '' || $('#password').val() == '') {
        return true;
    }

    //alert('emailIdLogin :'+ $('#emailIdLogin').val()+ 'passWord:'+ $('#passWord').val());

    //var isSuccess = false;

    var rememberMe = 0;
    if ($('#remember_me').is(":checked")) {
        rememberMe = 1;
    }
    $.ajax({
        type: "post",
        async: false,
        url: SITE_URL + 'auth/validate-login/',
        data: {'emailIdLogin': $('#emailIdLogin').val(), 'passWord': $('#passWord').val(), 'rememberMe': rememberMe},
        dataType: 'json',
        success: function (response, textStatus, jqXHR) {
            //Assuming you return true in server reply
            if (response.status == 'success') {
                isSuccess = true;
            } else {
                $.validator.messages.checkValidEmailPassword = response.message;
                isSuccess = false;
            }
        }
    });
    return isSuccess;
}, 'Invalid email or password');

/**
 * @desc Function to validatepassword
 * @param 
 * @author suman khatri on October 10 2014
 * @return result
 */
$.validator.addMethod("validatePassword",
        function (value, element) {
            var space = value.indexOf(' ');
            if (space >= 0) {
                return false;
            } else {
                return true;
            }
        }, 'Blank spaces are not allowed in password');

/**
 * @desc Function to validate pin number
 * @param 
 * @author suman khatri on October 10 2014
 * @return result
 */
$.validator.addMethod("validatePinNumber",
        function (value, element) {
            var reg = /^([0-9])+$/;
            return reg.test(value);
        }, 'Only numbers are allowed in pin munber');

/*************add method with jquery to check user name already exist or not
 * usig ajax for validate server side validation
 * 
 */
$.validator.addMethod("checkUserName", function (val, element) {
    var idValues = element.id
    var userId = $('#userId').val();

    var isSuccess = false;
    $.ajax({
        type: "post",
        async: false,
        url: SITE_URL + 'index/checkusername',
        data: {'username': val, 'userId': userId},
        dataType: 'json',
        error: function (jqXHR, textStatus, errorThrown) {
            //alert("some error occured while submitting the form");
            return false;
        },
        success: function (response, textStatus, jqXHR) {
            //Assuming you return true in server reply
            if (response.status == true) {
                isSuccess = true;

            } else {
                $.validator.messages.checkUserName = response.message;
                //return response.message;
                //Invalid Username
                isSuccess = false;
            }
        }
    });
    return isSuccess;
}, 'Username allready exist');


/**
 * Used in my account page
 */
/*********add method into jquery validation for check current pasword allready exist or not
 * Usingg ajax method
 * *********/
$.validator.addMethod("checkforgetpassword", function (val, element) {
    var isSuccess = false;

    $.ajax({
        type: "post",
        async: false,
        url: SITE_URL + 'myaccount/checkforgetpassword',
        data: {'oldpassword': val, 'passwordTextId': element.id},
        dataType: 'json',
        error: function (jqXHR, textStatus, errorThrown) {
            //alert("some error occured while submitting the form");
            return false;
        },
        success: function (response, textStatus, jqXHR) {
            //Assuming you return true in server reply
            if (response.status == 1) {
                isSuccess = true;

            } else {
                $.validator.messages.checkforgetpassword = response.message;
                //return response.message;
                //Invalid Username
                isSuccess = false;
            }
        }
    });
    return isSuccess;
}, 'Old password does not exist');
/*********End checkforgetpassword**************************/

$.validator.addMethod("doNotMatch", function (value) {
    var passVal = $('#oldPassword').val();
    return passVal == value ? false : true;
}, 'New password cannot be same as current password');

/* end doNotMatch */

$.validator.addMethod("checkChildName", function (val) {
    var isSuccess = false;

    var childId = $('#childId').val();

    /*if(childId != 'undefined' && childId != ''){
     var childId = $.base64.encode(childId);
     }*/

    if (childId == undefined) {
        childId = "";
    }

    $.ajax({
        type: "post",
        async: false,
        url: SITE_URL + 'child/validate-child-name',
        data: {'childname': val, 'childId': childId},
        dataType: 'json',
        error: function () {
            return false;
        },
        success: function (response) {
            isSuccess = true;
            if (response.status != 1) {
                $.validator.messages.checkforgetpassword = response.message;
                isSuccess = false;
            }
        }
    });
    return isSuccess;
}, 'Child\'s display name already exists');
/* End check kid name */

/**************add method into jquery validation to calculate age**************/
$.validator.addMethod("checkCgpaFraction", function (value, element) {
    var reg = /^[0-9]{1}?$/;
    var isSuccess = false;
    var message;
    var error = false;
    if (!reg.test(value) && value != '') {
        message = "Please enter fraction between 0 to 9";
        error = true;
    }
    if (!error) {
        isSuccess = true;
        message = '';
    }
    $.validator.messages.checkCgpaFraction = message;
    return isSuccess;
}, 'Invalid Fraction');

/**************add method into jquery validation to calculate age**************/
$.validator.addMethod("checkCgPa", function (value, element) {
    var reg = /^[0-9]{1,2}$/;
    var cgPa = value;
    var isSuccess = false;
    var message;
    var error = false;
    if (cgPa == '.') {
        message = "Please enter valid GPA";
        error = true;
    } else if (cgPa != '') {
        if (!reg.test(cgPa)) {
            message = "Please enter valid GPA";
            error = true;
        }
        if (cgPa < 1) {
            message = "Please enter valid GPA of value 1 or between 1-10";
            error = true;
        }
        if (cgPa <= 0) {
            message = "GPA must be greater then 0";
            error = true;
        }
    }
    if (!error) {
        isSuccess = true;
        message = '';
    }
    $.validator.messages.checkCgPa = message;
    return isSuccess;
}, 'Invalid GPA');
/********************end checkCgPa*****************/

$.validator.addMethod("checkPoints", function (value, element) {
    var isSuccess = false;
    var message;
    var regW = /^([0-9])+$/;

    if (value === '') {
        isSuccess = true;
    } else if (!regW.test(value)) {
        message = "Only numbers are allowed";
    } else if (value <= 0) {
        message = "Zero or negative values are not allowed";
    } else if (value.length > 8) {
        message = "Only 8 digits are allowed";
    } else {
        isSuccess = true;
    }
    $.validator.messages.checkPoints = message;
    return isSuccess;
}, 'points are not valid');

/*
 * Function to validate date of birth on manage kud page 
 */
$.validator.addMethod("validateDob", function (value, element) {
    if (value === '') {
        return true;
    }

    var regex = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!regex.test(value)) {
        $.validator.messages.validateDob = 'Please enter date in MM/DD/YYYY format';
        return false;
    }

    var bits = value.split('/');
    var y = bits[2], d = bits[1], m = bits[0];

    var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    // If evenly divisible by 4 and not evenly divisible by 100,
    // or is evenly divisible by 400, then a leap year
    if ((!(y % 4) && y % 100) || !(y % 400)) {
        daysInMonth[1] = 29;
    }

    if (!(d <= daysInMonth[--m])) {
        $.validator.messages.validateDob = 'Please enter a valid date';
        return false;
    }

    //Create date from input value
    var inputDate = new Date(value);

    //Get today's date
    var todaysDate = new Date();

    //call setHours to take the time out of the comparison
    if (inputDate.setHours(0, 0, 0, 0) > todaysDate.setHours(0, 0, 0, 0))
    {
        $.validator.messages.validateDob = 'Date of birth cannot be in future';
        return false;
    }

    return true;
}, 'Please enter a valid date');

// method to validate phone number
$.validator.addMethod("validatePhone", function (value, element) {
    var reg = /^[0-9]+$/;
    return reg.test(value);
}, 'Please enter valid mobile number');

/* PASSWORD REGULAR EXPRESSION VALIDATION */
$.validator.addMethod("regExCheck",
        function (value, element) {
            //return /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,16}$/.test(value);
            return /^(?=.*?[A-Za-z])(?=(.*[\d]))(?=(.*[\W]))(?!.*\s).{8,16}$/.test(value);
}, 'Password must have at least one number, one special character and one alphabet.');

$.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "No space allowed in child's display name");

/*
 * Function to validate GPA. It must not be greater than 10 and smaller then 0 
 */

function cgpavalidateValue(currentTab, nextTab)
{
    var cgpaV = $("#" + currentTab).val();
    var cgpaF = $("#" + nextTab).val();
    if (cgpaV >= 10) {
        $("#" + currentTab).val(10);
        $("#" + nextTab).val(0);
        $("#" + nextTab).attr('disabled', 'disabled');
    } else {
        $("#" + nextTab).removeAttr('disabled');
        if ($("#" + nextTab).val() == '' || $("#" + nextTab).val() == 0) {
            $("#" + nextTab).val("");
            $("#" + nextTab).focus();
        }
    }
}

function validateWeeklyGoal(weekValue) {
    var reg = /^([0-9])+$/;
    return reg.test(weekValue);
}

/***** subject list validation for learning customization page **************/
$.validator.addMethod("subjectsSelected",
        function (value, element) {
            var subjectCount = $('#subjectList').attr('subjectValue');
            if (subjectCount == 0)
                return false;
            else
                return true;
        }, 'Please select subjects');

/************** add method into jquery validation to validate weekly Goal**************/
$.validator.addMethod("checkGoalValue", function (value, element) {
    var isSuccess = false;
    var message = '';
    var regW = /^([0-9])+$/;

    value = $('#weeklyGoal').val();

    if (value == '') {
        isSuccess = true;
    } else if (!regW.test(value)) {
        message = "Only numbers are allowed in weekly goal";
    } else if (value <= 0) {
        message = "Zero or negative values are not allowed in weekly goal";
    } else if (value.length > 8) {
        message = "Only 8 digits are allowed in weekly goal";
    } else {
        isSuccess = true;
    }

    $.validator.messages.checkGoalValue = message;
    return isSuccess;
}, 'Weekly goal not valid');
/********************end checkGoalValue*****************/

/************************* function to validate child's learning customization info and save information *******************************/

$.validator.addMethod("checkSafeMobileNumber", function (val, element) {
    var isSuccess = false;
    var safeEditId = $('#safeEditId').val();
    var code = $('#code').val();
    $.ajax({
        type: "post",
        async: false,
        url: SITE_URL + 'safe-number/validate-number',
        data: {'mobilenumber': val, 'safeEditId': safeEditId, 'code': code},
        dataType: 'json',
        error: function () {
            return false;
        },
        success: function (response) {
            if (response.status == 1) {
                isSuccess = true;
            } else {
                $.validator.messages.checkSafeMobileNumber = response.message;
                isSuccess = false;
            }
        }
    });
    return isSuccess;
}, 'Mobile format not right');

$.validator.addMethod("validateSafeName", function (val, element) {
    var safeEditId = $('#safeEditId').val();
    var isSuccess = false;
    $.ajax({
        type: "post",
        async: false,
        url: SITE_URL + 'safe-number/validate-name',
        data: {'safename': val, 'safeEditId': safeEditId},
        dataType: 'json',
        error: function () {
            return false;
        },
        success: function (response) {
            if (response.status == 1) {
                isSuccess = true;
            } else {
                $.validator.messages.validateSafeName = response.message;
                isSuccess = false;
            }
        }
    });
    return isSuccess;
}, 'Safe Name does not exit');


/*
 * function to validate password and save parent account information
 * @param formId
 */
function validatPassProfile(formId) {

    var parentType = $('input[name=parentType]:checked').val();
    var data = $("#" + formId).serialize();    
    data += '&parentType=' + parentType;
    
    var url = SITE_URL + 'myaccount/saveparentinfo';
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: function (data) {
            var response = jQuery.parseJSON(data);
            var responseMessage = response.message;
            var responseStatus = response.status;
            
            if (response.returnName != "" && response.returnName != undefined) {
                $(".username >.bold").text(response.returnName);
                $(".page_tital > h3").text(response.returnName);
                
                $("#parent_name").html(response.returnName);
            }
            
            $('#confirmModal').modal('hide'); 
            showMessage(responseStatus, responseMessage);
        }
    });

}

function changePassword(formId) {

    var data = $("#" + formId).serialize();

    var url = SITE_URL + 'myaccount/changepassword';
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: function (data) {
            var response = jQuery.parseJSON(data);
            var responseMessage = response.message;
            var responseStatus = response.status;
            
            if(responseStatus == 'success') {
                window.location = SITE_URL;
            }else{
                $("#"+formId)[0].reset();
                showMessage(responseStatus, responseMessage);                
            }
        }
    });
}
