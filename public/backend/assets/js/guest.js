(function ($) {
    "use strict";

	$(document).on('click', '.print', function (event) {
		event.preventDefault();
		$("#preloader").css("display", "block");
		var div = "#" + $(this).data("print");
		$(div).print({
			timeout: 1000,
		});
	});

	$(document).on('click', '#close_alert', function () {
		$("#main_alert").fadeOut();
	});

	$(document).on('change', '.plan_type', function(){
		if($(this).val() == 'monthly'){
		  $('.monthly-plan').fadeIn();
		  $('.yearly-plan').fadeOut();
		  $('.lifetime-plan').fadeOut();
		}else if($(this).val() == 'yearly'){
		  $('.monthly-plan').fadeOut();
		  $('.yearly-plan').fadeIn();
		  $('.lifetime-plan').fadeOut();
		}else if($(this).val() == 'lifetime'){
		  $('.monthly-plan').fadeOut();
		  $('.yearly-plan').fadeOut();
		  $('.lifetime-plan').fadeIn();
		}
	});

})(jQuery);