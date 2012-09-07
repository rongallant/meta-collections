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
/**
    * Generates the html for the field type select in a metafield form.
    * Since it is the same in every form it is a shred function    
    * @access private
    */
function getFieldSelect($element){

$othervars		= "fieldtype: this.value, action:'changemetafieldtype'";
$elementinfo	= json_encode($element);
$elementinfo	= preg_replace("/\"/","'",$elementinfo);
$elementinfo 	= preg_replace("/}/",", $othervars}",$elementinfo);


echo"<select name=\"type\" onchange=\"jQuery('#edit_options_{$element[ID]}_{$element[cpt]}').load('admin-ajax.php', {$elementinfo});\">";
	
	foreach($this->entries as $metatype=>$metafile){
	$checked = ($metatype == $element[type]) ? "selected": "";
	echo"<option $checked value=\"{$metatype}\">{$metatype}</option>";
	}
	
	echo"</select>";
	
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


	foreach($this->entries as $entry){
	  $file				= ABSPATH."wp-content/plugins/meta-collections/core/fieldtypes/".$entry;

       if(file_exists($file)){
       include_once($file);       
     }
	}
  
  
	
	
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
			$metafieldBox .= "<div class=\"metafield-add\"><a href=\"#\" onclick=\"add_value_instance('{$element[ID]}_wrapper', '{$element[type]}');return false\" class=\"button-primary\">".__("+ add %s", "_coll")."</a></div>";
			}
			$metafieldBox .= "</div>
			</div>";
  
  /* replace all %s and %n with labels name to ensure proper translation */
  $metafieldBox  = preg_replace("/%s/", $element[label],  $metafieldBox);
  echo $metafieldBox;

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