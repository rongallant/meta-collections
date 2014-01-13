<?php
 /**
  * Handles all functions regarding the conbination field type, a repeating field with more subfields
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
class Combination extends Basics{
	
	function __construct($meta=null){
	
	parent::init();
	$this->Field 		= new Field();
	$this->fieldname 	= __("Combination field", "_coll");
	
	if($meta[action]!=null){
	//	print_r($meta[action]);
		echo $this->$meta[action]($meta);	
	//	die();
	}
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
			//$values	 	= ($values=="" && $element[default_value]!="") ? $element[default_value] : $values;
			//$values	 	= (!is_array($values)) ? array($values) : $values;


			if(!is_array($values)){
				$values[0] = array();
				
				 foreach($element[subfields] as $nonce=>$elementinfo){
				 $values[0][$nonce] = "";
				 }
			}
			
			
			//wysiwyg clonen verder afmaken.
			$instance=1;
			$this->Field = new Field();
			$this->Field->getFields();
			$this->Field->getClasses();
			$this->Field->getClassesWithSubfields();
			
			$html = "";
			$instance=1;
			foreach ($values as $row=>$elements){

			$html.="<div id=\"div_{$element[ID]}_$instance\" rel=\"{$instance}\" data=\"{$name}\">";
			
			foreach($element[subfields] as $nonce => $elementinfo){//determine earlier if a field is active status
			//$width 									= ($element[subfields][$nonce][width]!="") ? "width:{$element[subfields][$nonce][width]}%;" : "";
			$elementinfo[instance] 	= $instance;	
				
			$html .="<div class=\"subfield\" style=\"{$elementinfo[width]}\">";
			$c 				= ucfirst($elementinfo[type]);			
			$typeclass 		= new $c();							
			$html.= $typeclass->showsubfield($post, $elementinfo, $elements[$elementinfo[nonce]]);	
			$html.="</div>";			
			}
			
			$html.="<a class=\"delete_metavalue genericon_ genericon-trash\" title=\"".__("delete this", "_coll")." style=\"opacity:{$visibility}\" {$element[label]}\" href=\"#\" onclick=\"remove_value_instance(event, $(this).parent())\">&nbsp;</a>

			
			
			</div>";
			$instance++;
			}
			
			echo $this->Field->metafieldBox($html, $element);
			
			}
	

public function add_subfield($element){
	
	$this->Field->getFields();
	$this->Field->getClasses();
	$this->Field->getClassesWithSubfields();
	
	$element[type]	= 'text';//= add standard a text field
	$this->Field 	= new Field();
	
	$element[nonce] = wp_create_nonce($element[cpt].'_'.rand(0,100000000000));	
	
	$c 				= ucfirst($element[type]);
	$typeclass 		= new $c();
	$row 			= $element[row]+1;	

	//print_r($element);
	echo"<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
	<tr class=\"on\" id=\"tr_{$element[nonce]}\">
	<td class=\"row rownumber\" style=\"width:5%;\"><span class=\"sorthandle\" title=\"Drag to sort\">".$row."</span></td>
	
	<td class=\"row rowlabel\" style=\"width:65%;\"><span class=\"spanlabel spanlabel_{$element[nonce]}\"><i>".__("No label yet","_coll")."</i></span> {$this->nonce}
	
	<div class=\"srow-actions\">
		<span class=\"edit\"><a href=\"#\" id=\"close_{$element[nonce]}\" onclick=\"toggle_row(event, '{$element[nonce]}');\">".__("Close","_coll")."</a></span>&nbsp;
		<span class=\"delete\"><a href=\"#\" onclick=\"delete_row(event, this);\">".__("Delete","_coll")."</a></span>
	</div>
	</td>
	
	<td class=\"row rowstatus_{$element[nonce]}\" style=\"width:15%;\">enabled</td>
	<td class=\"row rowtype_{$element[nonce]}\" style=\"width:15%;\">{$element[type]}</td>
	</tr>
	
	<tr>
	<td colspan=\"4\" id=\"td_{$element[nonce]}\">";
	echo $typeclass->subfieldOptions($element, 1);//extra variable new and than true false values
	echo"</td></tr></table>
	<script>
	$('.subfield_{$element[ID]}').sortable('refresh');
	</script>";
	
	
	
}



/**
    * Shows the specific form for the fieldtype with all the options related to that field. 
    * @access public
    */	
