/**
 * Function to save for data
 * @param {type} formId
 * 
 */
function validatPassProfile(formId) {
    hideConfirm.click();
    var passWord = $("#passWordParent").val();
    if (passWord == '') {
        message = 'Please enter Password';
        addMessageForPopup('error', message);
        $("#passWordParent").addClass("error");
    } else {
        hideConfirm.click();
        $("#passWordParent").removeClass("error");
        var data = $("#" + formId).serialize();
        data += '&passWord=' + passWord;
        var url = SITE_URL + 'myaccount/saveparentinfo';
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            success: function(data) {
                var response = jQuery.parseJSON(data);
                var responseMessage = response.message;
                var responseStatus = response.status;
                if (responseStatus == 'success') {
                    $('#passWordParent').val('');
                    getparentdetail();
                    $(".Error").html('');
                    $("#passWord").val('');
                    $('#myModal').modal('hide');
                    addMessage('success', responseMessage);
                    $('html, body').animate({scrollTop: 0}, 'slow');
                } else {
                    $('input[name=passWordParent]').blur();
                    $('input[name=passWordParent]').focus();
                }
            }
        });
    }
}
/**
 * FUNCTION TO GET PARENT DETAILS
 * 
 */
function getparentdetail()
{
    //lodingBlockUI();//call function for spiner
    var url = SITE_URL + 'myaccount/getparentdetail';
    $.ajax({
        url: url,
        success: function(data)
        {
            var src = src1 = '';
            var response = jQuery.parseJSON(data);
            var fname = response.fname;
            var lname = response.lname;
            var image = response.image;
            var phone = response.phone;
            var email = response.email;
            var timezone_id = response.timezone_id;
            var parentType = response.gender;
            var displayname = response.displayname;
            if (phone != '' && phone != null) {
                phone = phoneNumberWithoutCountryCode(phone);
            }
            $("#firstName").val(fname);
            $("#lastName").val(lname);
            $("#emailIdp").val(email);
            
            /* SET TIMEZONE */
            $(".select2").select2("destroy");
            $(".select2").select2();
            $(".select2").select2("val", timezone_id);
            
            $("#parType").val(parentType);
            if (parentType == 'M') {
                $("#momSelected").removeClass("uncheck");
                $("#momSelected").addClass("check_list");
                $("#dadSelected").removeClass("check_list");
                $("#dadSelected").addClass("uncheck");
            } else {
                $("#momSelected").removeClass("check_list");
                $("#momSelected").addClass("uncheck");
                $("#dadSelected").removeClass("uncheck");
                $("#dadSelected").addClass("check_list");
            }
            var n = fname.length;
            if (n > 10) {
                name = fname.slice(0, 10);
                name = name + '...';
                name1 = name;
            } else {
                name = fname;
                name1 = name;
            }
            
            if(image == '' || image == null) {
                if (parentType == 'M') {
                    image = 'no-image_female.png';
                }else {
                    image = 'no-image.png';
                }
                src = src1 = SITE_URL + 'images/' + image;
            }else{
                src = SITE_URL + 'images/parentpics/' + image;
                src1 = SITE_URL + 'images/parentpics/parentthumb/' + image;
            }
            
            $("#parent_default_image").remove();
            $("#dynamic").remove();
            var img = $('<img width="100%" alt="parent-image" id="parent_default_image">'); //Equivalent: $(document.createElement('img'))
            img.attr('src', src);
            $("#userImageHeader").attr('src', src1);
            $("#userImageHeader1").attr('src', src1);
            img.appendTo('#imageblock');
            $('.blockUI').remove();
            
        }
    });
}

/**
 * function to show message div with respected message
 * @param {type} type
 * @param {type} message
 * @returns {undefined}
 */
function addMessage(type, message) {
    
    if(message != ''){
        $("#messAge").html('');
        if (type == 'error') {
            var html = '<div class="alert alert-danger">' + message + ' <a href="javascript:void(0);" class="alert-close">';
            html += '<img src="' + SITE_URL + 'images/close-arrow.png" alt=""></div>';
        } else if (type == 'success') {
            var html = '<div class="alert alert-success">' + message + ' <a href="javascript:void(0);" class="alert-close">';
            html += '<img src="' + SITE_URL + 'images/close-arrow.png" alt=""></div>';
        }
        $("#messAge").html(html);
        $("#messAge").removeClass("displayNone");
        $("#messAge").addClass("displayBlock");
    }
}


function closeWelcome(redirectUrl){
    var url = SITE_URL + 'myaccount/newuser';
    $.ajax({
        url: url,
        success: function(data) {
            var response = jQuery.parseJSON(data);
            var responsestatus = response.status;
            if (responsestatus == 'success') {
                
                jQuery("#welcomediv").remove();
                if (redirectUrl != '' && redirectUrl != undefined) {
                    window.location.href = redirectUrl;
                }
                
            }
        }
    });
    
}



/**
* @desc Function to save kid info
* @param 
* @author suman khatri on October 11 2014
* @return array
*/
function addChild(formId) {

    var data = $("#" + formId).serialize();
    
    $("#"+formId+" :input").prop("disabled", true);
    $(".select2").select2().enable(false);
    
    console.log(data); return false;
    
    /* disable form */
    
    $("#KidFirstName").attr('disabled', 'disabled');
    $("#KidLastName").attr('disabled', 'disabled');
    //$('input[name="childgender"]').attr('disabled', 'disabled');
    $("#grade_level").attr('disabled', 'disabled');
    $('input[name="radio-2-set"]').attr('disabled', 'disabled');
    $('#phoneNumberDevice').attr('disabled', 'disabled');
    $('#code').attr('disabled', 'disabled');
    $('#deviceEmailId').attr('disabled', 'disabled');
    
    
    var url = SITE_URL + 'child/addchildwithdevice';
    
    
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: function(data) {
            var response = jQuery.parseJSON(data);
            var responseMessage = response.message;
            var status = response.status;
            //var typeerror = response.typeerror;
            var child_id = response.child_id;
            var email = response.email;
            var phone = response.phone;
            
            if (status == 'success') {
            
                sendMailSMS(child_id,email,phone);
                
                addMessage('success', responseMessage);
                
                var html1 = '<hr><span class="grey_label"><strong>Step 1: Click on the link to download the app from Google Play Store.<br />';
                html1 += 'Step 2: Install the app and accept Google Android permissions. This is required for app to work.<br />';
                html1 += 'Step 3: Login into the application and assign the Device to your child.</strong></span>';
                var html = '<button type="button" class="btn btn-primary" id="finishButton"';
                html += 'name="finishButton" onclick="redirectOnScoreCard('+child_id+');">Finish</button>'
                
                $("#instructionBlockWhole").html(html1);
                
                
                
                $("#finishButtonBlock").html(html);
                
            } else {
                $('#'+typeerror).blur();
                $('#'+typeerror).focus();
                removeCalssOnId('pair_pre_btn', 'disableanchorN');
                return false;
            }

        }
    });
    
    return false;
}