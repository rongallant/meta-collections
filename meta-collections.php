	<?php
/*
* Plugin Name: Meta Collections
* Plugin URI: http://wordpress.org/extend/plugins/meta-collections
* Author: Bastiaan Blaauw <statuur@gmail.com>	 
* Version: 2.0
* @see http://metacollections.statuur.nl/ 
* @category Wordpress Plugin
* @package  Collections Wordpress plugin
* @author URI: http://metacollections.statuur.nl/
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
* @Creation date: September 2012
* @access Public
* @Description: <strong>Meta Collentions&deg;</strong> turns Wordpress into a collection management system. Create Metadata Schema's with easy and intuitively create a user interface for the backend to manage you collection. Besides a professional Collection management tool Meta Collections&deg; can be used to create a Google maps marker collection or a recepy database. Meta Collentions&deg; comes with extensive help and developers documentation.

* Text Domain: _coll
*/

if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ){
	die("Access denied.");
}

 /**
  * This core class contains all the basic backend functionality for the plugin.
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @package Collections Wordpress plugin
  */
  
  
  
class Basics{ 
		var $dir;
		var $name							= "Collections";
		var $path;
		//var $pagenow;
		var $postmetaprefix					= "collections_";//makes all the postmeta generated in this plugin unique		
		var $systemprefix 					= "__system__" ; //makes all the system metaboxes unique / distinguishes it from user generated (used for supports in register_post_type)
		var $wysiwygs_string 				= "----------" ;
		var $nodrag 						= "_nodrag";
		
		var $system_columns 				= array(
					'id'					=>  array("id"=>"id", "label"=>"ID"),
					'title'					=>  array("id"=>"title","label"=>"Title"),
					'images'				=>  array("id"=>"images", "label"=>"Images"),
					'author'				=>  array("id"=>"author", "label"=>"Author"),
					'categories'			=>  array("id"=>"categories", "label"=>"Categories"),
					'tags'					=>  array("id"=>"tags", "label"=>"Tags"),
					'date'					=>  array("id"=>"date", "label"=>"Date")
		);
					
		var $supports 						= array(
					'title'					=> 1,//standard on
					'editor'				=> 1,//standard on
					'author'				=> 0,
					'thumbnail'				=> 0,
					'excerpt'				=> 0,
					'trackbacks'			=> 0,
					'custom-fields'			=> -1,//we don't want to use this option ever
					'comments'				=> 0,
					'revisions'				=> 0,
					'page-attributes'		=> -1,//we don't want to use this option ever
					'post-formats'			=> 0,
					'tags'					=> 0,
					'category'				=> 0
					);
		
		var $sides							= array(
					"normal"				=> "post-body-content", 
					"side"					=> "postbox-container-1",  
					"advanced"				=> "postbox-container-2", 
					"inactive-system"		=> "", 
					"inactive"				=> ""
					);
					
