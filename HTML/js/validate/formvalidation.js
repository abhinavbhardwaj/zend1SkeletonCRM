//global variables to be accessed across other js files
var userLoginObj = forgotPassObj = userRegObj = '';

$(document).ready(function () {

    jQuery.validator.setDefaults({
        debug: true,
        success: "valid",
        highlight: function (element) {
            var elem = $(element);
            if (elem.hasClass("select2-offscreen")) {
                $("#s2id_" + elem.attr("id") + " a").addClass('error_msg');
            } else {
                elem.closest('.control-group').addClass('error_msg');
            }
        },
        unhighlight: function (element) {

            var elem = $(element);
            if (elem.hasClass("select2-offscreen")) {
                $("#s2id_" + elem.attr("id") + " a").removeClass('error_msg');
                $("#s2id_" + elem.attr("id") + " a").find('label.error').remove();
            } else {
                elem.closest('.control-group').removeClass('error_msg');
                elem.parent().find('label.error').remove();
            }
        },
        errorPlacement: function (error, element) {
            element.focus(function () {
                element.parent().find('label.error').remove();
                if ($(error).html()) {
                    $(this).after(error);
                    $(this).parent().find('label.error').show();
                }
            }).blur(function () {
                element.parent().find('label.error').remove();
            });
        },
        onfocusout: function (e) {
            this.element(e);
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    // apply jquery validation on login
    $("#userlogin").submit(function () {
        $("#passWord").rules('remove', "checkValidEmailPassword");
        if ($(this).valid()) {
            $("#passWord").rules('add', {checkValidEmailPassword: true});
        }
    });

    $("#passWord").keydown(function () {
        $(this).rules('remove', "checkValidEmailPassword");
    });

    userLoginObj = $("#userlogin").validate({
        rules: {
            emailIdLogin: {
                required: true,
                email: true
            },
            passWord: {
                required: true
            }
        },
        messages: {
            emailIdLogin: {
                required: "Please enter email",
                email: "Please enter valid email"
            },
            passWord: {
                required: "Please enter password",
            }
        }
    });
    // end


    $("#emailId").blur(function () {
        $(this).rules('add', {
            checkUserEmail: true,
        });
        $(this).valid();
    });

    $("#emailId").keydown(function () {
        $(this).rules('remove', "checkUserEmail");
    });

    $("#userRegistration").submit(function () {
        $("#emailId").rules('add', {
            checkUserEmail: true,
        });
    });

    userRegObj = $("#userRegistration").validate({
    	ignore: [], 
        rules: {
            firstName: {
                required: true,
                maxlength: 128,
            },
            emailId: {
                required: true,
                email: true,
                emailValidate: true,
                checkUserEmail: true
            },
            /*rPassWord: {
                required: true,
                minlength: 8,
                maxlength: 16,
                validatePassword: true,
                regExCheck: true
            },
            ConfrPassWord: {
                required: true,
                equalTo: "#rPassWord",
            }*/
            kid_f_name: {
	            required: true,
	            noSpace: true,
	            minlength: 1,
	            maxlength: 10
            },
            grade:{
                required: true
            }
        },
        messages: {
            firstName: {
                required: "Please enter your name",
                maxlength: "Please enter name of max 128 characters"
            },
            emailId: {
                required: "Please enter email address",
                email: "Please enter a valid email address"
            },
            /*rPassWord: {
                required: "Please enter your password",
                minlength: "Please enter password of min 8 characters",
                maxlength: "Please enter password of max 16 characters"
            },
            ConfrPassWord: {
                required: "Please enter your confirm password",
                equalTo: "Confirm password doesn't match",
            }*/
            kid_f_name: {
            	required: "Please enter child's display name",
                maxlength: "Please enter child name of max 10 characters"
            },
            grade:{
            	required: "Please select grade"
            }
        }
    });

    $("#parent_dob").change(function () {
        $(this).valid();
    });
    $("#userRegistrationPre").validate({
        rules: {
            parent_dob: {
                required: true,
                maxlength: 10,
            }
        },
        messages: {
            parent_dob: {
                required: "Please enter your date of birth",
                maxlength: "Please enter max 10 characters"
            }
        },
        submitHandler: function () {
            validatePreRegister();
            return false;
        }
    });

    // function to validate contact form
    $("#contactForm").validate({
        rules: {
            nameUser: {
                required: true,
                minlength: 1,
                maxlength: 128
            },
            emailUser: {
                required: true,
                email: true
            },
            webSite: {
                url: true
            },
            subJect: {
                required: true,
                maxlength: 100
            },
            contactMessage: {
                required: true,
                minlength: 1,
                maxlength: 500
            }

        },
        messages: {
            nameUser: {
                required: "Please enter name",
                minlength: "Please enter name of min 1 characters",
                maxlength: "Please enter name of max 128 characters"
            },
            emailUser: {
                required: "Please enter email",
                email: "Please enter a valid email"
            },
            webSite: {
                url: "Please enter a valid website"
            },
            subJect: {
                required: "Please enter subject",
                maxlength: "Please enter subject of max 100 characters"
            },
            contactMessage: {
                required: "Please enter message",
                minlength: "Please enter message of min 1 characters",
                maxlength: "Please enter message of max 500 characters"
            }
        },
        submitHandler: function () {
            submitContactForm('contactForm');
        }
    });

    /* end */
    $("#emailIdForgot").blur(function () {
        // dynamically set the rules
        $(this).rules('add', {
            checkUserEmail: true,
        });
        $(this).valid();
    });
    // SubmitButton
    $("#emailIdForgot").keydown(function () {
        $(this).rules('remove', "checkUserEmail");
    });

    $("#forgetPassword").submit(function () {
        // dynamically set the rules
        $("#emailIdForgot").rules('add', {
            checkUserEmail: true,
        });
    });

    forgotPassObj = $("#forgetPassword").validate({
        rules: {
            emailIdForgot: {
                required: true,
                email: true,
                checkUserEmail: true
            }
        },
        messages: {
            emailIdForgot: {
                required: "Please enter email",
                email: "Please enter a valid email"

            },
        }
    });

    //function to validate reset password
    $("#verifychangepassword").validate({
        rules: {
            newPassword: {
                required: true,
                minlength: 8,
                maxlength: 16,
                validatePassword: true,
                regExCheck: true
            },
            confPassword: {
                required: true,
                equalTo: '#newPassword'
            }
        },
        messages: {
            newPassword: {
                required: "Please enter new password",
                minlength: "Please enter new password of min 8 characters",
                maxlength: "Please enter new password of max 16 characters"

            },
            confPassword: {
                required: "Please enter confirm new password",
                equalTo: "New password and confirm password are not matched"
            }
        }
    });


    /**
     * My account validation starts
     */
    $("#parentProfile").validate({
        ignore: '#s2id_autogen1, .select2-input', //ignore these selectors from validation
        rules: {
            firstName: {
                required: true,
                maxlength: 128,
            }
        },
        messages: {
            firstName: {
                required: "Please enter your name",
                maxlength: "Please enter name of max 128 characters"
            }
        },
        submitHandler: function () {
            //openConfirmPopup();
            validatPassProfile('parentProfile');
        }
    });
    
   
    /**
     * My account validation ends
     */



    /**
     * CHANGE PASSWORD VALIDTION
     */
    $("#oldPassword").blur(function () {
        $(this).rules('add', {checkforgetpassword: true});
        $(this).valid();
    });

    $("#oldPassword").keydown(function () {
        $(this).rules('remove', "checkforgetpassword");
    });

    $("#changePass").submit(function () {
        $("#oldPassword").rules('add', {checkforgetpassword: true});
    });

    $("#changePass").validate({
        rules: {
            oldPassword: {
                required: true,
                //minlength: 8,
                //maxlength: 16,
                validatePassword: true
            },
            newPassword: {
                required: true,
                minlength: 8,
                maxlength: 16,
                validatePassword: true,
                doNotMatch: true,
                regExCheck: true
            },
            confirmPassword: {
                required: true,
                equalTo: '#newPassword'
            }
        },
        messages: {
            oldPassword: {
                required: "Please enter current password",
                //minlength: "Please enter current password of min 8 characters",
                //maxlength: "Please enter current password of max 16 characters"
            },
            newPassword: {
                required: "Please enter new password",
                minlength: "Please enter new password of min 8 characters",
                maxlength: "Please enter new password of max 16 characters"

            },
            confirmPassword: {
                required: "Please enter confirm password",
                equalTo: "New password and confirm password are not matched"
            }
        },
        submitHandler: function () {
            changePassword('changePass');
        }
    });
    // Change Password End


    /**
     * Validate add kid starts
     */

    // Add Kid Validation
    $("#KidFirstName").blur(function () {
        // dynamically set the rules
        $(this).rules('add', {
            checkChildName: true
        });
        $(this).valid();
    });

    // Submit Button
    $("#KidFirstName").keydown(function () {
        $(this).rules('remove', "checkChildName");
    });

    $("#addchildInfo").submit(function () {
        // dynamically set the rules
        $("#KidFirstName").rules('add', {
            checkChildName: true
        });
    });

    $("#addchildInfo").validate({
        ignore: [],
        rules: {
            coppa_required: {
                required: true
            },
            KidFirstName: {
                required: true,
                noSpace: true,
                maxlength: 10
            },
            grade_level: {
                required: true
            }
        },
        messages: {
            coppa_required: {
                required: "Please select age group"
            },
            KidFirstName: {
                required: "Please enter child's display name",
                maxlength: "Please enter child's display name of max 10 characters"
            },
            grade_level: {
                required: "Please select grade of child"
            }
        },
        submitHandler: function (form) {
            saveChildInfo(form);
        }
    });
    // Add Kid Validation End    

    /**
     * Validate send instruction
     */
    sendapplinkValidator = $("#sendapplink").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                validatePhone: true,
                minlength: 7,
                maxlength: 15
            },
        },
        messages: {
            phone: {
                required: "Please enter mobile number",
                minlength: "Mobile number length must be in between 7 to 15",
                maxlength: "Mobile number length must be in between 7 to 15"
            },
            email: {
                required: "Please enter email address"
            }
        },
        submitHandler: function (form) {
            sendAppLinkToDevice(form);
        }
    });

    // Edit Kid Validation Starts
    $("#childInfo input").focus(function () {
        $('#errChildGrade').css('display', 'none');
    });

    $("#firstname").blur(function () {
        $(this).rules('add', {checkChildName: true});
        $(this).valid();
    });

    $("#firstname").keydown(function () {
        $(this).rules('remove', "checkChildName");
    });

    $("#childInfo").submit(function () {
        $("#firstname").rules('add', {checkChildName: true});
    });

    $("#dateOfBirth").change(function () {
        $(this).blur();
        var date = $(this).datepicker('getDate');
        var today = new Date();
        var dayDiff = Math.ceil((today - date) / (1000 * 60 * 60 * 24));
        if(dayDiff < 4745){
        	$('#age_txt').hide();
        	$('#below_age_text').show();
        	$('#movetxt').addClass('animationtxt');
        	$(".below_age_msg").load();
        } else {
        	$('#age_txt').show();
        	$('#below_age_text').hide();
        	$('#movetxt').removeClass('animationtxt');
        }
    });

    $("#childInfo").validate({
        rules: {
            firstname: {
                required: true,
                noSpace: true,
                maxlength: 10,
            },
            schoolName: {
                required: false,
                minlength: 1,
                maxlength: 255
                        //    validateHtml: true
            },
            grade_level: {
                required: true,
            },
            cgpaValue: {
                required: false,
                checkCgPa: true
            },
            cgpaFraction: {
                checkCgpaFraction: true
            },
            dateOfBirth: {
                required: false,
                validateDob: true
            }

        },
        messages: {
            firstname: {
                required: "Please enter child's display name",
                maxlength: "Please enter child's display name of max 10 characters"
            },
            schoolName: {
                required: "Please enter school name",
                minlength: "Please enter school name of min 1 characters",
                maxlength: "Please enter school name of max 255 characters"
            },
            grade_level: {
                required: "Please select grade of child"
            },
            cgpaValue: {
                required: "Please enter GPA",
            },
            dateOfBirth: {
                required: "Please select date of birth",
            }

        },
        submitHandler: function () {
            validateChildInfo('childInfo');
        }
    });
    // Edit Kid Validation Ends

    // Learning Customization validation starts
    $("#btnSubList").change(function () {
        var par = $(".ui-multiselect");
        par.css("cssText", "border: 0px solid #B94A48 !important;");
        par.css('height', '38px');
        $('#errSubName').remove();
    });

    $("#weeklyGoal1").blur(function () {
        $(this).rules('add', {
            checkGoalValue: true,
        });
        $(this).valid();
    });

    $("#weeklyGoal1").keydown(function () {
        $(this).rules('remove', "checkGoalValue");
    });

    $("#learningInfo").validate({
        rules: {
            quesAskTime: {
                required: true,
            },
            noChances: {
                required: true,
            },
            unlockTime: {
                required: true,
            },
            subjectList: {
                subjectsSelected: true
            },
            customMessage: {
                //    required: true,
                maxlength: 60,
                //validateHtml: true
            },
            weeklyGoal1: {
                checkGoalValue: true
            }
        },
        messages: {
            quesAskTime: {
                required: "Please select ask question after time spent on apps",
            },
            noChances: {
                required: "Please select number of chances",
            },
            unlockTime: {
                required: "Please select lock device for",
            },
            subjectList: {
                subjectsSelected: "Please select subjects"
            },
            customMessage: {
                //   required: "Please enter custom message",
                maxlength: "Please enter custom message of max 60 characters"
            }
        },
        submitHandler: function () {
            validateLearningInfo('learningInfo');
        }
    });
    // Learning Customization END 

    // Safe Number
    $("#safeNumber").blur(function () {
        $(this).rules('add', {checkSafeMobileNumber: true});
        $(this).valid();
    });

    $("#safeNumberForm #code").change(function () {
        if ($("#safeNumber").val() != '') {
            $("#safeNumber").rules('add', {checkSafeMobileNumber: true});
            $("#safeNumber").valid();
        }
    });

    $("#safeNumber").keydown(function () {
        $(this).rules('remove', "checkSafeMobileNumber");
    });

    $("#safename").blur(function () {
        $(this).rules('add', {validateSafeName: true});
        $(this).valid();
    });

    $("#safename").keydown(function () {
        $(this).rules('remove', "validateSafeName");
    });

    $("#safeNumberForm").submit(function () {
        $("#safename").rules('add', {validateSafeName: true});
        $("#safeNumber").rules('add', {checkSafeMobileNumber: true});
    });

    $("#safeNumberForm").validate({
        rules: {
            safename: {
                required: true,
                maxlength: 128
            },
            safeNumber: {
                required: true,
                validatePhone: true,
                minlength: 7,
                maxlength: 15
            }
        },
        messages: {
            safename: {
                required: "Please enter name",
                maxlength: "Please enter name of max 128 characters"
            },
            safeNumber: {
                required: "Please enter mobile number",
                minlength: "Mobile number length must be in between 7 to 15",
                maxlength: "Mobile number length must be in between 7 to 15"
            }
        },
        submitHandler: function (form) {
            addSafeNumber(form);
        }
    });
    // Safe Number End

    initRewardValidation();

    /* initialize myaccount confirm password validation */
    validateConfirmPassword();

});

