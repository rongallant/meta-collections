<?php
 /**
  * Handles all functions regarding the field type youtube, which embeds youtube films in the metadata schema
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @version  1.0 $Revision: 60
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  * @todo field type youtube has yet to be developed. 
  */

class Youtube extends Basics{
	var $options;
	
	function __construct($meta=null){
		$this->meta = $meta;
		/*
		$this->options = array(
					'active',
					'label',
					'name',
					'type',
					'description',
					'required',
					'required_err',
					'default_value'
					);
		*/
		//echo"Text class";
	}
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    */	
		
function showfield($post=null, $element=null){
				
		
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];
			$value	 	= get_post_meta($post->ID, $name, true); 
			$value	 	= ($value=="" && $element[default_value]!="") ? $element[default_value] : $value;
			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			$max_length = ($element[max_length]!="") ? " maxlength=\"{$element[max_length]}\"" :"";
			$length 	= ($element[max_length]!="") ? " size=\"".($element[max_length]+2)."\"" :"20";
			//print_r($element);

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
			echo"
			<table class=\"metadata metafield\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">		
			<tr>
			<td style=\"width:15%\"> <label for=\"{$element[ID]}\">{$element[label]}:</label></td>
			<td><input type=\"text\" {$required} {$max_length} {$length} name=\"{$name}\" value=\"{$value}\"/></td>
			</tr>
			</table>";
			//print_r($_SESSION[required]);
}


/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
    * @access public
    */	
function fieldOptions($element){

echo"<table class=\"widefat metadata\" cellpadding=\"10\">";
$statusc = ($element[status]==1)? "checked":"";
	
	echo"<tr>
	<td style=\"width:25%\">".__("Status").":</td>
	<td><input type=\"checkbox\" {$statusc} name=\"status\" value=\"1\"/></td>
	</tr>
	
	<tr>
	<td style=\"width:25%\">".__("Label").": *</td>
	<td><input type=\"text\" name=\"label\" class=\"required\" value=\"{$element[label]}\"/></td>
	</tr>
	
	<tr>
	<td>".__("Description").":</td>
	<td><textarea name=\"description\" rows=\"3\" cols=\"60\">{$element[description]}</textarea></td>
	</tr>

	<tr>
	<td>".__("Type").":</td>
	<td>";
	
	 $this->Field->getfieldSelect($element);
	
	echo"</td>
	</tr>

	<tr>
	<td>".__("Required", "_coll").":</td>
	<td>";
	
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
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Max Length", "_coll").":<br/>".__("maximum character length of the field value", "_coll")."</td>
	<td><select name=\"max_length\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	$selector = ($element[max_length]=="")? 20 : $element[max_length];
	for ($i=1; $i<100; $i++){
	$selected = ($i==$selector)? "selected":"";	
	echo"<option value=\"{$i}\" {$selected}>$i</option>";
	}
	echo"</select>
	
	
	
	</td>
	</tr>
	
	";
	
	
	
	$element[formatting] = __("Inapplicable","_coll");
	echo"<tr>
	<td>".__("Formatting", "_coll").":</td>
	<td><input disabled type=\"text\" name=\"formatting\" value=\"{$element[formatting]}\"/>
	
	</td>
	</tr>
	
<tr>
	<td valign=\"top\">".__("Allow multiple values / instances of this element", "_coll").":</td>
	<td valign=\"top\">";
	
	$m_checked_yes	= ($element[multiple]==1)? "checked": "";
	$m_checked_no	= ($element[multiple]==0)? "checked": "";

	
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"multiple\" {$m_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"multiple\" {$m_checked_no}> ".__("No")."</label></li>
                </ul>
	
	</td>
	</tr>	

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
	if($('{$formID}').validate().form()){
	save_metafield('{$element[ID]}', '{$element[cpt]}', '".__("Field Options Saved")."...');
	}return false;
	\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a>	</td>
	</tr>
	
	
	</table>";
		
	echo"<script>
	 $(document).ready(function(){
	$('{$formID}').validate();
   });
   
   </script>";

//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}

}
?>