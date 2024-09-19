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

// Material Bootstrap Wizard Functions

var $validator;
$(document).ready(function(){
    // Code for the Validator
    $validator = $('.wizard-card form').validate({
        rules: {
            firstname: {
              required: true,
              minlength: 3
            },
            lastname: {
              required: true,
              minlength: 3
            },
            dateborn: {
              required: true,
            },
            email: {
              required: true,
              minlength: 3,
            },
            cellphone:{
              required: true,
              minlength: 10,
            },
            ciudad:{
                required: true,
            },
            tarifa:{
                required: true,
            },
            documentId:{
                required: true,
            },
            eps:{
                required: true,
            },
            maritalStatus:{
                required: true,
            },
            occupation:{
                required: true,
            },
            emergencyContact:{
                required: true,
            },
            emergencyPhone:{
                required: true,
                minlength: 10,
            },
            objective:{
                required: true,
            },
            pathology:{
                required: true,
            },
            channel:{
                required: true,
            },
            estatura:{
                required: true,
                min: 100,
            },
        },
        messages: {
            radio: "This is a required field",
            estatura: {
                min: "La estatura debe ser mayor o igual a 100 cm."
            }
        },

        errorPlacement: function(error, element) {
            $(element).parent('div').addClass('has-error');
         }
	});
});
