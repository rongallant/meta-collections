<?php
/**
  * Handles all functions regarding the Wordpress' own wysiwyg field (Tinymce is standard) 
  *
  * uses Wordpress function <b>wp_editor()</b>
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @see http://codex.wordpress.org/Function_Reference/wp_editor
  * @see http://www.tinymce.com/wiki.php  
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Wysiwyg extends Basics{
	var $options;
	
	function __construct($meta=null){
		parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Wysiwyg", "_coll");
	}
	
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
	
function showfield($post=null, $element=null){
			
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;

			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			$max_length = ($element[max_length]!="") ? " maxlength=\"{$element[max_length]}\"" :"";
			$length 	= ($element[max_length]!="") ? " size=\"".($element[max_length]+2)."\"" :"20";
		

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
			
		
			
			$html = "";
			
			
			foreach ($values as $value){
			$html="<div class=\"metafield-value\">
			
			<div class=\"reciever\"></div>
			
			
			</div>";
			}
			
			
			
			
			echo"<div id=\"{$element[ID]}_wysiwyg\">";
			$s=0;
			foreach ($values as $value){
			echo "<div id=\"wysiwyg_{$element[ID]}_{$s}\">";
			
			if($element[description]!=""){
			echo "<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}

			
			echo"<a class=\"delete_metavalue\" style=\"float:right\"  title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"jQuery('#wysiwyg_{$element[ID]}_{$s}').remove();return false;\">&nbsp;</a>
";
			wp_editor($value, $name.$this->wysiwygs_string.$s, array('dfw' => false, 'tabindex' => $s) );
			
			if(($s+1)<count($values)){
			echo"<div style=\"border-bottom: 1px solid #DFDFDF;margin:5px 0px 15px 0px;\">&nbsp;</div>";
			}
			echo"</div>";
			$s++;
			}
			echo "</div>";
			
		
		
		
		
		echo"<script>
		jQuery(document).ready(function () {
		wysiwyg_num['{$element[ID]}'] = {$s};
		jQuery('#{$element[ID]}_wysiwyg').appendTo('.reciever');
		});
			</script>";
			$this->Field->metafieldBox($html, $element);

}

/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
    * @access public
    */	
function fieldOptions($element){

	
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
	</tr>
	
	<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
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

//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}

}
?>