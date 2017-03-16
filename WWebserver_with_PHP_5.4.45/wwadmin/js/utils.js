
function showHelp (obj) {

	$('.file_comments').show();
	$(obj).hide();
	$(obj).parent ().hide();
	return false;
}

function restartWebserver (newURL) {

	var secCnt = 5;
	$('#popupWindowFilter').html ("<p>Going to restart with URL "+newURL+"<br /><br />Window will refresh in "+secCnt+" seconds.</p>");

	$('#popupWindowFilter').show ();

	var oldURL = "/wwadmin/index.php?view=status&cmd=restart";  
	
	$.ajax({  url: oldURL, data: '', 
		error: function(xhr, status) {
			alert ("Error occured. "+xhr.responseText);			
			$('#popupWindowFilter').hide ();		
		},
		success: function(data) {
		if (data=="OK") {
			var interval = setInterval (function () { 
				secCnt--;
				$('#popupWindowFilter').html ("<p>Going to restart with URL "+newURL+"<br /><br />Window will refresh in "+secCnt+" seconds.</p>");
				if (secCnt<=0) {
				
					clearInterval (interval);
					window.location.href=newURL; 
					$('#popupWindowFilter').hide ();

				}
			}, 1000);
		}
		else
		{
			alert ("Error occured. Could not restart.");			
			$('#popupWindowFilter').hide ();

		}
	}});


}

$(document).ready(function() 
{

});