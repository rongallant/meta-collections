var colortitle='', olscript, olfscript, gmscript, gmfscript, rFields;
var wysiwyg_num = new Array();
var $ = jQuery.noConflict();
$(document).ready(function() {


$('#post').validate({
	errorPlacement: function(error, element) {
	//error.html($(element).attr('data'));
	error.insertBefore(element);
	error
	}
	
});

});


$(document).on("submit","#post",function(event){
errors = true;
$('#post .required, #post .date, #post .creditcard').each(function (index, element) {
if(false===$('#post').validate().element($(element))){
errors = false;
$('#publish').removeClass("button-primary-disabled");//
}


});

return errors;	
});


function add_value_instance(wrapperID, fieldtype){

	if(fieldtype!="wysiwyg" && fieldtype!="combination"){
		the_clone = $('#'+wrapperID+' .metafield-value:last').clone().appendTo($('#'+wrapperID+' .metafield-body'))
	}
	
	switch(fieldtype){
		default:
		
		$('#'+wrapperID+' .metafield-value:last input').val('');
		$('#'+wrapperID+' .metafield-value:last .delete_metavalue').css({opacity:'1'});
		
		break;
	
	case "combination":
		//imagefields
		$('#'+wrapperID+' .metafield-body>div:last-child').clone().appendTo($('#'+wrapperID+' .metafield-body') );
		$('#'+wrapperID+' .metafield-body>div:last-child .image_container').css({"display": "none"});
		$('#'+wrapperID+' .metafield-body>div:last-child .upload-image-button').css({"display": "inline-block"});
		$('#'+wrapperID+' .metafield-body>div:last-child .wysiwygscontainer').html('//nu de wysiwyg nog....');
		$('#'+wrapperID+' .metafield-body>div:last-child input, #'+wrapperID+' .metafield-body>div:last-child select, #'+wrapperID+' .metafield-body>div:last-child textearea').each(function(index, el ) {
		$(this).val('');		
		
		elementinfo = $.parseJSON( $(el).attr('rel'));
		
		newname 	= elementinfo['postmetaprefix']+""+elementinfo['parent']+"["+(parseFloat(elementinfo['instance']+1))+"]["+elementinfo['nonce']+"]"
		$(el).attr('name', newname);//give all the instances a new name and a new row!
			
		$.each(elementinfo, function(key, value){
			if(key=="instance"){
				elementinfo[key] = parseFloat(value)+1;
			}
			
		})	
		$(el).attr('rel', JSON.stringify(elementinfo));
		});
		
		
		parts 			= $('#'+wrapperID+' .metafield-body>div:last-child').attr("id").split("_");
		newDivID 		= parts[0]+"_"+parts[1]+"_"+(parseFloat(parts[2])+1);
		newInputID	 	= elementinfo.postmetaprefix+"_"+elementinfo.parent+"_"+elementinfo.instance+"_"+elementinfo.nonce;
		
		$('#'+wrapperID+' .metafield-body>div:last-child').attr("id",newDivID);
		//$('#'+wrapperID+' .metafield-body>div:last-child td:last-child').css({"display":"block"});
		$('#'+wrapperID+' .metafield-body>div:last-child input[type=hidden]').attr("id",newInputID);
		$('#'+wrapperID+' .metafield-body>div:last-child .upload-image-button').attr("rel",newInputID);

		//metafieldID = wrapperID.split("_")[0];
		//wp-collections_combinatie[1][86f3483118]-wrap
		//console.log($('#'+wrapperID+' .metafield-body>div').find('.wysiwygs'));//metafieldID: metafieldID+'----------'+wysiwyg_num[metafieldID], tabindex:wysiwyg_num[metafieldID]
		jQuery.post('admin-ajax.php',  {action: 'add_wysiwyg_field', aa:'b'}, function(data){
		
		
		//jQuery(data).appendTo(jQuery('#'+wrapperID+' .metafield-body'))
		//wysiwyg_num[metafieldID]++;
		});
			 
	/*	
		
		$('#'+wrapperID+' .metafield-body>table:last-child').clone().appendTo($('#'+wrapperID+' .metafield-body') );
		$('#'+wrapperID+' .metafield-body>table:last-child .image_container').css({"display": "none"});
		$('#'+wrapperID+' .metafield-body>table:last-child .upload-image-button').css({"display": "inline-block"});
	
		$('#'+wrapperID+' .metafield-body>table:last-child input, #'+wrapperID+' .metafield-body>table:last-child select').each(function(index, el ) {
		$(this).val('');		
		
		elementinfo = $.parseJSON( $(el).attr('rel'));
		newname 	= elementinfo['postmetaprefix']+""+elementinfo['parent']+"["+(parseFloat(elementinfo['instance']+1))+"]["+elementinfo['nonce']+"]"
		$(el).attr('name', newname);//give all the instances a new name and a new row!
			
		$.each(elementinfo, function(key, value){
			if(key=="instance"){
				elementinfo[key] = parseFloat(value)+1;
			}
			
		})	
		$(el).attr('rel', JSON.stringify(elementinfo));
		});
		
		
		parts 			= $('#'+wrapperID+' .metafield-body>table:last-child').attr("id").split("_");
		newTableID 		= parts[0]+"_"+parts[1]+"_"+(parseFloat(parts[2])+1);
		newInputID	 	= elementinfo.postmetaprefix+"_"+elementinfo.parent+"_"+elementinfo.instance+"_"+elementinfo.nonce;
		
		$('#'+wrapperID+' .metafield-body>table:last-child').attr("id",newTableID);
		$('#'+wrapperID+' .metafield-body>table:last-child td:last-child').css({"display":"block"});
		$('#'+wrapperID+' .metafield-body>table:last-child input[type=hidden]').attr("id",newInputID);
		$('#'+wrapperID+' .metafield-body>table:last-child .upload-image-button').attr("rel",newInputID);
		*/
		break;
	
	case "date":

	//if (!false == $('#'+wrapperID+' .metafield-value:last .datepicker').hasClass('hasDatepicker')) {
	$('#'+wrapperID+' .metafield-value:last .datepicker').removeClass('hasDatepicker').datepicker('destroy').attr("id","").val("");		
	$('#'+wrapperID+' .metafield-value:last .delete_metavalue').css({opacity:'1'});
	break;	
	
	case "tsssext":
	break;
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

function remove_value_instance(event, element){
event.preventDefault();
//console.log(element)
element.remove();


}