function initRewardValidation() {
    // Parental Reward
    $(".reward-form").each(function () {
        var form = $(this);
        form.validate({
            rules: {
                reward_title: {
                    required: true
                },
                reward_points: {
                    required: true,
                    checkPoints: true
                }
            },
            messages: {
                reward_title: {
                    required: "Please enter reward title"
                },
                reward_points: {
                    required: "Please enter reward points"
                }
            },
             tooltip_options: {                
                    reward_points: {placement:'top'}                
                }, 
            submitHandler: function (form) {
                submitReward(form);
            }
        });
    });
    // Parental Reward End
}

/**
 * Myaccount confirm password popup validation
 */
function validateConfirmPassword() {

    $("#passWordParent").keydown(function () {
        $(this).rules('remove', "checkforgetpassword");
    });

    $("#passwordcheckParent").submit(function () {
        $("#passWordParent").rules('remove', "checkforgetpassword");
        if ($(this).valid()) {
            $("#passWordParent").rules('add', {checkforgetpassword: true});
        }
    });

    $("#passwordcheckParent").validate({
        rules: {
            passWordParent: {
                required: true,
                validatePassword: true
            }
        },
        messages: {
            passWordParent: {
                required: "Please enter password",
                validatePassword: "Blank spaces are not allowed in password",
                checkforgetpassword: "Password is not correct"
            }
        },
        submitHandler: function () {
            validatPassProfile('parentProfile');
        }
    });
}