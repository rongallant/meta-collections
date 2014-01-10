<?php
/**
  * Class contains all the basic functionality for managing fieldtypes
  * 
  * All functions are derived from ajax requests defined in collections.php
  *
  * @category Wordpress Plugin
  * @package  Collections Wordpress plugin
  * @author   Bastiaan Blaauw <statuur@gmail.com>
  * @version  1.0 $Revision: 60
  * @access   Public
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @see      http://metacollections.statuur.nl/ 
  */
class Field extends Basics{

function __construct(){
	parent::init();

}
/**
    * Generates the html for the field type select in a metafield form.
    * Since it is the same in every form it is a shred function    
    * @access private
    * @param array $element
    * @param bool $subfield 
    * @param bool $new
    */
function getFieldSelect($element, $subfield=null, $new=null){
$this->getFields();
$this->getClasses();
$this->getClassesWithSubfields(); 

unset($element[action]);
$action 		= ($subfield==1) ? "changesubfieldtype" :  "changemetafieldtype";

$othervars		= "type: this.value, action:'{$action}'";


$elementinfo	= json_encode($element);
$elementinfo	= preg_replace("/\"/","'",$elementinfo);
$elementinfo 	= preg_replace("/}/",", $othervars}",$elementinfo);

$row 			= $element[row]+1; 
$fieldID 		= ($subfield==1) ? "#td_{$element[nonce]}" :  "#edit_options_{$element[ID]}_{$element[cpt]}" ;
$fields 		= ($subfield==1) ? $this->ClassesWithSubfields : $this->entries;
$name 			= ($subfield==1) ? "subfields[".$element[nonce]."][type]" : "type";



echo"<select name=\"{$name}\" onchange=\"jQuery('{$fieldID}').load('admin-ajax.php', {$elementinfo});\">";
	
	foreach($fields as $metatype=>$metafile){
	$c = ucfirst($metatype);
	$typeclass = new $c();
	$checked = ($metatype == $element[type]) ? "selected": "";
	echo"<option $checked value=\"{$metatype}\">{$typeclass->fieldname}</option>";
	}
	
	echo"</select>";
	
}

public function getAttributesAndClasses($element){
			$classnames = array(); 
			$attributes = array(); 

			if($element[placeholder]!=""){
				$attributes [] = "placeholder=\"{$element[placeholder]}\" ";
			}
			//print_r($element);
			if($element[format]!=""){
				$attributes [] = "rel=\"{$element[format]}\" ";
			}
			
			if($element[length]!=""){
				$attributes [] = "size=\"{$element[length]}\" ";
			}
			
			if($element[type]=="date"){
				$classnames[] = "datepicker"; 
			}

			if($element[type]=="date_and_or_time"){
				$classnames[] = "datetimepicker"; 
				
				$options 	  = array();
				
				if(get_bloginfo( 'language')=="nl-NL"){
					 $options['lang']	  	= "nl";
				}	  
				
				if($element[preset]=="time"){
					$options[datepicker]	= false;
					$options[format]	  	= "H:i";
				}
				
				if($element[preset]=="date"){
					$options[timepicker]	= false;
					$options[format]	  	= "d/m/Y";
				}
				
				//timepicker:false,
				//format:'d/m/Y'
				//print_r($options);
				$attributes [] = "data='".json_encode($options)."'";
			}


			if($element[type]=="colorpicker"){
				$classnames[] = "colorpickers"; 
			}
			
			
			foreach($this->validation_options as $vname=>$options){
				
				if($element['validation'][$vname]==1 || $element['validation'][$vname]!=""){
					switch($options[1]){
						case "c":
					if($element['validation'][$vname]==1){//items that need some sort of validation
						
							$classnames[] = $vname; 
						}
						break;
						
						case "i":
						if ($vname=="max" || $vname=="min" || $vname=="minlength" || $vname=="maxlength"){
							
							$attributes [] = "{$vname}=\"{$element['validation'][$vname]}\" "; //"$name=\" \" ";
						}
						
						break;
						}
				}
			}
			
			return array($classnames, $attributes);
}
/**
    * Loads validation option in form for field options
    * @access public
    * @param array $element
    */	
    
public function getValidationOptions($element){
	
	echo"<tr>
	<td>".__("Validation", "_coll").":<br/>
	<i style=\"color:#aaa\">For more information about validation check: <a href=\"http://jqueryvalidation.org/documentation/\" target=\"_blank\">http://jqueryvalidation.org/documentation/</a></i>
	</td>
	<td>";
	//print_r($element);
	foreach($this->validation_options as $name=>$options){
	
	
	switch($options[1]){
	case "c":
	$o_checked	= ($element['validation'][$name]==1)? "checked": "";
	echo "<input type=\"checkbox\" value=\"1\" {$o_checked} name=\"validation[{$name}]\"/> {$options[0]}<br/>";
	break;	
	
	case "i":
	echo"<input type=\"text\" value=\"{$element['validation'][$name]}\" size=\"12\" name=\"validation[{$name}]\" placeholder=\"".__("enter a number", "_coll")."\" /> {$options[0]}<br/>";
	break;	
	
	case "r":
	echo"<input type=\"text\" value=\"{$element['validation'][$name]}\" size=\"8\" name=\"validation[{$name}]\"  placeholder=\"e.g. [13,23]\"/> {$options[0]}<br/>";
	break;	
	}
	
	
	}
	
	echo"</td>
	</tr>";

}


/**
    * Loads basic form values for fieldoptions
    * status, label and description are always the same
    * @access public
    * @param array $element
    */	
public function getBasics($element){
	$statusc = ($element[status]==1)? "checked":"";
	echo"<tr>
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
	</tr>";
}

/**
    * Reads trough the field types folder an generates an array with entries. Every field (php) found is added to the array
    * @access public
    */	
public function getFields(){
	$field_dir	= ABSPATH."wp-content/plugins/meta-collections/core/fieldtypes";
	$entries	= array();
	
	
	if ($handle = opendir($field_dir)) {
    
    while (false !== ($entry = readdir($handle)) ) {
    $file_ext  =  substr($entry, strlen($entry)-3, strlen($entry));
    $file_name =  substr($entry, 0, strlen($entry)-4);
    
     if($file_ext=="php"){
        $entries[$file_name] = $entry;  
        }
   }
    closedir($handle);
   
   
   $this->entries = array_unique($entries);
	
	return $this->entries;
	}
	
}

/**
    * Includes all classes for the metadata field types.   
    * @access public
    */
public function getClasses(){


	foreach($this->entries as $name=>$entry){
	
	  $file				= ABSPATH."wp-content/plugins/meta-collections/core/fieldtypes/".$entry;

       if(file_exists($file)){
       include_once($file);       
     }
	}
  
  }


public function getClassesWithSubfields(){
	$entries	= array();

	foreach($this->entries as $name=>$entry){
	//print_r();
	//echo $name."=>".$entry."<br/>";
	$c 				= ucfirst($name);
	$typeclass 		= new $c();
	if(method_exists($typeclass, 'subfieldOptions')){
	$entries[$name] = $entry;  
   //  die("bg");   	
	}
	
	}
	$this->ClassesWithSubfields = array_unique($entries);
	
}
	


/**
    * Puts a metafield in a neat box (not a metabox but a metafieldbox)   
    * @access public
    */
public function metafieldBox($html, $element){


	$metafieldBox = "<div id=\"{$element[ID]}_wrapper\" class=\"metafield-wrapper\">
			<div class=\"metafield-box\">
			<div class=\"metafield-label\" style=\"\">{$element[label]}</div>
			
			<div class=\"metafield-body\">{$html}</div>	";	
			
			
			
			
			if($element[multiple]==1){
			$buttontitle = ($element[addtitle]!="") ? "+ ".$element[addtitle] : __("+ add %s", "_coll");
			$metafieldBox .= "<div class=\"metafield-add\"><a href=\"#\" onclick=\"add_value_instance('{$element[ID]}_wrapper', '{$element[type]}');return false\" class=\"button-primary\">{$buttontitle}</a></div>";
			
			}
			$metafieldBox .= "</div>
			</div>";
  
  /* replace all %s and %n with labels name to ensure proper translation */
  $metafieldBox  = preg_replace("/%s/", $element[label],  $metafieldBox);
  //echo $metafieldBox;
  return $metafieldBox;
}

/**
    * Get and show the ID in a table row   
    * @access public
    * @param array $element metafieldinfo
    */
public function getID($element){
	$posttext = '$post->ID';
	echo"<tr>
	<td style=\"width:25%\">".__("ID").":<br/>".__("Unique name for this field","_coll")."</td>
	<td><b>{$element[ID]}</b><br/> ".__("for thema development use","_coll")." <code>get_post_meta({$posttext}, {$this->postmetaprefix}{$element[ID]}, true);</code> 
	".__("this function will return an array with values.", "_coll")." </td>
	</tr>";
}

}
?>