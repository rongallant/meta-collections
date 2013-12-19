<?php
/**
  * Saves all info stored in metadatafield in de collection form 
  * 
  * The variable: <b>$this->postmetaprefix</b> precedes al metafield names in order to identify collection fields.<br/>
  * The function only saves fields precedes by this name.
  *
  * @category Wordpress Plugin
  * @package  Collections Wordpress plugin
  * @author   Bastiaan Blaauw <statuur@gmail.com>
  * @access   Public
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @see http://metacollections.statuur.nl/
*/

class savePostMeta extends Basics{
	
/**
  * Basic Constructor
  *
  * Loads vars from parent Basics and gets field information from Class Field<br/>
  * Tries to load the appropriate action or loads the default overvew
  * @access public   
  * @todo find out which functions can be made private and protected for security 
  * @global array $post post info
  */	
public function __construct(){
global $post;
	$this->init();

	$wysiwygs = array();
	foreach($_POST as $key=>$value){
	
	$searchfor="/{$this->postmetaprefix}/";
	 
	if(preg_match($searchfor, $key)){
	//collections_description----------2
	if(preg_match("/{$this->wysiwygs_string}/", $key)){///wysiwyg fields need special attention
	//1: rebuild the names make a new key and array collections_description----------1
	$wkey = explode($this->wysiwygs_string, $key);
	$wysiwygs[$wkey[0]][] = $value;
	}else{
	update_post_meta($post->ID, $key, $value); 
	}
	
	
	}
	}
	
	
	
	
	//now save the wysiwyg's per metafield ID
	foreach($wysiwygs as $key => $value){
	update_post_meta($post->ID, $key, $value);
	}
	
	
	
	

	
	}
	
		
}


?>