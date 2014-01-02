<?php
if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ){
	die("Access denied.");
}

/**
  * Contains all the functionality for managing your metadata schema in the backend.
  * 
  * All functions are derived from ajax requests defined in collections.php
  *
  * @category Wordpress Plugin
  * @package  Collections Wordpress plugin
  * @author   Bastiaan Blaauw <statuur@gmail.com>
  * @access   Public
  * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
  * @see      http://metacollections.statuur.nl/
*/
 
class Metadata extends Basics{ 
/**
    * Constructor
    * loads the vars from class Basic and loads an array with all metadata field types and classes Field.
    * @access public
    
    */
public function __construct(){
	$this->init();
	$this->Field = new Field();
	$this->Field->getFields();
	$this->Field->getClasses();
	
	if($this->action!=""){
	call_user_func(array($this, $this->action)); // As of PHP 5.3.0
	}

}

/**
    * Deletes date related to the metadata field
    * * deletes the metafield data from the option array "metadata_".$_POST[cpt]<br/>
    * * deletes the metafield position in option array "userinterface_".$_POST[cpt]<br/>
    * works with posted values instead of params    
    * @access private
    */

private function delete_metafield(){
$metadataset 		= get_option("metadata_".$_POST[cpt]);
$userinterface		= get_option("userinterface_".$_POST[cpt]);

//

//
if(is_array($metadataset[$_POST[metafieldID]])){
	unset($metadataset[$_POST[metafieldID]]);
	
	}

if(is_array($userinterface[$_POST[metafieldID]])){
	unset($userinterface[$_POST[metafieldID]]);
}

update_option( "metadata_".$_POST[cpt], $metadataset, '', 'no'); 
update_option( "userinterface_".$_POST[cpt], $userinterface, '', 'no'); 

}

/**
    * Saves data related to the metadata field
    * * loops trough the metadata array 'option', redefines it and updates the option in the database.
    * * deletes the metafield position in option array "userinterface_".$_POST[cpt]<br/>
    * works with posted values instead of params  
    * always calles trough an ajax request  
    * @access public
    */
public function save_metafield(){
	$metadataset = get_option("metadata_".$_POST[cpt]); 
	print_r($_POST);	
	$_POST['ID'] = ($_POST['ID']=="new")? $this->slugify($_POST[label]) : $_POST['ID'];
	
	foreach($_POST as $key=>$value){
	
	if($key!="action" && $key!="cpt"){
	
	$value = (is_array($value)) ? $value : htmlentities($value);
		$metadataset[$_POST['ID']][$key] = $value;

	}
	
	if(!isset($_POST['status'])){
		$metadataset[$_POST['ID']]['status'] = 0;
	}
	
	}
	
		
	
	update_option( "metadata_".$_POST[cpt], $metadataset, '', 'no'); 
}


/**
    * Saves data related to the metadata field
    *
    * * loops trough the metadata array 'option', redefines it and updates the option in the database.<br/>
    * * deletes the metafield position in option array "userinterface_".$_POST[cpt]<br/>
    * works with posted values instead of params <br/> 
    * always calles trough an ajax request<br/>  
    * the form can be updated trough ajax by selecting differend field types.<br/>
    * @access public
    * @throws a form with the options for the specific metadata field.
    */
public function add_metafield(){

$buttons ="<a rel=\"action:editmetadata,cpt:{$_POST[cpt]}\" class=\"button ajaxify\" href=\"admin-ajax.php\">&lsaquo; ".__("Back")."</a>";			
			
echo"<div class=\"icon32\" id=\"icon-options-general\"><br></div><h2>
".__("Add Metafield to Set for: {$_POST[cpt]}",'_coll')." 
</h2>

<div style=\"height:40px;width:100%;padding:7px;\">
<div class=\"updated settings-error\" style=\"display:none;\" id=\"setting-error-settings_updated\"></div>
</div>

{$buttons}<br/><br/>";

	$metafield[cpt] = $_POST[cpt];
	$metafield[type]= 'text';
	$metafield[ID] 	= 'new';
	
	
	$this->getfieldform($metafield);

}


/**
    * Generates a table overview with the metadata fields related to the collection
    *
    * @access public
    * @throws an overview table with a metadataschema related to the collection.
    */
public function	editmetadata(){

$metadataset 	= get_option("metadata_".$_POST[cpt]); //{}	
$cpts 			= get_option("collection_cpt");
$collection 	= $cpts[$_POST['cpt']];
//print_r($metadataset);
$buttons ="<a rel=\"action:collectionoverview\" class=\"button ajaxify\" href=\"admin-ajax.php\">&lsaquo; ".__("Back")."</a>
<a rel=\"action:add_metafield, cpt:{$_POST[cpt]}\" class=\"button-primary ajaxify\" href=\"admin-ajax.php\">".__("Add Metafield")."</a>";			
			
echo"<div class=\"icon32\" id=\"icon-options-general\" ><br></div><h2>
".__("Edit Metadata Set: {$collection[labels][name]}",'_coll')." 
</h2>



<div style=\"height:40px;width:100%;padding:7px;\"><div class=\"updated settings-error\" style=\"display:none;\" id=\"setting-error-settings_updated\"></div></div>
{$buttons}<br/><br/>";

//<th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\">".__("Show in Collection overview", "_coll")."</th>

echo"


<div class=\"form-wrap\" >
                   
 <table class=\"widefat metadata\" cellspacing=\"0\">
            <thead class=\"content-types-list\">
              <tr>
              <th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\">".__('Label','_coll')."</th>
                                
                <th class=\"manage-column column-fields\" id=\"headstatus\" scope=\"col\">".__('Status','_coll')."</th>
                
                <th class=\"manage-column column-fields\" id=\"headtype\" scope=\"col\">".__('Type','_coll')."</th>
                
              </tr>
            </thead>
           ";

/*

*/
if(is_array($metadataset))
            foreach($metadataset as $metafield){ 
           //rel=\"action:editcollection,cpt:{$cpt[post_type]}\"   <span class=\"duplicate\"><a class=\"ajaxify\" href=\"admin-ajax.php}\">".__('Duplicate')."</a> | </span>
             echo"<tr id=\"content_{$metafield['ID']}\" class=\"\">
               
               
                
                
                
                <td class=\"column-name\">
                  <strong>
                    
                    <a class=\"row-title\" onclick=\"jQuery('#{$metafield['ID']}_edit').slideToggle(200, function(){
                    
                    
                    if (jQuery(this).is(':hidden')) {
                    jQuery('#content_{$metafield['ID']}').removeClass('on');
                    }else{
                    jQuery('#content_{$metafield['ID']}').addClass('on');                    }
                    
                    });return false;\" href=\"#\" title=\"Edit {$metafield['label']} {$metafield['ID']}  \">".stripslashes($metafield['label'])."</a></strong>
                    
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
                
