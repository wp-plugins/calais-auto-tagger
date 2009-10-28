<?php
/***************************************************************************

Plugin Name: Calais Auto Tagger
Plugin URI: http://www.dangrossman.info/wp-calais-auto-tagger
Description: Suggests tags for your posts based on semantic analysis of your post content with the Open Calais API.
Version: 1.3
Author: Dan Grossman
Author URI: http://www.dangrossman.info

***************************************************************************/

//Include the Open Calais Tags class by Dan Grossman
//http://www.dangrossman.info/open-calais-tags
require('opencalais.php');

//Initialization to add the box to the post page
add_action('admin_menu', 'calais_init');
function calais_init() {
	add_meta_box('calais', 'Calais Auto Tagger', 'calais_box', 'post', 'normal', 'high');
	add_submenu_page('options-general.php', 'Calais API Key', 'Calais API Key', 10, __FILE__, 'calais_conf');
}

function calais_box() {
	?>
	<style type="text/css">
		.calais_tag {
			float: left;
			padding: 3px 6px 7px 3px;
			background: #e1f3fd;
			font-size: 8pt;
			color: #000;
			margin: 0 5px 5px 0;
		}
		.calais_tag img {
			position: relative;
			top: 3px;
		}
	</style>
	
	<?php require('js.inc'); ?>

	<?php
	//Existing post tags
	global $post;
	$existing_tags = wp_get_post_tags($post->ID);
	
	$tags = array();
	
	if (count($existing_tags) > 0) {
	    foreach ($existing_tags as $tag) {
	        if ($tag->taxonomy == 'post_tag')
	            $tags[] = $tag->name;
	    }
	}

	?>
	<input type="hidden" name="calais_taglist" id="calais_taglist" value="<?php echo implode(', ', $tags); ?>" />
		
	<label for="calais_manual">Add your own tags:</label>
	<br />
	<input type="text" name="calais_manual" id="calais_manual" value="" /> <input type="button" class="button" onclick="calais_add_manual()" value="Add Tags" />
	<br /><br />

	<b>Post Tags:</b>
	<br /><br />
	<div id="calais_tag_box" style="min-height: 40px">
	</div>

	<div style="clear: left"></div>
	
	<b>Suggested Tags:</b>
	<br /><br />
	<div id="calais_suggestions" style="min-height: 40px">

	</div>

	<div style="clear: left"></div>
	
	<input type="button" class="button" onclick="calais_gettags()" value="Get Tag Suggestions" /><br /><br />

	<script type="text/javascript"> calais_redisplay_tags(); </script>

<?php	
}

function calais_conf() {

	if (isset($_POST['calais-api-key'])) {
		update_option('calais-api-key', $_POST['calais-api-key']);
	}
	
	?>

	<div class="wrap">
	<h2>Calais Configuration</h2>
	<div class="narrow">
	<form action="" method="post" id="calais-conf" style="">
	
	<p>The Calais Auto Tagger plugin requires an Open Calais API key. If you don't have one, <a href"http://www.opencalais.com/" target="_blank">visit their site</a>, and click the "Register" link at the top of the page. Once you have an account, <a href="http://developer.opencalais.com/apps/register" target="_blank">fill out this form</a> to get an API key.</p>
	
	<p>
		<label for="calais-api-key">What is your Open Calais API Key?</label><br />
		<input type="text" name="calais-api-key" value="<?php echo get_option('calais-api-key'); ?>" />
	</p>

	<p class="submit">
		<input type="submit" value="Submit" />
	</p>
		
	</form>
	</div>
	</div>

	<?php	
}

add_action('save_post', 'calais_savetags', 10, 2);

function calais_savetags($post_id, $post) {

	if ($post->post_type == 'revision')
		return;

	$taglist = $_POST['calais_taglist'];
	$tags = split(', ', $taglist);
	if (strlen(trim($taglist)) > 0 && count($tags) > 0) {
		wp_set_post_tags($post_id, $tags);
	} else {
		wp_set_post_tags($post_id, array());
	}
	
}


//Register an AJAX hook for the function to get the tags
add_action('wp_ajax_calais_gettags', 'calais_gettags');

//This is the function that runs when the author requests tags for 
//their post. It connects to the Open Calais API, sends the post text,
//parses the entities returned and puts them into a tag list to return
function calais_gettags() {
	
	$content = stripslashes($_POST['text']);

	$key = get_option('calais-api-key');
	if (empty($key)) {
		die("You have not yet configured this plugin. You must add your Calais API key from the plugins page.");
	}
	
	$oc = new OpenCalais($key);
	$entities = $oc->getEntities($content);
	
	if (count($entities) == 0)
		die("No Tags");

	die(implode($entities, ', '));
	
}

