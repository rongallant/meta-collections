<?php 
if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ){
	die("Access denied.");
}

 /**
  * Handles all functions regarding creation, edit and deletion of basic collections
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @version  1.0
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */
class Collections extends Basics{ 
/**
  * Basic Constructor
  *
  * Loads vars from parent Basics and gets field information from Class Field<br/>
  * Tries to load the appropriate action or loads the default overvew
  * @access public    
  */
public function __construct(){
	$this->init();
	//print_r($this);
	$this->Field = new Field();
	$this->Field->getFields();
	$this->Field->getClasses();	
	$this->action = ($this->action=="") ? "CollectionOverview": $this->action;
	
	if($this->action!=""){
	call_user_func(array($this, $this->action)); // As of PHP 5.3.0
	//$this::Overview();
	//echo"end";
	}else{
	$this::Overview();
	}
		
}

/**
    * Get's all positions where a collections may be places in the admin menu     
    * @access public    
    */
function getMenuPositions(){
	
	return array(
"5"=>__('Below Posts','_coll'), 
"10"=>__('Below Media','_coll'), 
"15"=>__('Below Links','_coll'), 
"20"=>__('Below Pages','_coll'), 
"25"=>__('Below Comments','_coll'), 
"60"=>__('Below First Seperator','_coll'), 
"65"=>__('Below Plugins','_coll'), 
"70"=>__('Below Users','_coll'), 
"75"=>__('Below Tools','_coll'), 
"80"=>__('Below Settings','_coll'), 
"100"=>__('Below Second Seperator','_coll') 
);

}

/**
    * Get's all menu icons by reading trough the images/cpt-icons folder
    *
    * Reads trough the folder and stored all png's in an alphabetically ordered array
    * @access public    
    */
public function getMenuIcons(){
	$items = array();
	
	if ($handle = opendir($this->path.'/images/cpt-icons')) {

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
        $extension = substr($entry, strlen($entry)-3, strlen($entry));
        if($extension=="png"){
     //   echo $entry ." - ". $extension ."<br/>";
    $items[] = $entry;
    }
}
    closedir($handle);
}
	natcasesort($items);
	return $items;
}

/**
    * Deletes a collection content for a deleted collection option
    * @access public
    * @param object $wpdb to enable database querying
    */
function deletecollectioncontent(){
 global $wpdb;	
 $qry = "delete FROM wp_posts where guid like '%?post_type={$_POST[cpt]}%'";		
 $states = $wpdb->get_results( $qry );							
echo __("Content deleted","_coll");
}



/**
    * Deletes a collection and al the data related to that collection
    * @access public
    */
function deletecollection(){
	
	delete_option( "metaboxes_".$_POST[cpt]); 
	delete_option( "metadata_".$_POST[cpt]);  
	delete_option( "userinterface_".$_POST[cpt]); 
	
	$cpts = get_option("collection_cpt");								
	unset($cpts[$_POST[cpt]]);			
	update_option( "collection_cpt", $cpts, '', 'no'); 									
echo __("Do you wan't to delete the entire content of the collection too?","_coll")." <a class=\"button-primary\" href=\"#\" 
onclick=\"deletecollectioncontent('{$_POST[cpt]}');\"

>".__("Yes")."</a> 
<a href=\"#\" onclick=\"jQuery('#setting-error-settings_updated').slideToggle('slow');clearTimeout(message_to)\" class=\"button\">".__("No")."</a>";
}

/**
    * Save new or edited Collection info from editCollection()
    *
    * All data is stored in array collection_cpt. The data is used later for the function register_post_type
    * All related 'options' used for storing user interface data, metabox data and metafield data are created here also
    * The function uses array $this->supports to store a number of system metaboxes in inactive
    * This function can store existing and new collections.
    * Always called trough an ajax request.
    * Uses $_POST posted values.
    * @access public
    */
