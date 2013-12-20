<?php
/**
  * Handles all functions regarding the field type date and or time (jquery mobiscroller)
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @author URI: http://metacollections.statuur.nl/
  * @see http://mobiscroll.com/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Date_and_or_time extends Basics{
	var $options;
	
	function __construct($meta=null){
		
	parent::init();
	$this->Field 		= new Field();	
	$this->fieldname 	= __("Date and / or Time", "_coll");

	}
	
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
	
function showfield($post=null, $element=null){
					
			if(sizeof($post)>0){//only load scripts when the function is called from the edot screen
			wp_enqueue_script( 'jquery.mobiscroll-2.0.2.custom.min', plugins_url().'/meta-collections/js/date/mobiscroll-2.0.2.custom.min.js', '', '2.0.1');  	//user only for the date field
			wp_enqueue_style( 'mobiscroll.core-2.0.2',  plugins_url('/css/mobiscroll.core-2.0.2.css', __FILE__), '', '2.0.1');  					//user only for the date field
			}
					
					
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;

			$required 	= ($element[required]==1) ? "class=\"required date_time\" " : "class=\"date_time\" ";
		

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
	
			$html = "";

			foreach ($values as $value){
			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
			<input type=\"text\" {$required} name=\"{$name}[]\" value=\"{$value}\"/> 
			<a class=\"delete_metavalue\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(this);return false;\">&nbsp;</a>
			</div>";
			}
		
			$this->Field->metafieldBox($html, $element);
			//minDate: new Date(now.getFullYear(), now.getMonth(), now.getDate()),var now = new Date();
			echo"<script>
			
			date_time_preset = '{$element[preset]}';
			jQuery(document).ready(function () {
			
			jQuery('.date_time').scroller({
			preset: date_time_preset,
			
			display: 'modal',
			mode: 'mixed'
			
			});

			});
			
			</script>";
			
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
	
	
	$date_c		= ($element[preset]=="date")? "checked": "";
	$date_c		= ($element[preset]=="")? "checked": "";
	
	$time_c		= ($element[preset]=="time")? "checked": "";
	$datetime_c	= ($element[preset]=="datetime")? "checked": "";	
	echo"<tr>
	<td>".__("Date type", "_coll").":</td>
	<td>
	<input type=\"radio\" name=\"preset\" value=\"date\" {$date_c}/> Date<br/>
	<input type=\"radio\" name=\"preset\" value=\"time\" {$time_c}/> Time<br/>
	<input type=\"radio\" name=\"preset\" value=\"datetime\" {$datetime_c}/> Datetime<br/>
	
	
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