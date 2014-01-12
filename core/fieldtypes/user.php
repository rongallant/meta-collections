<?php
 /**
  * Handles all functions regarding the native user. field type = radio, select or checkbox
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
class User extends Basics{
	
	function __construct($meta=null){
	
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("User", "_coll");
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
			
			
			$html = "";			
			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}
			
			$userdata 				= get_users(array('fields'       => 'all'));
			$users 					= array();
			foreach($userdata as $user){
			$usermeta 				= get_user_meta($user->data->ID, "", true);
			//print_r($user->caps);
			$user->data->fullname 	= $usermeta[first_name][0]." ".$usermeta[last_name][0]; 
			$user->data->caps		= $user->roles[0];
			$users[$user->data->ID] = $user->data;
			}
			


			$return_value 			= $element[return_value];
			$htmlelement 			= "";
			$i=0;
			foreach ($values as $value){
			
			switch ($element[field_type]) {
			    case "radio":			    
			    foreach($users as $user){
			    	$checked 		= (is_array($values) && in_array($user->$return_value, $values)) ? "checked" : "";			
			    	$htmlelement.= "<input type=\"radio\" name=\"{$name}\" {$checked} value=\"{$user->$return_value}\"> {$user->fullname} ({$user->caps})<br/>";
			    }
			    break;

				case "select":
				$htmlelement.= "<select name=\"{$name}[]\">";			    
			    foreach($users as $user){
			    	$htmlelement.= "<option value=\"{$user->$return_value}\"> {$user->fullname} ({$user->caps})</option>";
			    }
				$htmlelement.= "</select>";			    
			    break;

				case "select_multiple":
				
				$htmlelement.= "<select name=\"{$name}[]\" multiple=\"true\">";			    
			    foreach($users as $user){
			    	$checked 		= (is_array($values) && in_array($user->$return_value, $values)) ? "selected" : "";
			    	$htmlelement.= "<option {$checked} value=\"{$user->$return_value}\"> {$user->fullname} ({$user->caps})</option>";
			    }
				$htmlelement.= "</select>";			    
			    break;

			    case "checkbox":			    
			    foreach($users as $user){
			    	$checked 		= (is_array($values) && in_array($user->$return_value, $values)) ? "checked" : "";			    
			    	$htmlelement.= "<input type=\"checkbox\" {$checked} name=\"{$name}[]\" value=\"{$user->$return_value}\"> {$user->fullname} ({$user->caps})<br/>";
			    }
			    break;

			}	
			   
			    
			    		
			
			$html.="<div class=\"metafield-value\">
			{$htmlelement}";
			
			if($element[multiple]==1 && $element[field_type]!="radio"){
			$visibility = ($i==0) ? "0": "1";
			$html.="<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" style=\"opacity:{$visibility}\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>";
			}
			
			$html.="</div>";
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
	<td style=\"width:25%\">".__("Label").": *</td>
	<td><input type=\"text\" name=\"label\" class=\"required\" value=\"{$element[label]}\"/></td>
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
	<td valign=\"top\">".__("Field type", "_coll").":</td>
	<td valign=\"top\">
	<select name=\"field_type\">";
	
	$types = array("radio"=>"Radio (single choice)", "select"=>"Select (single choice)", "select_multiple"=>"Select (multiple choice)",  "checkbox"=>"Checkbox (multiple choice)");
	foreach ($types as $type=>$name){
	$selected = ($type==$element[field_type])? "selected":"";	
	echo"<option value=\"{$type}\" {$selected}>{$name}</option>";
	}
	echo"</select>
	
	</td>
	</tr>	


<tr>
	<td valign=\"top\">".__("Return value", "_coll").":</td>
	<td valign=\"top\">
	<select name=\"return_value\">";
	
	$rvalue = array("ID", "user_login", "user_nicename",  "user_email");
	foreach ($rvalue as $rv){
	$selected = ($rv==$element[return_value])? "selected":"";	
	echo"<option value=\"{$rv}\" {$selected}>{$rv}</option>";
	}
	echo"</select>
	
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

//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}

}
?>