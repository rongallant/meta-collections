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
    * Shows the specific subfieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function sshowsubfield($post=null, $element=null, $value){
			
			if(sizeof($post)>0){//only load scripts when the function is called from the edot screen
			wp_enqueue_script( 'datetimepicker.min', plugins_url().'/meta-collections/js/wysiwyg/jquery.wysiwyg.js', '', '2.1.5');
			}
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element['parent']."[".$element[instance]."][".$element['nonce']."]";
			$element[postmetaprefix] = $this->postmetaprefix;
			
			//$values	 	= get_post_meta($post->ID, $name, true); 
			//$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			//$values	 	= (!is_array($values)) ? array($values) : $values;
					
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			$rel 		= json_encode($element );
			
			$html 		= "";	
			
			$html.="
			<div id=\"wysiwygcontainer\" class=\"wysiwygscontainer metafield-value\" rel=\"{$element[nonce]}\"></div>";
			
			echo"<div id=\"wysiwygs_{$element[nonce]}\" class=\"wysiwygs\" rel=\"$name\"><label for=\"{$element[ID]}\">{$element[label]}:</label><br/>";
			
			$w_opts 	= array("textarea_name" 	=> $name."[]");
			$config_opts= array("teeny", "wpautop", "media_buttons");
			
			foreach($config_opts as $o){
				$w_opts[$o] = $element[$o]; 	
			}
			
			$num=0;	
			
			echo"<div>";
			
			wp_editor($value[0], $name, $w_opts);
			
			echo"</div>";
			
			echo"</div>";
			
			return $html;
}	
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
function showfield($post=null, $element=null){
			
			if(sizeof($post)>0){//only load scripts when the function is called from the edot screen
			wp_enqueue_script( 'datetimepicker.min', plugins_url().'/meta-collections/js/wysiwyg/jquery.wysiwyg.js', '', '2.1.5');
			}
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;

			
			//$max_length = ($element[max_length]!="") ? " maxlength=\"{$element[max_length]}\"" :"";
			$length 	= ($element[max_length]!="") ? " size=\"".($element[max_length]+2)."\"" :"20";
			$html 		= "";
	
			
			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}

						
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			//$("#wysiwygs").appendTo('#wysiwygcontainer'); dus alle wysiwygs plaatsen in wysiwygcontainer
					
			$html.="<div class=\"metafield-value\">
			<div id=\"wysiwygcontainer\" class=\"wysiwygscontainer\" rel=\"{$element[ID]}\"></div>
			</div>";
			
			echo"<div id=\"wysiwygs_{$element[ID]}\" class=\"wysiwygs\" rel=\"$name\">";
			
			$w_opts 	= array("textarea_name" 	=> $name."[]");
			$config_opts= array("teeny", "wpautop", "media_buttons");
			
			foreach($config_opts as $o){
				$w_opts[$o] = $element[$o]; 	
			}
			
			$num=0;	
			foreach ($values as $value){
			echo"<div>";
			
			wp_editor($value, $name, $w_opts);
			
			if($element[multiple]==1){
			$visibility = ($num==0) ? "0": "1";
			echo"
			<a class=\"delete_metavalue genericon_ genericon-trash\" style=\"width:20px;display:inline-block;color:white;border-radius:4px;opacity:{$visibility}\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(event, $(this).parent())\">&nbsp;</a>";
			
			}
			echo"</div>";
			$num++;
			}
			echo"</div>";
			
			
			echo $this->Field->metafieldBox($html, $element);
			
			

}



/**
    * Shows the specific form for the fieldtype with all the options related to that subfield. 
    * @access public
    */	
public function ssubfieldOptions($element, $new=null){

$statusc 	= ($element[status]==1)? "checked":"";
$parent 	= ($element[parent]=="") ? $element[ID] : $element[parent]; 

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
	
	echo"<tr>
	<td>".__("Automatic Paragraph Tags (wpautop)", "_coll").":</td>
	<td>
	<input type=\"checkbox\" name=\"subfields[{$element[nonce]}][wpautop]\" {$a_checked} value=\"1\"/>
	</td>
	</tr>

	<tr>
	<td>".__("Media buttons", "_coll").":</td>
	<td>
	<input type=\"checkbox\" name=\"subfields[{$element[nonce]}][media_buttons]\" {$m_checked} value=\"1\"/>
	</td>
	</tr>


	<tr>
	<td>".__("Toolbar", "_coll").":</td>
	<td>";
	
	$t_checked_yes	= ($element[teeny]==0)? "checked": "";
	$t_checked_no	= ($element[teeny]==1)? "checked": "";

	
	echo"<input type=\"radio\" name=\"subfields[{$element[nonce]}][teeny]\" value=\"0\" {$t_checked_yes}/> ".__("Full", "_coll")."<br/>
	<input type=\"radio\" name=\"subfields[{$element[nonce]}][teeny]\" value=\"1\" {$t_checked_no}/> ".__("Simple", "_coll")." 
	
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
	<td>".__("Type").":<br/>
	<i class=\"hint\">".__("Technical reference","_coll").":<br/><a href=\"http://codex.wordpress.org/Function_Reference/wp_editor\" target=\"_blank\">http://codex.wordpress.org/Function_Reference/wp_editor</a></i>
	</td>
	<td>";
	
	 $this->Field->getfieldSelect($element);
	
	echo"</td>
	</tr>";
	
	$this->Field->getBasics($element);
	
	$a_checked	= ($element[wpautop]==1)? "checked": "";
	$m_checked	= ($element[media_buttons]==1)? "checked": "";
	
	echo"
	

	<tr>
	<td>".__("Automatic Paragraph Tags (wpautop)", "_coll").":</td>
	<td>
	<input type=\"checkbox\" name=\"wpautop\" {$a_checked} value=\"1\"/>
	</td>
	</tr>

	<tr>
	<td>".__("Media buttons", "_coll").":</td>
	<td>
	<input type=\"checkbox\" name=\"media_buttons\" {$m_checked} value=\"1\"/>
	</td>
	</tr>


	<tr>
	<td>".__("Toolbar", "_coll").":</td>
	<td>";
	
	$t_checked_yes	= ($element[teeny]==0)? "checked": "";
	$t_checked_no	= ($element[teeny]==1)? "checked": "";

	
	echo"<input type=\"radio\" name=\"teeny\" value=\"0\" {$t_checked_yes}/> ".__("Full", "_coll")."<br/>
	<input type=\"radio\" name=\"teeny\" value=\"1\" {$t_checked_no}/> ".__("Simple", "_coll")." 
	
	</td>
	</tr>";
	
	/*
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
	*/
	
	echo"<tr>
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