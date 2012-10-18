$(document).ready(function() { 
    var options = { success:       showResponse };
    $('.presents_post').ajaxForm(options); 
}); 

function showResponse(responseText, statusText, xhr, $form)  { 
    $(".presents_div").html(responseText);
};
