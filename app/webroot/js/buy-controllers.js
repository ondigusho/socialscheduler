function ResponseHandlerReview(status, response){
    //console.log(response);
    if (response.error) 
    {
        // Stripe.js failed to generate a token. The error message will explain why.
        // Usually, it's because the customer mistyped their card info.
        $('#buy-messages').empty();
         $('#buy-messages').append('<div class="alert alert-error"><b>'+ response.error.message +'</b>\
                <br> Please fix the problem and try again!</div>');
         // immediately disable the submit button to prevent double submits
         $('#buy-submit-button').removeAttr("disabled");
    } 
    else
    {   
        // Stripe.js generated a token successfully. We're ready to charge the card!
        var token = response.id;
        var firstName = $("#first-name").val();
        var lastName = $("#last-name").val();
        var email = $("#email").val();
        var cardNumber = $('#card-number').val();
        var cardCVC = $('#card-security-code').val();
        var price = $('input[name=optionsPrice]:checked').val();
        
        //alert (token + email + price);
        // Make the call to the server-script to process the order.
        // Pass the token and non-sensitive form information.
        var request = $.ajax ({
            type: "POST",
            url: "buy/review",
            data: {
                "stripeToken" : token,
                "firstName" : firstName,
                "lastName" : lastName,
                "email" : email,
                "cardNumber" : cardNumber,
                "cardCVC" : cardCVC,
                "price" : price
                },
            success:function(rsp){
                    request.done(function(rsp)
                    {
                            //empty content
                            $('content').empty();
                            //load data
                            $('#content').html(rsp);
//                        else
//                        {
//                            $('#buy-messages').empty();
//                            $('#buy-messages').append('<div class="alert alert-error"><b>'+ rsp.message +'</b>\
//                                   <br> Please fix the problem and try again!</div>');
//                            // immediately disable the submit button to prevent double submits
//                            $('#buy-submit-button').removeAttr("disabled");
//                        }
                    });
            },
        });
    }
}

//main 
$(document).ready(function() 
{
    $('#review-form').submit(function(event)
    {   
        //remove this error div's
        $('#iferror-fname').empty();
        $('#iferror-lname').empty();
        $('#iferror-cc').empty();
        $('#iferror-cvc').empty();
        
        var fName = $('#first-name').val();
        var lName = $('#last-name').val();
        var email = $('#email').val();
        var cardNumber = $('#card-number').val();
        var cardCVC = $('#card-security-code').val();
         
        // First and last name fields: make sure they're not blank
        if (fName === "") {
            $('#iferror-fname').append('<div class="emerror">Insert valid first name</div>');
           // showErrorDialogWithMessage("Please enter your first name.");
            return;
        }
        if (lName === "") {
            $('#iferror-lname').append('<div class="emerror">Insert valid last name</div>');
            return;
        }
         
        // Stripe will validate the card number and CVC for us, so just make sure they're not blank
        if (cardNumber === "") {
            $('#iferror-cc').append('<div class="emerror">Insert valid credit card</div>');
            return;
        }
        if (cardCVC === "") {
           $('#iferror-cvc').append('<div class="emerror">Insert valid cvc</div>');
            return;
        }
         
        //We passed the basic validation, so we're ready to send the info to 
        // Stripe to create a token!
         Stripe.createToken({
            number: cardNumber,
            cvc: cardCVC,
            exp_month: $('#expiration-month').val(),
            exp_year: $('#expiration-year').val()
        }, ResponseHandlerReview);
        
        // immediately disable the submit button to prevent double submits
        //$('#buy-submit-button').attr("disabled", "disabled");
        
        // Prevent the default submit action on the form
        return false;
    });
    
});




//function showErrorDialogWithMessage(message)
//{
//    // For the tutorial, we'll just do an alert. You should customize this function to 
//    // present "pretty" error messages on your page.
//    alert(message);
// 
//    // Re-enable the order button so the user can try again
//    $('#buy-submit-button').removeAttr("disabled");
//}

//function stripeResponseHandler(status, response)
//{
//    if (response.error) 
//    {
//        // Stripe.js failed to generate a token. The error message will explain why.
//        // Usually, it's because the customer mistyped their card info.
//        // You should customize this to present the message in a pretty manner:
//        //alert(response.error.message);
//        //remove messages
//        $('#buy-messages').empty();
//         $('#buy-messages').append('<div class="alert alert-error"><b>'+ response.error.message +'</b>\
//                <br> Please fix the problem and try again!</div>');
//         // immediately disable the submit button to prevent double submits
//         $('#buy-submit-button').removeAttr("disabled");
//    } 
//    else
//    {   
//        // Stripe.js generated a token successfully. We're ready to charge the card!
//        var token = response.id;
//        var firstName = $("#first-name").val();
//        var lastName = $("#last-name").val();
//        var email = $("#email").val();
//        var price = $('input[name=optionsPrice]:checked').val();
//        
//        //alert (token + email + price);
//        // Make the call to the server-script to process the order.
//        // Pass the token and non-sensitive form information.
//        var request = $.ajax ({
//            type: "POST",
//            url: "buy/process",
//            data: {
//                "stripeToken" : token,
//                "firstName" : firstName,
//                "lastName" : lastName,
//                "email" : email,
//                "price" : price
//                },
//            success:function(rsp){
//                    request.done(function(rsp)
//                    {
//                        if (rsp.result === 0)
//                        {
//                            // Customize this section to present a success message and display whatever
//                            // should be displayed to the user.
//                            //alert("The credit card was charged successfully!");
//                            $('#buy-messages').empty();
//                            $('#buy-messages').append('<div class="alert alert-success"><b>'+ rsp.message +'</b></div>');
//                            // immediately disable the submit button to prevent double submits
//                            $('#buy-submit-button').attr("disabled", "disabled");
//                        }
//                        else
//                        {
//                            // The card was NOT charged successfully, but we interfaced with Stripe
//                            // just fine. There's likely an issue with the user's credit card.
//                            // Customize this section to present an error explanation
//                            // Customize this section to present a success message and display whatever
//                            // should be displayed to the user.
//                            //alert("The credit card was charged successfully!");
//                            $('#buy-messages').empty();
//                            $('#buy-messages').append('<div class="alert alert-error"><b>'+ rsp.message +'</b>\
//                                   <br> Please fix the problem and try again!</div>');
//                            // immediately disable the submit button to prevent double submits
//                            $('#buy-submit-button').removeAttr("disabled");
//                        }
//                    });
//            },
//            dataType: "json"
//        });
//        
//     //  console.log(response);
//     //  var obj = jQuery.parseJSON(response);
//       
//    }
//}