		var $isides							= array(//check is nessacery
					"normal"				=> "post-body-content", 
					"side"					=> "postbox-container-1",  
					"advanced"				=> "postbox-container-2"
					);			

		
/**
    * Constructor
    *
    * Loads basic function, javascript and css<br/>
    * Defines basic actions
    * Loads the proper locatisation
    * some rightsmanagement, only admin with the privileg&eacute; manage options will see the Collections menu and will be able to manage Collections configuration
    * Initializes the plugin and loads basic shared variables, function and actions
    * @access public
    * @global string $pagenow
    * @global string $current_user
    * @global array $post    
    
    */		
public function __construct(){
	global $pagenow, $current_user, $post;
	
	require(ABSPATH . WPINC . '/pluggable.php');
	
	
	
	load_plugin_textdomain('_coll', false, basename(dirname(__FILE__)).'/lang' );
	
	
	$this->init();
	$user = get_currentuserinfo();
	if (user_can($current_user, 'manage_options')) {
	add_action('admin_menu', array($this,'collection_menu'));	
	}	
	
	
	// $this->menu = add_action('admin_menu', array($this, 'wp_admin_widgets_admin_menu'));  
	/*********** ACTIONS FOR COLLECTIONS *************/
	
	
	/** loadproper css en js if on collection page  ($pagenow=="admin.php" && || $pagenow=="post.php") ***/
	if( $_GET[page]=="collections" || $_GET[page]=="manage-media-metadata"){
	add_action('admin_init', array($this, 'load_admin_scripts'));
	
	}
	
	if(!$_GET[page]=="collections" && ($pagenow=="admin.php" || $pagenow=="post.php" || $pagenow=="post-new.php")){
	add_action('admin_init', array($this, 'load_user_scripts'));

	}
	/** 
	* action that points to the function that saves the metadata 
	*/
	if($pagenow=="post.php" && isset($_POST[post_ID])){
	add_action('save_post', array( $this, 'savePostMeta') );
	}
	
	
	if($pagenow=="post-new.php" || ($pagenow=="post.php" && $_GET[action]=="edit" && !isset($_POST[post_ID]) ) ){//only on post edit pages && get_post_type( $_GET[post])==""
	add_action('admin_init', array( $this, 'buildinterface') );
	add_action('admin_footer', array( $this, 'postformvalidation') );

	}
	
	
	if($pagenow=="edit.php" && isset($_GET[post_type]) && $_GET[post_type]!="page" && $_GET[post_type]!="post" ){
	////todo only on post overview and only on registered post types from collections!
	////echo get_post_type($post);
	add_action( 'admin_init', array( $this, 'table_columns') );
	}
	
	
	add_action('init', array($this, 'LoadCollections'));
	add_action('admin_footer', array($this, 'LoadCss'));

	/** actions are further handled in included document ***/
	/*********** AJAX ACTIONS FOR COLLECTIONS *************/
	add_action('wp_ajax_editcollection', array($this, 'collection'));
	add_action('wp_ajax_deletecollection', array($this, 'collection'));
	add_action('wp_ajax_deletecollectioncontent', array($this, 'collection'));
	
	add_action('wp_ajax_savecollection', array($this, 'collection'));
	add_action('wp_ajax_collectionoverview', array($this, 'collection'));
	add_action('wp_ajax_check_post_type', array($this, 'collection'));
	
	add_action('wp_ajax_meta-box-order', array($this, 'collection'));

	
	/*********** AJAX ACTIONS FOR METADATA *************/
	add_action('wp_ajax_editmetadata', array($this, 'metadata'));
	//add_action('wp_ajax_newmetadata', array($this, 'metadata'));
	add_action('wp_ajax_metadata', array($this, 'metadata'));
	add_action('wp_ajax_delete_metafield', array($this, 'metadata'));
	add_action('wp_ajax_add_metafield', array($this, 'metadata'));
	add_action('wp_ajax_save_metafield', array($this, 'metadata'));
	add_action('wp_ajax_changemetafieldtype', array($this, 'metadata'));
	add_action('wp_ajax_changeinoverview', array($this, 'metadata'));
	add_action('wp_ajax_add_wysiwyg_field', array($this, 'metadata'));
	
	/*********** AJAX ACTIONS FOR USER INTERFACE *************/
	
	add_action('wp_ajax_rename_metabox', array($this, 'userinterface'));
	add_action('wp_ajax_save_metabox', array($this, 'userinterface'));
	add_action('wp_ajax_delete_metabox', array($this, 'userinterface'));
	add_action('wp_ajax_edituserinterface', array($this, 'userinterface'));
	add_action('wp_ajax_saveuserinterface', array($this, 'userinterface'));
	add_action('wp_ajax_editoverviewtable', array($this, 'userinterface'));	
	add_action('wp_ajax_savetableoverview', array($this, 'userinterface'));

	
	/*********** AJAX ACTIONS FOR USER MEDIA INTERFACE *************/
	add_action('wp_ajax_manage_mediametadata', array($this, 'manage_mediametadata'));
	add_shortcode( 'collections',				array( $this, 'convertShortcode') );	
	}


/**
    * Initiates Class <b>Basics</b> ad loads all plugin wide shared variables
    *
    * @access public
    */
public function init(){
	global $pagenow;
	$this->pagenow 	= $pagenow;
	$this->path 	= dirname(__FILE__);
	$this->basedir 	= plugins_url('',__FILE__);
	$this->action	= (isset($_POST[action]))? $_POST[action] : $_REQUEST[action] ;
	$this->searchfor= "/{$this->systemprefix}/";
}
     
         
/**
    * Converts the shortcode into the right metafield value
    *
    * @access public
    */
public function convertShortcode($attributes){
	global $post;

	if($attributes[metafield]!=""){
	$instance 	= ($attributes[instance]!="") ? $attributes[instance] : 0; 
	$value 		= get_post_meta($post->ID, "collections_".$attributes[metafield], true);
	
	if($instance=="all"){
	$output = "";
	$seperator = ($attributes[seperator]!="") ? $attributes[seperator] : " ";
	$seperator = ($seperator=="br") ? "<br/>" : $seperator;
	foreach($value as $v){
	$output.= $v.$seperator;	
	}
	
	}else{
	$output = $value[$instance];	
		
	}
	
	return $output;
	}
	
	
}

/**
    * Adds validation support to the post form for the Collection.
    *
    * All required fields and error messages are stored in $_SESSION[required] by an Field ID and an error message
    * This function generated the proper javascript. 
    * @access public    
    */
public function postformvalidation(){
	echo"<script>
	jQuery(document).ready(function(){
	
	jQuery('#post').submit(function(){
	return dopost()
	});
	
	jQuery('#publish').mousedown(function(){
	return dopost()
	});
		
   });
   
   function dopost(){
   if(
   jQuery('#post').validate(";
	
	if(count($_SESSION[required])>0){
	echo"{messages: {\n";
	
		
	$c=1;
	foreach($_SESSION[required] as $ID=>$message){
	$comma = ($c==count($_SESSION[required]))? "":", \n";
	echo"{$postmetaprefix}{$ID}: '{$message}' {$comma}"	;
	$c++;
	}
	
	echo"}}";
	}	
	echo").form()
	){
	
	}else{
	return false;
	}
   }
	
	</script>";
}


/**
    * Loads all the javascripts and css necessary for collection configuration management.
    *
    * @access public
    */   
public function load_admin_scripts(){ //for configuring Collections
	 wp_enqueue_script('jquery');
	
	 wp_enqueue_script('jquery.collections', plugins_url('/js/jquery.collections.js', __FILE__), '', '1.0'); //admin
	 wp_enqueue_script('jquery.tabs', plugins_url('/js/jquery.tabs.js', __FILE__), '', '1.0');	//admin
	 
	 wp_enqueue_script('metafield', plugins_url('/js/metafield.dev.js', __FILE__), '');	//admin
	 wp_enqueue_script('jquery.dd', plugins_url('/js/jquery.dd.min.js', __FILE__), '', '2.38.4');	//admin
	 wp_enqueue_script('widgets'); //?
	 wp_enqueue_script('postbox'); //admin
	 wp_enqueue_script('post'); //admin
	 wp_enqueue_script('wp-pointer');//admin
	 
	 //wp_enqueue_style('collections',  plugins_url('/css/collections.css', __FILE__), '', '1.0'); //admin
	 wp_enqueue_style('css.dd',  plugins_url('/css/msdropdown/dd.css', __FILE__)); //admin
	 wp_enqueue_style('collections-admin',  plugins_url('/css/collections-admin.css', __FILE__), '', '1.0'); //admin
	 wp_enqueue_style('wp-pointer');//admin
	 
	 $this->load_user_scripts();
}

/**
    * Loads all the javascripts and css necessary for adding and editing resources to the collection in <b>post(-new).php</b> and <b>edit.php</b>.
    * @access public
    */
public function load_user_scripts(){ //for Collections management one function for overlapping
 	 wp_enqueue_style( 'collections-post',  plugins_url('/css/collections-post.css', __FILE__), '', '1.0'); //admin
	 wp_enqueue_script( 'jquery.validate.min', plugins_url('/js/jquery.validate.min.js', __FILE__), '', '1.7');//user					
	 wp_enqueue_script( 'googleapis', 'http://maps.google.com/maps/api/js?sensor=false', '', '3.0'); //user
	 wp_enqueue_script( 'jquery.googlemaps', plugins_url('/js/jquery.googlemaps.js', __FILE__), '', '1.0'); //user
	 wp_enqueue_script('jquery.widget', plugins_url('/js/jquery.ui.widget.min.js', __FILE__), '', '1.0');	//admin
	
	 wp_enqueue_script( 'jquery.mobiscroll-2.0.2.custom.min', plugins_url('/js/mobiscroll-2.0.2.custom.min.js', __FILE__), '', '2.0.1'); //admin
	 wp_enqueue_style( 'mobiscroll.core-2.0.2',  plugins_url('/css/mobiscroll.core-2.0.2.css', __FILE__), '', '2.0.1'); //admin
	 
	  
	 wp_enqueue_script('jquery-ui-datepicker');
	 wp_enqueue_script('jquery-ui-widget');
	 	 
	 //wp_enqueue_script('jquery.ui.datepicker', plugins_url('/js/jquery-ui-1.8.20.custom.min.js', __FILE__), '', '1.8.2.0'); //admin
	 //wp_enqueue_script('jquery.ui.datepicker', plugins_url('/js/jquery-ui.min.js', __FILE__), '', '1.9.2'); //admin
	  //wp_enqueue_script(' jquery-ui-core , '', '1.9.2'); //admin
	  //jquery-ui-core 
	   
	   wp_enqueue_script('jquery.colorpicker.js', plugins_url('/js/jquery.colorpicker.js', __FILE__), '', '1.0.6'); //admin
	 //wp_enqueue_script('jquery.colorpickerlang.js', plugins_url('/js/i18n/jquery.ui.colorpicker-nl.js', __FILE__), '', '0.9.2'); //admin

	 //wp_enqueue_style('jquery-ui',plugins_url('/css/smoothness/jquery-ui-1.10.3.custom.min.css', __FILE__));
	 wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/overcast/jquery-ui.css');
	 wp_enqueue_style('css.colorpicker', plugins_url('/css/jquery.colorpicker.css', __FILE__));
	 wp_enqueue_script('jquery.collections.post', plugins_url('/js/jquery.collections-post.js', __FILE__), '', '1.0'); //admin

	}


/**
    * Prepares intervention in the Collection overview table by adding a filter (for table heads) and an action 9for table body)
    *
    * @access public
    */
public function table_columns(){
	 add_filter("manage_edit-{$_GET[post_type]}_columns", array($this, 'add_table_columns'));
	 add_action("manage_{$_GET[post_type]}_posts_custom_column", array($this,'manage_columns'), 10, 2);
}

/**
    * Adds the head columns to the Collection overview table 
    *	
    * The content and order is generate by looping option $tableorder and comparing it with 
    * @access public
    * @todo find out how to manage (show / hide) present and used system fields such as id, author and categories
    */
public function add_table_columns($columns) {//we wan't to know which custom fields are checked for use in overview table here	
global $post;

$post_type 		= get_post_type( $post );
$metadataset 	= get_option("metadata_".$post_type); //{}
$metadataset 	= (!is_array($metadataset )) ? array() :$metadataset; 	

$tableorder		= get_option("tableorder_".$post_type);	
$tableorder		= explode(",",$tableorder[active]);
$new_columns['cb'] = '<input type="checkbox" />' ;
			foreach($tableorder as $rawmetafieldID){
			$patterns = array('/system-element-/', '/meta-element-/');
			$metafieldID 				= preg_replace($patterns,"", $rawmetafieldID);
			$is_meta					= preg_match("/meta-element-/", $rawmetafieldID);
			$label 						= (preg_match("/system-element-/", $rawmetafieldID))? $this->system_columns[$metafieldID][label] : $metadataset[$metafieldID][label]  ;
			$label 						= (preg_match("/system-element-/", $rawmetafieldID))? __($label) :$label;
			$new_columns[$metafieldID] 	= $label;
			}
			
		return $new_columns;
	}
	
	
/**
    * Adds the body columns with values to the Collection overview table
    *
    * @access public
    */
function manage_columns($column_name, $id) {
global $wpdb;
	$name	 = $this->postmetaprefix.$column_name;
	$value	 = get_post_meta($id, $name, true); 
///get the field type


if( $column_name=="id"){
		echo $id;
}else if($column_name=="images"){
//$num_images = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = {$id};"));
$num_images = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = {$id};");
echo $num_images; 
}else{

if(is_array($value)){
$key = array_keys($value);

$s=1;
foreach($value as $v){
if($s==3){
echo"...";	
break;
}
$v 	= (strlen($v)>50) ? substr($v, 0,50)."..." : $v;
$br = ($s<count($value))? "<br/>" :"";
echo $v.$br;
$s++;

}
//print_r($value);	
}else{

//determine fieldtype
$post_type		= get_post_type($id);
$metadataset 	= get_option("metadata_".$post_type); //{}	
$fieldtype 		= $metadataset[$column_name]['type']; 


if($fieldtype=="taxonomy"){
$terms 			= get_the_terms($id, $column_name);

foreach($terms as $term){
echo $term->name."<br/>";;
}
}
	
}

}
			
	}	


/**
    * Includes the right files for Filed management
    *
    * @access public
    * @todo function includes should have a better name e.g. include_fieldfiles()
    */
public function includes(){
	//if($_SERVER[HTTP_X_REQUESTED_WITH]=="XMLHttpRequest"){
	include(ABSPATH."wp-content/plugins/meta-collections/core/field.php");
	include(ABSPATH."wp-content/plugins/meta-collections/core/system_elements.php");
	//}
}


/**
    * Loads the css in order to display the Collection icon in the menu in a proper way becuase the plaugin works with menu icon sprites.
    *
    * @access public
    */
public function LoadCss(){
$cpts = get_option("collection_cpt");
$cpts_css = array();

if(is_array($cpts))
foreach($cpts as $post_type=>$args){

if($args[active]=="1"){
$cpts_css[$post_type] = "


#menu-posts-{$post_type} .wp-menu-image{
/* prevents the sprite from overflowing and overlap the menu item below collections config menu .wp-not-current-submenu*/
	overflow:hidden;	
}

/* proper menu image behaviour with sprite image */	
#menu-posts-{$post_type}.wp-not-current-submenu .wp-menu-image img{
	position:relative;
	top:-24px;
}

#menu-posts-$post_type.wp-not-current-submenu:hover img{
	position:relative;
	top:0px;
}
";

}

}

