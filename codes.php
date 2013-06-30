<?php
$str = '
[sourcecode language="javascript"]

&lt;script id="anime" type="text/html"&gt;

&lt;ul&gt;

	{{#watched}}

	&lt;li&gt;{{.}}&lt;/li&gt;

	{{/watched}}

&lt;/ul&gt;

&lt;/script&gt;

[/sourcecode]
</p><pre class="brush: c-sharp; toolbar: false">cdwampwww</pre>
<p>To create a new rails project and specify that the project will be using mysql as the database. Type:</p><pre class="brush: c-sharp; toolbar: false">rails new RailsProject -d mysql</pre>
<p>Doesn’t matter what the name of the project is. You can name it anything you want.</p>
<p>If that command is successful, you will see bunch of files being created.</p>
<p><a href="http://rootninja.files.wordpress.com/2011/04/image13.png"><img style="background-image:none;padding-left:0;padding-right:0;display:inline;padding-top:0;border-width:0;margin:0;" title="image" border="0" alt="image" src="http://rootninja.files.wordpress.com/2011/04/image_thumb13.png" width="244" height="174"></a></p>
<p>You can now check on the www directory to see if the files are indeed created:</p>
<p><a href="http://rootninja.files.wordpress.com/2011/04/image14.png"><img style="background-image:none;padding-left:0;padding-right:0;display:inline;padding-top:0;border-width:0;margin:0;" title="image" border="0" alt="image" src="http://rootninja.files.wordpress.com/2011/04/image_thumb14.png" width="244" height="138"></a></p>
<blockquote><pre class="brush: c-sharp; toolbar: false">$cfg[\'Servers\'][$i][\'pmadb\'] = \'phpmyadmin\';
$cfg[\'Servers\'][$i][\'bookmarktable\'] = \'pma_bookmark\';
$cfg[\'Servers\'][$i][\'relation\'] = \'pma_relation\';
$cfg[\'Servers\'][$i][\'table_info\'] = \'pma_table_info\';
$cfg[\'Servers\'][$i][\'table_coords\'] = \'pma_table_coords\';
$cfg[\'Servers\'][$i][\'pdf_pages\'] = \'pma_pdf_pages\';
$cfg[\'Servers\'][$i][\'column_info\'] = \'pma_column_info\';
$cfg[\'Servers\'][$i][\'history\'] = \'pma_history\';
$cfg[\'Servers\'][$i][\'designer_coords\'] = \'pma_designer_coords\';</pre></blockquote>
';

$codes_pattern = '/\[sourcecode\slanguage\=\"(\w*)\"\]/';
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

	$markdown_blockquote_code[] = "\n```\n" . $blockquote_pres_code[$i] . "\n```\n";
	
}

$str = str_replace($blockquote_pres, $markdown_blockquote_code, $str);


$pre_tags_pattern = '/\<pre.*\>(.*)\<\/pre\>/s';
preg_match_all($pre_tags_pattern, $str, $pre_tags_matches);

$pre_tags = $pre_tags_matches[0];
$pre_tags_codes = $pre_tags_matches[1];

$markdown_pre_tags = array();

foreach($pre_tags_codes as $p){

	$markdown_pre_tags[] = "\n```\n" . $p . "\n```\n";
}

$str = str_replace($pre_tags, $markdown_pre_tags, $str);

echo $str;
?>

