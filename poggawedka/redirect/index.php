<?
	$url=base64_decode($_GET['u']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head> 
	<meta content="text/html; charset=UTF-8" http-equiv="content-type">
	<title>www.ą.tk</title>
	
	<META HTTP-EQUIV=Refresh CONTENT="6; URL=<?echo $url;?>">
	
	
	
	<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-29144607-1']);
  _gaq.push(['_setDomainName', 'xn--2da.tk']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	
</head><body><?


?>
<div id="gora" style="width: auto; height: auto;position: absolute; bottom:0px; left:0px;text-align:right; overflow:hidden;" onmouseover="pokazReklame(this)" onmouseout="przesunMaterial();"><div>
<script type="text/javascript" id="AdTaily_Widget" src="http://static.adtaily.pl/widget.js#uWf9ZMkLsXwptVP">

</script>
<noscript><a href="http://www.adtaily.pl">Reklama w internecie</a></noscript></div></div>

<div style="width: 800px; height: 300px; position: absolute; top: 30%; margin-top: -150px; left: 50%; margin-left: -400px; text-align:center;">
	<center>
	
	<img src="poggawedka.png"><br>
	<div id="message">

		Za momencik wyświetlimy Ci stronę <span id="link" style="display:none;">
	
		<a href="<?	echo $url;?>"><?echo $url; ?></a>
		</span>
		<span id="nonlink"><i><?echo substr($url,0,20); ?>...</i></span>
	</div>
	<div id="redir" style="display:none;">
		
		Trwa otwieranie strony <a href="<?	echo $url;?>"><?echo $url; ?></a>
	
	</div>
	
	
	<script>
	function showLink()
	{
		document.getElementById('link').setAttribute('style','');
		document.getElementById('nonlink').setAttribute('style','display:none;');
	}
	
	function redir()
	{
		document.getElementById('redir').setAttribute('style','');
		document.getElementById('message').setAttribute('style','display:none;');
		location.href='<?echo $url; ?>';
	}
	
	setTimeout("showLink()",4000);
	setTimeout("redir()",6000);
	
	
	
	

	</script>
	
	
	<hr>
		<script id="_wauvlr">var _wau = _wau || []; _wau.push(["small", "skroctkidn", "vlr"]);(function() { var s=document.createElement("script"); s.async=true; s.src="http://widgets.amung.us/small.js";document.getElementsByTagName("head")[0].appendChild(s);})();</script><br>
		Skróć link w domenie <a href="http://xn--2da.tk">ą.tk</a>, <a href="http://ujeb.tk">ujeb.tk</a>, <a href="http://xn--nea.tk">ę.tk</a> 
	
	</center></div>


</body></html>