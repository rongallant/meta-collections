<?php 
if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ){
	die("Access denied.");
}


 /**
  * Handles all functions regarding creation, edit and deletion of metadata in Media
  *
  *
  * @author  Bastiaan Blaauw <statuur@gmail.com>
  *
  * @version  1.0 $Revision: 60
  * @author URI: http://www.statuur.nl/
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @access Public
  * @package Collections Wordpress plugin
  */
class mediaMetadata extends Basics{ 
/**
  * Basic Constructor
  *
  * Loads vars from parent Basics and gets field information from Class Field<br/>
  * Tries to load the appropriate action or loads the default overvew
  * @access public    
  */
public function __construct(){
	$this->init();
	//print_r($this);
	$this->Field = new Field();
	$this->Field->getFields();
	$this->Field->getClasses();	
	$this->action = ($this->action=="") ? "mediaMetaOverview": $this->action;
	
	if($this->action!=""){
	call_user_func(array($this, $this->action)); // As of PHP 5.3.0
	}
	
}

public function mediaMetaOverview(){

$buttons 		= "<a rel=\"action:collectionoverview\" class=\"button ajaxify\" href=\"admin-ajax.php\">&lsaquo; ".__("Back")."</a>
<a rel=\"action:add_metafield, cpt:{$_POST[cpt]}\" class=\"button-primary ajaxify\" href=\"admin-ajax.php\">".__("Add Metafield")."</a>";		

$metadataset 	= get_option("mediametadata"); 
//print_r($metadataset);
/*
$metadataset 	= array();

$metadataset[identifier] = array(
					"ID" 			=> "identifier",
            		"status" 		=> "1",
            		"label" 		=> "Identifier",
            		"description" 	=> "An unambiguous reference to the resource within a given context.",
            		"type" 			=> "text",
            		"required" 		=> "1",
            		"required_err" 	=> "Dit is een verplicht veld.",
            		"default_value" => "000",
            		"max_length"	=> "28",
            		"multiple" 		=> "0",
            		"overview" 		=> "1"
						);

$metadataset[language] = array(
		            "ID" 			=> "language",
		            "status" 		=> "1",
		            "label"  		=> "Language",
		            "description" 	=> "A language of the resource.",
		            "type" 			=> "text",
		            "required" 		=> "1",
		            "required_err" 	=> "Dit is een verplichte taal",
		            "default_value" => "Nederlands",
		            "max_length" 	=> "39",
		            "multiple" 		=> "0",
		            "overview" 		=> "1"
        );
update_option( "mediametadata", $metadataset, '', 'no'); 
*/ 
echo"<div class=\"wrap\" id=\"collections_wrapper\">";
screen_icon( 'upload' );

echo"<h2>
".__("Media Metadata",'_coll')." 
</h2>  


<div style=\"height:40px;width:100%;padding:7px;\"><div class=\"updated settings-error\" style=\"display:none;\" id=\"setting-error-settings_updated\"></div></div>
{$buttons}<br/><br/>";

echo"<div class=\"form-wrap\" >
                   
 <table class=\"widefat metadata\" cellspacing=\"0\">
            <thead class=\"content-types-list\">
              <tr>
              <th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\">".__('Label','_coll')."</th>
              <th class=\"manage-column column-fields\" id=\"headstatus\" scope=\"col\">".__('Status','_coll')."</th>
              <th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\">".__('Type','_coll')."</th>  
              </tr>
            </thead>";
            
if(is_array($metadataset))
            foreach($metadataset as $metafield){ 
           //rel=\"action:editcollection,cpt:{$cpt[post_type]}\"   <span class=\"duplicate\"><a class=\"ajaxify\" href=\"admin-ajax.php}\">".__('Duplicate')."</a> | </span>
             echo"<tr id=\"content_{$metafield['ID']}\" class=\"\">
               
               
                
                
                
                <td class=\"column-name\">";
                               echo" <strong>
                    
                    <a class=\"row-title\" onclick=\"jQuery('#{$metafield['ID']}_edit').slideToggle(200, function(){
                    
                    
                    if (jQuery(this).is(':hidden')) {
                    jQuery('#content_{$metafield['ID']}').removeClass('on');
                    }else{
                    jQuery('#content_{$metafield['ID']}').addClass('on');                    }
                    
                    });return false;\" href=\"#\" title=\"Edit {$metafield['label']} {$metafield['ID']}  \">{$metafield['label']}</a></strong>
                    
                    <div class=\"row-actions\">
                    <span class=\"edit\"><a onclick=\"jQuery('#{$metafield['ID']}_edit').slideToggle(200, function(){
                    
                    
                    if (jQuery(this).is(':hidden')) {
                    jQuery('#content_{$metafield['ID']}').removeClass('on');
                   
                    }else{
                    jQuery('#content_{$metafield['ID']}').addClass('on');
                                        }
                    
                    });return false;\" href=\"#\">".__('Edit')."</a> | </span>
                   
                    <span class=\"delete\"><a onclick=\"deletemetafield('{$_POST[cpt]}', '{$metafield['ID']}', '".__("Are you sure to delete this Metafield?","_coll")."', '".__("Metafield deleted","_coll")."')\" href=\"#\">".__('Delete')."</a></span>
                  </div>
                  </td>";
                
                
                
                
               
                $status = ($metafield[status]==1)? __("Active", "_coll"):__("Deactivated", "_coll");
                echo"<td class=\"categories column-categories\">{$status}</td>
                <td class=\"categories column-categories\">{$metafield[type]}</td>

                            </tr>
                            <tr>
                            <td colspan=\"4\" style=\"display:none;\" class=\"metadataform\" id=\"{$metafield['ID']}_edit\">";
                           
                             
                            $this->getfieldform($metafield);
                            
                            echo"</td></tr>
                            ";   
} 
            


echo"</tbody>
        </table>
<div style=\"height:40px;width:100%;padding:7px;\">{$buttons}</div>
</div>";
}

/**
    * Changes the form for editing or adding a metadatafield. 
    *
    * This function is called from <b>Metadata::edit_metafield</b> or <b>Metadata::add_metafield</b>
    * always preceded by Metadata::changemetafieldtype()
    * @access public
    * @throws an overview table with a metadata field options.
    */
public function getfieldform($metafield){

if($metafield[type]!=""){	
	$metafield[cpt] = $_POST[cpt];
	$c = ucfirst($metafield[type]);
	$typeclass = new $c();
	                    	                       
if($metafield[noform]!=1){
echo"<form id=\"edit_options_{$metafield[ID]}_{$_POST[cpt]}\">";
}

echo"
<input type=\"hidden\" name=\"action\" value=\"save_metafield\"/>
<input type=\"hidden\" name=\"cpt\" value=\"{$_POST[cpt]}\"/>
<input type=\"hidden\" name=\"ID\" id=\"ID\" value=\"{$metafield[ID]}\"/>
";
	       
	                   echo $typeclass::fieldOptions($metafield);
	             if($metafield[noform]!=1){      
	                   echo"</form>";
}
	                       
	 }   
	
}

}