//if(count($cpts_css)>0){
 echo "<style type=\"text/css\">
 #toplevel_page_collections .wp-menu-image{
/* prevents the sprite from overflowing and overlap the menu item below collections config menu .wp-not-current-submenu*/
	overflow:hidden;	
}

/* proper menu image behaviour with sprite image */	
#toplevel_page_collections.wp-not-current-submenu .wp-menu-image img{
	position:relative;
	top:-24px;
}

#toplevel_page_collections.wp-not-current-submenu:hover img{
	position:relative;
	top:0px;
}

label.error{
	color:red;
	padding: 0px 0px 0px 15px;
}

";

foreach($cpts_css as $cpt_css){
          echo $cpt_css ."\n";

}

echo"</style>";
         
//}
}


/**
    * Registers all the active Collections trought register_post_type 
    *
    * @access public
    */
public function LoadCollections(){
$cpts = get_option("collection_cpt");


if(is_array($cpts))
foreach($cpts as $post_type=>$args){
	
if($args[active]=="1"){
	 $args[labels][name] = stripslashes($args[labels][name]); 
	$labels = array(
    'name' 				=> stripslashes($args[labels][name]),
    'singular_name' 	=> $args[labels][singular_name],
    'add_new' 			=> $args[labels][add_new],
    'add_new_item' 		=> $args[labels][add_new_item],
    'edit_item' 		=> $args[labels][edit_item],
    'new_item' 			=> $args[labels][new_item],
    'all_items' 		=> $args[labels][all_items],
    'view_item' 		=> $args[labels][view_item],
    'search_items' 		=> $args[labels][search_items],
    'not_found' 		=> $args[labels][not_found],
    'not_found_in_trash'=> $args[labels][not_found_in_trash], 
    'parent_item_colon' => '',
    'menu_name' 		=> stripslashes($args[labels][name])

  );
  $pattern = "/%s/";
  
  /* replace all %s and %n with labels name and singular name admin-collections.php :: save_collection */
  $labels= preg_replace("/%s/", stripslashes($labels[name]),  $labels);
  $labels= preg_replace("/%n/", stripslashes($labels[singular_name]),  $labels);
 
 
 $metaboxes 		= get_option("metaboxes_".$post_type);
//print_r($metaboxes);	
 $supports			= array();
 $taxonomies 		= array();

if(is_array($metaboxes)){

foreach($this->isides as $context=>$divname){
	
if(is_array($metaboxes[$context])){	
foreach(array_keys($metaboxes[$context]) as $metaboxID){
	$searchfor	="/{$this->systemprefix}/";
	if(preg_match($searchfor, $metaboxID) ){
	
	if($metaboxID=="__system__post_tag" || $metaboxID=="__system__category"){
	$taxonomies[]	= preg_replace($searchfor, '', $metaboxID);
	}else{
	$supports[] 	= preg_replace($searchfor, '', $metaboxID);

	}
	
	}
}
}
	
}

}
	
/*
  if($args['categories']==1){
	$taxonomies[] = 'category';  
  }
   print_r($taxonomies);
  if($args['tags']==1){
	$taxonomies[] = 'post_tag';  
  }
  */
 
  $args = array(
    'labels' 			=> $labels,
    'public' 			=> $args['public'],
    'publicly_queryable'=> $args['public'],
    'show_ui' 			=> true, 
    'show_in_menu' 		=> true, 
    'query_var' 		=> true,
    'rewrite' 			=> array( 'slug' => $post_type, 'with_front' => false ),
    'capability_type' 	=> 'post',
    'has_archive' 		=> true, 
    'hierarchical' 		=> false,
    'menu_position' 	=> $args['menu_position'],
    'menu_icon'			=> $args['menu_icon'],
    'taxonomies' 		=> $taxonomies,
    'supports' 			=> $supports//array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments','author', 'trackbacks', 'revisions', 'post-formats')// 'custom-fields'get this values out supports option
    
  );
	
	
	if(!post_type_exists( $post_type) ){
	register_post_type($post_type, $args );
	$this->get_taxonomies($post_type);
	}
	
		 
}
}


}


