<?php
/**
  * Contains all the functionality for managing your metadata schema in the backend.
  * 
  * All functions are derived from ajax requests defined in collections.php
  *
  * @category Wordpress Plugin
  * @package  Collections Wordpress plugin
  * @author   Bastiaan Blaauw <statuur@gmail.com>
  * @version  1.0
  * @access   Public
  * @license  GPL2
  * @see      http://metacollections.statuur.nl/ 
*/
class SystemElements extends Basics{
	
function __construct(){
		$this->init();
		
		/*
add_meta_box('submitdiv', __('Publish'), 'post_submit_meta_box', $pagenow, 'side', 'core');	//publish
add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', $pagenow, 'side', 'low'); //featured image
add_meta_box( 'formatdiv', _x( 'Format', 'post format' ), 'post_format_meta_box', $pagenow, 'side', 'core' ); //notation
add_meta_box('pageparentdiv', 'page' == $_POST[cpt] ? __('Page Attributes') : __('Attributes'), 'page_attributes_meta_box', $pagenow, 'side', 'core');


add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', $pagenow, 'normal', 'core');
add_meta_box('trackbacksdiv', __('Send Trackbacks'), 'post_trackback_meta_box', $pagenow, 'normal', 'core');

add_meta_box('commentstatusdiv', __('Discussion'), 'post_comment_status_meta_box', $pagenow, 'normal', 'core');
add_meta_box('commentsdiv', __('Comments'), 'post_comment_meta_box', $pagenow, 'normal', 'core');
add_meta_box('slugdiv', __('Slug'), 'post_slug_meta_box', $pagenow, 'normal', 'core');

//'supports' 			=> array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments','author', 'trackbacks', 'custom-fields', 'revisions', 'post-formats')//dezen in andere option opslaan


		*/
		
	}
	
	 
function taxonomy($taxonomy){
global $pagenow;	
	add_meta_box('tagsdiv-' . $taxonomy[ID].$this->nodrag, $taxonomy[label]." <span class=\"description\">taxonomy</span>", 'post_tags_meta_box', $pagenow, 'side', 'core', array( 'taxonomy' => $taxonomy[ID] ));
//print_r($taxonomy);
}	
	
function title($context){
global $pagenow;
	add_meta_box($this->systemprefix.'title'.$this->nodrag, _( 'Title'), array($this, 'get_title_contents'), $pagenow, $context, 'core' ); //notation
	
}	


function editor($context){
global $pagenow;
	add_meta_box( $this->systemprefix.'editor'.$this->nodrag, _( 'Editor'), array($this, 'get_editor_contents'), $pagenow, $context, 'core' ); //notation
}

function thumbnail($context){
global $pagenow;
	add_meta_box($this->systemprefix.'thumbnail', __('Featured Image'), 'post_thumbnail_meta_box', $pagenow, $context, 'low');	
}

function excerpt($context){
global $pagenow;
	add_meta_box($this->systemprefix.'excerpt', __('Excerpt'), 'post_excerpt_meta_box', $pagenow, $context, 'core');

}

function comments($context){
global $pagenow;
	add_meta_box($this->systemprefix.'comments', __('Comments'), 'post_comment_meta_box', $pagenow, $context, 'core');
}

function author($context){
global $pagenow;
	add_meta_box($this->systemprefix.'author', __('Author'), 'post_author_meta_box', null, 'normal', 'core');
}


function trackbacks($context){
global $pagenow;
	add_meta_box($this->systemprefix.'trackbacks', __('Send Trackbacks'), 'post_trackback_meta_box', $pagenow, $context, 'core');

}

function custom_fields(){}//just to be sure;


function revisions($context){
global $pagenow;
	add_meta_box($this->systemprefix.'revisions', __('Revisions'), 'post_revisions_meta_box', $pagenow, $context, 'core');
}

function post_formats($context){
global $pagenow;
	add_meta_box($this->systemprefix.'post_formats', _x( 'Format', 'post format' ), 'post_format_meta_box', $pagenow, 'side', 'core' ); //notation
}











function get_title_contents($context){
	echo"<div id=\"titlediv\">
<div id=\"titlewrap\">
	<label class=\"hide-if-no-js\" style=\"visibility:hidden\" id=\"title-prompt-text\" for=\"title\">".__( 'Enter title here' )."</label>
	<input type=\"text\" disabled name=\"post_title\" size=\"30\" tabindex=\"1\" value=\"".__( 'Enter title here' )."\" id=\"title\" autocomplete=\"off\" />
</div>
<div class=\"inside\"></div>
</div>";
}

function get_editor_contents($context){
	wp_editor($post->post_content, 'content', array('dfw' => true, 'tabindex' => 1, 'textarea_rows' => 4) ); 
}


}

?>