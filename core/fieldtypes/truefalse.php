<?php
/**
  * Handles all functions regarding the field type input type=checkbox true false
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Truefalse extends Basics{
	
	function __construct(){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname	= __("True false", "_coll");
	}
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
	
	
function showfield($post=null, $element=null){
			
			
	$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;

			
			$html 		= "";
	
			
			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}

						
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			
			
			foreach ($values as $value){		
			$element_c 	= ($value==1) ? "checked":"";
			
			$html.="<div class=\"metafield-value\">
			<input type=\"checkbox\" {$element_c} name=\"{$name}\" class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])." value=\"1\"/> {$element[message]}
			<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>
			</div>
			";
			}
			
			echo $this->Field->metafieldBox($html, $element);
}
	
function oldshowfield($post=null, $element=null){
			
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			
			$name 		= $this->postmetaprefix.$element[ID];
			$value 		= get_post_meta($post->ID, $name, true); 
			//print_r($value);
			
			$element_c 	= ($value==1) ? "checked":"";
			$html ="";
			
			
			
			if($element[description]!=""){
			$html.= "<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span><br/>";	
			}
			
			$html.="
			<input type=\"checkbox\" {$element_c} name=\"{$name}\" value=\"1\"/> {$element[message]}";
			
			
			echo $this->Field->metafieldBox($html, $element);
}


/**
    * Shows the specific form for the fieldtype with all the options related to that field. 
    * @access public
    */	
public function fieldOptions($element){

echo"<table class=\"widefat metadata\" cellpadding=\"10\">";

	
	$this->Field->getID($element);
		
	
	echo"<tr>
	<td>".__("Type").":</td>
	<td>";
	$this->Field->getfieldSelect($element);
	echo"</td>
	</tr>";
	
	$this->Field->getBasics($element);
	$this->Field->getValidationOptions($element, 1);
	
	$d_checked	= ($element[default_value]==1)? "checked": "";

	echo"
	<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"checkbox\" name=\"default_value\" {$d_checked} value=\"1\"/>
	</td>
	</tr>
	
	<tr>
	<td>".__("Message with field (e.g. contains nuclear waste)", "_coll").":</td>
	<td><input type=\"text\" name=\"message\" size=\"90\"  value=\"{$element[message]}\"/>
	
	</td>
	</tr>	
	
	<tr>
	<td valign=\"top\">".__("Allow multiple values / instances of this element", "_coll").":</td>
	<td valign=\"top\">";
	
	$m_checked_yes	= ($element[multiple]==1)? "checked": "";
	$m_checked_no	= ($element[multiple]==0)? "checked": "";
	$formID 		= "#edit_options_{$element[ID]}_{$element[cpt]}";
	
	
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"multiple\" {$m_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"multiple\" {$m_checked_no}> ".__("No")."</label></li>
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
	
	
	</table>";
		
	echo"<script>
	 jQuery(document).ready(function(){
	jQuery('{$formID}').validate();
   });
   
   </script>";
}

function oldfieldOptions($element){
//$element (){
	
echo"<table class=\"widefat metadata\" cellpadding=\"10\">";

$statusc = ($element[status]==1)? "checked":"";
$this->Field->getID($element);	
	
	echo"	
	<tr>
	<td>".__("Type").":</td>
	<td>";
	
	 $this->Field->getfieldSelect($element);
	
	echo"</td>
	</tr>
	
	<tr>
	<td style=\"width:25%\">".__("Status").":</td>
	<td><input type=\"checkbox\" {$statusc} name=\"status\" value=\"1\"/></td>
	</tr>
		
	<tr>
	<td style=\"width:25%\">".__("Label").":</td>
	<td><input type=\"text\" name=\"label\" class=\"required\" value=\"{$element[label]}\"/></td>
	</tr>
	
	<tr>
	<td>".__("Description").":</td>
	<td><textarea name=\"description\" rows=\"3\" cols=\"60\">{$element[description]}</textarea></td>
	</tr>

	

	<tr>
	<td>".__("Required", "_coll").":</td>
	<td>";
	$formID = "#edit_options_{$element[ID]}_{$element[cpt]}";

	$r_checked_yes	= ($element[required]==1)? "checked": "";
	$r_checked_no	= ($element[required]==0)? "checked": "";
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"required\" {$r_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"required\" {$r_checked_no}> ".__("No")."</label></li>
                </ul>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Required Errormessage", "_coll").":</td>
	<td><input type=\"text\" name=\"required_err\" value=\"{$element[required_err]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Message with field (e.g. contains nuclear waste)", "_coll").":</td>
	<td><input type=\"text\" name=\"message\" size=\"90\"  value=\"{$element[message]}\"/>
	
	</td>
	</tr>
	
	
	<tr>
	<td colspan=\"2\" style=\"padding:10px\">
	<a href=\"#\" onclick=\"
	if(jQuery('{$formID}').validate().form()){
	save_metafield('{$element[ID]}', '{$element[cpt]}', '".__("Field Options Saved")."...');
	}return false;
	\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a></td>
	</tr>
	
	
	</table>";


	echo"<script>
	 jQuery(document).ready(function(){
	jQuery('{$formID}').validate();
   });
   
   </script>";
//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}

}
?>