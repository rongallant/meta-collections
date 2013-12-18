<?php
/**
  * Saves data for generating taxonomies related to the Collection. 
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Taxonomy extends Basics{
	var $options;
	
	function __construct($meta=null){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Taxonomy", "_coll");
	}
	
	
/**
    * This function is not used for txonomies. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
	
function showfield($post=null, $element=null){
		
			$element = ($element[id]!="") ? $element[args]: $element;
			$name	 = $this->postmetaprefix.$element[ID];
			$value	 = get_post_meta($post->ID, $name, true); 
			}


/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
    * @access public
    */	
function fieldOptions($element){
//$element (){
	
echo"<table class=\"widefat metadata\" cellpadding=\"10\">";
$statusc = ($element[status]==1)? "checked":"";
	
	echo"
	<tr>
	<td colspan=\"2\">
	".$this->helpicon(__('Taxonomies','_coll'), __("Taxonomies is a special field and the functionality is part of Wordpress.", "_coll"))." ".__("General info about Taxonomies. For more info see: <a href='http://codex.wordpress.org/Taxonomies' target='_blank'>http://codex.wordpress.org/Taxonomies</a>","_coll")."
	</td>
	</tr>";
	
	$this->Field->getID($element);
	
	echo"<tr>
	<td>".__("Type").":</td>
	<td>";
	
	 $this->Field->getfieldSelect($element);
	
	echo"</td>
	</tr>
	
	<tr>
	<td style=\"width:25%\">
	
	".__("Status").":<br/>
	
	
	</td>
	<td><input type=\"checkbox\" {$statusc} name=\"status\" value=\"1\"/></td>
	</tr>
	
	<tr>
	<td style=\"width:25%\">".__("Taxonomy Name").":</td>
	<td><input type=\"text\" name=\"label\" class=\"required\" value=\"{$element[label]}\"/> ".__('e.g. Ingredients','_coll')."</td>
	</tr>
	
	<tr>
	<td style=\"width:25%\">".__("Taxonomy Singular Name").":</td>
	<td><input type=\"text\" name=\"singular_name\" class=\"required\" value=\"{$element[singular_name]}\"/> ".__('e.g. Ingredient','_coll')."</td>
	</tr>
	
	
	

	<tr>
	<td>".__("Show in menu item in the collection navigation menu", "_coll").":</td>
	<td>";
	
	$s_checked_yes	= ($element[show_in_nav_menus]==1)? "checked": "";
	$s_checked_no	= ($element[show_in_nav_menus]==0)? "checked": "";
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"show_in_nav_menus\" {$s_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"show_in_nav_menus\" {$s_checked_no}> ".__("No")."</label></li>
                </ul>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Hierarchical", "_coll").":</td>
	<td>";
	
	$h_checked_yes	= ($element[hierarchical]==1)? "checked": "";
	$h_checked_no	= ($element[hierarchical]==0)? "checked": "";
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"hierarchical\" {$h_checked_yes}> ".__("Yes")." (".__("categories with descendants","_coll").")</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"hierarchical\" {$h_checked_no}> ".__("No")." (".__("Tags","_coll").")</label></li>
                </ul>
	
	</td>
	</tr>";	
		
	echo"	
<tr>
	<td valign=\"top\">".__("Show this field in Collection overview<br/>(this field has to be dragged in user interface before showing up)", "_coll").":</td>
	<td valign=\"top\">";
	
	$s_checked_yes	= ($element[overview]==1)? "checked": "";
	$s_checked_no	= ($element[overview]==0)? "checked": "";
	$formID = "#edit_options_{$element[ID]}_{$element[cpt]}";
	
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"overview\" {$s_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"overview\" {$s_checked_no}> ".__("No")."</label></li>
                </ul>
	
	</td>
	</tr>
	
	<tr>
	<td colspan=\"2\" style=\"padding:10px\">
	<a href=\"#\" onclick=\"
	if(jQuery('{$formID}').validate().form()){
	save_metafield('{$element[ID]}', '{$element[cpt]}', '".__("Field Options Saved")."...');
	}return false;
	\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a>	</td>
	</tr>
	
	
	</table>
	";
	
	

//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}

}
?>