
function get_metadata(elementID, vimeoID){

	 url = (vimeoID!=undefined) ? vimeoID : jQuery("#"+elementID).val();
	
	var provider = url.match(/https:\/\/(:?www.)?(\w*)/)[2],id;
	
	if(provider=="vimeo"){		
	vimeoID 	= url.split("/");
	vimeo_json 	= "http://vimeo.com/api/v2/video/"+vimeoID[3]+".json";
	
	jQuery.getJSON(vimeo_json+ '?callback=?', {format: "json"}, function(data) {
         jQuery('#table_'+elementID).css({display:'block'})
         data = data[0];
         
         jQuery.each(data, function(index, value) { 
        
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
