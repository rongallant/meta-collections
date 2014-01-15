$(document).ready(function() {

//$("#wysiwygs").appendTo('#wysiwygcontainer');
//textareaID = ($("#wysiwygs").attr('rel'));
//$("#"+textareaID).attr("class", $("#wysiwygs").attr('class'));

$(".wysiwygscontainer").each(function( index, element ) {
	wyss = $("#wysiwygs_"+$(element).attr('rel'));
	//console.log(wyss);
		wyss.appendTo(element);
});


});

