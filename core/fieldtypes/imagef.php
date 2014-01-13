<?php
 /**
  * Handles all functions regarding the field type image
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
class Imagef extends Basics{
	
	function __construct($meta=null){
	
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Image field", "_coll");
}
	

	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function showfield($post=null, $element=null){
			global $post;		
			 
			 if(sizeof($post)>0){//only load scripts when the function is called from the edot screen		
			 wp_enqueue_script( 'jquery.imagef', plugins_url().'/meta-collections/js/imagef/jquery.imagef.js', '', '1.0'); 
			 }
			 
			 
			 
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;
			
			
			
			$element[postmetaprefix] = $this->postmetaprefix;
			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			
			$length 	= ($element[max_length]!="") ? " size=\"".($element[max_length]+2)."\"" :"20";
			$rel 		= json_encode($element );
			
			$element[return_value] = ($element[return_value]=="") ? "object": $element[return_value];
			
			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}

						
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
			$html = "";
			$i=0;
			
			foreach ($values as $value){
			$showimagediv		= ($value!="")?"block":"none";
			$showbutton			= ($value=="")?"inline-block":"none";

			if($showimagediv=="block"){
				$valueO 		= json_decode($value);
				$return_value 	= ";background: url('{$valueO->sizes->thumbnail->url}')";
			}
			$hiddenid 			= $this->postmetaprefix.$element[ID]."_".$i;
			$img_container_id 	= $this->postmetaprefix.$element[ID]."_".$i."_img";
			
			$html.="<div class=\"metafield-value\" style=\"display: 
			-moz-inline-stack;
			display: inline-block;
			vertical-align: top;
			zoom: 1;
			*display: inline;
			min-height: 200px;
			_height: 200px;
			margin-right:20px;\">
			<label for=\"{$element[ID]}\">{$element[label]}: </label><br/>
			
			<input type=\"hidden\" name=\"{$name}[]\" id=\"{$hiddenid}\" rel='$rel' value='{$value}' class=\"".implode(" ", $fieldfinfo[0])."\"/> 
			
			<div class=\"image_container\" style=\"display:{$showimagediv}$return_value\" id=\"{$img_container_id}\">
			<ul>
			<li class=\"genericon genericon-edit edit_image\" rel=\"upload-image-button_{$element[ID]}\" title=\"".__("Select other image","_coll")."\"></li>
			<li class=\"genericon genericon-trash delete_image\" title=\"".__("Delete image","_coll")."\"></li>
			</ul>
			</div>
			
			<a class=\"upload-image-button button\" style=\"position:relative;top:-2px;display:{$showbutton}\" id=\"upload-image-button_{$element[ID]}\" rel=\"{$hiddenid}\"><span class=\"genericon-image genericon_\" style=\"vertical-align:bottom;padding:0px 2px 0px 0px;\"></span>".__("Insert Media")."</a>
			";
			
			if($element[multiple]==1){
			$visibility = ($i==0) ? "0": "1";
			$html.="<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")."\" style=\"margin-top:4px;border-radius:4px;opacity:{$visibility}\" {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(event, $(this).parent('.metafield-value'))\">&nbsp;</a>";
			}			
			
			$html.="</div>";
			$i++;
			}
		
			
		
			echo $this->Field->metafieldBox($html, $element);
			
			}




/**
    * Shows the specific subfieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function showsubfield($post=null, $element=null, $value){
			global $post;		
			
			if(sizeof($post)>0){//only load scripts when the function is called from the edot screen		
			wp_enqueue_script( 'jquery.imagef', plugins_url().'/meta-collections/js/imagef/jquery.imagef.js', '', '1.0'); 
			}
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element['parent']."[".$element[instance]."][".$element['nonce']."]";
			$hiddenid 	= $this->postmetaprefix.$element['parent']."_".$element[instance]."_".$element['nonce'];
			$img_container_id = $this->postmetaprefix.$element['parent']."_".$element[instance]."_".$element['nonce']."_img";
			$element[postmetaprefix] = $this->postmetaprefix;
			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			
			$length 	= ($element[max_length]!="") ? " size=\"".($element[max_length]+2)."\"" :"20";
			$rel 		= json_encode($element );

			
			$element[return_value] = ($element[return_value]=="") ? "object": $element[return_value];
			$showimagediv	= ($value!="")?"block":"none";
			$showbutton		= ($value=="")?"inline-block":"none";

			if($showimagediv=="block"){
				$valueO 		= json_decode($value);
				$return_value 	= ";background: url('{$valueO->sizes->thumbnail->url}')";
			}
			

			$html = "";

			$html.="<div class=\"metafield-value\">
			<label for=\"{$element[ID]}\">{$element[label]}: </label><br/>
			<input type=\"hidden\" {$required} size=\"90\" {$length} name=\"{$name}\" id=\"{$hiddenid}\" rel='$rel' value='{$value}'/> 
			
			<div class=\"image_container\" style=\"display:{$showimagediv}$return_value\" id=\"{$img_container_id}\">
			<ul>
			<li class=\"genericon genericon-edit edit_image\" rel=\"upload-image-button_{$element[nonce]}\" title=\"".__("Select other image","_coll")."\"></li>
			<li class=\"genericon genericon-trash delete_image\" title=\"".__("Delete image","_coll")."\"></li>
			</ul>
			
			</div>
			
			
			
			<a class=\"button-secondary upload-image-button\" style=\"display:{$showbutton}\" id=\"upload-image-button_{$element[nonce]}\" rel=\"{$hiddenid}\">".__("get / upload image","_coll")."</a>			
			</div>
			
			<script>
			$(document).ready(function() {
			//$('#upload_".$element['nonce']."').data('uploadinfo', {title: 'Choose an image', 'button_title': 'Insert image', return_object:'{$hiddenid}', img_container_id:'{$img_container_id}'});
			});
			</script>";
			
		
		return $html;
}


/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
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
	</tr>";
	
		
	$this->Field->getBasics($element);
	$this->Field->getValidationOptions($element, 1);
	
	echo"
	<tr>
	<td>".__("Placeholder value", "_coll").":<br/> 
	<i class=\"hint\">".__("A greyed out value when the field is empty. Ideal to use for hints.","_coll")."</i></td>
	<td><input type=\"text\" name=\"placeholder\" value=\"{$element[placeholder]}\"/>
	
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

//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
}


/**
    * Shows the specific form for the fieldtype with all the options related to that subfield. 
    * @access public
    */	
