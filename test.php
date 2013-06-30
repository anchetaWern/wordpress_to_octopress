<?php
set_time_limit(0);
session_start();

$images_dir = 'source/images/posts'; //path in which to save images
$settings = array(
	'images_dir' => $images_dir,
	'invalid_chars' => 	array(
		'+', ',', '=', "'", '"', '$', '%', '^', '~', '`', 
		'@', '!', '&', '(', ')', '.', '/', ":", ";", '*', 
		"#", "|", "{", "}", "[", "]", "\\", " "
	)
);

if(empty($_SESSION['posts'])){

	$html = 'wern-ancheta.xml'; //path to the wordpress xml file export
	$doc = new DOMDocument();
	libxml_use_internal_errors(true); 
	$doc->load($html);
	libxml_clear_errors(); 

	$posts = array();


	$image_types = array('png', 'jpg', 'jpeg', 'gif');

	$items = $doc->getElementsByTagName('item');

	foreach($items as $k => $i){

		$title = '';
		$pub_date = '';
		$post_type = '';
		$content = '';
		$tags = array();

		foreach($i->childNodes as $child){

			$value = $child->nodeValue;
			
			switch($child->nodeName){
				
				case 'title':
					$title = $value;
				break;

				case 'wp:post_date':
					$pub_date = $value;
				break;

				case 'wp:post_type':
					$post_type = $value;
				break;

				case 'content:encoded':
					//html_to_md($value, $title, $settings)
					$content = $value;
				break;
				
				case 'category':
					if($child->getAttribute('domain') == 'post_tag'){
						$tags[] = $value;
					}
				break;
			}



		}

		if($post_type == 'post'){	
			$posts[] = array(
				'title' => $title,
				'pub_date' => $pub_date,
				'post_type' => $post_type,
				'content' => $content,
				'tags' => $tags
			);
		}
		
	}

	$_SESSION['posts'] = $posts;
}else{
	$posts = $_SESSION['posts'];
}

$post = $posts[1];
$content = html_to_md($post['content'], $post['title'], $settings);
echo $content;


function file_newname($path, $filename){
  if($pos = strrpos($filename, '.')){
		$name = substr($filename, 0, $pos);
		$ext = substr($filename, $pos);
  }else{
    $name = $filename;
  }

  $newpath = $path .'/'. $filename;
  $newname = $filename;
  $counter = 0;

	while(file_exists($newpath)){
		$newname = $name .'_'. $counter . $ext;
		$newpath = $path .'/'. $newname;
		$counter++;
  }

  return $newname;
}


function save_image($loc, $img_url){

	$filename = substr($img_url, strrpos($img_url, '/') + 1);
	$file = $loc . '/' . $filename;

	if(file_exists($file)){

		$filename = file_newname($loc, substr($img_url, strrpos($img_url, '/') + 1));
		$file = $loc . '/' . $filename;
	}
	
	$img_contents = file_get_contents($img_url);
	
	$handle = fopen($file, 'w');
	fwrite($handle, $img_contents);
	fclose($handle);

	return $file;
}


function html_to_md($str, $title, $settings = array()){

	$img_dir = $settings['images_dir'];

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
	$links_and_images = array();
	$image_source = array();

	$img_doc = new DOMDocument();
	libxml_use_internal_errors(TRUE); 
	$img_doc->loadHTML($str);
	libxml_clear_errors(); 
	$img_path = new DOMXPath($img_doc);
	$img_res = $img_path->query('//a[@href]//img');

	foreach($img_res as $img){

		for($link = $img; $link->tagName !== 'a'; $link = $link->parentNode);

		$links_and_images[] = $img_doc->saveXML($link);
		$image_source[] = $link->getAttribute('href');

	}

	$markdown_img = array();

	$loc = $img_dir . '/' . str_replace($settings['invalid_chars'], "-", $title);
	if(!file_exists($loc)){
		mkdir($loc);
	}

	foreach($image_source as $i => $img_url){
		$file = save_image($loc, $img_url);

		$markdown_img[] = "![](/{$file})";
	}

	$str = str_replace($links_and_images, $markdown_img, $str);
	
	//replace links
	$link_pattern = '/\<a\shref\=\"(.*)\"\>((?:\w|\s){1,})\<\/a\>/';

	preg_match_all($link_pattern, $str, $link_matches);

	$links = $link_matches[0];
	$urls = $link_matches[1];
	$url_text = $link_matches[2]; 
?>
<pre>
	<?php print_r($link_matches); ?>
</pre>
<?php
	$markdown_links = array();

	foreach($urls as $i => $u){

		$markdown_links[] = "[{$url_text[$i]}]({$u})";
	}

	$str = str_replace($links, $markdown_links, $str);


	//replace codes
	$codes_pattern = '/\[sourcecode(?:\slanguage\=\"(\w*)\")?\]/';
	$codes_close_pattern = '[/sourcecode]';
	preg_match_all($codes_pattern, $str, $code_matches);

	$sourcecode_opening = $code_matches[0];
	$languages = $code_matches[1];

	$markdown_sourcecode_opening = array();

	foreach($sourcecode_opening as $i => $s){

		$markdown_sourcecode_opening[] = '```' . $languages[$i];
	}

	$str = str_replace($sourcecode_opening, $markdown_sourcecode_opening, $str);
	$str = str_replace($codes_close_pattern, '```', $str);


	$blockquote_pre_pattern = '/\<blockquote\>\<pre.*\>(.+)\<\/pre\>\<\/blockquote\>/s';
	preg_match_all($blockquote_pre_pattern, $str, $blockquote_pre_pattern_matches);

	$blockquote_pres = $blockquote_pre_pattern_matches[0];
	$blockquote_pres_code = $blockquote_pre_pattern_matches[1];

	$markdown_blockquote_code = array();

	foreach($blockquote_pres as $i => $b){

		$markdown_blockquote_code[] = "\n```\n" . html_entity_decode($blockquote_pres_code[$i]) . "\n```\n";
		
	}

	$str = str_replace($blockquote_pres, $markdown_blockquote_code, $str);


	$pre_tags_pattern = '/\<pre.*\>(.*)\<\/pre\>/s';
	preg_match_all($pre_tags_pattern, $str, $pre_tags_matches);

	$pre_tags = $pre_tags_matches[0];
	$pre_tags_codes = $pre_tags_matches[1];

	$markdown_pre_tags = array();

	foreach($pre_tags_codes as $p){

		$markdown_pre_tags[] = "\n```\n" . html_entity_decode($p) . "\n```\n";
	}

	$str = str_replace($pre_tags, $markdown_pre_tags, $str);


	//replace smart quotes
	$str = str_replace(array("’", "’’"), array("'", '"'), $str);
	return strip_tags($str);
}
?>