/**
    * Called from Basics::loadCollections() to get the related Taxonomies for a Collection (post type) 
    *
    * @access public
    * @param string $post_type
    * @return registers the taxonmies with the Collection
    */
public function get_taxonomies($post_type){
	
	$this->metadataset 	= get_option("metadata_".$post_type);
	if(is_array($this->metadataset)){
		$types 				= $this->search_nested_arrays($this->metadataset, "type");
		$has_taxonomies		= in_array("taxonomy", $types);
		
		if($has_taxonomies){ //if there are resistered taxonomy fields loop trough them
			
			foreach($this->metadataset as $metadataID=>$metaINFO){ 
		
				if($metaINFO[type]=="taxonomy" && $metaINFO[status]==1){
				
				$labels = array(
			    'name' => stripslashes($metaINFO[label]),
			    'singular_name' => __( $metaINFO[label], 'taxonomy singular name' ),
			    'search_items' =>  __('Search')." ".stripslashes($metaINFO[label]),
			    'all_items' => __( 'All')." ".stripslashes($metaINFO[label]),
			    'parent_item' => __( 'Parent ')." ".$metaINFO[singular_name],
			    'parent_item_colon' => __( 'Parent')." ".stripslashes($metaINFO[singular_name]),
			    'edit_item' => __( 'Edit')." ".stripslashes($metaINFO[label]), 
			    'update_item' => __( 'Update')." ".stripslashes($metaINFO[label]),
			    'add_new_item' => __( 'Add New', "_coll" )." ".stripslashes($metaINFO[label]),
			    'new_item_name' => __( 'New Name:')." ".stripslashes($metaINFO[label]),
			    'menu_name' => stripslashes($metaINFO[label])
			  ); 	
			  
			  
			    register_taxonomy($metaINFO[ID],array($post_type), array(
			    'hierarchical' 		=> $metaINFO[hierarchical],
			    'labels'			=> $labels,
			    'show_in_nav_menus'	=> $metaINFO[show_in_nav_menus],	
			    'show_ui' 			=> true,
			    'query_var'			=> true,
			    'rewrite' 			=> array( 'slug' =>$metaINFO[ID]),
			    	));
  
					//print_r($metaINFO[ID]);	 
			}
		}
	}

}


}