public function subfieldOptions($element, $new=null){
global $_wp_additional_image_sizes;
	
	$statusc 	= ($element[status]==1)? "checked":"";
	$parent 	= ($element[parent]=="") ? $element[ID] : $element[parent]; 
	echo"
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][parent]\" value=\"{$parent}\"/>
	<input type=\"hidden\" name=\"subfields[{$element[nonce]}][nonce]\" value=\"{$element[nonce]}\"/>
	<table class=\"widefat metadata\" cellpadding=\"10\"><tr>
	<td>".__("Type").": {$new}</td>
	<td>";
	
	if($new==1){
	unset($element[label]);	
	unset($element[description]);	
	}
	$this->Field->getfieldSelect($element, 1, $new);
	
	echo"</td>
	</tr>

<tr>
	<td style=\"width:25%\">".__("Status").":</td>
	<td><input type=\"checkbox\" {$statusc} name=\"subfields[{$element[nonce]}][status]\" value=\"1\" onclick=\"$('.rowstatus_{$element[nonce]}').addClass((this.checked)? 'genericon-show' : 'genericon-hide').removeClass((this.checked)? 'genericon-hide' : 'genericon-show')\" /></td>
	</tr>	
	<tr>
	<td style=\"width:25%\">".__("Label").": *</td>
	<td><input type=\"text\" name=\"subfields[{$element[nonce]}][label]\" id=\"label_{$element[nonce]}\" class=\"required label\" rel=\"{$element[nonce]}\" value=\"{$element[label]}\"/></td>
	</tr>
	
	<tr>
	<td>".__("Description").":</td>
	<td><textarea name=\"subfields[{$element[nonce]}][description]\" rows=\"3\" cols=\"60\">{$element[description]}</textarea></td>
	</tr>";
	
	$autoselected = ($element[width]=="") ? "selected" : "";
	echo"<tr>
	<td>".__("Width").":</td>
	<td>
	<select name=\"subfields[{$element[nonce]}][width]\">
	<option value=\"\" $autoselected>auto</option>
	";
	
	for($i=1;$i<101;$i++){
	$selected = ($i==$element[width])? "selected": "";
	echo"<option value=\"{$i}\" {$selected}>{$i}</option>";
	}
	
	
	echo"</select> %
	</td>
	</tr>


	
	<tr>
	<td>".__("Required", "_coll").":</td>
	<td>";
	
	$r_checked_yes	= ($element[required]==1)? "checked": "";
	$r_checked_no	= ($element[required]==0)? "checked": "";
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"subfields[{$element[nonce]}][required]\"  disabled {$r_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" checked disabled name=\"subfields[{$element[nonce]}][required]\" {$r_checked_no}> ".__("No")."</label></li>
                </ul>
	
	</td>
	</tr>";
	
		/*	
	echo"<tr>
	<td valign=\"top\">".__("Return size", "_coll").":</td>
	<td valign=\"top\">";
	//print_r($_wp_additional_image_sizes);
	
	echo"<select name=\"subfields[{$element[nonce]}][return_size]\">";
	
	$sizes = $this->list_thumbnail_sizes();
	foreach ($sizes as $size => $atts){
	$selected = ($size==$element[return_value])? "selected":"";	
	echo"<option value=\"{$size}\" {$selected}>{$size} ".implode( 'x', $atts )."</option>";
	}
	echo"</select>
	
	</td>
	</tr>		
	*/
	
	echo"<tr>
	<td colspan=\"2\" style=\"padding:10px\">
	<a href=\"#\" onclick=\"toggle_row(event, '{$element[nonce]}')\" class=\"button closefield\" id=\"savesubfield\">".__("Close Field")."</a>
	</td>
	</tr>
	
	
	</table>
	<script>
	 jQuery(document).ready(function(){
	 $('.rowtype_{$element[nonce]}').html('{$this->fieldname}');
	 });
	</script>";
}


public function list_thumbnail_sizes(){
     global $_wp_additional_image_sizes;
     	$sizes = array();
 		foreach( get_intermediate_image_sizes() as $s ){
 			$sizes[ $s ] = array( 0, 0 );
 			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
 				$sizes[ $s ][0] = get_option( $s . '_size_w' );
 				$sizes[ $s ][1] = get_option( $s . '_size_h' );
 			}else{
 				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
 					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
 				//unset($sizes[ $s ]);
 			}
 		}
 
 		//foreach( $sizes as $size => $atts ){
 		//	echo $size . ' ' . implode( 'x', $atts ) . "\n";
 		//}
 		
 		
 		return $sizes;
 }

}
?>