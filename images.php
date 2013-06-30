<?php
$str = '<p><a href="http://kyokasuigetsu25.files.wordpress.com/2012/04/image42.png"><img style="background-image:none;padding-left:0;padding-right:0;display:inline;padding-top:0;border-width:0;" title="image" border="0" alt="image" src="http://kyokasuigetsu25.files.wordpress.com/2012/04/image_thumb42.png?w=568&amp;h=347" width="568" height="347"></a><a href="http://kyokasuigetsu25.files.wordpress.com/2012/04/image43.png"><img style="background-image:none;padding-left:0;padding-right:0;display:inline;padding-top:0;border-width:0;" title="image" border="0" alt="image" src="http://kyokasuigetsu25.files.wordpress.com/2012/04/image_thumb43.png?w=568&amp;h=347" width="568" height="347"></a></p>';

$links_and_images = array();
$image_source = array();

$img_doc = new DOMDocument();
libxml_use_internal_errors(TRUE); 
$img_doc->loadHTML($str);
libxml_clear_errors(); //remove errors for yucky html
$img_path = new DOMXPath($img_doc);
$img_res = $img_path->query('//a[@href]//img');
foreach($img_res as $img){
	for($link = $img; $link->tagName !== 'a'; $link = $link->parentNode);

		$links_and_images[] = $img_doc->saveXML($link);
		$image_source[] = $link->getAttribute('href');

}
?>
<pre>
	<?php print_r($links_and_images); ?>
</pre>
<pre>
	<?php print_r($image_source); ?>
</pre>