/**
    * Generates the collection menu in the admin backend
    *
    * @access public
    */
public function collection_menu() {
			// add collections menu to options menu	
			$this->menu = add_utility_page(__("Collections",'_coll'), __("Collections",'_coll'), 'manage_options', 'collections', array($this,'collection_from_menu'), $this->basedir."/images/cpt-icons/block.png");

			//$this->pagehook	= add_submenu_page( 'collection-config', __("User interfaces",'_coll'), __("User interfaces",'_coll') , 'manage_options', 'userinterface', array($this,'userinterface'));
			//add_submenu_page( 'collection-config', __("Metadata Sets",'_coll'), __("Metadata Sets",'_coll') , 'manage_options', 'metadata', array($this,'metadata'));			 
			//add_submenu_page( 'collections', __("Media Metadata",'_coll'), __("Media Metadata",'_coll') , 'manage_options', 'manage-media-metadata', array($this,'manage_media_metadata')); 			
			//add_submenu_page( 'collection-config', __("Import Mappings",'_coll'), __("Import Mappings",'_coll') , 'manage_options', 'import-metadata', array($this,'import_metadata')); 			
			//add_submenu_page( 'collection-config', __("Create shortcodes",'_coll'), __("Create shortcodes",'_coll') , 'manage_options', 'create_shortcode', array($this,'create_shortcode')); 
			
			}

	
