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
function printThisPage() {

                        var divContents = $("#print-div").html();
                        var printWindow = window.open('', '', 'height=1000,width=900');
                        printWindow.document.write('<html><head>');
                        printWindow.document.write('<link rel="stylesheet" type="text/css" href="'+BASE_URL+'/assets_admin/css/bootstrap.min.css"/>');
                        printWindow.document.write('</head><body >');
                        printWindow.document.write(divContents);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.print();
}




$(function() {
        $(document.body).on('click',".print",function (e) {
                        printThisPage();
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

        $(document.body).on('change',".unit_price",function (e) {
       // $(".client-details").hide();
        var rate                =       $(this).val();
        var quentity            =       $(".ordered_quentity").val();

        if(( quentity != "")&&( rate != "")){ //no errors
          var total = (quentity)*(rate);
           $(".TotalAmount").val(total.toFixed(2));
                }

        });

//Jquery function for challan page
        $(document.body).on('change',".quantity",function (e) {
        $(".AmountInWords").hide();// by default this dive will be hidden
        var quentity            =       $(this).val();
        var rate                =       $(".productRate").val();
        var vat                 =       $(".vat").attr("vat");
        var shipping            =       $(".shipping").attr("shipping");
        var discount            =       $(".discount").attr("discount");

        if(( quentity != "")&&( rate != "")){
          var subTotal          =       parseFloat((quentity)*(rate));//calculate subtotle
          var vatTotal          =       parseFloat((subTotal)*(vat)/100);//calculate Vat
          var shippingTotal     =       parseFloat(shipping);//calculate Vat
          var discountTotal     =       parseFloat((subTotal)*(discount)/100);//calculate discount
          var  Total            =       parseFloat(((subTotal+shippingTotal+vatTotal)) - (discountTotal) );//calculate The total amount

          var TotalInWords      =       toWords(Total);

           $(".sub_total").val(subTotal.toFixed(2)); //show subtotal
           $(".vat").val(vatTotal.toFixed(2));//Show vat
           $(".shipping").val(shippingTotal);//Show shipping
           $(".discount").val(discountTotal.toFixed(2));//Show Discount
           $(".total").val(Total.toFixed(2));//Show Discount
           if (Total > 0) {
            $(".AmountInWords").show();
            $(".AmountInWords").html("<strong>Amount In words:</strong>  " + TotalInWords + "only");//Show Amount in words
            $(".totalInWords").val(TotalInWords);//Show Amount in words
           }


         }

        });

//Jquery function for challan page
        $(document.body).on('change',".productRate",function (e) {
        $(".AmountInWords").hide();// by default this dive will be hidden
        var quentity            =       $(".quantity").val();
        var rate                =       $(this).val();
        var vat                 =       $(".vat").attr("vat");
        var shipping            =       $(".shipping").attr("shipping");
        var discount            =       $(".discount").attr("discount");

        if(( quentity != "")&&( rate != "")){
          var subTotal          =       parseFloat((quentity)*(rate));//calculate subtotle
          var vatTotal          =       parseFloat((subTotal)*(vat)/100);//calculate Vat
          var shippingTotal     =       parseFloat(shipping);//calculate Vat
          var discountTotal     =       parseFloat((subTotal)*(discount)/100);//calculate discount
          var  Total            =       parseFloat(((subTotal+shippingTotal+vatTotal)) - (discountTotal) );//calculate The total amount

          var TotalInWords      =       toWords(Total);

           $(".sub_total").val(subTotal.toFixed(2)); //show subtotal
           $(".vat").val(vatTotal.toFixed(2));//Show vat
           $(".shipping").val(shippingTotal);//Show shipping
           $(".discount").val(discountTotal.toFixed(2));//Show Discount
           $(".total").val(Total.toFixed(2));//Show Discount
           if (Total > 0) {
            $(".AmountInWords").show();
            $(".AmountInWords").html("<strong>Amount In words:</strong>  " + TotalInWords + "only");//Show Amount in words
            $(".totalInWords").val(TotalInWords);//Show Amount in words
           }


         }

        });


 });