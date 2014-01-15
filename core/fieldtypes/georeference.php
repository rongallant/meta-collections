<?php
/**
  * Handles all functions regarding the field type georeference 
  * Able to store address, longitude en langitude
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @author URI: http://collections.statuur.nl/
  * @see https://developers.google.com/maps/documentation/javascript/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package  Collections Wordpress plugin
  */

class Georeference extends Basics{
	
	function __construct(){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Georeference","_coll");
	}
	
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. 
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
	
function showfield($post=null, $element=null, $c=null){
			
			//if(sizeof($post)>0){//only load scripts when the function is called from the edot screen
			wp_enqueue_script( 'googleapis', 'http://maps.google.com/maps/api/js?sensor=false', '', '3.0'); //user only for the georeference field
			wp_enqueue_script( 'jquery.googlemaps', plugins_url().'/meta-collections/js/georeference/jquery.googlemaps.min.js', '', '1.0'); //user only for the georeference field
			//}

			$element 			= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;



			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			
			$map 		= "map_{$element[ID]}";
			$addressval	= ($values[address]=="") ? $element[default_value] : $values[address];
//print_r($element );
			
			$latval		= ($values[latitude]=="")? $element[latitude]: $values[latitude];

			$longval	= ($values[longitude]=="")? $element[longitude]: $values[longitude];
			
					

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
			
		
			$mapheight	= ($element[height]!="")?  $element[height] : 250;
			
			$html = "";

			if($element[description]!=""){
			$html.="<span style=\"font-size:10px;font-style:italic\">{$element[description]}</span>";	
			}
//<label for=\"{$element[ID]}\">{$element[label]}:</label><br/>
			
		//foreach ($values as $value){
			$html.="<div class=\"metafield-value\">
			<table class=\"widefat metadata metafield\" id=\"table_{$element[ID]}\" cellspacing=\"0\" cellpadding=\"10\">	
			<tr>
			<td style=\"width:15%\">".__("Location", "_coll").":</td>
			<td><input type=\"text\" name=\"{$name}[address]\" id=\"address\" $required value=\"{$addressval}\" style=\"min-width:200px;width:100%;\"/></td>
			<td style=\"width:260px;\">
			<a class=\"button\" onclick=\"googleMaps.geocode(
			jQuery('#address'),
			jQuery('#latitude'),
			jQuery('#longitude'),
			'map_{$element[ID]}'
			)\">".__("Geocode","_coll")." &rarr;</a>  
			
			
			<a class=\"button\" onclick=\"googleMaps.reversegeocode(
			jQuery('#address'),
			jQuery('#latitude').val(),
			jQuery('#longitude').val()
			
			)\">".__("Reverse Geocode","_coll")." &larr;</a></td>
			<td><input type=\"text\" name=\"{$name}[latitude]\" id=\"latitude\" value=\"{$latval}\"/> &deg; <input type=\"text\" name=\"{$name}[longitude]\" id=\"longitude\" value=\"{$longval}\"/> &deg;</td>
			</tr>
			</table> 
					
			
			
			<div id=\"{$map}\" style=\"height:{$mapheight}px;background:#f3\">loading...</div>";
				if($element[multiple]==1){
					$html.="<a class=\"delete_metavalue\" title=\"".__("delete this", "_coll")." {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(this);return false;\">&nbsp;</a>";
				}		
			$html.="</div>";
	//			}
		//echo $html;

			$html.="<script>";
	
			$map 		= "map_{$element[ID]}";
			$zoom 		= ($element[zoom]=="")? 10 : $element[zoom];
	
			 $html.="
			 var {$map};
			
			 jQuery(document).ready(function () {
			 		
			 		placeMarks = [{title: '{$element[ID]}', latitude: '{$latval}', longitude: '{$longval}'}];
			 		{$map} = googleMaps;
			        mapOptions = {zoom: {$zoom},center: new google.maps.LatLng('{$latval}', '{$longval}'), mapTypeId: google.maps.MapTypeId.ROADMAP};
			        {$map}.showmap({mapOptions:mapOptions, name:'{$map}', elementID: '{$element[ID]}' ,placeMarks: placeMarks}); 
			         
			        
		           });
		</script>";
	
		echo $this->Field->metafieldBox($html, $element);

}

/**
    * Shows the specific form for the fieldtype iwith all the options related to that field. 
    * @access public
    */	
