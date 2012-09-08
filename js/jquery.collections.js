var fieldsort, cp, date_time_preset, date_preset, changedanger, mypointer;
jQuery(document).ready(function() {

 addlisteners();

});





function addPointers(){//enables wordpress own pointers ass tooltips. :-)
	
	jQuery('.tooltips').pointer({
	content:  "<h3>loading...</h3>",
	position: 'top',
	open: function(a, b) {
	      //console.log(jQuery(a.target).attr('title'));//jQuery(b.pointer).find('.wp-pointer-content')
	       
	       jQuery(b.pointer).find('.wp-pointer-content').html("<h3>"+jQuery(a.target).attr('title')+"</h3><p>"+jQuery(a.target).attr('rel')+"</p>");
	       jQuery(b.pointer).css({'left':"-=57px"})//set the pointer at the right position	
	        }
	});
	
	jQuery('.tooltips').mouseenter(function(){
	jQuery(this).pointer('open');
	});
	
	jQuery('.tooltips').mouseleave(function(){
	  jQuery(this).pointer('close');
	   });
	   	
	}

function addlisteners(){
	//console.log(ajaxurl);
	jQuery.each(jQuery('.ajaxify'), function(index, element) { 

		jQuery(element).click(function(){
			 
			 var properties = jQuery(element).attr('rel').split(',');
			 var postedvars = {};
			
				jQuery.each(properties, function(index, property) { 
					
					
					//property
					var tup = property.split(':');
				 	if( typeof tup === 'object') { 						 	
					 	postedvars[tup[0]] = tup[1];				 	
				 	}
				 	
					
					});
			//console.log(postedvars)
			//if(element.tagName=="A"){	
			jQuery('#collections_wrapper').load(this.href, postedvars, function() {
				addlisteners();
			});
			
			
			
			return false;
			});


			});


}


function set_post_type(el){

newval 	= el.value.replace(/[^\w\s]/gi, '')
newval	= newval.replace(" ","_").toLowerCase();
	jQuery('#spost_type').attr('value', newval);
}

var message_to
function setMessage(message, timeout){
	timeout = (timeout==undefined) ? 3000 : timeout;
	jQuery('#setting-error-settings_updated').css({display:'block'}).html("<p><strong>"+message+"</strong></p>");
	message_to = setTimeout(function(){jQuery('#setting-error-settings_updated').slideToggle('slow')}, timeout)
}


function save_collection(message){
jQuery.post(ajaxurl, jQuery('#savecollection').serialize(), function(){
setMessage(message);
setTimeout(function(){document.location.reload();},1500);
});
return false;
}

function save_metafield(elementID, cpt, message){

jQuery.post(ajaxurl, jQuery('#edit_options_'+elementID+'_'+cpt).serialize(), function(){

jQuery('#collections_wrapper').load(ajaxurl, {action: 'editmetadata', cpt: cpt}, function() {
				addlisteners();
				setMessage(message);
			});


});

return false;

}

function delete_metabox(element, cpt, message){

if(confirm(message)){

jQuery.post(ajaxurl,{action:'delete_metabox', metaboxid:element.attr('id'), cpt:cpt}, function(){

jQuery(element).fadeTo('slow', 0, function() {
      jQuery(element).remove();
    });


});
  
 

 }else{
	 return false;
 }
 
};


function rename_metabox(cpt, element, question){//question,title
 renamedmetabox = prompt(question,jQuery(element).parent().prev().html())
 jQuery(element).parent().prev().html(renamedmetabox);


jQuery.post(ajaxurl,{action:'rename_metabox', metaboxname: renamedmetabox, metaboxid:jQuery(element).parent().prev().attr('class'), cpt:cpt}, function(){

setMessage('Settings updated');


});


  }



function save_metabox(message){
	
	 var theid 	= '#'+jQuery('input:radio[name=position]:checked').val();
     var theside = jQuery('input:radio[name=position]:checked').attr('rel');
     jQuery('#side').val(jQuery('input[name=position]:checked').attr('rel'));       
        
         
	jQuery.post(ajaxurl,  jQuery('#metabox_add').serialize(), function(data){
	jQuery(theid+' #'+theside+'-sortables').append(data);
	jQuery('form')[0].reset();
	});

	setMessage(message);//should be in the function above but the var message isnt getting trough there.
	
}


function deletemetafield(cpt, metafieldID, message, dmessage){

if(confirm(message)){
	
	jQuery.post(ajaxurl,  {action:'delete_metafield', cpt:cpt, metafieldID: metafieldID}, function(data){
	setMessage(dmessage);
	//console.log(jQuery('#content_'+metafieldID));
	jQuery('#content_'+metafieldID).fadeTo('slow', 0, function() {
    jQuery('#content_'+metafieldID).remove();
    
    jQuery('#'+metafieldID+'_edit').remove();
    });
	});
		
	}else{
		return false;
	}
	
	return false;

}


function deletecollectioncontent(cpt){
jQuery.post(ajaxurl,  {action:'deletecollectioncontent', cpt:cpt}, function(data){
setMessage(data);
});


}

function deletecollection(cpt, message){
	
	if(confirm(message)){
	
	jQuery.post(ajaxurl,  {action:'deletecollection', cpt:cpt}, function(data){
	setMessage(data, 100000);
	//setTimeout(function(){document.location.reload();},1500);
	jQuery('#collectie_'+cpt).fadeTo('slow', 0, function() {
      jQuery('#collectie_'+cpt).remove();
      
    });
	});
		
	}else{
		return false;
	}
	
	return false;
}

function save_uinterface(cpt, message){
	sides 			= new Array("normal", "side", "advanced", "inactive", "inactive-system");
	metaboxes		= {};
	uiOrder			= {};
	
	jQuery(sides).each(function(i, side) { //loop trough the sides
		
	metaboxContainer= jQuery('#'+side+'-sortables .postbox');
	mbOrder			= {};
	
	jQuery(metaboxContainer).each(function(index, metabox) { //loop trough the metaboxes
	//console.log(metabox.id);
	jQuery("#"+metabox.id+" .meta-field-box").each(function(indexmf, metafield) {//loop trough the metafields
	metadataID = metafield.id.split("meta-element-")[1];
	//name 						=  
	uiOrder[metadataID]			= {metadataID: metadataID, metaboxID:metabox.id, order: indexmf};
	//console.log({metadataID: metadataID, metaboxID:metabox.id, order: indexmf});
	}); 
	
	//each en dan id met array metadataID, metaboxID en order 
	
	
	name = (metabox.id.match(/__system__/g) || metabox.id.match(/submitdiv/g) ) ? jQuery('#'+metabox.id+' h3 span').html() : jQuery('#'+metabox.id+' h3 span span').html();
	
	//console.log(label+" - "+metabox.id);	
	
	mbOrder[index]	= {ID: metabox.id, name:name};	
	//uiOrder[]			= {metabox.id};
	//}
	
	});
	
	metaboxes[side] = mbOrder;
	
	});
	
//console.log(uiOrder);
	postvars	= {action: 'saveuserinterface', cpt: cpt, metaboxes: metaboxes, ui: uiOrder};
	jQuery('#footer').load(ajaxurl, postvars, function(){
		setMessage(message)
	});
}



