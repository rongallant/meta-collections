<?php
 /**
  * Handles all functions regarding the field type radio button
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @see http://www.w3schools.com/tags/tag_select.asp
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Radio extends Basics{
	var $options;
	
	function __construct(){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Radio","_coll");
	}


/**
    * Shows the specific subfieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function showsubfield($post=null, $element=null, $value){
			$element[postmetaprefix] = $this->postmetaprefix;
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element['parent']."[".$element[instance]."][".$element['nonce']."]";
			$class	 	= $this->postmetaprefix.$element['parent']."_".$element[instance]."_".$element['nonce'];
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			$rel 		= json_encode($element);
			$options 	= explode("\n", $element[options]);
		

			$html		="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>";
			
			foreach($options as $option){			
			$option 	= trim($option);
			$key_val	= explode(":", $option);
			
			$option_key = (count($key_val)==2) ? $key_val[0] : $option;
			$option_val = (count($key_val)==2) ? $key_val[1] : $option;

			$checked 		= ($value==$option_val) ? "checked" : "";
			$checked 		= ($value=="" && $option_val==$element[default_value]) ? "checked" : $checked;
			
			$html.="<input type=\"radio\" class=\"$class ".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])." {$checked} value=\"{$option_val}\" name=\"{$name}[]\"> {$option_key}<br/>";
			}
			
			$html.="<div style=\"margin-top:10px;height:30px;\"><a href=\"#\" onclick=\"event.preventDefault();$('.{$class}').attr('checked', false);
	
			\" title=\"".__("Uncheck all","_coll")."\" class=\"uncheck collection_button genericon_ genericon-maximize\"></a></div>
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

			$options 	= explode("\n", $element[options]);
		
			$html = "";

			
			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}
				
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);


			foreach ($values as $value){
			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>";
			
			foreach($options as $option){			
			$option 	= trim($option);
			$key_val	= explode(":", $option);
			
			$option_key = (count($key_val)==2) ? $key_val[0] : $option;
			$option_val = (count($key_val)==2) ? $key_val[1] : $option;

			$checked 		= ($value==$option_val) ? "checked" : "";
			$checked 		= ($value=="" && $option_val==$element[default_value]) ? "checked" : $checked;
			
			$html.="<input type=\"radio\" class=\"$name ".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])." {$checked} value=\"{$option_val}\" name=\"{$name}[]\"> {$option_key}<br/>";
			}
			
			$html.="<div style=\"margin-top:10px;height:30px;\"><a href=\"#\" onclick=\"event.preventDefault();$('.{$name}').attr('checked', false)\" title=\"".__("Uncheck all","_coll")."\" class=\"uncheck collection_button genericon_ genericon-maximize\"></a>";
			if($element[multiple]==1){
			echo"<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>";
			}
			$html.="</div>
			</div>";
			}
			
			
			echo $this->Field->metafieldBox($html, $element);
	
}		


/**
    * Shows the specific form for the fieldtype with all the options related to that field. 
    * @access public
    */	

public function fieldOptions($element){
	$formID 		= "#edit_options_{$element[ID]}_{$element[cpt]}";

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

	echo"
	
	<tr>
	<td>".__("Default Value", "_coll").":<br>
	<i class=\"hint\">".__("a value that matches on of the values in the options field", "_coll")."</td>
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
	<td><textarea name=\"options\" rows=\"3\" cols=\"60\" class=\"required\">{$element[options]}</textarea>
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

$parent 	= ($element['parent']=="") ? $element[ID] : $element[parent]; 

echo"
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][parent]\" value=\"{$parent}\"/>
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][nonce]\" value=\"{$element[nonce]}\"/>
	<table class=\"widefat metadata\" cellpadding=\"10\">
	<tr>
	<td>".__("Type").": </td>
	<td>";
	
	if($new==1){
	unset($element[label]);	
	unset($element[description]);	
	}

	$this->Field->getfieldSelect($element, 1);
	
	echo"</td>
	</tr>";
	
	$this->Field->getSubBasics($element);
	$this->Field->getValidationOptions($element,1);
			
	echo"<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][default_value]\" value=\"{$element[default_value]}\"/>
	
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
	<td><textarea name=\"subfields[{$element[nonce]}][options]\" rows=\"3\" cols=\"60\" class=\"required\">{$element[options]}</textarea>
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