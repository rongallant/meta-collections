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
			$return_value = $element[return_value];
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
			
			
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);

			
			$htmlelement 			= "";
			$html.="<div class=\"metafield-value\">";
			
			switch ($element[field_type]) {
			   
			    case "radio":			    
			    foreach($users as $user){
			    	$checked 		= (is_array($values) && in_array($user->$return_value, $values)) ? "checked" : "";			
			    	$html.= "<input type=\"radio\" name=\"{$name}\" {$checked} value=\"{$user->$return_value}\"> {$user->fullname} ({$user->caps})<br/>";
			    }
			    break;

				case "select":
				$html.= "<select name=\"{$name}[]\" class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1]).">";			    
			    foreach($users as $user){
			    	$selected = ($user->$return_value == $value)? "selected": "";
			    	$html.= "<option {$selected} value=\"{$user->$return_value}\">{$user->fullname} ({$user->caps})</option>";
			    }
				$html.= "</select>";			    
			    break;

				case "select_multiple":
				
				$html.= "<select name=\"{$name}[]\" multiple=\"true\"  class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1]).">";			    
			    foreach($users as $user){
			    	$checked 		= (is_array($values) && in_array($user->$return_value, $values)) ? "selected" : "";
			    	$html.= "<option {$checked} value=\"{$user->$return_value}\"> {$user->fullname} ({$user->caps})</option>";
			    }
				$html.= "</select>";			    
			    break;

			    case "checkbox":			    
			    foreach($users as $user){
			    	$checked 		= (is_array($values) && in_array($user->$return_value, $values)) ? "checked" : "";			    
			    	$html.= "<input type=\"checkbox\" {$checked} name=\"{$name}[]\"  class=\"".implode(" ", $fieldfinfo[0])."\" value=\"{$user->$return_value}\"> {$user->fullname} ({$user->caps})<br/>";
			    }
			    break;
				}
		        		
			
			
			$html.="</div>";
						
			
			echo $this->Field->metafieldBox($html, $element);
			}


/**
    * Shows the specific form for the fieldtype with all the options related to that field. 
    * @access public
    */	
public function fieldOptions($element){

echo"<table class=\"widefat metadata\" cellpadding=\"10\">";
	$statusc = ($element[status]==1)? "checked":"";
	$formID = "#edit_options_{$element[ID]}_{$element[cpt]}";
	$this->Field->getID($element);
	
	echo"
	
	<tr>
	<td>".__("Type").":</td>
	<td>";
	
	 $this->Field->getfieldSelect($element);
	
	echo"</td>
	</tr>";
	
	$this->Field->getBasics($element);
	$this->Field->getValidationOptions($element,1);
	
	/*	
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
	*/

echo"<tr>
	<td valign=\"top\">".__("Field type", "_coll").":</td>
	<td valign=\"top\">
	<select name=\"field_type\">";
	//"radio"=>"Radio (single choice)", 
	$types = array("select"=>"Select (single choice)", "select_multiple"=>"Select (multiple choice)",  "checkbox"=>"Checkbox (multiple choice)");
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