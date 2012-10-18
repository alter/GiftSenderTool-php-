$(document).ready(function() { 
    var options = { success:       showResponse };
    $('.login_post').ajaxForm(options); 
}); 

function showResponse(responseText, statusText, xhr, $form)  { 
    $(".login_div").html(responseText);
};
