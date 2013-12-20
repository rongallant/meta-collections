<?php
/**
  * Handles all functions regarding the field type input type=text colorpicker from vanderlee.com
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  * @see http://www.vanderlee.com/martijn/?page_id=314
  * @version  1.0 $Revision: 60
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */
  
class Colorpicker extends Basics{
	var $options;
	
	function __construct($meta=null){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Colorpicker", "_coll");
		}
	
	
	
function showfield($post=null, $element=null){

			if(sizeof($post)>0){//only load scripts when the function is called from the edot screen
			wp_enqueue_script('jquery.colorpicker.js', plugins_url().'/meta-collections/js/colorpicker/jquery.colorpicker.js', '', '1.0.6'); 							//user only for the colorpicker field
			wp_enqueue_style('css.colorpicker', plugins_url().'/meta-collections/css/colorpicker/jquery.colorpicker.css'); 											//user only for the colorpicker field
			
			 if(get_bloginfo( 'language')=="nl-NL"){ 	
				 wp_enqueue_script('colorpicker.lang', plugins_url().'/meta-collections/js/i18n/jquery.ui.colorpicker-nl.js'); //admin
			}
	
			}
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];
			$value	 	= get_post_meta($post->ID, $name, true); 
			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];
			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;

			$required 	= ($element[required]==1) ? "class=\"required colorpickers\" " : "class=\"colorpickers\"";
			
			
			$html = "";
//onclick=\"jQuery(this).next().colorpicker.show()\" 
			foreach ($values as $value){
			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
			
			 <div style=\"position:relative;float:left;width:20px;height:20px;top:1px;background:#{$value}\">&nbsp;</div><input type=\"text\" $required name=\"{$name}[]\" id=\"{$name}\" value=\"{$value}\"/> 
			 
			<a class=\"delete_metavalue\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(this);return false;\">&nbsp;</a>
			</div>";
			}
		
			$this->Field->metafieldBox($html, $element);
			
			
			echo "
			<script>	
				colortitle = '".__("Choose a color","_coll")."';
				
				jQuery( function() {
               
                cp = jQuery('.colorpickers').colorpicker({
                	parts: 'full',
					alpha: true,
					color: '#c0c0c0',
					title: colortitle,
					select: function(data, color){
					jQuery(this).prev().css({background: '#'+color.formatted});
					}
					});
			});
			
			
			</script>";
			
}


function fieldOptions($element){
//$element (){
	
echo"<table class=\"widefat metadata\" cellpadding=\"10\">";
$statusc = ($element[status]==1)? "checked":"";
	
	$this->Field->getID($element);
	
	echo"<tr>
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
	<td><input type=\"text\" name=\"label\" required value=\"{$element[label]}\"/></td>
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
	<td>".__("Default Color", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Use Alpha transparency", "_coll").":</td>
	<td>";
	
	$a_checked_yes	= ($element[alpha]==1)? "checked": "";
	$a_checked_no	= ($element[alpha]==0)? "checked": "";
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"alpha\" {$a_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"alpha\" {$a_checked_no}> ".__("No")."</label></li>
                </ul>
	
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
	$formID 		= "#edit_options_{$element[ID]}_{$element[cpt]}";

	
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
	
	
	</table>";

//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}

}
?>