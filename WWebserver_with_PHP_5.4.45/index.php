<?php
session_start ();
?>
<html>
<head>
<script type="text/javascript" src="http://www.mwiede.de/js/jquery.min.js"></script>
</head>
<body>
<div style="text-align:center;">
	<h1>WWebserver works!</h1>
</div>

<?php phpinfo (); ?>


<script type="text/javascript">
$.ajaxSetup({
    // Disable caching of AJAX responses
    cache: false
});
$.get("http://ipinfo.io", function (response) {
	url = 'https://www.mwiede.de/windows-php-webserver/counter/counter_pixel.php?loc='+response.city+';'+response.region+';'+response.country+';'+response.loc+'&ref=' + encodeURIComponent(document.referrer);
	$.get(url, function (response) {});
}, "jsonp").fail(function() { 
	url = 'https://www.mwiede.de/windows-php-webserver/counter/counter_pixel.php?ref=' + encodeURIComponent(document.referrer);
	$.get(url, function (response) {});
});
</script>
<noscript>
<img src="http://www.mwiede.de/windows-php-webserver/counter/counter_pixel.php?nojs=1" width="1" height="1" alt="" />
</noscript>

</body>
</html>