/**
    * Portals all actions related to metadata management (defined in Basics::__construct()) to admin-metadata.php <b>Class Metadata</b>
    * 
    * All actions are calles trough an ajax request
    * @access public
    */	
public function metadata(){
	$this->includes();
	include('core/admin/admin-metadata.php');
	$this->Metadata	= new Metadata();
	die();
}	

/**
    * Portals all actions related to user interface management (defined in Basics::__construct()) to admin-ui.php <b>Class UI</b>
    * 
    * All actions are calles trough an ajax request
    * @access public
    */		
public function userinterface(){
	$this->includes();
	include('core/admin/admin-ui.php');
	$this->UI	= new UI();
	die();
}
		

/**
    * Portals all actions related to basic collection management (defined in Basics::__construct()) to admin-collections.php <b>Class Collections</b>
    * 
    * @access public
    */	
function collection(){

	$this->includes();
	//echo $_POST[action];
	include('core/admin/admin-collections.php');
	$this->Collections	= new Collections();
	die();
}

/**
    * Portals all actions related to basic collection management (defined in Basics::__construct()) to admin-metadata.php <b>Class UI</b>
    * 
    * All actions are calles trough an ajax request except CollectionOverview
    * @access public
    */	
function collection_from_menu(){
	$this->includes();
	include('core/admin/admin-collections.php');
	$this->Collections	= new Collections();
	
}



