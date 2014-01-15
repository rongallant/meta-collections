<?php
/**
  * Handles all functions regarding the field type textarea
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @see http://www.w3schools.com/tags/tag_textarea.asp
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */


class Textarea extends Basics{
	var $options;
	
	function __construct(){
	
	parent::init();
	$this->Field = new Field();
	$this->fieldname 	= __("Textarea","_coll");
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
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			$rel 		= json_encode($element);
			
			$html="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
			<textarea name=\"{$name}\" class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1]).">{$value}</textarea>
			</div>";

			
			
			return $html;

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
			$values	 	= ($values=="" && $element[default_value]!="") ? array($element[default_value]) : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;
			
			$required 	= ($element[required]==1) ? "class=\"required\" " : "";

			$html 		= "";
			
			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}
			
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			
			
			foreach ($values as $value){
			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
			<textarea name=\"{$name}[]\" class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1]).">{$value}</textarea>";
			
			if($element[multiple]==1){
			$visibility = ($i==0) ? "0": "1";
			$html.="<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")." style=\"opacity:{$visibility}\" {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>";
			}

			
			$html.="</div>";
			}
		
			echo $this->Field->metafieldBox($html, $element);
			
			
}


/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
    * @access public
    */	
function fieldOptions($element){
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
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Placeholder value", "_coll").":<br/> 
	<i style=\"color:#aaa\">".__("A greyed out value when the field is empty. Ideal to use for hints.","")."</i></td>
	<td><input type=\"text\" name=\"placeholder\" value=\"{$element[placeholder]}\"/>
	
	</td>
	</tr>";
	
	$cols = ($element[columns]=="")? 40 : $element[columns];
	echo"<tr>
	<td>".__("Columns", "_coll").":<br/>".__("Columns length of the textarea field", "_coll")."</td>
	<td><select name=\"columns\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	for ($i=1; $i<50; $i++){
	$selected = ($i==$cols)? "selected":"";	
	echo"<option value=\"{$i}\" {$selected}>$i</option>";
	}
	echo"</select>
	
	
	
	</td>
	</tr>";
	
	$rows = ($element[rows]=="")? 7 : $element[rows];	
	echo"<tr>
	<td>".__("Rows", "_coll").":<br/></td>
	<td><select name=\"rows\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	for ($i=1; $i<50; $i++){
	$selected = ($i==$rows)? "selected":"";	
	echo"<option value=\"{$i}\" {$selected}>$i</option>";
	}
	echo"</select>
	
	
	
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
	$this->Field->getValidationOptions($element);
			
	echo"<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][default_value]\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Placeholder value", "_coll").":<br/> 
	<i style=\"color:#aaa\">".__("A greyed out value when the field is empty. Ideal to use for hints.","")."</i></td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][placeholder]\" value=\"{$element[placeholder]}\"/>
	
	</td>
	</tr>";
	
	$cols = ($element[columns]=="")? 40 : $element[columns];
	echo"<tr>
	<td>".__("Columns", "_coll").":<br/>".__("Columns length of the textarea field", "_coll")."</td>
	<td><select name=\"subfields[{$element[nonce]}][columns]\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	for ($i=1; $i<50; $i++){
	$selected = ($i==$cols)? "selected":"";	
	echo"<option value=\"{$i}\" {$selected}>$i</option>";
	}
	echo"</select>
	</td>
	</tr>";
	
	$rows = ($element[rows]=="")? 7 : $element[rows];	
	echo"<tr>
	<td>".__("Rows", "_coll").":<br/></td>
	<td><select name=\"subfields[{$element[nonce]}][rows]\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	for ($i=1; $i<50; $i++){
	$selected = ($i==$rows)? "selected":"";	
	echo"<option value=\"{$i}\" {$selected}>$i</option>";
	}
	echo"</select>
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