<?php
/**
  * Handles all functions regarding the field type date (jquery ui datepicker)
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @author URI: http://www.statuur.nl/
  * @see http://jqueryui.com/demos/datepicker/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Date extends Basics{
	var $options;
	
	function __construct($meta=null){
	parent::init();
	$this->Field 		= new Field();		
	$this->fieldname	= __("Date","_coll");
	}
	
	
/**
    * Shows the specific subfieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function showsubfield($post=null, $element=null, $value){
			
			if(sizeof($post)>0){//only load scripts when the function is called from the edit screen
			wp_enqueue_script('jquery-ui-datepicker'); 																										
			wp_enqueue_script('datepicker', plugins_url().'/meta-collections/js/date/jquery.datepicker.js');

			
			if(get_bloginfo( 'language')=="nl-NL"){ 	
				wp_enqueue_script('datepicker.lang', plugins_url().'/meta-collections/js/i18n/jquery.ui.datepicker-nl.js');
			}
			
			}
			
			$value		= ($value=="" && $element[default_value]!="") ? $element[default_value] : $value;
			$element[postmetaprefix] = $this->postmetaprefix;
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element['parent']."[".$element[instance]."][".$element['nonce']."]";
			$rel 		= json_encode($element );			
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			
			$html.="<div class=\"metafield-value\">
			<label for=\"{$name}[]}\">{$element[label]}:</label><br/>
			<input type=\"text\" $required name=\"{$name}\" value=\"{$value}\" rel='$rel' class=\"special ".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])."/><span class=\"add-on genericon_ genericon-week\"></span>
			</div>";
			
			return $html;
}	
	
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    * @todo make internationalisation for colorpicker and date dynamic
    */	
	
function showfield($post=null, $element=null){
			if(sizeof($post)>0){//only load scripts when the function is called from the edit screen
			wp_enqueue_script('jquery-ui-datepicker'); 																										
			wp_enqueue_script('datepicker', plugins_url().'/meta-collections/js/date/jquery.datepicker.js');

			
			if(get_bloginfo( 'language')=="nl-NL"){ 	
				wp_enqueue_script('datepicker.lang', plugins_url().'/meta-collections/js/i18n/jquery.ui.datepicker-nl.js');
			}
			
			}
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];
			$rel 		= json_encode($element );			
			
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
			<label for=\"{$name}[]}\">{$element[label]}:</label><br/>
			<input type=\"text\" $required name=\"{$name}[]\" value=\"{$value}\" rel='$rel' class=\"special ".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])."/><span class=\"add-on genericon_ genericon-week\"></span>";  
			
			if($element[multiple]==1){
			$visibility = ($i==0) ? "0": "1";
			$html.="<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")."\" href=\"#\" style=\"opacity:{$visibility}\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>";
			}
			
			$html.="</div>";			
			$i++;
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
	echo"
	
	<tr>
	<td>".__("Type").":</td>
	<td>";
	$this->Field->getfieldSelect($element);
	echo"</td>
	</tr>";
	
	$this->Field->getBasics($element);
	$this->Field->getValidationOptions($element);	
		
	echo"<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Placeholder value", "_coll").":<br/> 
	<i class=\"hint\">".__("A greyed out value when the field is empty. Ideal to use for hints.","")."</i></td>
	<td><input type=\"text\" name=\"placeholder\" value=\"{$element[placeholder]}\"/>
	
	</td>
	</tr>";
	
	$formats = array(	
						"Atom"		=> "$.datepicker.Atom",
						"Cookie"	=> "$.datepicker.COOKIE",
						"ISO_8601"	=> "$.datepicker.ISO_8601",
						"RFC_822"	=> "$.datepicker.RFC_822",
						"RFC_850"	=> "$.datepicker.RFC_850",
						"RFC_1036"	=> "$.datepicker.RFC_1036",
						"RFC_1123"	=> "$.datepicker.RFC_1123",
						"RFC_2822"	=> "$.datepicker.RFC_2822",
						"RSS"		=> "$.datepicker.RSS",
						"W3C"		=> "$.datepicker.W3C",
						"Timestamp"	=> "$.datepicker.TIMESTAMP",
						"TICKS"		=> "!",
						"dd-mm-yy"	=>	"dd-mm-yy",	
						);

	echo"<tr>
	<td>".__("Format", "_coll").":<br/>
	<i class=\"hint\">for more info on formats see:<br/> <a href=\"http://docs.jquery.com/UI/Datepicker/formatDate\" target=\"_blank\">http://docs.jquery.com/UI/Datepicker/formatDate</a></i>
	</td>
	<td><select name=\"format\">";
	
	foreach($formats as $name=>$format){
		$selected =($format==$element['format'])? "selected":"";
		echo"<option {$selected} value=\"{$format}\">{$name}</option>";
	}
	
	echo"</select>	
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
	<td colspan=\"2\" style=\"padding:10px\">
	<a href=\"#\" onclick=\"save_metafield('{$element[ID]}', '{$element[cpt]}', '".__("Field Options Saved")."...');\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a></td>
	</tr>
	
	
	</table>
	
	<script>
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
	
	$parent 	= ($element[parent]=="") ? $element[ID] : $element[parent]; 
	
	echo"
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][parent]\" value=\"{$parent}\"/>
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][nonce]\" value=\"{$element[nonce]}\"/>
	<table class=\"widefat metadata\" cellpadding=\"10\"><tr>
	<td>".__("Type").":</td>
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
	
	
	
	
	$formats = array(	
						"Atom"		=> "jQuery.datepicker.Atom",
						"Cookie"	=> "jQuery.datepicker.COOKIE",
						"ISO_8601"	=> "jQuery.datepicker.ISO_8601",
						"RFC_822"	=> "jQuery.datepicker.RFC_822",
						"RFC_850"	=> "jQuery.datepicker.RFC_850",
						"RFC_1036"	=> "jQuery.datepicker.RFC_1036",
						"RFC_1123"	=> "jQuery.datepicker.RFC_1123",
						"RFC_2822"	=> "jQuery.datepicker.RFC_2822",
						"RSS"		=> "jQuery.datepicker.RSS",
						"W3C"		=> "jQuery.datepicker.W3C",
						"Timestamp"	=> "jQuery.datepicker.TIMESTAMP",
						"TICKS"		=> "!",
						"dd-mm-yy"	=>	"dd-mm-yy",	
						);
	
	echo"<tr>
	<td>".__("Format", "_coll").":<br/>
	for more info on formats see:<br/> <a href=\"http://docs.jquery.com/UI/Datepicker/formatDate\" target=\"_blank\">http://docs.jquery.com/UI/Datepicker/formatDate</a>
	</td>
	<td><select name=\"subfields[{$element[nonce]}][format]\">";
	
	foreach($formats as $name=>$format){
		$selected =($format==$element['format'])? "selected":"";
		echo"<option {$selected} value=\"{$format}\">{$name}</option>";
	}
	
	echo"</select>	
	</td>
	</tr>
	
		<tr>
	<td>".__("Default Value", "_coll").":</td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][default_value]\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Placeholder value", "_coll").":<br/> 
	<i style=\"color:#aaa\">".__("A greyed out value when the field is empty. Ideal to use for hints.","")."</i></td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][placeholder]\" value=\"{$element[placeholder]}\"/>
	
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
	</script>";

}

}
?>