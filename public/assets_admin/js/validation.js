function validateLogin(){
		var error = false; 
		var newPassword = $('#password').val();
		if($('#username').val()==''){
			$("#username").addClass("errorA");
			$("#username_error").html("Please enter username");
			$("#username_error").show();
			error = true;
			//apprise("Please Enter User Name");
		}else{
				$("#username_error").hide();
				$("#username").removeClass("errorA");
			}

		if(newPassword == '') {
			$("#password").addClass("errorA");
			$("#passwordError").html("Please enter password");
			$("#passwordError").show();
			error = true;
		}else if(newPassword != '') {
			var space = newPassword.indexOf(' '); 
			if(space>=0) {
				$("#password").addClass("errorA");
				$("#passwordError").html("Invalid password");
				$("#passwordError").show();
				error = true;
			}else{
				$("#password").removeClass("errorA");
				$("#passwordError").hide();
				}
		}
		if(error){
	return false;
			}else{
				return true;
				}
	}
function closeThisError(id){
	$('#'+id).css("display","none");
} 