/**
    * Portals the action save post meta to admin-save-post-type.php <b>Class savePostMeta</b>
    * 
    * Single action
    * @access public
   
    */
function savePostMeta(){

// do not call action if it concerns an auto save routine
//if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

if($_SESSION[saved]!=1){ 
//TODO the hook os called twice and we don't want to redefine a class twice. use the defined class and function the second time
	
	
	$this->includes();
	include(ABSPATH."wp-content/plugins/meta-collections/core/admin/admin-save-post-type.php");
	$this->savePostMeta	= new savePostMeta();
	$_SESSION[saved]=1;
	}
	}


/**
    * Called from the collection menu
    *
    * @access public
    * @param string $post_type
    * @todo develop functionallity to add metadata fields to the wordpress media schema.
    */
function manage_media_metadata(){
	$this->includes();
	include(ABSPATH."wp-content/plugins/vcollections/core/admin/admin-media-metadata.php");
	$this->mediametadata	= new mediaMetadata();

}

/**
    * Portals all actions related to building the actual interface for managing Collections (defined in Basics::__construct()) to admin-post-type.php <b>Class UI</b>
    * 
    * Single action
    * @access public
    */	
function buildinterface(){
	$this->includes();
	
	include(ABSPATH."wp-content/plugins/meta-collections/core/admin/admin-post-type.php");
	$this->buildInterface	= new buildInterface();
	
	
	}

