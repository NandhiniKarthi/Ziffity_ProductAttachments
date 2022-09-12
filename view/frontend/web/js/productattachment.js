define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
     
    function attachment(config, element) {
        var $element = $(element);
        var ajaxUrl = config.ajaxUrl;
         
        var dataForm = $('#regulatory-form');
        dataForm.mage('validation', {});         
		var regulatoryButton = dataForm.find('button[type=submit]');

		dataForm.submit(function(){          
            if(dataForm.validation('isValid')) {
        	    event.preventDefault();
					regulatoryButton.attr("disabled", "disabled");
	                var param = dataForm.serialize();        
                    $.ajax({
                        showLoader: true,
                        url: ajaxUrl,
                        data: param,
						dataType: "json",
                        type: "POST"
                    }).done(function (data) {
						regulatoryButton.removeAttr('disabled');

                        var content = "<div class='error'>"+data.message+"</div>";
						$(content).hide().appendTo('#reg-response-msg').fadeIn(1000);
						setTimeout(function(){ 
							$('#reg-response-msg').fadeOut(1000, function() {
							   $(this).empty().show();
							});
						}, 5000);

                        $("#regulatory-form")[0].reset();
                        return true;
                    });
                }
        });
    };
	return attachment;     
     
});
