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
	case "date":
	$('#'+wrapperID+' .metafield-body>div:last-child .datepicker').each(function(index, el) {			
		
		
		$(this).removeClass("hasDatepicker").removeAttr("id").datepicker("destroy");
		dateinfo = $.parseJSON($(this).attr('rel'));
		dateformat = dateinfo.format;
		
		$(this).datepicker({
			 dateFormat: eval(dateformat)
		}).val();
		$(this).next().next().css({"opacity": 1});
		});
		
	break;
	
	case "date_and_or_time":
			$('#'+wrapperID+' .metafield-body>div:last-child .datetimepicker').each(function(index, el) {			

		opts = $(this).attr('data');
		opts = $.parseJSON(opts);
		$(this).datetimepicker(opts);
		$(this).next().next().css({"opacity": 1});
		
		});

	break;
	
	case "combination":
		//imagefields
		$('#'+wrapperID+' .metafield-body>div:last-child').clone().appendTo($('#'+wrapperID+' .metafield-body') );
		$('#'+wrapperID+' .metafield-body>div:last-child .image_container').css({"display": "none"});
		$('#'+wrapperID+' .metafield-body>div:last-child .upload-image-button').css({"display": "inline-block"});
		
		//wysiwyg
		$('#'+wrapperID+' .metafield-body>div:last-child .wysiwygscontainer').html('//save the page please in order to load the wysiwyg...');
		
		/* all input, select and textarea fields
		*  give new names and updates counting rel json attributes 
		*/
		$('#'+wrapperID+' .metafield-body>div:last-child input, #'+wrapperID+' .metafield-body>div:last-child select, #'+wrapperID+' .metafield-body>div:last-child textearea').each(function(index, el ) {
		$(this).val('');		
		
		
		if($(el).attr('rel')!=undefined){
		console.log($(el).attr('rel'));
		elementinfo = $.parseJSON( $(el).attr('rel'));
		newname 	= elementinfo['postmetaprefix']+""+elementinfo['parent']+"["+(parseFloat(elementinfo['instance']+1))+"]["+elementinfo['nonce']+"]"
		$(el).attr('name', newname);//give all the instances a new name and a new row!
			
		$.each(elementinfo, function(key, value){
			if(key=="instance"){
				elementinfo[key] = parseFloat(value)+1;
			}
			
		})	
		
		$(el).attr('rel', JSON.stringify(elementinfo));
		
		}
		
		});
		
		
		if(elementinfo!=undefined){
		
		parts 			= $('#'+wrapperID+' .metafield-body>div:last-child').attr("id").split("_");
		newDivID 		= parts[0]+"_"+parts[1]+"_"+(parseFloat(parts[2])+1);
		newInputID	 	= elementinfo.postmetaprefix+"_"+elementinfo.parent+"_"+elementinfo.instance+"_"+elementinfo.nonce;

		/* all input[type=hidden], image and fields get a net id al rel
		*  also the div
		*/
		$('#'+wrapperID+' .metafield-body>div:last-child').attr("id",newDivID);
		$('#'+wrapperID+' .metafield-body>div:last-child input[type=hidden]').attr("id",newInputID);
		$('#'+wrapperID+' .metafield-body>div:last-child .upload-image-button').attr("rel",newInputID);
		
		
		//for datepicker files
		$('#'+wrapperID+' .metafield-body>div:last-child .datepicker').each(function(index, el) {			

		$(this).removeClass("hasDatepicker").removeAttr("id").datepicker("destroy");
		dateinfo = $.parseJSON($(this).attr('rel'));
		dateformat = dateinfo.format;
		
		$(this).datepicker({
			 dateFormat: eval(dateformat)
		}).val();
		});


		//for datetimepicker files
		$('#'+wrapperID+' .metafield-body>div:last-child .datetimepicker').each(function(index, el) {			

		opts = $(this).attr('data');
		opts = $.parseJSON(opts);
		$(this).datetimepicker(opts);
		});
		}
		

		break;
	}
}



function remove_value_instance(event, element){
event.preventDefault();
//console.log(element)
element.remove();


}


