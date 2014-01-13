<?php
/**
  * Handles all functions for the Youtubefield, in combination with javascript it gets its data from a Youtube json call
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Youtube extends Basics{
	
	function __construct(){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname	= __("Youtube", "_coll");
	}
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
public function showfield($post=null, $element=null){
				
			if(sizeof($post) > 0){//only load scripts when the function is called from the edot screen
				wp_enqueue_script( 'jquery.youtube', plugins_url().'/meta-collections/js/youtube/jquery.youtube.min.js', '', '1.0');  
			}
			
			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;
			
			
			
			
			$fieldfinfo = $this->Field->getAttributesAndClasses($element);
		
			
			$html="
			<div style=\"position:relative;float:left;width:100%;margin:10px 0px 25px 4px;\">
			<label for=\"{$element[ID]}\">Url:</label> 
			<input type=\"text\" name=\"{$name}[url]\" size=\"73\" id=\"url_{$element[ID]}\" class=\"".implode(" ", $fieldfinfo[0])."\" ".implode(" ", $fieldfinfo[1])." onblur=\"(jQuery(this).val().length<1) ? jQuery('#get_yt_data').addClass('button-disabled') : jQuery('#get_yt_data').removeClass('button-disabled')\" size=\"30\" value=\"{$values[url]}\"/>";
			
			
			
			
			
			//if($element[api]==1){
			$button_title 		= ($values[url]=="") ? __("Get metadata for this url", "_coll"): __("Renew metadata for this url", "_coll");
			$button_disabled	= ($values[url]=="button-disabled") ? "" : "";
			$html.="&nbsp;<a onclick=\"if(this.className!='button-disabled'){get_yt_metadata('url_{$element[ID]}')};\"  class=\"button\"><span class=\"genericon_ genericon-youtube\" style=\"vertical-align:bottom;padding:0px 2px 0px 0px;\"></span> {$button_title}</a><br/>
			e.g. http://www.youtube.com/watch?v=UAvm9jIv9LQ";	
			//}
			//{$button_disabled}
			$html.="</div>
			<div style=\"position:relative;float:left;width:48%;margin-right:1%\">
			
			";
			
			
			
			$html.="<table class=\"widefat metadata metafield\" id=\"table_metadata_{$element[ID]}\" style=\"displays:none\" cellspacing=\"0\" cellpadding=\"10\">
			<tr>
			<td colspan=\"2\"><b>".__("Url & Metadata", "_coll").":</b></td>
			</tr>
			
			<tr>
			<td></td>
			<td><a onclick=\"jQuery('#title').val(jQuery('#yt_title').val());tinyMCE.activeEditor.setContent(jQuery('#yt_description').val());\" class=\"button\" id=\"getytmetadata\">Copy Youtube's title and decription to main title and description</a></td>
			</tr>

			<tr>
			<td>".__("ID", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_id]\" id=\"yt_id\" value=\"{$values[yt_id]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Title", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_title]\" id=\"yt_title\" value=\"{$values[yt_title]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Description", "_coll").":</td>
			<td><textarea name=\"{$name}[yt_description]\" id=\"yt_description\" style=\"min-width:200px;width:100%;height:80px;\"/>{$values[yt_description]}</textarea></td>
			</tr>

			<tr>
			<td>".__("Category", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_category]\" id=\"yt_category\" value=\"{$values[yt_category]}\" style=\"min-width:200px;width:100%;\"/>
			</td>
			</tr>

			<tr>
			<td>".__("Aspect Ratio", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_aspectRatio]\" id=\"yt_aspectRatio\" value=\"{$values[yt_aspectRatio]}\" style=\"min-width:200px;width:100%;\"/>
			</td>
			</tr>

			<tr>
			<td>".__("Uploader", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_uploader]\" id=\"yt_uploader\" value=\"{$values[yt_uploader]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>
			
			<tr>
			<td>".__("Upload date", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_uploaded]\" id=\"yt_uploaded\" value=\"{$values[yt_uploaded]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>
			
			<tr>
			<td>".__("Update date", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_updated]\" id=\"yt_updated\" value=\"{$values[yt_updated]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>
			
			<tr>
			<td style=\"width:15%\">".__("Thumbnail", "_coll").":</td>
			<td><img id=\"yt_thumbnail_sqDefault\" src=\"{$values[yt_thumbnail_sqDefault]}\" border=\"1\"/>
			<input type=\"hidden\" name =\"{$name}[yt_thumbnail_small]\" id=\"yt_thumbnail_small\" value=\"{$values[yt_thumbnail_small]}\"/>
			
		
			</td>
			</tr>

			<tr>
			<td style=\"width:15%\">".__("Duration", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[yt_duration]\" id=\"yt_duration\" value=\"{$values[yt_duration]}\" style=\"min-width:200px;width:100%;\"/>
			
			</td>
			</tr>
			
			</table>	
			</div>
			
			
			
			
			<div style=\"position:relative;float:left;width:48%;\">
			
			<table class=\"widefat metadata metafield\" id=\"table_embed_{$element[ID]}\" style=\"displays:none\" cellspacing=\"0\" cellpadding=\"10\">
			<tr>
			<td colspan=\"2\"><b>".__("Embed options", "_coll").":</b></td>
			</tr>
			
			<tr>
			<td>".__("Example", "_coll").":</td>
			<td id=\"yt_td\">";
			$display 			= ($values[yt_id]=="") ?"none":"block";
			$autohide_checked 	= ($values[yt_autohide]==1)?"checked":"";
			
			
			$showinfo_checked 	= ($values[yt_showinfo]==1)? "checked":"";
			$controls_checked 	= ($values[yt_controls]==1)? "checked":"";
			$autoplay_checked 	= ($values[yt_autoplay]==1)?"checked":"";
			$loop_checked 		= ($values[yt_loop]==1)?"checked":"";
			$themes				= array("light", "dark");
			
			
			$url = "http://www.youtube.com/embed/{$values[yt_id]}/?showinfo={$values[yt_showinfo]}&amp;autohide={$values[yt_autohide]}&amp;autoplay={$values[yt_autohide]}&amp;theme=".$values[yt_theme];
				
			$html.="<iframe width=\"420\" height=\"315\" style=\"display:{$display}\" id=\"yt_iframe\" src=\"{$url}\" frameborder=\"1\" allowfullscreen></iframe>
			Embedding url: <textarea style=\"width:100%\" name=\"{$name}[yt_embed]\" id=\"yt_embed\" rows=\"2\" readonly>{$values[yt_embed]}</textarea>
			</td>
			</tr>";
	
			
			
			$html.="<tr>
			<td>".__("Intro", "_coll").":</td>
			<td>";
			
			$html.="
			<input type=\"checkbox\" {$showinfo_checked} name=\"{$name}[yt_showinfo]\" onclick=\"format_yt_url()\" id=\"yt_showinfo\" value=\"1\"/> Show info&nbsp;
			<input type=\"checkbox\" {$controls_checked} name=\"{$name}[yt_controls]\" onclick=\"format_yt_url()\" id=\"yt_controls\" value=\"1\"/> Controls&nbsp;
			<input type=\"checkbox\" {$autohide_checked} name=\"{$name}[yt_autohide]\" onclick=\"format_yt_url()\" id=\"yt_autohide\" value=\"1\"/> Autohide controls after start.
			
			</td>
			</tr>
			
			<tr>
			<td>".__("Other", "_coll").":</td>
			<td>";
			
			$html.="
			<input type=\"checkbox\" {$autoplay_checked} name=\"{$name}[yt_autoplay]\" onclick=\"format_yt_url()\" id=\"yt_autoplay\" value=\"1\"/> Autoplay this video.<br/>
			<input type=\"checkbox\" {$loop_checked} name=\"{$name}[yt_loop]\" onclick=\"format_yt_url()\" id=\"yt_loop\"value=\"1\"/> Loop this video<br/><br/>
			</td>
			</tr>
			
			<td>".__("Theme", "_coll").":</td>
			<td><select id=\"yt_theme\" name=\"{$name}[yt_theme]\" onchange=\"format_yt_url()\">
			<option>".__("Choose theme","_coll")."</option>";
			
			foreach($themes as $theme){
			$sel = ($values[yt_theme]==$theme)? "selected":"";
			$html.="<option {$sel} value=\"{$theme}\">{$theme}</option>";	
			}
			$html.="</select>
			</td>
			</tr>
			
			</table>	
			
			</div>
			
			
			<div class=\"ui-helper-clearfix \">&nbsp;</div>
			";
			
					
			echo $this->Field->metafieldBox($html, $element);
			}




/**
    * Shows the specific form for the fieldtype with all the options related to that field. 
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
	</tr>";
	
	$this->Field->getBasics($element);
	$this->Field->getValidationOptions($element);
	
	

	$formID 	= "#edit_options_{$element[ID]}_{$element[cpt]}";
	
	$apicheckedy = ($element[api]==1 || $element[api]=="")? "checked":"";
	$apicheckedn = ($element[api]==0)? "checked" : "";
	
	echo"
	<tr>
	<td>".__("Default Value", "_coll").":<br/>
	<i class=\"hint\">".__("use al valid Youtube url here if you want to use a default value", "_coll")."</i>
	</td>
	<td><input type=\"text\" name=\"default_value\" value=\"{$element[default_value]}\"/>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Placeholder value", "_coll").":<br/> 
	<i class=\"hint\">".__("A greyed out value when the field is empty. Ideal to use for hints.","_coll")."</i></td>
	<td><input type=\"text\" name=\"placeholder\" value=\"{$element[placeholder]}\"/>
	
	</td>
	</tr>
	
	
	<tr>
	<td colspan=\"2\" style=\"padding:10px\">
	<a href=\"#\" onclick=\"
	if(jQuery('{$formID}').validate().form()){
	save_metafield('{$element[ID]}', '{$element[cpt]}', '".__("Field Options Saved")."...');
	}return false;
	\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a></td>
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