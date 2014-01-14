<?php
 /**
  * Handles all functions regarding the field type input type=text
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @see http://www.w3schools.com/tags/tag_input.asp
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  * @todo this type of field type classes probably don't need a constructor function
  */
class Text extends Basics{
	
	function __construct($meta=null){
	
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Text field", "_coll");
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
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/><input type=\"text\" name=\"{$name}\" rel='$rel' class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])." value=\"{$value}\"/>
			</div>";
			
			
			return $html;

}	


/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function showfield($post=null, $element=null, $value=null){
				
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
			
			$i=0;
			foreach ($values as $value){
			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
			<input type=\"text\" class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])." name=\"{$name}[]\" value=\"{$value}\"/>";
			
			if($element[multiple]==1){
			$visibility = ($i==0) ? "0": "1";
			$html.="<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")."\" href=\"#\" style=\"opacity:{$visibility}\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>";
			}
			
			$html."</div>";
			$i++;
			
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
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Placeholder value", "_coll").":<br/> 
	<i class=\"hint\">".__("A greyed out value when the field is empty. Ideal to use for hints.","_coll")."</i></td>
	<td><input type=\"text\" name=\"placeholder\" value=\"{$element[placeholder]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Length", "_coll")."</td>
	<td><select name=\"length\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	$selector = ($element[length]=="")? 20 : $element[length];
	for ($i=1; $i<100; $i++){
	$selected = ($i==$selector)? "selected":"";	
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
//print_r($element);
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
	<i class=\"hint\">".__("A greyed out value when the field is empty. Ideal to use for hints.","_coll")."</i></td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][placeholder]\" value=\"{$element[placeholder]}\"/>
	
	</td>
	</tr>
	
	
	<tr>
	<td>".__("Length", "_coll").":</td>
	<td><select name=\"subfields[{$element[nonce]}][length]\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	$selector = ($element[max_length]=="")? 20 : $element[max_length];
	for ($i=1; $i<100; $i++){
	$selected = ($i==$selector)? "selected":"";	
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