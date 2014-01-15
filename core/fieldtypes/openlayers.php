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

class OpenLayers extends Basics{
	
	function __construct(){
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("OpenLayers","_coll");
	}
	
	
/**
    * Shows the specific fieldtype in UI::edituserinterface, post(-new).php or edit.php. This openlayers field stores objects in an input type is hidden. These objects contain latitude, longitude, title, date,, time and amount.      
    * @access public
    * @param object $post the post info
    * @param array $element info about the metadata field
    */	
	
function showfield($post=null, $element=null, $c=null){

			if(sizeof($post)>0){//only load scripts when the function is called from the edot screen
			wp_enqueue_script( 'openlayers', 'http://openlayers.org/api/OpenLayers.js', '', '1.0'); 
			wp_enqueue_script( 'jquery.json', plugins_url().'/meta-collections/js/openlayers/jquery.json.js', '', '1.0'); 						
			wp_enqueue_script( 'jquery.openlayers', plugins_url().'/meta-collections/js/openlayers/jquery.openlayers.min.js', '', '1.0'); 
			wp_enqueue_style( 'openlayers',  get_option('siteurl').'/wp-content/plugins/meta-collections/css/openlayers/jquery.openlayers.css', ''); 
			}


			$element 	= ($element[id]!="") ? $element[args]: $element;
			$name	 	= $this->postmetaprefix.$element[ID]."--------ol";
			$id	 		= $this->postmetaprefix.$element[ID];

			$values	 	= get_post_meta($post->ID, $name, true); 
			
			$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			$values	 	= (!is_array($values)) ? array($values) : $values;



			$required 	= ($element[required]==1) ? "class=\"required\" " : "";
			
			$map 		= "map_{$element[ID]}";
			$addressval	= ($values[address]=="") ? $element[default_value] : $values[address];

			
			$latval		= ($values[latitude]=="")? $element[latitude]: $values[latitude];

			$longval	= ($values[longitude]=="")? $element[longitude]: $values[longitude];
			
					

			if($element[required]==1){
			$_SESSION[required][$element[ID]] = $element[required_err]	;
			}
			
		
		
			$html = "";
			
			
			$mapheight	= ($element[height]!="")?  $element[height] : 250;
			//$ivalues = http_build_query($values);
			
			//$patterns = '/%5B/';
			//$replacements = '=';
			
			
			$values = json_encode($values);
			
			$html.="<div class=\"metafield-value\">
			<div id=\"{$map}\" style=\"height:{$mapheight}px;background:#f3\">loading...</div><br/>
			<input type=\"hidden\" size=\"120\" {$required} {$max_length} {$length} name=\"{$name}[]\" id=\"{$id}\" value=\"\"/> ";
				$html.="</div>";

			$this->Field->metafieldBox($html, $element);
			$element['input'] = $id;
			$element[features] = $values;
			
			//, "date", "time", "amount"
			$element[lang] = array(
			"title" 		=> __("title","_coll"),
			"date" 			=> __("date","_coll"),
			"time" 			=> __("time","_coll"),
			"amount"		=> __("amount","_coll"),
			"Navigation"	=> __("Pan / Zoom","_coll"),
			"ModifyFeature"	=> __("Edit points"),
			"DrawFeature"	=> __("Add points","_coll"),
			"DeleteFeature"	=> __("Delete points","_coll"),
			"SelectFeature"	=> __("Edit point's metadata","_coll"),
			"Baselayer"		=> __("Base layer","_coll"),
			"properties"	=> __("Properties","_coll"),
			"properties_mod"=> __("Properties modified...<br/>Save the page in order to save modifies marker properties.","_coll")
			
			
			
			);
			
			$options = json_encode($element);
			
			$html.="<script>
			
			 jQuery(document).ready(function () {
			 	$('#{$map}').OpenLayer({$options}); 	
			        
		           });
		</script>";
		
		echo $this->Field->metafieldBox($html, $element);
			
}

/**
    * Shows the specific form for the fieldtype with all the options related to that field. 
    * @access public
    */	
function fieldOptions($element){
			
	echo"
	<link rel='stylesheet' id='openlayers-css'  href='".plugins_url()."/meta-collections/css/openlayers/jquery.openlayers.css?ver=3.8' type='text/css' media='all' />
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
	<td valign=\"top\"><br/>".__("Default Location", "_coll").":<br/>
	<i class=\"hint\">".__("Sometimes the map doesn't show at startup, click the + control to fix the problem.", "_coll")."</i>
	
	</td>
	<td valign=\"top\"><br/>
	<div id=\"fieldmap_{$element[ID]}\" style=\"width:100%;height:300px;\"></div>	<br/>
Latitude: <input id=\"latitude\" class=\"ol_latitude\" type=\"text\" size=\"23\" value=\"\" readonly name=\"latitude\">° Longitude: <input id=\"longitude\" class=\"ol_longitude\" type=\"text\" size=\"23\" value=\"\" readonly name=\"longitude\"> °


	 <br/>
	
	
	<br/>
	
	
	
	</div>
	
	</td>
	</tr>
	
	<tr>
	<td>".__("Default Zoom level", "_coll")." :</td>
	<td><select name=\"zoom\" class=\"fieldzoom\" id=\"fieldzoom\" onchange=\"$('#fieldmap').data('olmap').setZoom(this.value)\">";
	//<input type=\"text\" name=\"max_length\" value=\"{$element[max_length]}\"/>
	for ($i=0; $i<19; $i++){
	$selected = ($i==$element[zoom])? "selected":"";	
	$selected = ($element[zoom]=="" && $i==7)? "selected" : $selected ;	
	
	echo"<option value=\"{$i}\" {$selected}>$i</option>";
	}
	echo"</select>
	
	</td>
	</tr>";
	
	$layers = array(
	"OpenStreetMap"		=> "osm", 
	"Google Physical"	=> "google_terrain",
	"Google Streets" 	=> "google_streets",
	"Google Hybrid"		=> "google_hybrid",
	"Google Satellite"	=> "google_satellite"
	);
	$formID 		= "#edit_options_{$element[ID]}_{$element[cpt]}";
	
	echo"<tr>
	<td>".__("Choose layers", "_coll")." :<br/>
	<i class=\"hint\">".__("Choose the layers with tile distributors.", "_coll")."</i>
	</td>
	<td>";

	foreach($layers as $name=>$code){//$code
	$checked = ($element[layers][$name]==1) ? "checked":"";
	echo"<input type=\"checkbox\" name=\"layers[$name]\" {$checked} value=\"1\"/> {$name}<br/>
	";	
	}
	
	
	echo"</td>
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
	
	if(olscript===undefined){
	olscript =$.getScript(\"http://openlayers.org/api/OpenLayers.js\", function( data, textStatus, jqxhr ) {
	
	olfscript =$.getScript(\"".plugins_url()."/meta-collections/js/openlayers/jquery.openlayersfield.js\", function( data, textStatus, jqxhr ) {
	
	$('#fieldmap_{$element[ID]}').OpenLayerField(".json_encode($element).");
		
	});

	
	});
	}else{
	$('#fieldmap_{$element[ID]}').OpenLayerField(".json_encode($element).");

	}	
});
   
   </script>";

}
}

?>
