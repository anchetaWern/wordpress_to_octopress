<?php
$str = '<a href="http://wernancheta.files.wordpress.com/2012/06/image.png"><img style="background-image:none;padding-left:0;padding-right:0;display:inline;padding-top:0;border-width:0;" title="image" src="http://wernancheta.files.wordpress.com/2012/06/image_thumb.png?w=450&amp;h=441" alt="image" width="450" height="441" border="0"></a>
<a href="http://google.com">google super dupex</a>';
$links_pattern = '/\<a\shref\=\"(.*)\"\>((?:\w|\s){1,})\<\/a\>/';
preg_match_all($links_pattern, $str, $matches);
?>
<pre>
	<?php print_r($matches); ?>
</pre>