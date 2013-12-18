<?php
/**
  * Handles all functions regarding the field type date (jquery ui datepicker)
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @author URI: http://www.statuur.nl/
  * @see http://jqueryui.com/demos/datepicker/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Date extends Basics{
	var $options;
	
	function __construct($meta=null){
	parent::init();
	$this->Field 		= new Field();		
	$this->fieldname	= __("Date","_coll");
	}
	
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    * @todo make internationalisation for colorpicker and date dynamic
    */	
	
function showfield($post=null, $element=null){
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;

			$required 	= ($element[required]==1) ? "class=\"required datepicker\" " : "class=\"datepicker\" ";
			$max_length = ($element[max_length]!="") ? " maxlength=\"{$element[max_length]}\"" :"";
			$length 	= ($element[max_length]!="") ? " size=\"".($element[max_length]+2)."\"" :"20";
		

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
			
		
			
			$html = "";

			foreach ($values as $value){
			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
			<input type=\"text\" $required name=\"{$name}[]\" value=\"{$value}\"/> 
			<a class=\"delete_metavalue\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(this);return false;\">&nbsp;</a>
			</div>";
			}
		
			$html.="<script>
			jQuery(document).ready(function () {
			 date_preset = {$element['format']} ;
			 jQuery('.datepicker').datepicker({
			 
			 dateFormat: date_preset
			
			 }).val();
			 
			});
			
			
			</script>";
		
			$this->Field->metafieldBox($html, $element);
			//, jQuery.datepicker.regional['nl']
			/*
			echo"<script>
			$(document).ready(function () {
			 date_preset = {$element['format']} ;
			 jQuery('.datepicker').datepicker({
			 
			 dateFormat: date_preset
			
			 }).val();
			 
			});
			
			
			</script>";
				*/ 	
}

/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
    * @access public
    */	
function fieldOptions($element){
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
	
	
	$formats = array(	
						"Atom"		=> "jQuery.datepicker.Atom",
						"Cookie"	=> "jQuery.datepicker.COOKIE",
						"ISO_8601"	=> "jQuery.datepicker.ISO_8601",
						"RFC_822"	=> "jQuery.datepicker.RFC_822",
						"RFC_850"	=> "jQuery.datepicker.RFC_850",
						"RFC_1036"	=> "jQuery.datepicker.RFC_1036",
						"RFC_1123"	=> "jQuery.datepicker.RFC_1123",
						"RFC_2822"	=> "jQuery.datepicker.RFC_2822",
						"RSS"		=> "jQuery.datepicker.RSS",
						"W3C"		=> "jQuery.datepicker.W3C",
						"Timestamp"	=> "jQuery.datepicker.TIMESTAMP",
						"TICKS"		=> "!",
						"dd-mm-yy"	=>	"dd-mm-yy",	
						);
	
	echo"<tr>
	<td>".__("Format", "_coll").":<br/>
	for more info on formats see:<br/> <a href=\"http://docs.jquery.com/UI/Datepicker/formatDate\" target=\"_blank\">http://docs.jquery.com/UI/Datepicker/formatDate</a>
	</td>
	<td><select name=\"format\">";
	
	foreach($formats as $name=>$format){
		$selected =($format==$element['format'])? "selected":"";
		echo"<option {$selected} value=\"{$format}\">{$name}</option>";
	}
	
	echo"</select>	
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

	
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"overview\" {$s_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"overview\" {$s_checked_no}> ".__("No")."</label></li>
                </ul>
	
	</td>
	</tr>
	
	<tr>
	<td colspan=\"2\" style=\"padding:10px\">
	<a href=\"#\" onclick=\"save_metafield('{$element[ID]}', '{$element[cpt]}', '".__("Field Options Saved")."...');\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a></td>
	</tr>
	
	
	</table>";

//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}

}
?>