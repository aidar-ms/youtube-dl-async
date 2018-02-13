//$( "button[value='submit']" ).click( function() {} );

// To be called on submit button click (disable enter submit if needed)

$('#input-field').on('beforeSubmit', function() {
    var $form = $(this);

    $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: $form.serializeArray(),
    }).done(function(data) {
        if(data.success) {
            console.log(data);
        } else if (data.validation) {
            // server validation failed
            $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
        } else {
            alert('Server response failed');
            // incorrect server response
        }
    }).fail(function (e) {
        alert('Request failed');
    })

return false; // prevent default form submission
});

/*$('#getvalues').on('click', function() {
    var $form = $('#input-field');
    $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: {
            searchData: $form.serializeArray()
        },
        success: function(response) {
            console.log(response)
        },
        error: function() {
            alert('Error occured with AJAX');
        }
    })

}); */
 /*
$('#input-field').on('beforeSubmit', function() {
    var $form = $(this);
    // extract values. Make sure to send _csrf token
   $.ajax({
       url: $form.attr('action'),
       type: $form.attr('method'),
       data: $form.serializeArray(),
       success: function(response) {
           console.log(response)
       },
       error: function(e) {
           alert(e);
       }
   })
    .done(function(data) {
        if(data.success) {
            alert("Submitted:" + data)
        } else if(data.validation) {
            $form.yiiActiveForm('updateMessages', data.validation, true);
        } else {
            alert("Check the server response")
        }
    })
    .fail(function() {
        alert("Request failed")
    })

   return false;
}); */