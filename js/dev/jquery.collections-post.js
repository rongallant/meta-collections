var colortitle='', olscript, olfscript, gmscript, gmfscript, file_frame;
var wysiwyg_num = new Array();
var $ = jQuery.noConflict();

//IMAGEF!!!!
$(document).ready(function() {

$(document).on("click",".delete_image",function(event){
//console.log($(event.currentTarget).parent().parent());
$(event.currentTarget).parent().parent().css({"display": "none", 'background': 'transparent'});
$(event.currentTarget).parent().parent().prev().val('');
$(event.currentTarget).parent().parent().next().css({'display':'inline-block'});

//console.log($(event.currentTarget).parent().parent().prev());


});

$(document).on("click",".upload-image-button",function(event){
event.preventDefault();
 
file_frame 	= wp.media.frames.file_frame = wp.media({
title: "choose image",
button: {
text: "insert image",
},
multiple: false,
editing: true,
image_container:$(event.currentTarget).prev(),
currentTarget: $(event.currentTarget)
});
 

// When an image is selected, run a callback.
file_frame.on( 'select', function() {

attachment = file_frame.state().get('selection').first().toJSON();
file_frame.options.image_container.css({"display": "block", 'background': 'url('+attachment.sizes.thumbnail.url+') no-repeat'});
file_frame.options.currentTarget.css({'display':'none'});
});
 
// Finally, open the modal
file_frame.open();


});
});



function add_value_instance(wrapperID, fieldtype){

	if(fieldtype!="wysiwyg" && fieldtype!="combination"){
		the_clone = $('#'+wrapperID+' .metafield-value:last').clone().appendTo($('#'+wrapperID+' .metafield-body'))
	}
	
	switch(fieldtype){
		default:

				//jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				//jQuery('#'+wrapperID+' .metafield-value:last textarea').val('');

		break;
	
	case "combination":
		$('#'+wrapperID+' .metafield-body>table:last-child').clone().appendTo($('#'+wrapperID+' .metafield-body') );
		//$('#'+wrapperID+' .metafield-body>table:last-child select').children()[0].selected=true;
		
		$('#'+wrapperID+' .metafield-body>table:last-child input, #'+wrapperID+' .metafield-body>table:last-child select').each(function(index, el ) {
		
		elementinfo = $.parseJSON( $(el).attr('rel'));
		newname = elementinfo['postmetaprefix']+""+elementinfo['parent']+"["+(parseFloat(elementinfo['instance']+1))+"]["+elementinfo['nonce']+"]"
		$(el).attr('name', newname);//give all the instances a new name and a new row!
			
		$.each(elementinfo, function(key, value){
			if(key=="instance"){
				elementinfo[key] = parseFloat(value)+1;
			}
			
		})	
		$(el).attr('rel', JSON.stringify(elementinfo));
		});
		
		
		parts = $('#'+wrapperID+' .metafield-body>table:last-child').attr("id").split("_");
		newID = parts[0]+"_"+parts[1]+"_"+(parseFloat(parts[2])+1);
		
		$('#'+wrapperID+' .metafield-body>table:last-child').attr("id",newID);
		$('#'+wrapperID+' .metafield-body>table:last-child td:last-child').css({"display":"block"});
		$('#'+wrapperID+' .metafield-body>table:last-child td:last-child a').attr("rel",newID);
		
		break;
		
	case "text":
	
	}
}


function add_value_instanceold(wrapperID, fieldtype){
	
	if(fieldtype!="wysiwyg"){
	the_clone = jQuery('#'+wrapperID+' .metafield-value:last').clone().appendTo(jQuery('#'+wrapperID+' .metafield-body'))
	}
	
	switch(fieldtype){
		case "colorpicker":
				jQuery('#'+wrapperID+' .metafield-value:last input').prev().css('background:#fff');
				jQuery('#'+wrapperID+' .metafield-value:last input').colorpicker({
                	parts: 'full',
					alpha: true,
					color: '#c0c0c0',
					title: colortitle,
					select: function(data, color){
					jQuery(this).prev().css({background: '#'+color.formatted});
					}					
					});
		break;


  		case "date_and_or_time":
				jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				jQuery('#'+wrapperID+' .metafield-value:last input').scroller({
						preset: date_time_preset,
						
						display: 'modal',
						mode: 'mixed'
						});
  
		break;

  		case "date":
				jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				jQuery('#'+wrapperID+' .metafield-value:last input').datepicker({
					 
					 dateFormat: date_preset
					
					 }).val();
			 
		break;

		case "select":
				
				jQuery('#'+wrapperID+' .metafield-value:last select').children()[0].selected=true;
				
				break;
		
		case "combination":
		alert(12);
		break;
				
		case "wysiwyg":
		metafieldID = wrapperID.split("_")[0];
		jQuery.post('admin-ajax.php',  {action: 'add_wysiwyg_field', metafieldID: metafieldID+'----------'+wysiwyg_num[metafieldID], tabindex:wysiwyg_num[metafieldID]}, function(data){
		jQuery(data).appendTo(jQuery('#'+wrapperID+' .metafield-body'))
		wysiwyg_num[metafieldID]++;
		});
		
		
		break;
				
/*
		default:

				jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				jQuery('#'+wrapperID+' .metafield-value:last textarea').val('');

		break;
		*/
  }
	
	
	
	
}

function remove_value_instance(event){
event.preventDefault();
//console.log();
$('#'+$(event.currentTarget).attr('rel')).remove();
//jQuery(inputEL).parent().remove();inputEL

}


