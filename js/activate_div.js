$(document).ready(function() { 
  var options = { success:       showResponse };
  $('.activate_post').ajaxForm(options); 
}); 

function showResponse(responseText, statusText, xhr, $form)  { 
  $(".activate_div").html(responseText);
};