function fieldOptions($element){


	//wp_enqueue_script( 'jquery.googlemaps', plugins_url().'/meta-collections/js/georeference/jquery.googlemaps.min.js', '', '1.0'); //user only for the georeference field
					
	echo"
	<script type='text/javascript' src='http://localhost/collections/wp-content/plugins/meta-collections/js/georeference/jquery.googlemaps.min.js'></script>
	<table class=\"widefat metadata\" id=\"another\" cellspacing=\"0\" cellpadding=\"10\">";
	
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

	echo"<tr>
	<td>".__("Field height", "_coll").":</td>
	<td><input type=\"text\" name=\"height\" value=\"{$element[height]}\"/> px
	
	</td>
	</tr>

	
	<tr>
	<td valign=\"top\"><br/>".__("Default Location", "_coll").":</td>
	<td valign=\"top\"><br/>
	<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"widefat metadata\">
	<tr>
	<td width=\"130\">".__("Address").":</td>
	<td><input type=\"text\" onblur=\"
	googleMaps.geocode(
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #default_value'),
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #latitude'),
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #longitude')
	)\" size=\"80\" name=\"default_value\" id=\"default_value\" value=\"{$element[default_value]}\"/></td>
	</tr>
		
	<tr>
	<td>".__("Geocode").":</td>
	<td>
	
	<a class=\"button\" onclick=\"googleMaps.geocode(
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #default_value'),
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #latitude'),
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #longitude')
	
	)\">".__("Geocode","_coll")." &darr;</a> 
	<a class=\"button\" onclick=\"googleMaps.reversegeocode(
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #default_value'),
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #latitude').val(),
	jQuery('#edit_options_{$element[ID]}_{$element[cpt]} #longitude').val()
	
	)\">".__("Reverse Geocode","_coll")." &uarr;</a>
	<div id=\"default_value_errors\"></td>
	</tr>
	
	<tr>
	<td>".__("Location").":</td>
	<td>".__("Latitude").":<br/><input type=\"text\" name=\"latitude\" id=\"latitude\" value=\"{$element[latitude]}\" size=\"23\"> &deg;<br/><br/>".__("Longitude").":<br/><input type=\"text\" name=\"longitude\" value=\"{$element[longitude]}\" id=\"longitude\"size=\"23\"> &deg;</td>
	</tr>
	</table>
	
	 <br/>
	
	
	<br/>
	
	
	
	</div>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Default Zoom level", "_coll").":</td>
	<td><select name=\"zoom\">";
	$formID 		= "#edit_options_{$element[ID]}_{$element[cpt]}";

	for ($i=0; $i<19; $i++){
	$selected = ($i==$element[zoom])? "selected":"";	
	$selected = ($element[zoom]=="" && $i==7)? "selected" : $selected ;	
	echo"<option value=\"{$i}\" {$selected}>$i</option>";
	}
	echo"</select>
	
	</td>
	</tr>
	
	
	<tr>
	<td colspan=\"2\">
	<a href=\"#\" onclick=\"
	if(jQuery('{$formID}').validate().form()){
	save_metafield('{$element[ID]}', '{$element[cpt]}', '".__("Field Options Saved")."...');
	}return false;
	\" class=\"button-primary\" id=\"savemetafield\">".__("Save")."</a>
	</td>
	</tr>
	
	
	</table>";

echo"<script>
jQuery(document).ready(function(){
	jQuery('{$formID}').validate();
	
	
	//gewoon script tags!
	/*
	if(gmscript===undefined){
	
	gmscript =$.getScript(\"http://maps.google.com/maps/api/js?sensor=false\", function( data, textStatus, jqxhr ) {
	$.getScript(\"".plugins_url()."/meta-collections/js/georeference/jquery.googlemaps.js\", function( data, textStatus, jqxhr ) {
	googleMaps = googleMaps.init();
	console.log(googleMaps);
	});
	});
	
	}
	*/
});
   
   </script>";

}
/*
wp_enqueue_script( 'googleapis', 'http://maps.google.com/maps/api/js?sensor=false', '', '3.0'); //user only for the georeference field
			wp_enqueue_script( 'jquery.googlemaps', plugins_url().'/meta-collections/js/jquery.googlemaps.js', '', '1.0'); //user only for the georeference field
				
*/
}

?>
