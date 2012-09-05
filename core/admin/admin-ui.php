<?php
if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ){
	die("Access denied.");
}


/**
  * Contains all the functionality for managing your backend User interface.
  * 
  * All functions are derived from ajax requests defined in collections.php
  *
  * @category Wordpress Plugin
  * @package  Collections Wordpress plugin
  * @author   Bastiaan Blaauw <statuur@gmail.com>
  * @access   Public
  * @license  GPL2
  * @see      http://metacollections.statuur.nl/
  */

class UI extends Basics{ 
/**
    * Constructor
    * loads the vars from class Basic and loads an array with all metadata field types and classes Field
    * loads the class for the Wordpress system metabox elements
    * @access public
    */
public function __construct(){
	$this->init();
	
	$this->action 			= ($this->action=="") ? "Overview": $this->action;
	$this->metafields		= array();
	$this->Field 			= new Field();
	$this->Field->getFields();
	$this->Field->getClasses();//+':parent'
	$this->metaboxoptions 	= " <span class=\"metabox_options\"> - <a href=\"#\" onclick=\"rename_metabox('{$_POST[cpt]}',this, '".__("Please enter a new name for this Metabox: ", "-coll")."')\">".__("Rename Metabox","_coll")."</a> <a href=\"#\" onclick=\"delete_metabox(jQuery(this).parent().parent().parent().parent(), '".$_POST[cpt]."', '".__("Are you sure to delete this metabox? All metafield in it will return to the Inactive Elements Metadataset.","_coll")."');return false;\">".__("Delete Metabox")."</a></span>";
	//$this->metafieldoptions = " <span class=\"metabox_options\"><a href=\"#\" onclick=\"alert(23);\">".__("Deactivate Metafield position")."</a> </span>";	
	$this->SystemElements	= new SystemElements();
		
	if($this->action!=""){
	call_user_func(array($this, $this->action)); // As of PHP 5.3.0
	//eval('$instance = '.$className.'::GetInstance();');
	//print_r($this->sides);
	//$this->edituserinterface();
	}	
	
	}


/**
    * Saves the new name for the metabox
    * ajaxrequest, vars via $POST
    * @access public
    */
public function rename_metabox(){
$metaboxes 			= get_option("metaboxes_".$_POST[cpt]);

if(is_array($metaboxes)){
	foreach($metaboxes as $side=>$metaboxinfo){ 
	
	if($metaboxinfo[$_POST[metaboxid]]!=""){
	
	$metaboxes[$side][$_POST[metaboxid]][name] = $_POST[metaboxname];
	//unset($metaboxes[$side][$_POST[metaboxid]]);
	}
	
	}
	
	
	 }	
	//print_r($metaboxes);
	update_option("metaboxes_".$_POST[cpt], $metaboxes, '', 'no'); 


}


/**
    * Deletes the metabox
    * ajaxrequest, vars via $POST
    * @access public
    */
function delete_metabox(){
	
$metaboxes	= get_option("metaboxes_".$_POST[cpt]);
	


	if(is_array($metaboxes)){
	foreach($metaboxes as $side=>$metaboxinfo){ 
	
	if($metaboxinfo[$_POST[metaboxid]]!=""){
	//print_r($metaboxinfo[$_POST[metaboxid]]);
	unset($metaboxes[$side][$_POST[metaboxid]]);
	}
	
	}
	
	
	 }	
	
	//echo"\n----------------------\n";
	
	//print_r($metaboxes);
	update_option("metaboxes_".$_POST[cpt], $metaboxes, '', 'no'); 
}


/**
    * inserts a metabox via tab add metabox in the DOM
    * ajaxrequest, vars via $POST
    * @access public
    */
public function save_metabox(){
global $pagenow;

//add_meta_box($ID, "<span class=\"{$ID}\">{$metabox[name]}</span>".$this->metaboxoptions, array($this, 'getFields'), $pagenow, $_POST[side], 'core', array($metabox, $_POST[cpt]));	
//do_meta_boxes($pagenow, $side, $data); 	
//add metabox function here!

$metaboxID = $this->slugify($_POST[label]);

echo "
<div class=\"postbox ui-area\" id=\"{$metaboxID}\">
<div title=\"Klik om te wisselen\" class=\"handlediv\"><br></div>
<h3 class=\"hndle\"><span>{$_POST[label]}</span></h3>

<div class=\"inside\">
<div class=\"meta-field-sortables ui-sortable\">
<div class=\"meta-field-sortables ui-sortable\" id=\"elements-sortables\">
//save en reload this page in order to drag and drop metafield here.....
</div>
</div>
</div>
</div>";


}



/**
    * Is al similar function as do_meta_boxes, a Wordpress function. 
    
    * in this case this function generates all metafields in a particular area stored in de the array $this->metafields
    * ajaxrequest, vars via $POST
    * @access public
    */
public function do_meta_fields($page, $context, $object ){
	//do_meta_boxes($pagenow, 'elements', $data)
	
	printf('<div id="%s-sortables" class="meta-field-sortables">', htmlspecialchars($context));
	
	
	if(is_array($this->metafields[$context])){
	foreach($this->metafields[$context] as $box){
		echo '<div id="' . $box['id'] . '" class="meta-field-box ' . postbox_classes($box['id'], $page) . $hidden_class . '" ' . '> ' ."\n";
					if ( 'dashboard_browser_nag' != $box['id'] )
						echo '<div class="handlediv" title="' . esc_attr__('Click to toggle') . '"><br /></div>';
					echo "<h3 class='meta-field-hndle'><span>{$box['title']}</span></h3>\n";
					echo '<div class="inside">' . "\n";
					call_user_func($box['callback'], $object, $box);
					echo "</div>\n";
					echo "</div>\n";
		
	}
	
	}

	echo"</div>";

}


/**
    * Caches a metafield in de the array $this->metafields
    * @param string $id metafield id
    * @param string $title metafield id
    * @param string $callback optional callback function
    * @param string $post_type collection/post type
    * @param string $context the area/metabox to display the metafield in
    * @param string $priority order of the metafield (not used as such)
    * @param mixed $callback_args args send with the $callback    
    * @access public
    */
function add_meta_field($id, $title, $callback, $post_type, $context, $priority, $callback_args){
	
		$this->metafields[$context][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $callback_args);
}


/**
    * Generates all the inactive system elements metaboxes such as featured image
     * @access public
    */
public function inactive_systemelements(){
global $pagenow;

$systemboxes = get_option("metaboxes_".$_POST[cpt]);
//print_r($systemboxes);
if(is_array($systemboxes['inactive-system'])){	
		foreach($systemboxes['inactive-system'] as $title=>$options){ //loop settings
			$searchfor="/{$this->systemprefix}/";
			$function = preg_replace($searchfor, '', $title);
			$function = preg_replace('/-/', '_', $function);//replace '-' with '_' . functions with '-' in name are not supported in php 

			call_user_func(array($this->SystemElements, $function), 'inactive-system'); // As of PHP 5.3.0

		}
}

/***** Add meta Element ***/// jQuery('.columns-prefs input:checked')margin:10px 10px 25px 25px;
//<span style=\"color:white\" class=\"description\"> - ".__("Drag elements into the preview area","_coll")."</span>
/*<thead class=\"content-types-list\">
              <tr>
                <th scope=\"col\"><h3>".__('Available System Elements Wordpress','_coll')." </h3></th>
              </tr>
            </thead>  position:relative;float:left;*/

//echo __("The standard user interface of Wordpress have some things that can't be changed. For instance the position of title and description.","_coll");

echo $this->helpicon(__('Inactive System Elements Wordpress','_coll'), __("The standard user interface of Wordpress have some things that can't be changed. For instance the position of title and description.", "_coll"));

echo"<div id=\"meta_elements\" style=\"padding:0px 10px 0px 10px;width:100%;\"> 
                 
 <table cellspacing=\"0\" style=\"width:100%;\">
            
            <tbody>
            <tr id=\"content-type-{$meta_element['label']}\" class=\"\">
            <td>
            <div id=\"poststuff\" class=\"metabox-holder\">
		<div class=\"meta-field-sortables ui-sortable\">";// style=\"width:250px;\"
		 
            do_meta_boxes($pagenow, 'inactive-system', $data); //$data goes back to add_meta_box
            
         echo"</div></div>
         </td>
             </tr></table>
             
             </div>";
}


/**
    * Add metabox form under tab all metabox
     * @access public
    */
public function add_metabox(){//BEWARE OF DOUBLE METABOXES ID'S BECAUSE OF SAVING UI'S
echo"<form id=\"metabox_add\" action=\"{$_SERVER[REQUEST_URI]}\" method=\"post\" >
         <input type=\"hidden\" name=\"action\" value=\"save_metabox\"/>
         <input type=\"hidden\" name=\"side\" id=\"side\" value=\"normal\"/>
         <table style=\"width:100%;\" cellspacing=\"0\">

            <thead class=\"content-types-list\">
              <tr>
	         <th style=\"padding-bottom:10px;\" colspan=\"2\" class=\"manage-column column-name\" id=\"name\" scope=\"col\"><h3>".__('Add Metabox','_coll')." <span class=\"description\" style=\"color:white\"> - ".__("Inserted in preview area","_coll")."</span></h3></th>
              </tr>
            </thead>
            
            <tbody>
            
          
             <tr>
                <td width=\"140\">".__('Name')." <span style=\"color:red;\">*</span></td>
                <td ><input type=\"text\" name=\"label\" size=\"25\" value=\"\"/></td>
            </tr>
            
            
             <tr>
                <td>".__('Position')."</td>
                <td>
                
                <ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" rel=\"normal\" checked=\"true\" name=\"position\" value=\"post-body-content\"/> Normal (upper left columns)</label></li>
                <li><label><input type=\"radio\" rel=\"side\"  name=\"position\" value=\"postbox-container-1\"/> Side (side columns</label></li>
                <li><label><input type=\"radio\" rel=\"advanced\"  name=\"position\" value=\"postbox-container-2\"/> Advanced (down left columns</label></li>
                </ul>
                </td>
            </tr>

                        
            <tr>
            <td></td>
                <td style=\"padding:15px;\">
                <a rel=\"action:savemetabox\" class=\"button-primary\" onclick=\"save_metabox('".__("Save the User interface to save the created Metabox.", "_coll")."');return false;\" href=\"#\">".__('Insert Metabox','_coll')."</a>

</td>
</tr>
</table>
</form>";

}


/**
    * Generates all the inactive metadata elements
     * @access public
    */
public function inactive_metadataset(){
global $pagenow;
$this->metadataset 		= get_option("metadata_".$_POST[cpt]);
$this->userinterface	= get_option("userinterface_".$_POST[cpt]);

//echo"<hr/>";
//print_r($this->userinterface);

if(is_array($this->userinterface)){
foreach($this->userinterface as $metadataID=>$UIinfo){//delete the values that are used in UI and keep the inactive that way
		unset($this->metadataset[$UIinfo[metadataID]]);		
}
}

if(is_array($this->metadataset)){	//are there still inactive elements?

	foreach($this->metadataset as $metafield){ //loop settings 
//            print_r($metafield);

            if($metafield[type]!="" && $metafield[type]!="taxonomy"){	//don't include taxonomy fields.
	                       //$metafield[cpt] = $_POST[cpt];
	                       $c = ucfirst($metafield[type]);
	                       $typeclass = new $c();
	                       $this->add_meta_field("meta-element-{$metafield[ID]}", $metafield[label], array($typeclass, 'showfield'), $pagenow, 'inactive', 'core', $metafield);
	                       
	                       
	                       }  
            
            }
	
}

echo"<div id=\"meta_elements\" style=\"padding:0px 10px 0px 10px;width:100%\"> 
           
 <table cellspacing=\"0\" style=\"width:100%;\">
            
            <tbody>
            <tr id=\"content-type-{$meta_element['label']}\" class=\"\">
            <td>
            <div id=\"poststuff\" class=\"metabox-holder\">
		<div class=\"meta-field-sortables ui-sortable\" >";//style=\"width:250px;\"
		 
            $this->do_meta_fields($pagenow, 'inactive', $data); //$data goes back to add_meta_box
            
         echo"</div></div>
         </td>
             </tr></table></div>
	";

}





/**
    * Saves the userinterface for the collection
     * @access public
    */
function saveuserinterface(){
	$metaboxes 		= get_option("metaboxes_".$_POST[cpt]);
	$userinterface	= get_option("userinterface_".$_POST[cpt]);
	$ordered_mb		= array();
		
	//$ordered_ui		= array();
		
	foreach($_POST[metaboxes] as $side=>$sideinfo){
		
		foreach($sideinfo as $order=>$metaboxinfo){
		$metaboxID 	= $metaboxinfo[ID];
		$label 		= $metaboxinfo[label];

		$key 	= array_search($metaboxID, $metaboxes); // $key = 2;
		
		//if its a system metabox the value always 0 for inactive side and 1 for all others;
		if (preg_match("/{$this->systemprefix}/i", $metaboxID)) {
		
		$metaboxID = preg_replace("/{$this->nodrag}/i", "", $metaboxID);
		$ordered_mb[$side][$metaboxID][ID] 	= $metaboxID;
		
		}else{
		$ordered_mb[$side][$metaboxID] 			= $metaboxes[$mside[context]][$metaboxID];//what if side changes?
		$ordered_mb[$side][$metaboxID][ID] 		= $metaboxID;
		$ordered_mb[$side][$metaboxID][name] 	= $metaboxinfo[name];
		
		$ordered_mb[$side][$metaboxID][style] 	= 'standard';
		$ordered_mb[$side][$metaboxID][context] = $side;
		
				
		}
		
		
		}
	}
	
	
	//print_r($ordered_mb);
	//echo"<hr/>";
	//print_r($_POST);
	 	
	
	update_option("metaboxes_".$_POST[cpt], $ordered_mb, '', 'no'); // this one saves al the info  and order for metaboxes
	update_option("userinterface_".$_POST[cpt], $_POST[ui], '', 'no'); // this one saves al the info  and order for metafields
	
	
	
	/** save metabox order**/

}

/**
    * Editsthe userinterface for the collection
     * @access public
    */
public function edituserinterface(){
global $pagenow; 

require_once('./includes/meta-boxes.php');



	
	
	
$this->metadataset 	= get_option("metadata_".$_POST[cpt]);
$metaboxes 			= get_option("metaboxes_".$_POST[cpt]);
$userinterface		= get_option("userinterface_".$_POST[cpt]);

$buttons ="<a rel=\"action:collectionoverview\" class=\"button ajaxify\" href=\"admin-ajax.php\">&lsaquo; ".__("Back")."</a>
<a onclick=\"save_uinterface('{$_POST[cpt]}', '".__("Settings updated", "_coll")."');return false;\"  class=\"button-primary\" href=\"#\">".__('Save user Interface','_coll')."</a>

<a style=\"float:right\" class=\"button\" onclick=\"jQuery('.side-text, .tooltips').slideToggle();return false;\" href=\"#\">".__('Toggle help','_coll')."</a>
";


/* GENERATE METABOXES */

//always add a publish metabox and make it undraggable//<span class=\"description\">".__("Cannot be moved","_coll")."</span>
add_meta_box('submitdiv'.$this->nodrag, __('Publish')."", 'post_submit_meta_box', $pagenow, 'side', 'core'); 


/* check for taxonomies */
if(is_array($this->metadataset)){
$types 				= $this->search_nested_arrays($this->metadataset, "type");
$has_taxonomies		= in_array("taxonomy", $types);
	if($has_taxonomies){ //if there are resistered taxonomy fields loop trough them
		foreach($this->metadataset as $metadataID=>$metaINFO){ 
		
			if($metaINFO[type]=="taxonomy" && $metaINFO[status]==1){
				//print_r($metaINFO);
				call_user_func(array($this->SystemElements, 'taxonomy'), $metaINFO);
		 
			}
		}
	}

}

if(is_array($metaboxes)){
foreach($metaboxes as $side=>$metaboxinfo){ 
foreach($metaboxinfo as $ID=>$metabox){ //add metaboxes in ui
//echo $ID."<br/>";

$searchfor	="/{$this->systemprefix}/";
if(preg_match($searchfor, $ID) ){
$function = preg_replace($searchfor, '', $ID);
$function = preg_replace('/-/', '_', $function);//replace '-' with '_' . functions with '-' in name are not supported in php 
$function = ($function == "formatdiv") ? "post_formats" :  $function;

call_user_func(array($this->SystemElements, $function), $side); //system elements

}else{
//print_r($metabox);
add_meta_box($ID, "<span class=\"{$ID}\">{$metabox[name]}</span>".$this->metaboxoptions, array($this, 'getFields'), $pagenow, $side, 'core', array($metabox, $_POST[cpt]));	

}

}
}
}



echo "<div class=\"wrap\" id=\"collections_wrapper\">
".screen_icon('options-general')."<h2>
".__("Edit User Interface for:",'_coll')." {$_POST[cpt]} 
</h2>
<br/>
{$buttons}

 <div style=\"height:30px;width:100%;padding:7px;\"><div class=\"updated settings-error\" style=\"display:none;\" id=\"setting-error-settings_updated\"></div></div>
         

<div id=\"ui_controls\" class=\"ui_controls\">
<h3 class=\"nav-tab-wrapper\">
    
    
    <a href=\"#\" class=\"nav-tab nav-tab-active\" id=\"nav-0\">".__('Inactive Elements Metadataset','_coll')."</a>
    <a href=\"#\" class=\"nav-tab\" id=\"nav-1\">".__('Inactive System Elements Wordpress','_coll')."</a>
    
    <a href=\"#\" class=\"nav-tab\" id=\"nav-2\">".__('Add Metabox','_coll')."</a>
</h3>



<div class=\"tab-content content-active\" id=\"c-0\">";
$this->inactive_metadataset();
echo"</div>
<div class=\"tab-content\" id=\"c-1\">";
$this->inactive_systemelements();
echo"</div>";
/*<a href=\"#\" class=\"nav-tab\" id=\"nav-2\">".__('Inactive Metaboxes','_coll')."</a>
<div class=\"tab-content\" id=\"c-2\">";
$this->inactive_metaboxes();
echo"</div>";
*/ 
echo"<div class=\"tab-content\" id=\"c-2\">";
$this->add_metabox();
echo"</div>


</div>
";


//echo"<div class=\"add_toggler\" id=\"add_toggler\">";

//$this->edituserinterfaceControls();          
            
echo"</div>


<div class=\"helpmessage\">".__("This is the preview area of you Collection edit interface","_coll")."</div>
	<div style=\"float:left;width:100%;margin-top:20px;border-bottom:1px solid #DFDFDF;\"></div>
	 <div class=\"clear\"></div>";








/************* THE PREVIEW AREA START HERE action:, cpt:{$_POST[cpt]******/
echo"
<form id=\"userinterfaceform\" action=\"{$_SERVER[REQUEST_URI]}\" method=\"post\" >
<input type=\"hidden\" name=\"action\" value=\"saveuserinterface\"/>
<input type=\"hidden\" name=\"cpt\" value=\"{$_POST[cpt]}\"/>
<div id=\"poststuff\" class=\"metabox-holder metabox-holder-collections\">
<div id=\"post-body\" class=\"metabox-holder columns-2\">";

foreach($this->sides as $side=>$elementID){ //loop though the different UI parts normal side and advances and print metaboxes 
	if($elementID!=""){
	echo"<div id=\"{$elementID}\" class=\"postbox-container \"><div class=\"side-text\">".__("Drag elements to this area","_coll")." '{$side}'</div>
	<!-- {$side} -->";


	do_meta_boxes($pagenow, $side, $data); 	

	echo"</div>";
	}
}

//jQuery( ".selector" ).sortable( "option", "disabled", true );

//jQuery('.meta-box-sortables').sortable({
echo"</div>
 

</form>

</div>

</div>

</div>

<div style=\"padding-top:30px;float:left;width:100%;\">{$buttons}</div>
<script>
 
 
 
jQuery(document).ready(function() {

jtabs.init({tabID: 'ui_controls'});
postboxes.add_postbox_toggles('{$pagenow}');

metafield.add_postbox_toggles('{$pagenow}');


jQuery('.meta-field-box').addClass('closed');//#meta_elements 
addPointers();

jQuery('.postbox').addClass('ui-area');


jQuery('#meta_elements .metabox-holder .postbox').addClass('closed');//#meta_elements 
jQuery('.meta-box-sortables').sortable({cancel: 'div[id$=\"{$this->nodrag}\"]'});

jQuery('.meta-box-sortables').sortable({
				start: function(e,ui) {
				jtabs.activate(1);
				}
				
				});


});



jQuery('.metabox-holder-collections textarea, .metabox-holder-collections input[type=text], .metabox-holder-collections input[type=checkbox],  .metabox-holder-collections input[type=submit]').prop('disabled', true);	





</script>

";

}

/**
    * Not used
    */
function metafieldorder(){ //drag and drop function for order
	print_r($_POST);
	
}

/**
    * Called from add_meta_box to get the stored metafields in the metaboxes.
     * @access public
    */
public function getFields($ob, $metabox){
global $pagenow;
$this->userinterface= get_option("userinterface_".$metabox[args][1]);

$this->metadataset 	= get_option("metadata_".$metabox[args][1]); //{}
$metaboxinfo 		= $metabox[args][0];  //moet $this worden

echo"<div class=\"meta-field-sortables ui-sortable\">
<div id=\"elements-sortables\" class=\"meta-field-sortables ui-sortable\">";


if(is_array($this->userinterface)){
foreach($this->userinterface as $metadataID=>$UIinfo){
	
	if($UIinfo[metaboxID]==$metaboxinfo[ID]){//find the right cstom fields with the metabox
	 $c = ucfirst($this->metadataset[$UIinfo[metadataID]][type]);
	 $typeclass = new $c($this->metadataset[$UIinfo[metadataID]]);
	 
	 $metafield = $this->metadataset[$UIinfo[metadataID]];
	 $this->add_meta_field("meta-element-{$metafield[ID]}", $metafield[label], array($typeclass, 'showfield'), $pagenow, $metaboxinfo[ID], 'core', $metafield);

	}
	

	 }
	 
	 $this->do_meta_fields($pagenow, $metaboxinfo[ID], $data);
}
echo"</div>
</div>";
 	
}



}


?>