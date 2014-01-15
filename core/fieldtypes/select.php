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
    * Shows the specific subfieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function showsubfield($post=null, $element=null, $value){

			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element['parent']."[".$element[instance]."][".$element['nonce']."]";
			$element[postmetaprefix] = $this->postmetaprefix;
			
			//$values	 	= get_post_meta($post->ID, $name, true); 
			//$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			//$values	 	= (!is_array($values)) ? array($values) : $values;
			
			$first_op 	= ($element[first_option]!="") ? $element[first_option] : __("Choose","_coll");
			$options 	= explode("\n", $element[options]);
			$rel 		= json_encode($element );

			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			$html = "";
			
			$html.="<div class=\"metafield-value\">";
			
			
			if($element[description]!=""){
			echo "<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}
			$html.="<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
					<select name=\"{$name}\" rel='$rel' class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1]).">";
			
			if($element[multiple]!=1){
			$html.="<option>{$first_op}</option>";
			}
			
			foreach($options as $option){			
			$option 	= trim($option);
			$key_val	= explode(":", $option);
			
			$option_key = (count($key_val)==2) ? $key_val[0] : $option;
			$option_val = (count($key_val)==2) ? $key_val[1] : $option;
			$sel 		= ($option_key==$value || $option_val==$value || ($value=="" && $option_val==$element[default_value])) ? "selected" : "";
			$html.="<option {$sel} value=\"{$option_val}\">{$option_val}</option>";
			}
			$html.="</select>
			
			
			</div>";
		
			return $html;
	
	
	
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
		
			$html = "";

			
			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}
				
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			
			
			foreach ($values as $value){	
			$html.="<div class=\"metafield-value\">
			<select class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])." name=\"{$name}[]\">";
			
			if($element[multiple]!=1 && $element[first_option]!=""){

			
			$html.="<option value=\"\">{$element[first_option]}</option>";
			}
			foreach($options as $option){			
			$option 	= trim($option);
			$key_val	= explode(":", $option);
			
			$option_key = (count($key_val)==2) ? $key_val[0] : $option;
			$option_val = (count($key_val)==2) ? $key_val[1] : $option;
			//$sel 		= ($option_val==$selval)? "selected":"";
			$sel 		= ( (is_array($values) && in_array($option, $values)) || ($value=="" && $option_val==$element[default_value])) ? "selected" : "";
			$html.="<option {$sel} value=\"{$option_key}\">{$option_val}</option>";
			}
			$html.="</select>";
			
			if($element[multiple]==1){
			$visibility = ($i==0) ? "0": "1";
			$html.="<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")." style=\"opacity:{$visibility}\" {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>";
			}
			
			$html.="</div>";
			}
			
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
	$this->Field->getValidationOptions($element);

	echo"
	<tr>
	<td>".__("First option text", "_coll").":</td>
	<td><input type=\"text\" name=\"first_option\" value=\"{$element[first_option]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	</td>
	</tr>
	
	<tr>
	<td>".__("Options", "_coll").":<br/>
	Each option on a new line<br/>
	
	a<br/>
	b<br/>
	<i>or</i><br/>
	key:value<br/>
	key2:value2
	</td>
	<td><textarea name=\"options\" rows=\"20\" cols=\"19\">{$element[options]}</textarea>
	</td>
	</tr>
	
	<tr>
	<td valign=\"top\">".__("Allow multiple values / instances of this element", "_coll").":</td>
	<td valign=\"top\">";
	
	$m_checked_yes	= ($element[multiple]==1)? "checked": "";
	$m_checked_no	= ($element[multiple]==0)? "checked": "";
	$formID = "#edit_options_{$element[ID]}_{$element[cpt]}";
	
	
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



/**
    * Shows the specific form for the fieldtype with all the options related to that subfield. 
    * @access public
    */	
public function subfieldOptions($element, $new=null){
	$parent 	= ($element[parent]=="") ? $element[ID] : $element[parent]; 
	$statusc 	= ($element[status]==1)? "checked":"";
	
	echo"
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][parent]\" value=\"{$parent}\"/>
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][nonce]\" value=\"{$element[nonce]}\"/>
	<table class=\"widefat metadata\" id=\"another\" cellspacing=\"0\" cellpadding=\"10\">";
	
	$statusc = ($element[status]==1)? "checked":"";
	
	echo"
	<tr>
	<td>".__("Type").":</td>
	<td>";
	
	if($new==1){
	unset($element[label]);	
	unset($element[description]);	
	}
	$this->Field->getfieldSelect($element, 1);

	
	echo"</td>
	</tr>

<tr>";
	
	
	$this->Field->getSubBasics($element);
	$this->Field->getValidationOptions($element);
	
	$autoselected = ($element[width]=="") ? "selected" : "";
	
	
	$first_option = ($element[first_option]=="")? __("Choose","_coll"): $element[first_option];
	
	echo"<tr>
	<td>".__("First option text", "_coll").":</td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][first_option]\" value=\"{$first_option}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][default_value]\" value=\"{$element[default_value]}\"/>
	
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
	<td><textarea name=\"subfields[{$element[nonce]}][options]\" rows=\"20\" cols=\"19\">{$element[options]}</textarea>
	</td>
	</tr>

	
	<tr>
	<td colspan=\"2\" style=\"padding:10px\">
	
	
	
	<a href=\"#\" onclick=\"toggle_row(event, '{$element[nonce]}')\" class=\"button closefield\">".__("Close Field")."</a>	</td>
	</tr>
	
	
	</table>
	<script>
	 jQuery(document).ready(function(){
	 $('.rowtype_{$element[nonce]}').html('{$this->fieldname}');
	 });
	</script>
	";

}

}




?>
<?php
//AIzaSyAFJKmLHcvDWgYQuY6hm0FES-R3_gPya6g
?>