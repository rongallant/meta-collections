$(document).ready(function() {
//$('#wysiwygcontainer').html();
$("#wysiwygs").appendTo('#wysiwygcontainer');
textareaID = ($("#wysiwygs").attr('rel'));
$("#"+textareaID).attr("class", $("#wysiwygs").attr('class'));
//console.log($("#wysiwygs").attr('class'));
});