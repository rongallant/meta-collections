var colortitle='';
var wysiwyg_num = new Array();

function get_metadata(elementID, vimeoID){


	 url = (vimeoID!=undefined) ? vimeoID : jQuery("#"+elementID).val();
	
	/*
	if (preg_match('/^(http|https):\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/', $vimeo_url, $vimeo_id)){
    $vimeoid = $vimeo_id[4];
}else{
   // error message... 
}
	*/
	
	//^(http|https)
	var provider = url.match(/https:\/\/(:?www.)?(\w*)/)[2],id;
	//var provider2 = url.match(/https:\/\/(:?www.)?(\w*)/)[2],id;
	//var provider = url.match(/^(http|https):\/\/(:?www.)?(\w*)/)[2],id;
	//var vimeoUrl = url.match(/^http:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/);
	
	if(provider=="vimeo"){
		
	//http://vimeo.com/32585445
		
	vimeoID 	= url.split("/");
	vimeo_json 	= "http://vimeo.com/api/v2/video/"+vimeoID[3]+".json";
	
	//jQuery.getJSON(vimeo_json, function(data) {
	//console.log(data);
	//});
	
	//console.log(vimeo_json);

	jQuery.getJSON(vimeo_json+ '?callback=?', {format: "json"}, function(data) {
         jQuery('#table_'+elementID).css({display:'block'})
         data = data[0];
         
         jQuery.each(data, function(index, value) { 
	       //  console.log(jQuery("#vimeo_"+index).length+" - "+index+" - "+value);
        
         if(jQuery("#vimeo_"+index).length>0){
	         
	         jQuery("#vimeo_"+index).val(value);
	         
         }
         
         if(index=="thumbnail_medium"){
	         jQuery('#img_thumbnail_medium').attr("src", value);
	       
         }
         
          if(index=="user_portrait_medium"){
	         jQuery('#img_user_portrait_medium').attr("src", value);
	        // console.log(value);
         }
         
         });
         
});
	format_vimeo_url();

	
	}else{	
	throw new Error("this is not a valid Vimeo URL"); 
	}
}

function format_vimeo_url(){


var portraitchecked 	= (jQuery('#vimeo_intro_portrait').is(':checked')) ? 1 : 0;
var titlechecked 		= (jQuery('#vimeo_intro_title').is(':checked')) ? 1 : 0;
var bylinechecked 		= (jQuery('#vimeo_intro_title').is(':checked')) ? 1 : 0; 

var autoplaychecked 	= (jQuery('#vimeo_autoplay').is(':checked')) ? 1 : 0;
var loophecked 			= (jQuery('#vimeo_loop').is(':checked')) ? 1 : 0;
var vimeoID				= jQuery('#vimeo_id').val();  	
var color				= (jQuery('#vimeo_color').val().length==6)? "&amp;color="+jQuery('#vimeo_color').val() : "";
url 					= "http://player.vimeo.com/video/"+vimeoID+"/?title="+titlechecked+"&amp;byline="+bylinechecked+"&amp;portrait="+portraitchecked+"&amp;autoplay="+autoplaychecked+"&amp;loop="+loophecked+color;

jQuery('#vimeo_iframe').attr('src',url).css({"display":"block"});

}

function add_value_instance(wrapperID, fieldtype){
	
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
				//ni = jQuery('#'+wrapperID+' .metafield-value:last input').attr('id')+10;
				//jQuery('#'+wrapperID+' .metafield-value:last input').attr('id', ni);
				//this trigger isnt working yet
				jQuery('#'+wrapperID+' .metafield-value:last input').datepicker({
					 
					 dateFormat: date_preset
					
					 }).val();
					// console.log(jQuery('#'+wrapperID+' .metafield-value:last input'));		 
		break;

		case "select":
				
				jQuery('#'+wrapperID+' .metafield-value:last select').children()[0].selected=true;
				
				break;
				
		case "wysiwyg":
		metafieldID = wrapperID.split("_")[0];
		jQuery.post('admin-ajax.php',  {action: 'add_wysiwyg_field', metafieldID: metafieldID+'----------'+wysiwyg_num[metafieldID], tabindex:wysiwyg_num[metafieldID]}, function(data){
		jQuery(data).appendTo(jQuery('#'+wrapperID+' .metafield-body'))
		wysiwyg_num[metafieldID]++;
		});
		
		
		break;
				

		default:

				jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				jQuery('#'+wrapperID+' .metafield-value:last textarea').val('');

		break;
  }
	
	
	
	
}

function remove_value_instance(inputEL){
jQuery(inputEL).parent().remove();

}