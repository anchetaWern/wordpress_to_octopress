<?php
$title = 'deploying';
$images_dir = 'source/images/posts';
$str = '<a href="http://recluze.wordpress.com/2007/09/05/deploying-visual-studionet-crystal-reports-projects/">Deploying .net application +&nbsp; crystal reports</a>
<a href="http://google.com">google</a>
<ul>
	<li>Naruto</li>
	<li>Bleach</li>
	<li>Onepiece</li>
</ul>
<a href="http://rootninja.files.wordpress.com/2010/10/image14.png"><img style="background-image:none;padding-left:0;padding-right:0;display:inline;padding-top:0;border:0;margin:0;" title="image" src="http://rootninja.files.wordpress.com/2010/10/image_thumb14.png" border="0" alt="image" width="244" height="133" /></a>
';
//replace lists
$list_pattern = '/\<li\>(.+)\<\/li\>/';
preg_match_all($list_pattern, $str, $list_matches); 

$lists = $list_matches[0];
$list_text = $list_matches[1];

$markdown_lists = array();

foreach($list_text as $i => $l){
	$markdown_lists[] = "- " . $l;
}

$str = str_replace($lists, $markdown_lists, $str);

//replace images
$link_image_pattern = '/\<a\shref\=\".+\"\>(\<img\s.+src="([^"]*)"\s.+alt="([^"]*)"\s.+\s\/\>)\<\/a\>/';
preg_match_all($link_image_pattern, $str, $link_images);


$links_and_images = $link_images[0];
$images = $link_images[1];
$image_source = $link_images[2];
$image_alt = $link_images[3];

$markdown_img = array();

foreach($image_source as $i => $s){

	$loc = $images_dir . '/' . $title;
	mkdir($loc);
	
	$filename = substr($s, strrpos($s, '/') + 1);
	$file = $loc . '/' . $filename;
	
	$img_contents = file_get_contents($s);

	$handle = fopen($file, 'w');
	fwrite($handle, $img_contents);
	fclose($handle);
	
	$markdown_img[] = "![$image_alt[$i]](/{$file})";

}

$str = str_replace($links_and_images, $markdown_img, $str);

//replace links
$link_pattern = '/\<a\shref\=\"(.+)\"\>(.+)\<\/a\>/';

preg_match_all($link_pattern, $str, $link_matches);

$links = $link_matches[0];
$urls = $link_matches[1];
$url_text = $link_matches[2]; 

$markdown_links = array();

foreach($urls as $i => $u){

	$markdown_links[] = "[{$url_text[$i]}]({$u})";
}

$str = str_replace($links, $markdown_links, $str);

echo $str;
?>