public function saveCollection(){	
	
	$post_type			= ($_POST[post_type]=="") ? $this->slugify($_POST[name]) : $_POST[post_type];
	$_POST[name]		= preg_replace('/[\/\&%#;$]/', '', $_POST[name]);	
	$_POST[name] 		= stripslashes($_POST[name]);  
	$labels = array
				(
					'name'					=> $_POST[name],
					'singular_name'			=> $_POST[singular_name],
					'add_new'				=> __( 'Add New', '_coll'),
					'add_new_item'			=> __( 'Add New %n', '_coll'),
					'edit'					=> __( 'Edit', '_coll'),
					'edit_item'				=> __( 'Edit %s', '_coll'),
					'new_item'				=> __( 'New %s', '_coll' ),
					'all_items' 			=> __( 'All %s', '_coll'),
					'view'					=> __( 'View %s', '_coll' ),
					'view_item'				=> __( 'View %s' , '_coll'),
					'search_items'			=> __( 'Search %n', '_coll' ),
					'not_found'				=> __( 'No %s found', '_coll' ),
					'not_found_in_trash'	=> __( 'No %s found in Trash' , '_coll'),
					'parent'				=> __( 'Parent %n', '_coll' ),
					'parent_item_colon' 	=> "{$_POST[name]}",
					'menu_name' 			=> "{$_POST[name]}"
					);

	$cpt = array(
					//'post_type'			=> $_POST[post_type],
					'labels'			=> $labels,
					'description'		=> $_POST[description],
					'singular_label'	=> $_POST[singular_name],
					'public'			=> $_POST['public'],
					'menu_position'		=> $_POST[menu_position],
					'hierarchical'		=> false,
					'capability_type'	=> 'post',
					'rewrite'			=> array( 'slug' => $post_type, 'with_front' => false),
					'menu_icon'			=> $_POST[menu_icon],
					'query_var'			=> true,
					//'supports'			=> array('title', 'editor', 'author', 'thumbnail', 'comments', 'revisions'),//in metaboxes option
					'active'			=> $_POST[active]	
				);	
				
				
					$ecpts = get_option("collection_cpt");
					
					if($_POST[post_type]==""){//if it concerns a new collection
					$post_type			= $this->slugify($_POST[name]);
					/*
					$metadataset 	= get_option("metadata_".$_POST[cpt]);
					$metaboxes 		= get_option("metaboxes_".$_POST[cpt]);
					$userinterface	= get_option("userinterface_".$_POST[cpt]);
					*/
					//generate supports
					
					//update_option( "collection_cpt", $ecpts, '', 'no'); 
					 
					 $metaboxes = "";//array();
					
					 $i=0;
					 foreach($this->supports as $supports=>$setting){
					
					 if($setting!=-1){
					 $systemname = $this->systemprefix . $supports;
					 $context = ($setting==1) ? "normal" : "inactive-system";
					 $metaboxes[$context][$systemname] = array("ID"=>$supports, "order"=>$i);
					 $i++;
					 }
					 }
					 
					 
					 
					 add_option("metaboxes_".$post_type, $metaboxes, "", "no");
					 add_option("metadata_".$post_type, "", "", "no");
					 add_option("userinterface_".$post_type, "", "", "no");
					
					
										
					
					$ecpts[$post_type]= $cpt;
					//print_r($ecpts);
					}else if(is_array($ecpts[$_POST[post_type]])){
					$ecpts[$_POST[post_type]]= $cpt;	
					}
					
					//$ecpts = get_option("collection_cpt");
					update_option( "collection_cpt", $ecpts, '', 'no'); 
					//create a metaboxes array with inactive system elements
	
}


/**
    * Generates the initial Collection overview table
    * Basic collection info is stored in get_option("collection_cpt")
    * @access public
    */
