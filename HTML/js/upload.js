(function () 
{
	
	var input = document.getElementById("images"), 
	formdata = false;
	var path = $("#controller_pathImage").val();
        
	function showUploadedItem (source) 
	{
		var url=source.split("\/");
		var urlLenth = url.length;
		var thumUrl = SITE_URL +'images/parentpics/parentthumb/'+url[urlLenth-1];
		$("#parent_default_image").remove();
		 $("#image-item").remove();
		 $("#dynamic").remove();
		  
		 
		 if(path == 'myaccount/uploadparentimage')
		 {
			 var img = $('<img id="parent_default_image" width="100%" alt="parent-image" >');//Equivalent: $(document.createElement('img'))
			 img.attr('src', source);
			 $("#userImageHeader").attr('src',thumUrl); 
			 $("#userImageHeader1").attr('src',thumUrl); 
			 img.appendTo('#imageblock');
			 $("#images").attr('title','Edit Image');
		 }
		 else if(path == 'child/uploadimage')
		 {
			 var img = $('<img id="dynamic" class="UserImgBig parentProfileImage" >');//Equivalent: $(document.createElement('img'))
			 img.attr('src', source);
			 //$("#imageblock").attr('src',source);
			 img.appendTo('#imageblock');
                         $("#images").attr('title','Edit Image');
                         $("#ImageAnchor").attr('title','Edit Image');
		 }
		 else 
		 {
			 var img = $('<img id="parent_default_image" width="100%" alt="parent-image" >');//Equivalent: $(document.createElement('img'))
			 img.attr('src', source);
			 img.appendTo('#imageblock');
			 $("#images").attr('title','Edit Image');
                         img.appendTo('#images');
                         
		 }
		 
		 
	}   
	if (window.FormData) 
	{
  		formdata = new FormData();
	}
	
	
	if (!this.addEventListener) {
	    this.attachEvent("readystatechange", function (evt) {
	    	if($('#images').val() != '')
	    	{
	    		var ext = $('#images').val().split('.').pop().toLowerCase();
	    		if($.inArray(ext, ['png','bmp','jpeg','jpg','tiff']) == -1)
	    		{
                            addMessage(status, "This field allow only png , bmp , jpeg , jpg , tiff image file");
                            //apprise("This field allow only png , bmp , jpeg , jpg , tiff image file");
                            return false;
	    		}
	    	}
	 		file = this.files[0];
			if(!!file.type.match(/image.*/)) 
			{
				if( window.FileReader ) 
				{
					reader = new FileReader();
					reader.onloadend = function (e) 
					{ 
						//showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}
				if(formdata) 
				{
					formdata.append("images", file);
				}
			}
			if(path == 'parent/updateparentimage')
			{
				var oldfile =$("#old_image").val();
				formdata.append("old_image", oldfile);
				imageFullPath = SITE_URL +'images/parentpics/';
			}else if(path=='myaccount/uploadparentimage'){
				imageFullPath = SITE_URL +'images/parentpics/';
			}else if(path=='child/uploadimage'){
				var oldfile =$("#old_image").val();
				formdata.append("old_image", oldfile);
			   var childId =   $("#childId").val();
			   formdata.append("childID", childId);
			imageFullPath = SITE_URL +'images/childrenpics/';
		}
			
			
			
			
			
			
			var url = SITE_URL+path;
			if (formdata) 
			{
				$.ajax(
				{
					url: url,
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					success: function (res) 
					{
						var response	= jQuery.parseJSON(res);
						var status 		= response.status;
						var message		= response.message;
						if(status == 'error'){
                                                    addMessage(status, message);
                                                    //apprise(message);
						}else{
							$("#image_file_name").val(message);
							var IMAGEFULLNAME = imageFullPath+message;
							showUploadedItem(IMAGEFULLNAME);
						}
						
					}
				});
			}
		});
	} else {
		
	    this.addEventListener("readystatechange", 	input.addEventListener("change", function (evt) 
	    	 	{
	    	if($('#images').val() != '')
	    	{
	    		var ext = $('#images').val().split('.').pop().toLowerCase();
	    		if($.inArray(ext, ['png','bmp','jpeg','jpg','tiff']) == -1)
	    		{
                            addMessage(status, "This field allow only png , bmp , jpeg , jpg , tiff image file");
                            //apprise("This field allow only png , bmp , jpeg , jpg , tiff image file");
                            return false;
	    		}
	    	}
	 		file = this.files[0];
			if(!!file.type.match(/image.*/)) 
			{
				if( window.FileReader ) 
				{
					reader = new FileReader();
					reader.onloadend = function (e) 
					{ 
						//showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}
				if(formdata) 
				{
					formdata.append("images", file);
				}
			}
			if(path == 'parent/updateparentimage')
			{
				var oldfile =$("#old_image").val();
				formdata.append("old_image", oldfile);
				imageFullPath = SITE_URL +'images/parentpics/';
			}else if(path=='myaccount/uploadparentimage'){
				imageFullPath = SITE_URL +'images/parentpics/';
			}else if(path=='child/uploadimage'){
				var oldfile =$("#old_image").val();
				formdata.append("old_image", oldfile);
			   var childId =   $("#childId").val();
			   formdata.append("childID", childId);
			imageFullPath = SITE_URL +'images/childrenpics/';
		}
			
			
			var url = SITE_URL+path;  
			if (formdata) 
			{
				$.ajax(
				{
					url: url,
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					success: function (res) 
					{
						var response	= jQuery.parseJSON(res);
						var status 		= response.status;
						var message		= response.message;
						if(status == 'success'){
							$("#image_file_name").val(message);
							var IMAGEFULLNAME = imageFullPath+message;
							showUploadedItem(IMAGEFULLNAME);
                                                        message = 'Image uploaded successfully';
						}
                                                
                                                addMessage(status, message);
						
					}
				});
			}
		}, false));
	}
}());