$(document).ready(function() {
	permalink();
	mousemove();
	var options = {
			target: '#output',   // target element(s) to be updated with server response
			beforeSubmit: beforeSubmit,  // pre-submit callback
			success: afterSuccess,  // post-submit callback
			resetForm: true        // reset the form after successful submit
		};

	 $('#ajaxUpload').submit(function() {
			$(this).ajaxSubmit(options);
			// always return false to prevent standard browser submit and page navigation
			return false;
		});
});

function mousemove () {
	$('.hoverimg').on( "mousemove", function( event ) {
	  $('.hoverimg>img').css({'left':event.pageX+10,'top':event.pageY+10});
	});
}

function permalink() {
	$('.permalink').keyup(function(){
	    this.value = this.value.replace(/\s+/g, '-').toLowerCase();
	});
}

function ajaxUpload(id,width,height,append){
	$('#ajaxUploadwidth').val(width);
	$('#ajaxUploadheight').val(height);
	$('#ajaxUploadid').val(id);
	$('#ajaxUploadappend').val(append);
	$('#lightbox').addClass('active');
}

function removeLightbox(){
	var id = $('#ajaxUploadid').val();
	var image = $('#ajaxUploadimage').val();
	var url = $('#ajaxUploadurl').val();
	if(image){
	if($('#ajaxUploadappend').val()==1)
	$('#'+id).val($('#'+id).val() + "![Alt img]("+ url + image + ")");
	else
	$('#'+id).val(image);
	}
	$('#lightbox').removeClass('active');
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
	$("#output").html("");
}

function afterSuccess()
{
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
	var fix = $('#ajaxUploadfix').data('img');
	$('#ajaxUploadimage').val(fix);
}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{

		if( !$('#imageInput').val()) //check empty input filed
		{
			$("#output").html("Are you kidding me?");
			return false
		}

		var fsize = $('#imageInput')[0].files[0].size; //get file size
		var ftype = $('#imageInput')[0].files[0].type; // get file type


		//allow only valid image file types
		switch(ftype)
        {
            case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> Unsupported file type!");
				return false
        }

		//Allowed file size is less than 1 MB (1048576)
		if(fsize>10048576)
		{
			$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
			return false
		}

		$('#submit-btn').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output").html("");
	}
	else
	{
		//Output error to older browsers that do not support HTML5 File API
		$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
		return false;
	}
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}