public function CollectionOverview(){

$supports = array
				(
					'title'					=> 0,
					'editor'				=> 0,
					'author'				=> 0,
					'thumbnail'				=> 0,
					'excerpt'				=> 0,
					'trackbacks'			=> 0,
					'custom-fields'			=> -1,//we don't want to use this option ever
					'comments'				=> 0,
					'revisions'				=> 0,
					'page-attributes'		=> -1,//we don't want to use this option ever
					'post-formats'			=> 0
					);
					
		
				

echo"<div class=\"wrap\" id=\"collections_wrapper\">
<div class=\"icon32\" id=\"icon-options-general\"><br></div><h2>
".__("Collections management",'_coll')." <a class=\"add-new-h2 ajaxify\" rel=\"action:editcollection\" href=\"admin-ajax.php\">".__("Add New Collection",'_coll')."</a>
</h2>  <div style=\"height:40px;width:100%;padding:7px;\"><div class=\"updated settings-error\" style=\"display:none;\" id=\"setting-error-settings_updated\"></div></div>";




echo"   <div class=\"form-wrap\">
          <form id=\"form_edit\" action=\"{$_SERVER[REQUEST_URI]}\" method=\"post\" >
          <table class=\"widefat tag fixed\" cellspacing=\"0\">
            <thead class=\"content-types-list\">
              <tr>
               
                <th style=\"\" class=\"manage-column column-name\" id=\"name\" scope=\"col\">".__('Label','_coll')."</th>
                <th style=\"\" class=\"manage-column column-fields\" id=\"label\" scope=\"col\">".__('Post type','_coll')."</th>
              </tr>
            </thead>
            <tbody id=\"the-list\">";
            
            $cpts = get_option("collection_cpt");
			
			if(is_array($cpts))
            foreach($cpts as $postype => $cpt){ 
           
	         $cpt['labels']['name'] = stripslashes($cpt['labels']['name']);  	
            $opacity = ($cpt['active']==0) ? "opacity:0.5;-moz-opacity:0.5;" : "";
            $inactive = ($cpt['active']==0) ? __("(inactive)", "_coll") : "";
                echo"<tr id=\"collectie_{$cpt['rewrite']['slug']}\" style=\"{$opacity}\">
               
                <td class=\"name column-name\">
                  <strong>
                    <div style=\"float:left;width:22px;height:20px;overflow:hidden\"><img align=\"absmiddle\" src=\"{$cpt[menu_icon]}\"/></div> 
                    <a class=\"row-title ajaxify\" rel=\"action:editcollection,cpt:{$postype}\" title=\"Edit &ldquo;{$cpt['name']}&rdquo;\" href=\"admin-ajax.php\">{$cpt['labels']['name']}</a></strong><br />{$cpt[description]}
                    
                    <div class=\"row-actions\">
                    <span class=\"edit\"><a class=\"ajaxify\" rel=\"action:editcollection,cpt:{$postype}\" href=\"admin-ajax.php\">".__('Edit Collection','_coll')."</a> - </span>
                    <span class=\"edit_custom_fields\"><a class=\"ajaxify\" rel=\"action:editmetadata,cpt:{$postype}\" href=\"admin-ajax.php\">".__('Edit Metadata set','_coll')."</a> -</span>
                    <span class=\"edit_custom_fields\"><a class=\"ajaxify\" rel=\"action:edituserinterface,cpt:{$postype}\" href=\"admin-ajax.php\">".__('Edit User interface','_coll')."</a> |</span>
                    <span class=\"delete\"><a href=\"#\" onclick=\"deletecollection('{$postype}', '".__("Are you sure to delete this entire collection including metadataset en user interface?","_coll")."')\">".__('Delete Collection','_coll')."</a></span>
                  </div>
                </td>
                
                <td class=\"categories column-categories\">{$postype} {$inactive}";
                //print_r($cpt);
            //
    
                echo"</td>
            </tr>";          
            }else{
	            
	         echo"<tr id=\"no-collections\">
               
                <td class=\"name column-name\" colspan=\"2\" style=\"text-align:center;padding:30px;font-style:italic\">".__("No Collections yet, click on ","_coll")." '".__("Add New Collection",'_coll')."' ".__("to create one.",'_coll')."
                </td></tr>
                ";   
            }  
            echo"</tbody>
            </tbody>
            </table>
            </form>
            </div></div>
            
            
            
            ";
            echo"<script>

jQuery(document).ready(function() {
//jQuery('#meta_elements .postbox').addClass('closed');
//console.log(postboxes);


//jQuery('#collections_wrapper').load('admin-ajax.php', {action:'edituserinterface',cpt:'afleveringen'}, function() {
//			addlisteners();
//			});

});

</script>";
 }  


/**
    * Checks if a collections already exists or if a name is reserved.
    *
    * To prevent double collection names or names that are reserved
    * @access public    
    */