public function fieldOptions($element){
		$element[action]	= "add_subfield";
		$element[cpt] 		= $_POST[cpt];
	
		
		$e 					= json_encode($element);
		$statusc 			= ($element[status]==1)? "checked":"";
		
		
		
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

	
	echo"<tr>
	<td>".__("Subfields").":</td>
	<td>
	
	<table class=\"widefat metadata\" cellspacing=\"0\" id=\"subfieldoverview\">
            <thead class=\"content-types-list\">
              <tr class=\"ons\">              
              <th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\" style=\"width:5%\">".__('Order','_coll')."</th>
              <th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\" style=\"width:65%\">".__('Label','_coll')."</th>
              <th class=\"manage-column column-fields\" id=\"headstatus\" scope=\"col\" style=\"width:15%\">".__('Status','_coll')."</th>
              <th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\" style=\"width:15%\">".__('Type','_coll')."</th>
                
              </tr>
            </thead><tbody>
            
            
            <tr><td colspan=\"4\" id=\"subfield_{$element[ID]}\" class=\"subfields subfield_{$element[ID]}\">
            ";
  
    if(is_array($element[subfields])){
    
    $this->Field = new Field();
	$this->Field->getFields();
	$this->Field->getClasses();
	$this->Field->getClassesWithSubfields();
    $row=1;
    foreach($element[subfields] as $nonce=>$elementinfo){
    $cstatus =($elementinfo[status]==1) ? "genericon-show":"genericon-hide";
    
    //print_r($elementinfo[status]);
    echo"<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" ><tr id=\"tr_{$nonce}\" class='on'>
	<td class=\"row rownumber\" style=\"width:5%\"><span class=\"sorthandle\" title=\"Drag to sort\">".$row."</span></td>
	
	<td class=\"row rowlabel\" style=\"width:65%\"><span class=\"spanlabel spanlabel_{$nonce}\">{$elementinfo[label]}</span>
	
	<div class=\"srow-actions\">
		<span class=\"edit\"><a href=\"#\" id=\"close_{$nonce}\" onclick=\"toggle_row(event, '{$nonce}');\">".__("Edit")."</a></span>&nbsp;
		<span class=\"delete\"><a href=\"#\" onclick=\"delete_row(event, this);\">Verwijderen</a></span>
	</div>
	</td>
	
	<td class=\"row rowstatus_{$nonce} genericon_ {$cstatus} \"  style=\"width:15%\">&nbsp;</td>
	<td class=\"row rowtype_{$nonce}\" style=\"width:15%\">{$elementinfo[type]}</td>
	</tr>
	
	<tr>
	<td colspan=\"4\" id=\"td_{$nonce}\" style=\"display:none\">";
	$c 				= ucfirst($elementinfo[type]);
	$typeclass 		= new $c();
	
	echo $typeclass->subfieldOptions($elementinfo);//if status is 1
	echo"</td></tr></table>"; 
     $row++;;
    }
    
    
    }else{
	    echo"<tr id=\"no-collections\">
             <td class=\"name column-name\" colspan=\"4\" id=\"subfield\"><div style=\"text-align:center;padding:30px;font-style:italic\">".__("No subfield Set defined yet, click on Add Subfield to create one","_coll")."</div>
             </td>
             </tr>";  
	    
    }      
          
            
     echo"</tbody></table>
     <br/>
     <a onclick='add_subfield(event, $e);' class='button-primary' href='#'>".__("Add Subfield", "_coll")."</a>
	
	
	</td>
	</tr>

	
	<tr>
	<td valign=\"top\">".__("Allow multiple values / instances of this element", "_coll").":</td>
	<td valign=\"top\">";
	
	$m_checked_yes	= ($element[multiple]==1 || $element[multiple]=="")? "checked": "";
	$m_checked_no	= ($element[multiple]==0)? "checked": "";
	$formID = "#edit_options_{$element[ID]}_{$element[cpt]}";
	
	
	echo"<ul class=\"radio_list radio vertical\">
                <li><label><input type=\"radio\" value=\"1\" name=\"multiple\" {$m_checked_yes}> ".__("Yes")."</label></li>
                <li><label><input type=\"radio\" value=\"0\" name=\"multiple\" {$m_checked_no}> ".__("No")."</label></li>
                </ul>
	
	</td>
	</tr>";
	
	$addtitle = ($element[addtitle]=="") ? __("Add another") : $element[addtitle];
	echo"<tr>
	<td valign=\"top\">".__("Button title to add another instance", "_coll").":</td>
	<td valign=\"top\"><input type=\"text\" name=\"addtitle\" value=\"{$addtitle}\"/></td>
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
	
	if(cbscript===undefined){
	cbscript =$.getScript(\"".plugins_url()."/meta-collections/js/combination/jquery.combination.js\");
	}	
	
	
	
	$('.subfield_{$element[ID]}').sortable({ 
							forceHelperSize: false,
							cursor: 'ns-resize',
							helper: 'clone',
							handle: '.sorthandle',
							update: function( event, ui ) {
							

							$('.subfield_{$element[ID]}').children().each( function(index, element) {							
							$(element).find('.rownumber span').animate({'border-color':'#e17341', 'color':'#e17341', 'background-color':'#fff'}, 100);
							setTimeout(function(){
							$(element).find('.rownumber span').html(index+1);
							$(element).find('.rownumber span').animate({'border-color':'#fff', 'color':'#fff', 'background-color':'transparent'}, 1200);
							}, 1200)								
							
							
							});
							
							}
							});
   });
   
   </script>";
/**/
//Cancel <a href=\"#\" onclick=\"\" class=\"button\">".__("Cancel")."</a>
/*
	jQuery(document).ready(function(){
	
	jQuery('{$formID}').validate();
	
	if(olscript===undefined){
	olscript =$.getScript(\"http://openlayers.org/api/OpenLayers.js\", function( data, textStatus, jqxhr ) {
	
	olfscript =$.getScript(\"".plugins_url()."/meta-collections/js/openlayers/jquery.openlayersfield.js\", function( data, textStatus, jqxhr ) {
	
*/
}

}
?>