/**
    * Portals all actions related to building the actual interface for managing Collections (defined in Basics::__construct()) to admin-post-type.php <b>Class UI</b>
    * 
    * Not used yet
    * @access public
    * @todo Develope functionality if necessary and make faq site.s
    */	
public function addSettingsLink($links){
			array_unshift( $links, '<a href="http://collections.statuur.nl/faq/">Help</a>' );
			array_unshift( $links, "<a href=\"options-general.php?page=collection-config\">".__("Settings",'_coll')."</a>");
			
			return $links; 
}


/**
    * Generates a help icon with the proper tooltip message 
    * 
    * @access public
    * @param string $item title for the tooltip
    * @param string $tip body messsage for the tooltip
    */
function helpicon($item, $tip=null){//item is title
	return "<img src=\"{$this->basedir}/images/help.gif\" class=\"tooltips\" title=\"".__($item,"_coll")."\"  rel=\"".__($tip,"_coll")."\" />";
}


/**
    * Generates proper 'slug' and ids from labels and other names 
    * 
    * removes spaces and special chars from label or name
    * @access public
    * @param string $text to be slugified 
    */
static public function slugify($text)
{ 
  // replace non letter or digits by _
  $text = preg_replace('~[^\\pL\d]+~u', '_', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text))
  {
    return 'n-a';
  }

  return $text;
}

/**
    * GSearched in Nested multidimensional arrays and returns results 
    * 
    * removes spaces and special chars from label or name
    * @access public
    * @param string $text to be slugified 
    * @return false if no results are found otherwise the results
    * @todo create a function that not only finds keys in nested multidimensional arrays but also the multidimenional index to be able to point directly to the values in the array
    */
function search_nested_arrays($array, $key){
    if(is_object($array))
        $array = (array)$array;
   
    // search for the key
    $result = array();
    foreach ($array as $k => $value) {
        if(is_array($value) || is_object($value)){
            $r = $this->search_nested_arrays($value, $key);
            if(!is_null($r))
                array_push($result,$r);
        }
    }
   
    if(array_key_exists($key, $array))
        array_push($result,$array[$key]);
   
   
    if(count($result) > 0){
        // resolve nested arrays
        $result_plain = array();
        foreach ($result as $k => $value) {
            if(is_array($value))
                $result_plain = array_merge($result_plain,$value);
            else
                array_push($result_plain,$value);
        }
        return $result_plain;
    }
    return NULL;
}
}


$collections = new Basics();


?>