public function check_post_type(){
	$post_type 	= $this->slugify($_POST[post_label]);
	$exists = (post_type_exists($post_type)) ? 1 : 0;
	
	if ($exists==1){
	$returnedjavascript = "setMessage(\"".__("This Collection name already exists or is a reserved post type. Try a different name please.","_coll")."\");
	jQuery('.submit_c').attr('disabled',true);";

	}else{
	$returnedjavascript = "
	if(jQuery('.submit_c').attr('disabled')){
	setMessage(\"".__("That's a good one!","_coll")."\");
	jQuery('.submit_c').attr('disabled',false);
	}
	";
	
		
	}
	
	echo"<script>
	$returnedjavascript
	
	
	</script>";
}


/**
    * Edit a collection
    *
    * Always called trough an ajax request.
    * @access public
    * @throws a form with the existing collection info which can be edited. 
    * @todo add advanced options table for register_post_type to configure the collections more detailed
    */
function editCollection(){
	
	
	$cpts = get_option("collection_cpt");
	if(isset($_POST['cpt'])){
	$collection = $cpts[$_POST['cpt']];
	}


$buttons = "<a rel=\"action:collectionoverview\" class=\"button ajaxify\" href=\"admin-ajax.php\">&lsaquo; ".__("Back")."</a>
<a class=\"button-primary submit_c\" onclick=\"return (jQuery(this).attr('disabled')) ?  false : save_collection('".__("Collection\'s configuration saved, reloading page....")."')\" href=\"#\">".__('Save Collection','_coll')."</a>";

echo"
<div class=\"wrap\" id=\"collections_wrapper\">
<div class=\"icon32\" id=\"icon-options-general\"><br></div><h2>
".__("Edit Collection",'_coll')."
</h2>



<div style=\"height:40px;width:100%;padding:7px;\"><div class=\"updated settings-error\" style=\"display:none;\" id=\"setting-error-settings_updated\"></div></div>
<form class=\"wpcf-types-form wpcf-form-validate\" action=\"\" method=\"post\" id=\"savecollection\">


{$buttons}


<input type=\"hidden\" name=\"post_type\" value=\"{$_POST['cpt']}\">";
//if(isset($_POST['cpt'])){
	echo"<input type=\"hidden\" name=\"action\" value=\"savecollection\">";
//}else{
//	echo"<input type=\"hidden\" name=\"action\" value=\"savenewcollection\">";
//}
////setMessage('".__("This is not a valid label for a post type, it exists already or is reserved like 'post' and 'page' are. Try another please","_coll")."')

$bluraction  = (!isset($_POST['cpt']))? "onblur=\"
jQuery('#check_getter').load('admin-ajax.php', {action:'check_post_type', post_label:this.value});




\" ": "" ;

echo"<br/>
<br/>
 <table class=\"widefat tag\" cellspacing=\"0\">
            <thead class=\"content-types-list\">
              <tr>
                <th style=\"\" colspan=\"2\" class=\"manage-column column-name\" id=\"name\" scope=\"col\">".__('Name & Description','_coll')." </th>
              </tr>
            </thead>
            <tbody>
            
            <tr>
                <td class=\"name column-name\" width=\"20%\">".$this->helpicon(__('Collection Name','_coll'), __("Give a suitable name matching the content of the collection.","_coll"))."  ".__('Collection Name','_coll')." <span style=\"color:red;\">*</span></td>
                <td class=\"categories column-categories\"><input type=\"text\" name=\"name\" size=\"20\" {$bluraction} value=\"{$collection[labels][name]}\"/> ".__('e.g. Treasures','_coll')."</td>
            </tr>
            
            
            <tr>
                <td class=\"name column-name\">".$this->helpicon(__('Collection Singular Name','_coll'), __("The singular name of the Collection","_coll"))." ".__('Collection Singular Name','_coll')." </td>
                <td class=\"categories column-categories\"><input type=\"text\" name=\"singular_name\" size=\"20\" value=\"{$collection[labels][singular_name]}\"/> ".__('e.g. Treasure','_coll')."</td>
            </tr>";
            
  /*          
            <tr>
                <td class=\"name column-name\">".__('Post Type Name','_coll')." <span style=\"color:red;\">*</span></td>
                <td class=\"categories column-categories\"><input type=\"text\" class=\"disabled\" 	readonly=\"readonly\" name=\"post_type\" id=\"spost_type\" size=\"20\" onblur=\"set_post_type(this);\" value=\"{$collection[post_type]}\"/> ".__('e.g. treasures,','_coll')."</td>onblur=\"set_post_type(this);\" 
            </tr>
    */        
            
       echo"     <tr>
                <td class=\"name column-name\">
                ".$this->helpicon(__('Collection Description','_coll'), __("Brief description of the Collections","_coll"))."
                ".__('Description','_coll')." </td>
                <td class=\"categories column-categories\"><textarea  title=\"".__('Collection Description','_coll')."\" name=\"description\" cols=\"40\" rows=\"3\">{$collection[description]}</textarea></td>
            </tr>
            
            </tbody>
</table>
<br/>

<br/>";

 
$menu_icons		= $this->getMenuIcons();
$menu_positions = $this->getMenuPositions();
$c_active		= ($collection[active]==1) ? "checked" :"";
$c_public		= ($collection['public']==1) ? "checked" :"";

echo"<table class=\"wpcf-types-form-table widefat\" cellspacing=\"0\">
            <thead class=\"content-types-list\">
              <tr>
                <th style=\"\" colspan=\"2\" class=\"manage-column column-name\" id=\"name\" scope=\"col\">".__('Visibility','_coll')."</th>
              </tr>
            </thead>
            <tbody>
            
            <tr>
                <td class=\"name column-name\">
                ".$this->helpicon(__('Active','_coll'), __("Activate or Deactivate Collection", "_coll"))."
                
                ".__('Active','_coll')." </td>
                <td class=\"categories column-categories\">
                <input type=\"checkbox\" name=\"active\" {$c_active} value=\"1\"/>
                </td>
            </tr>
            
            <tr>
                <td class=\"name column-name\">
                ".$this->helpicon(__('Public','_coll'), __("Whether a post type is intended to be used publicly either via the admin interface or by front-end users", "_coll"))."
                
                ".__('Public','_coll')." </td>
                <td class=\"categories column-categories\">
                <input type=\"checkbox\" name=\"public\" {$c_public} value=\"1\"/>
                </td>
            </tr>
            
            <tr>
                <td class=\"name column-name\">
                ".$this->helpicon(__('Menu icon','_coll'), __("The icon used for the Collection menu.","_coll") )."
                ".__('Menu icon','_coll')."</td>
                <td style=\"overflow:visible\" class=\"categories column-categories\">
                <select name=\"menu_icon\" id=\"menu_icon\">";
                $s=1;
                $slashes 		= explode("/", $collection[menu_icon]);
	            $icon_file		= $slashes[(count($slashes)-1)];
                 foreach($menu_icons as $menu_icon){
	              //echo "{$this->basedir}/images/cpt-icons/{$menu_icon}";
	              if($s==200){
		              break;
	              }
	              
	              
	              $selected 	=($icon_file==$menu_icon)? "selected":"";
	              
	              
	               echo"<option {$selected} value=\"{$this->basedir}/images/cpt-icons/{$menu_icon}\" title=\"{$this->basedir}/images/cpt-icons/{$menu_icon}\">".ucfirst($menu_icon)."</option>"; 
                $s++;
                
                }

                
                echo"</select>
                
                
                </td>
            </tr>
            
            
            <tr>
                <td class=\"name column-name\" width=\"20%\">
                ".$this->helpicon(__('Position of the collection in left menu','_coll'), __("The position in the menu order the post type should appear. show_in_menu must be true","_coll"))."
                ".__('Position of the collection in left menu','_coll')."</td>
                <td class=\"categories column-categories\">
                <select name=\"menu_position\">";
                
                foreach($menu_positions as $position=>$name){
	               echo"<option value=\"{$position}\">{$name}</option>"; 
                }
                
                echo"</select>
                </td>
            </tr>
            </tbody>
</table>

<div style=\"display:none;\" id=\"check_getter\"></div>
<br/>";



/****** ADVANCED OPTIONS *****/
/*
echo"<div>
			<div class=\"sidebar-name\" onclick=\"jQuery('#advanced_option_holder').toggle();\">
				<div class=\"sidebar-name-arrow\"><br></div>
				<h3>".__("Advanced settings","_coll")."	<span><img alt=\"\" title=\"\" class=\"ajasx-feedback\" src=\"/wp-admin/images/wpspin_light.gif\"></span>
				</h3>
			</div>
			<div id=\"advanced_option_holder\" class=\"widget-holder inactive\" style=\"
			  border-bottom-left-radius: 3px;
    border-bottom-right-radius: 3px;
    border-style: none solid solid;
    border-width: 0 1px 1px;
			border-color:#DFDFDF;\">
				<div class=\"widgets-sortables ui-sortable\" id=\"wp_inactive_widgets\">
<div class=\"sidebar-description\">
	<p class=\"description\">Hier komen advanced options</p></div>
</div>
				<br class=\"clear\">
			</div>
		</div>

<br/><br/>
		*/
echo"
{$buttons}
</form>

<script>
	jQuery('#menu_icon').msDropDown();
	jQuery('#advanced_option_holder').toggle();
	addPointers();	
</script>
";				
	die();
}

}

?>
