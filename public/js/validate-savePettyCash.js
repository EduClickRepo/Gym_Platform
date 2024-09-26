/*!

 =========================================================
 * Material Bootstrap Wizard - v1.0.2
 =========================================================
 
 * Product Page: https://www.creative-tim.com/product/material-bootstrap-wizard
 * Copyright 2017 Creative Tim (http://www.creative-tim.com)
 * Licensed under MIT (https://github.com/creativetimofficial/material-bootstrap-wizard/blob/master/LICENSE.md)
 
 =========================================================
 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 */
var $validator;
$(document).ready(function(){
    // Code for the Validator
    $validator = $('.wizard-card form').validate({
        rules: {
            transactionType:{
                required: true,
            },
            paymentMethodId:{
                required: true,
            },
            amount:{
                required: true,
            },
            payDay:{
                required: true,
            },
            data:{
                required: true,
            },
            person:{
                required: true,
            },
            },
        messages: {
            radio: "This is a required field"
        },

        errorPlacement: function(error, element) {
            $(element).parent('div').addClass('has-error');
            if($validator.errorMap.hasOwnProperty("transactionType")){
                $('label[for="transactionType-income"], label[for="transactionType-expense"]').css("color", "red");
            }else{
                $('label[for="transactionType-income"], label[for="transactionType-expense"]').css("color", "");
            }

            $validator.focusInvalid();
        }
    });

});