                /*
                $ochecked =($metafield[overview]==1)? "checked":"";
                echo"<td><input type=\"checkbox\" onclick=\"jQuery.post('admin-ajax.php',  {action:'changeinoverview', cpt:'{$_POST[cpt]}', metafieldID: '{$metafield[ID]}', status:this.checked}, function(){setMessage('".__("Settings updated...","_coll")."')});\" name=\"show_in_overview\" $ochecked value=\"1\">
                <div class=\"row-actions\">
                 <span class=\"edit\"><a href=\"".get_bloginfo('url')."/wp-admin/edit.php?post_type={$_POST[cpt]}\" target=\"_blank\"> ".__("Show overview","_coll")."</a></span></div>
                </td>
                ";
                */
                $status = ($metafield[status]==1)? __("Active", "_coll"):__("Deactivated", "_coll");
                echo"<td class=\"categories column-categories\">{$status}</td>
                <td class=\"categories column-categories\">";
                $c = ucfirst($metafield[type]);
                $typeclass = new $c();
                echo $typeclass->fieldname;
                //{$metafield[type]}
                //print_r($typeclass);
                echo"</td>

                            </tr>
                            <tr>
                            <td colspan=\"4\" style=\"display:none;\" class=\"metadataform\" id=\"{$metafield['ID']}_edit\">";
                           
                             
                            $this->getfieldform($metafield);
                            
                            echo"</td></tr>
                            ";   
} else{
	
	
	 echo"<tr id=\"no-collections\">
               
                <td class=\"name column-name\" colspan=\"4\" style=\"text-align:center;padding:30px;font-style:italic\">".__("No Metadata set defined yet, click on ","_coll")." '".__("Add Metafield")."' ".__("to create one.",'_coll')."
                </td></tr>
                ";   
}
            


echo"</tbody>
        </table>
<div style=\"height:40px;width:100%;padding:7px;\">{$buttons}</div>


</div>";
}


/**
    * Changes the setting for the metadatafield whether or not to be displayed in a collection overview table. 
    *
    * This function is called from <b>Metadata::editmetadata()</b><br/>
    * By clicking the checkbox the setting is saved immediately.
    * @access public
    */
public function changeinoverview(){
$value			= ($_POST[status]=='true')? 1:0;

$metadataset 	= get_option("metadata_".$_POST[cpt]);
$metadataset[$_POST[metafieldID]][overview] = $value;
update_option( "metadata_".$_POST[cpt], $metadataset, '', 'no'); 

}


/**
    * Changes the form for editing or adding a metadatafield. 
    *
    * This function is called from <b>Metadata::edit_metafield()</b> or <b>Metadata::add_metafield()</b>
    * It defines values for the $metadata array() and calles Metadata::getfieldform() for the right form
    * @access public
    */
function changemetafieldtype(){
	$metafield[ID] 		= $_POST[ID];
	$metafield[type] 	= $_POST[fieldtype];
	$metafield[cpt] 	= $_POST[cpt];
	$metafield[noform] 	= 1;
	$this->getfieldform($metafield);
	//print_r($_POST);
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
//
echo"
<input type=\"hidden\" name=\"action\" value=\"save_metafield\"/>
<input type=\"hidden\" name=\"cpt\" value=\"{$_POST[cpt]}\"/>
<input type=\"hidden\" name=\"ID\" id=\"ID\" value=\"{$metafield[ID]}\"/>
";
	       
	                   echo $typeclass->fieldOptions($metafield);
	             if($metafield[noform]!=1){      
	                   echo"</form>";
	                   
				if($metafield[ID]!="new"){                   
	                   echo"<script>
	                   
	                   jQuery('form :input').change(function() {
  						
  						if(changedanger==undefined){
  						setMessage('".__("Changing a fieldtype or other variables while there is already saved date can result in data loss. Beware of that fact.","_coll")."', 10000);
  						changedanger = 1;
  						}
						
  						});

	                   
	                   </script>
	                   
	                   ";
	                   }
}
	                       
	 }   
	
}



public function add_wysiwyg_field(){
	//echo"<textarea>herere</textarea>";__("save the post first in order to use the wysiwyg please","_coll")
	wp_editor('', $this->postmetaprefix.$_POST[metafieldID], array('dfw' => false, 'tabindex' => $_POST[tabindex]) );
}

}



?>