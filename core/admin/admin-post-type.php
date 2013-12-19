<?php
if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ){
	die("Access denied.");
}

/**
  * Contains all the functionality for building the actual additional interface with the metadataschema for you collection in post.php and edit.php.
  * 
  * All functions are derived from ajax requests defined in collections.php
  *
  * @category Wordpress Plugin
  * @package  Collections Wordpress plugin
  * @author   Bastiaan Blaauw <statuur@gmail.com>
  * @access   Public
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @see  http://metacollections.statuur.nl/   
  */

class buildInterface extends Basics{ 
/**
  * Basic Constructor
  *
  * Loads vars from parent Basics and gets field information from Class Field<br/>
  * Tries to load the appropriate action or loads the default overvew
  * @access public    
  */
public function __construct(){
	
	$this->init();
	$this->Field = new Field();
	$this->Field->getFields();
	$this->Field->getClasses();
	
	$this->SystemElements		= new SystemElements();
	
	$this->post_type = (get_post_type( $_GET[post])=="") ? $_GET[post_type] : get_post_type( $_GET[post]);
	$this->post_type = ($this->post_type=="") ? "post" : $this->post_type;
	///print_r($this->post_type);
	
	$this->Build();
	
	
	
	//if(isset($_POST[post_ID])){}
	
}

/**
  * Builds the actual additional interface in post.php and edit.php
  *
  * Get's al the related options for the collection and builds an additional interface with metaboxes and metadata fields
  * add_meta_box is a wordpress function for caching metaboxes info. Al metaboxes get there content from buildInterface::getFields()
  * @access public    
  */
public function Build(){
global $pagenow;
	$_SESSION[required]	= array();
	$this->metaboxes 	= get_option("metaboxes_".$this->post_type);
	$this->userinterface= get_option("userinterface_".$this->post_type);
	$this->metadataset 	= get_option("metadata_".$this->post_type); //{}
	$this->searchfor	= "/_nodrag/";
	//	print_r($this->metaboxes);
	if(is_array($this->metaboxes)){	
	
	foreach($this->isides as $context=>$container){
	
	if(is_array($this->metaboxes[$context])){	
	
	foreach($this->metaboxes[$context] as $metabox){//all sides
	
	if($metabox[ID]!="submitdiv" && !preg_match($this->searchfor, $metabox[ID])){
	
	add_meta_box($metabox[ID], $metabox[name], array($this, 'getFields'), $this->post_type, $context, 'core', array($void, $metabox));	
		
	}
	//else{
	
	//$function = preg_replace($this->searchfor, '', $metabox[ID]);
	//$function = preg_replace('/-/', '_', $function);//replace '-' with '_' . functions with '-' in name are not supported in php 
	//echo $function;
	//call_user_func(array($this->SystemElements, $function), 'inactive-system');
	
	
	//}
	
	}
	
	}
	}
	}
	
}


/**
  * Get the appropriate metafield in the metabox that calles this actions
  *
  * The metafield is called trough call_user_func which loads the appropriate class and always the function showfield. That;s part of the convention whereby the plugin is build.
  * @access public  
  * @global array $post post info
  * @param array $post post info
  * @param array $metaboxinfo metabox info
  * @return metadatafield html in user interface 
  */
function getFields($post, $metaboxinfo){
global $post;
//print_r($this->userinterface);
//echo gettype($this->userinterface);
if(gettype($this->userinterface)=="array"){
foreach($this->userinterface as $metadataID=>$UIinfo){
	//print_r($UIinfo);
	if($UIinfo[metaboxID]==$metaboxinfo[id] && $this->metadataset[$UIinfo[metadataID]][status]==1){//find the right custom fields with the metabox
	//echo"HIER ".$UIinfo[metadataID]."<br>";&& $metaboxinfo[status]==1	
	
	 $c = ucfirst($this->metadataset[$UIinfo[metadataID]][type]);
	 //echo"$metaboxinfo[status]";
	 //print_r();
	 $typeclass = new $c();
	
	 call_user_func(array($typeclass, 'showfield'), $post, $this->metadataset[$UIinfo[metadataID]]);
	//echo"<br/>";
	}
	
	}

}

}


}