<?php
 /**
  * Handles all functions regarding the field type select
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @see http://www.w3schools.com/tags/tag_select.asp
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Select extends Basics{
	var $options;
	
	function __construct(){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Select","_coll");
	}
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
		
public function showfield($post=null, $element=null){
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;

			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			$first_op 	= ($element[first_option]!="") ? $element[first_option] : __("Choose","_coll");
			$options 	= explode("\n", $element[options]);
		

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
			
		
			
			$html = "";

			$multiples = ($element[multiple]==1)?"multiple=\"true\"":""; 
			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
						<select {$required} name=\"{$name}[]\" {$multiples}>";
			
			if($element[multiple]!=1){
			$html.="<option>{$first_op}</option>";
			}
			foreach($options as $option){			
			$option 	= trim($option);
			$key_val	= explode(":", $option);
			
			$option_key = (count($key_val)==2) ? $key_val[0] : $option;
			$option_val = (count($key_val)==2) ? $key_val[1] : $option;
			//$sel 		= ($option_val==$selval)? "selected":"";
			$sel 		= (is_array($values) && in_array($option, $values)) ? "selected" : "";
			$html.="<option {$sel} value=\"{$option_key}\">{$option_val}</option>";
			}
			$html.="</select>
			
			
			</div>";
		
		
			$this->Field->metafieldBox($html, $element);
	
	}

/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
    * @access public
    */	

public function fieldOptions($element){


echo"
	

	<table class=\"widefat metadata\" id=\"another\" cellspacing=\"0\" cellpadding=\"10\">";
	
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
	<td><input type=\"text\" name=\"label\" value=\"{$element[label]}\"/></td>
	</tr>
	
	<tr>
	<td>".__("Description").":</td>
	<td><textarea name=\"description\" rows=\"3\" cols=\"60\">{$element[description]}</textarea></td>
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
	</tr>";
	
	$first_option = ($element[first_option]=="")? __("Choose","_coll"): $element[first_option];
	
	echo"<tr>
	<td>".__("First option text", "_coll").":</td>
	<td><input type=\"text\" name=\"first_option\" value=\"{$first_option}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Options", "_coll").":<br/>Each option on a new line<br/>
	
	a<br/>
	b<br/>
	<i>or</i><br/>
	key:value<br/>
	key2:value2
	</td>
	<td><textarea name=\"options\" rows=\"3\" cols=\"60\">{$element[options]}</textarea>
	
	
	
	
	
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
	<td valign=\"top\">".__("Show in Collection overview", "_coll").":</td>
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
	\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a>
	</td>
	</tr>
	
	
	</table>
	";


}

}




?>
<?php
//AIzaSyAFJKmLHcvDWgYQuY6hm0FES-R3_gPya6g
?>