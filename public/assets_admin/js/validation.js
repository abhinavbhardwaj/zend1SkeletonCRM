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
/*
 *function to prin specific div
 */
function printThisPage(divId) {
     $("#"+divId).print();
}


$(function() {
         $(document.body).on('click',".print",function (e) {
                        var divContents = $("#print-div").html();
                        var printWindow = window.open('', '', 'height=1000,width=900');
                        printWindow.document.write('<html><body >');
                        printWindow.document.write(divContents);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.print();

               // $("#print-div").printThis(); 
        });

        $(document.body).on('change',".selectClient",function (e) {
        $(".client-details").hide();
        var element     =       $(this).find('option:selected');
        var address     =       element.attr("address");
        var phone       =       element.attr("phone");
        var company_name=       element.attr("company_name");

        if(typeof company_name != "undefined"){ //no errors
           $(".client-details").show();
           $(".company_name").val("Company Name:  "+company_name);
           $(".address").val("Address:  "+address);
           $(".phone").val("Phone:  "+phone);
        }

        });


        $(document.body).on('change',".selectProduct",function (e) {
       // $(".client-details").hide();
        var element     =       $(this).find('option:selected');
        var unit        =       element.attr("unit");
        var stock       =       element.attr("stock");
        var price       =       element.attr("price");

        if(typeof stock != "undefined"){ //no errors
          // $(".client-details").show();
           $(".stock").val(stock);
           $(".unit").html(" "+unit);
           $(".unit_price").val(price);
           $(".unit_rate").html("/"+unit);
        }

        });

        $(document.body).on('change',".ordered_quentity",function (e) {
       // $(".client-details").hide();
        var quentity            =       $(this).val();
        var rate                =       $(".unit_price").val();

        if(( quentity != "")&&( rate != "")){ //no errors
          var total = (quentity)*(rate);
           $(".TotalAmount").val(total.toFixed(2));
                }

        });


 });