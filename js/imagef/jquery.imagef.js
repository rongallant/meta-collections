var file_frame;
var $ = jQuery.noConflict();

$(document).ready(function() {

$(document).on("click",".edit_image",function(event){
buttonId = $(event.currentTarget).attr('rel');
$( "#"+buttonId).trigger( "click" );
});


$(document).on("click",".delete_image",function(event){
$(event.currentTarget).parent().parent().css({"display": "none", 'background': 'transparent'});
$(event.currentTarget).parent().parent().prev().val('');
$(event.currentTarget).parent().parent().next().css({'display':'inline-block'});
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
input_container:$(event.currentTarget).attr('rel'),
image_container:$(event.currentTarget).prev(),
currentTarget: $(event.currentTarget)
});
 

// When an image is selected, run a callback.
file_frame.on( 'select', function() {

attachment 		= file_frame.state().get('selection').first().toJSON();
return_value 	= JSON.stringify(attachment);

file_frame.options.image_container.css({"display": "block", 'background': 'url('+attachment.sizes.thumbnail.url+') no-repeat'});
file_frame.options.currentTarget.css({'display':'none'});
$("#"+file_frame.options.input_container).val(return_value);
});
 
// Finally, open the modal
file_frame.open();


});
});