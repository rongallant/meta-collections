<?php
/**
  * Handles all functions for the Vimeo field, in combination with javascript it gets its data from the vimeo api
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @author URI: http://metacollections.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Vimeo extends Basics{
	
	function __construct(){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname	= __("Vimeo", "_coll");
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
			//print_r($values	);	
			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			$max_length = ($element[max_length]!="") ? " maxlength=\"{$element[max_length]}\"" :"";
			$length 	= ($element[max_length]!="") ? " size=\"".($element[max_length]+2)."\"" :"20";
		

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
			
		
			$html="
			<div style=\"position:relative;float:left;width:100%;margin:10px 0px 25px 4px;\">
			<label for=\"{$element[ID]}\">Url:</label> 
			<input type=\"text\" name=\"{$name}[url]\" id=\"url_{$element[ID]}\" 
			onblur=\"(jQuery(this).val().length<1)? jQuery('#getvimeometadata').addClass('button-disabled') : jQuery('#getvimeometadata').removeClass('button-disabled')\" size=\"30\" value=\"{$values[url]}\"/>";
			/*onkeydown=\"(jQuery(this).val().length<1)? jQuery('#getvimeometadata').removeClass('button-disabled') :  jQuery('#getvimeometadata').addClass('button-disabled');\"
			onchange=\"(jQuery(this).val().length<1)? jQuery('#getvimeometadata').removeClass('button-disabled') :  jQuery('#getvimeometadata').addClass('button-disabled');\"  */
			if($element[api]==1){
			$button_title 		= ($values[url]=="") ? __("Get metadata for this url", "_coll"): __("Renew metadata for this url", "_coll");
			$button_disabled	= ($values[url]=="button-disabled") ? "" : "";
			$html.="&nbsp;<a onclick=\"if(this.className!='button-disabled'){get_metadata('url_{$element[ID]}')};\" class=\"button \" id=\"getvimeometadata\">{$button_title}</a>";	
			}
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
			<td><a onclick=\"jQuery('#title').val(jQuery('#vimeo_title').val());tinyMCE.activeEditor.setContent(jQuery('#vimeo_description').val());\" class=\"button\" id=\"getvimeometadata\">Copy Vimeo's title and decription to main title and description</a></td>
			</tr>

			<tr>
			<td>".__("ID", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_id]\" id=\"vimeo_id\" value=\"{$values[vimeo_id]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Title", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_title]\" id=\"vimeo_title\" value=\"{$values[vimeo_title]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Description", "_coll").":</td>
			<td><textarea name=\"{$name}[vimeo_description]\" id=\"vimeo_description\" style=\"min-width:200px;width:100%;height:80px;\"/>{$values[vimeo_description]}</textarea></td>
			</tr>

			<tr>
			<td>".__("Upload date", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_upload_date]\" id=\"vimeo_upload_date\" value=\"{$values[vimeo_upload_date]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>
			<tr>
			<td style=\"width:15%\">".__("Thumbnail", "_coll").":</td>
			<td><img id=\"img_thumbnail_medium\" src=\"{$values[vimeo_thumbnail_medium]}\" border=\"1\"/>
			<input type=\"hidden\" name =\"{$name}[vimeo_thumbnail_small]\" id=\"vimeo_thumbnail_small\" value=\"{$values[vimeo_thumbnail_small]}\"/>
			<input type=\"hidden\" name =\"{$name}[vimeo_thumbnail_medium]\" id=\"vimeo_thumbnail_medium\" value=\"{$values[vimeo_thumbnail_medium]}\"/>
			<input type=\"hidden\" name =\"{$name}[vimeo_thumbnail_large]\" id=\"vimeo_thumbnail_large\" value=\"{$values[vimeo_thumbnail_large]}\"/>
			
		
			</td>
			</tr>

			<tr>
			<td style=\"width:15%\">".__("User Portrait", "_coll").":</td>
			<td><img id=\"img_user_portrait_medium\" src=\"{$values[vimeo_user_portrait_medium]}\" border=\"1\"/>
			<input type=\"hidden\" name=\"{$name}[vimeo_user_portrait_small]\" id=\"vimeo_user_portrait_small\" value=\"{$values[vimeo_user_portrait_small]}\"/>
			<input type=\"hidden\" name=\"{$name}[vimeo_user_portrait_medium]\" id=\"vimeo_user_portrait_medium\" value=\"{$values[vimeo_user_portrait_medium]}\"/>
			<input type=\"hidden\" name=\"{$name}[vimeo_user_portrait_large]\" id=\"vimeo_user_portrait_large\" value=\"{$values[vimeo_user_portrait_large]}\"/>
			<input type=\"hidden\" name=\"{$name}[vimeo_user_portrait_huge]\" id=\"vimeo_user_portrait_huge\" value=\"{$values[vimeo_user_portrait_huge]}\"/>
			<input type=\"hidden\" name=\"{$name}[vimeo_user_id]\" id=\"vimeo_user_id\" value=\"{$values[vimeo_user_id]}\"/>
			
			</td>
			</tr>


			<tr>
			<td>".__("User Name", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_user_name]\" id=\"vimeo_user_name\" value=\"{$values[vimeo_user_name]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("User url", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_user_url]\" id=\"vimeo_user_url\" value=\"{$values[vimeo_user_url]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Duration", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_duration]\" id=\"vimeo_duration\" value=\"{$values[vimeo_duration]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Width", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_width]\" id=\"vimeo_width\" value=\"{$values[vimeo_width]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Height", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_height]\" id=\"vimeo_height\" value=\"{$values[vimeo_height]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Tags", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_tags]\" id=\"vimeo_tags\" value=\"{$values[vimeo_tags]}\" style=\"min-width:200px;width:100%;\"/></td>
			</tr>

			<tr>
			<td>".__("Embed privacy", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[vimeo_embed_privacy]\" id=\"vimeo_embed_privacy\" value=\"{$values[vimeo_embed_privacy]}\" style=\"min-width:200px;width:100%;\"/></td>
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
			<td id=\"vimeo_td\">";

			$portrait 			= ($values[vimeo_intro_portrait]==1)? 1:0;
			$title 				= ($values[vimeo_intro_title]==1)? 1:0;
			$byline 			= ($values[vimeo_intro_byline]==1)? 1:0;
			$autoplay		 	= ($values[vimeo_autoplay]==1)? 1:0;
			$loop		 		= ($values[vimeo_loop]==1)? 1:0;
			

			$html.="<iframe style=\"displady:none;\" id=\"vimeo_iframe\" src=\"http://player.vimeo.com/video/{$values[vimeo_id]}/?title={$title}&amp;byline={$byline}&amp;portrait={$portrait}&amp;autoplay={$autoplay}&amp;loop={$loop}&amp;color={$values[vimeo_color]}\" width=\"300\" height=\"169\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>

						</td>
			</tr>";
			/*
			<iframe id=\"iframe_{$element[ID]}\" src=\"http://player.vimeo.com/video/32585445?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ff0000\" width=\"300\" height=\"169\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
			*/
			$backgroundcolor = ($values[vimeo_color]!="")? "#".$values[vimeo_color] : "#00adef";
			
			$html.="<tr>
			<td>".__("Color", "_coll").":</td>
			<td>
			#<input type=\"text\" id=\"vimeo_color\" name=\"{$name}[vimeo_color]\" maxlength=\"7\" onkeydown=\"jQuery('#div_color_{$element[ID]}').css({background:'#'+this.value})\" onblur=\"format_vimeo_url()\" value=\"{$values[vimeo_color]}\"/> <div id=\"div_color_{$element[ID]}\" style=\"border-radius:3px;padding:4px 7px;display:inline;background:{$backgroundcolor};\">&nbsp;&nbsp;</div> (".__("e.g. 00adef=vimeo blue", "_coll").")
			
			</td>
			</tr>
			
			<tr>
			<td>".__("Intro", "_coll").":</td>
			<td>";
			$portrait_checked 	= ($values[vimeo_intro_portrait]==1)? "checked":"";
			$title_checked 		= ($values[vimeo_intro_title]==1)? "checked":"";
			$byline_checked 	= ($values[vimeo_intro_byline]==1)? "checked":"";
			
			$html.="
			<input type=\"checkbox\" {$portrait_checked} name=\"{$name}[vimeo_intro_portrait]\" onclick=\"format_vimeo_url()\" id=\"vimeo_intro_portrait\" value=\"1\"/> Portrait&nbsp;
			<input type=\"checkbox\" {$title_checked} name=\"{$name}[vimeo_intro_title]\" onclick=\"format_vimeo_url()\" id=\"vimeo_intro_title\" value=\"1\"/> Title&nbsp;
			<input type=\"checkbox\" {$byline_checked} name=\"{$name}[vimeo_intro_byline]\" onclick=\"format_vimeo_url()\" id=\"vimeo_intro_byline\" value=\"1\"/> Byline
			
			</td>
			</tr>
			
			<tr>
			<td>".__("Other", "_coll").":</td>
			<td>";
			
			$autoplay_checked 	= ($values[vimeo_autoplay]==1)?"checked":"";
			$loop_checked 		= ($values[vimeo_loop]==1)?"checked":"";
			
			$html.="
			<input type=\"checkbox\" {$autoplay_checked} name=\"{$name}[vimeo_autoplay]\" onclick=\"format_vimeo_url()\" id=\"vimeo_autoplay\" value=\"1\"/> Autoplay this video.<br/>
			<input type=\"checkbox\" {$loop_checked} name=\"{$name}[vimeo_loop]\" onclick=\"format_vimeo_url()\" id=\"vimeo_loop\"value=\"1\"/> Loop this video
			</td>
			</tr>
			</table>	
			
			</div>
			
			
			<div class=\"ui-helper-clearfix \">&nbsp;</div>
			";
			
					
			$this->Field->metafieldBox($html, $element);
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
	</tr>
	
	<tr>
	<td style=\"width:25%\">".__("Status").":</td>
	<td><input type=\"checkbox\" {$statusc} name=\"status\" value=\"1\"/></td>
	</tr>
		
	<tr>
	<td style=\"width:25%\">".__("Label").":</td>
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
	</tr>";
	
	$apicheckedy =($element[api]==1)? "checked": "";
	$apicheckedn =($element[api]==0)? "checked": "";


	$apicheckedy =($element[api]=="") ?  "checked": $apicheckedy ;
	$apicheckedn =($element[api]=="") ?  "" : "checked";
	echo"<tr>
	<td>".__("Use Vimeo's API to acquire the footage's metadata and store it in seperate fields", "_coll").":</td>
	<td>
	<input type=\"radio\" name=\"api\" {$apicheckedy} value=\"1\"/> ".__("Yes")."<br/> 
	<input type=\"radio\" name=\"api\" {$apicheckedn} value=\"0\"/> ".__("No")."
	
	</td>
	</tr>
	
<tr>
	<td valign=\"top\">".__("Show this field in Collection overview<br/>(this field has to be dragged in user interface before showing up)", "_coll").":</td>
	<td valign=\"top\">";
	
	$s_checked_yes	= ($element[overview]==1)? "checked": "";
	$s_checked_no	= ($element[overview]==0)? "checked": "";

	$formID = "#edit_options_{$element[ID]}_{$element[cpt]}";
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