var fieldsort, cp, date_time_preset, date_preset, changedanger, mypointer;
var $ = jQuery.noConflict();

$(document).ready(function() {

addlisteners();
});



function addlisteners(){

$(document).on("click",".ajaxify",function(event){
	event.preventDefault();
	
	var properties = $(this).attr('rel').split(',');
	var postedVars = {};
	
	$.each(properties, function(index, property) {
	var tup = property.split(':');
				 	if( typeof tup === 'object') { 						 	
					 	postedVars[tup[0]] = tup[1];				 	
				 	}
	})
	$('#collections_wrapper').load(this.href, postedVars);
	
});

$(document).on("mouseenter",".tooltips",function(event){
	
	$(this).pointer({
	content:  "<h3>loading...</h3>",
	position: 'top',
	open: function(a, b) {
	      $(b.pointer).find('.wp-pointer-content').html("<h3>"+$(this).attr('title')+"</h3><p>"+$(this).attr('rel')+"</p>");
	      $(b.pointer).css({'left':"-=57px"})//set the pointer at the right position	
	        }
	});
	
	$(this).pointer('open');
	});
	
	
	$(document).on("mouseleave",".tooltips",function(event){
	$(this).pointer('close');
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


function rename_metabox(cpt, element, question, message){

renamedmetabox = prompt(question,jQuery(element).parent().prev().html())

if(renamedmetabox!=null){
jQuery(element).parent().prev().html(renamedmetabox);

jQuery.post(ajaxurl,{action:'rename_metabox', metaboxname: renamedmetabox, metaboxid:jQuery(element).parent().prev().attr('class'), cpt:cpt}, function(){
setMessage(message);
});
}
}


function edituserinterface(cpt){	
	postVars = {action: 'edituserinterface', cpt:cpt};
	$('#collections_wrapper').load(ajaxurl, postVars);
}

/*
function save_metabox(message, cpt){
postVars = $('#metabox_add').serialize();
console.log(postVars);

//Query.post(ajaxurl,  postVars, function(data){
//});

}
*/
function save_metabox(message, cpt){
	
	 var theid 	= '#'+jQuery('input:radio[name=position]:checked').val();
     var theside = jQuery('input:radio[name=position]:checked').attr('rel');
     jQuery('#side').val(jQuery('input[name=position]:checked').attr('rel'));       
        
         
	jQuery.post(ajaxurl,  jQuery('#metabox_add').serialize(), function(data){
	jQuery(theid+' #'+theside+'-sortables').append(data);
	jQuery('form')[0].reset();
	
	save_uinterface(cpt, message, 1);
	//setMessage(message);
	
	
	});
	//action	edituserinterface cpt	waarnemingen
	//$("#inactive-sortables").append("<div>hello world</div>")
	
	//odv = $("<div class='meta-field-box  closed' id='meta-element-tekstveld3'></div>");
	//odv.html("<div title='Klik om te wisselen' class='handlediv'><br></div><h3 class='meta-field-hndle'><span>tekstveld3</span> </h3><div class='inside'></div></div>");
	//$("#inactive-sortables").append(odv);

	//metafieldSortable.sortable( "refresh" );
	//$('.meta-field-sortables').sortable( "destroy" );
	//metafieldSortable.sortable( "option", "connectWith", "#nieuwe");
	//metafieldSortable.sortable().disableSelection();
	//metafieldSortable.sortable( "refresh" );

	//console.log(metafieldSortable);//$('.meta-field-sortables').sortable( "refreshPositions" );
	//$('.meta-field-sortables').sortable( "refresh" );

		 
	//metafield.add_postbox_toggles(pagenow, {post_type: cpt});
	// opslaan en ajax request naar editinterface!
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
	if(cpt!="post"){
	setMessage(data, 100000);
	 }
	jQuery('#collectie_'+cpt).fadeTo('slow', 0, function() {
    jQuery('#collectie_'+cpt).remove();
     
    });
    
	});
		
	}else{
		return false;
	}
	
	return false;
}

function save_uinterface(cpt, message, reload){
	
	sides 			= new Array("normal", "side", "advanced", "inactive", "inactive-system");
	metaboxes		= {};
	uiOrder			= {};
	
	jQuery(sides).each(function(i, side) { //loop trough the sides
		
	metaboxContainer= jQuery('#'+side+'-sortables .postbox');
	mbOrder			= {};
	
	jQuery(metaboxContainer).each(function(index, metabox) { //loop trough the metaboxes
	//console.log(jQuery("#"+metabox.id+" .meta-field-box"));
	
	jQuery("#"+metabox.id+" .meta-field-box").each(function(indexmf, metafield) {//loop trough the metafields
	metadataID = metafield.id.split("meta-element-")[1];
	//name 						=  
	uiOrder[metadataID]			= {metadataID: metadataID, metaboxID:metabox.id, order: indexmf};
	
	}); 
	
	//each en dan id met array metadataID, metaboxID en order 
	
	
	name = (metabox.id.match(/__system__/g) || metabox.id.match(/submitdiv/g) ) ? jQuery('#'+metabox.id+' h3 span').html() : jQuery('#'+metabox.id+' h3 span span').html();
	
	//console.log(label+" - "+metabox.id);	
	mbOrder[index]	= {ID: metabox.id, name:name};	
	//uiOrder[]			= {metabox.id};
	//}
	
	});
	
	metaboxes[side] = mbOrder;
	//console.log(metaboxes[side]);
	});

	postVars	= {action: 'saveuserinterface', cpt: cpt, metaboxes: metaboxes, ui: uiOrder};
	
	$('#rcver').load(ajaxurl, postVars, function(){
		setMessage(message);
		 
		 if(reload==1){
		 setTimeout(function(){edituserinterface(cpt)},2000);
			
		}	
	});
}



