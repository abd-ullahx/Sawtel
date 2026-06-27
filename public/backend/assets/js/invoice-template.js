(function($) {
    "use strict";

    var current_element;

    var clipboard = new ClipboardJS("#data > ul > li");
    clipboard.on("success", function(e) {
        $.toast({
            text: "Coiped " + e.text,
            icon: "info",
            position: "top-right",
        });
    });

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on("change", "#template_type", function() {
        if ($(this).val() == "invoice") {
            $(".invocie-field").removeClass("d-none");
            $(".quotation-field").addClass("d-none");
        } else if ($(this).val() == "quotation") {
            $(".quotation-field").removeClass("d-none");
            $(".invocie-field").addClass("d-none");
        }
    });

    var codes = [$("#invoice-canvas").html()];
    var codesTrash = [];

    $('#redo-btn').prop('disabled',true);
    $('#undo-btn').prop('disabled',true);

    $(document).on("updateInvoiceCanvas", function () {
        codes.push($("#invoice-canvas").html());
        codes.length === 0 ? $('#undo-btn').prop('disabled', true) : $('#undo-btn').prop('disabled', false);
	});

    $(document).on('click','#undo-btn', function () {
        codesTrash.push(codes.slice(-1));
        codes.pop();
        if(codes.length === 0){
            return;
        }
        $("#invoice-canvas").html(codes.slice(-1));
        codesTrash.length === 0 ? $('#redo-btn').prop('disabled',true) : $('#redo-btn').prop('disabled',false);
        reset_ui();
    });

    $(document).on('click','#redo-btn', function () {
        codes.push(codesTrash.slice(-1));
        codesTrash.pop();
        $("#invoice-canvas").html(codes.slice(-1));
        codesTrash.length === 0 ? $('#redo-btn').prop('disabled',true) : $('#redo-btn').prop('disabled',false);
        reset_ui();
    });

    $(document).on("click", "#btn-preview, #btn-editor", function() {
        $("#invoice-canvas div").removeClass("ui-droppable-hover ui-state-hover");
        $("#invoice-canvas div > i.fa-edit").toggle();
        $("#invoice-canvas div > i.fa-trash-alt").toggle();
        $("#invoice-canvas div > i.fa-clone").toggle();
        $("#invoice-canvas div").toggleClass("toggle-preview");
        $("#btn-preview").toggleClass("d-none");
        $("#btn-editor").toggleClass("d-none");
    });

    $(document).on("keyup", "#custom-css", function() {
        var cssCode = $(this).val();
        $("#custom-css-code").html(cssCode);
    });

    $(document).on("click", "#invoice-canvas", function() {
        $("#invoice-canvas div").removeClass("ui-droppable-hover ui-state-hover");
    });

    $("#invoice-canvas").sortable({
        connectWith: ".dot-element",
        update: function( event, ui ) {
            $(document).trigger('updateInvoiceCanvas');
        }
    });

    //Drop New element
    $("#invoice-canvas").droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        greedy: true,
        drop: function(event, ui) {
            var droppable = $(this);
            var draggable = ui.draggable;
            var element = draggable.data("element");

            if (typeof element !== "undefined") {
                $.ajax({
                    url: _admin_url + "/invoice_templates/element/" + element,
                    beforesend: function() {
                        $("#preloader").fadeIn();
                    },
                    success: function(data) {
                        $("#preloader").fadeOut();
                        var json = JSON.parse(data);
                        var option_fields = json["option_fields"];

                        $(droppable).append(json["element"]);
                        var item = $(droppable).children().last();

                        new_droppable(item);

                        $(item).find('div').each(function(index, childItem) {
                            if ($(childItem).data('drop') == true) {
                                new_droppable(childItem);
                            }
                        });

                        $(item).append(option_fields);
                        $(item).attr("data-element-type", element);

                        if ($(item).data("sort") == true) {
                            $(item).sortable({
                                connectWith: ".dot-element",
                            }).disableSelection();
                        }

                        $(item).find('div').each(function(index, childItem) {
                            if ($(childItem).data('sort') == true) {
                                $(childItem).sortable({
                                    connectWith: ".dot-element",
                                }).disableSelection();
                            }
                        });

                        $(document).trigger('updateInvoiceCanvas');

                    },
                });
            } else if (draggable.data("element-type") !== "undefined") {
                //$(droppable).append(draggable);
            }
        },
    });

    $("#components ul > li").draggable({
        revert: "invalid",
        containment: "document",
        helper: "clone",
        cursor: "move",
        start: function(event, ui) {
            $(ui.helper).addClass("ui-helper");
        },
    });

    //Edit Element Click
    $(document).on("click", "#invoice-canvas div > i.fa-edit", function() {
        current_element = $(this).parent();
        var form_field =
            '<form class="submit-element-settings" autocomplete="off" method="post"><div class="row px-3 py-2">';
        form_field += $(this).parent().find(">form").html();
        form_field += "</div></form>";
        $("#main_modal .modal-title").html("Element Settings");
        $("#main_modal .modal-body").html(form_field);
        $("#main_modal").modal("show");
    });

    //Clone Element Click
    $(document).on("click", "#invoice-canvas div > i.fa-clone", function() {
        current_element = $(this).parent();
        $(current_element).after(current_element.clone());
        $(document).trigger('updateInvoiceCanvas');
    });

    //Remove Element Click
    $(document).on("click","#invoice-canvas div > i.fa-trash-alt",function() {
            Swal.fire({
                title: $lang_alert_title,
                text: $lang_alert_message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: $lang_confirm_button_text,
                cancelButtonText: $lang_cancel_button_text,
            }).then((result) => {
                if (result.value) {
                    $(this)
                        .parent()
                        .fadeOut(300, function() {
                            $(this).remove();
                            $(document).trigger('updateInvoiceCanvas');
                        });
                }
            });
        }
    );

    //Submit Settings Form
    $(document).on("submit", ".submit-element-settings", function(event) {
        event.preventDefault();
        var element_type = current_element.data("element-type");
        var form_data = $(this).serializeArray();

        $.each(form_data, function(key, field) {
            var field_name = field["name"];
            var field_value = field["value"];

            var change_class = $("." + field_name).attr("data-change-class");
            var change_action = $("." + field_name).attr("data-change-action");

            var functions = change_action.split("_");

            // If element has any parent
            if (change_class != "") {
                if (change_action == "addClass") {
                    $(current_element)
                        .find(change_class)
                        .removeClass(
                            $(current_element)
                            .find("." + field_name)
                            .val()
                        )
                        .addClass(change_class.slice(1));
                    $(current_element).removeClass(
                        $(current_element).find("." + field_name).val()
                    );
                }

                if (functions.length > 1) {
                    $(current_element).find(change_class)[functions[0]](functions[1], field_value);
                } else {
                    $(current_element).find(change_class)[functions[0]](field_value);
                    if (functions[0] == "addClass") {
                        //$(current_element)[functions[0]](field_value);
                    }
                }
            } else {
                if (change_action == "addClass") {
                    $(current_element).removeClass(
                        $(current_element).find("." + field_name).val()
                    );
                }

                if (functions.length > 1) {
                    $(current_element)[functions[0]](functions[1], field_value);
                } else {
                    $(current_element)[functions[0]](field_value);
                }
            }

            if (
                $(
                    $(current_element)
                    .find("form")
                    .find("." + field_name)
                ).is("input")
            ) {
                $(current_element)
                    .find("form")
                    .find("." + field_name)
                    .attr("value", field_value);
            } else if (
                $(
                    $(current_element)
                    .find("form")
                    .find("." + field_name)
                ).is("textarea")
            ) {
                $(current_element)
                    .find("form")
                    .find("." + field_name)
                    .html(field_value);
            } else {
                $(current_element)
                    .find("form")
                    .find("." + field_name)
                    .find(":selected")
                    .removeAttr("selected");
                $(current_element)
                    .find("form")
                    .find(
                        "." + field_name + " option[value=" + field_value + "]"
                    )
                    .attr("selected", "selected");
            }
        });

        $(document).trigger('updateInvoiceCanvas');
        $("#main_modal").modal("hide");
    });

    //Store Invoice Template
    $(document).on("click", "#store_invoice_template", function(event) {
        event.preventDefault();

        var body_code = get_body_code();
        var editor_code = get_editor_code();

        $.ajax({
            url: $("#action").attr('action'),
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                name: $("#template_name").val(),
                template_type: $("#template_type").val(),
                body: body_code,
                editor: editor_code,
                custom_css: $("#custom-css").val(),
            },
            beforesend: function() {},
            success: function(data) {
                var json = JSON.parse(JSON.stringify(data));
                if (json["result"] == "success") {
                    $.toast({
						text: json["message"],
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right'
					});

                    window.setTimeout(function () { 
                        window.location.href = _admin_url + '/invoice_templates/' + json['data']['id'] + '/edit'
                    }, 500);
                } else {
                    if (Array.isArray(json["message"])) {
                        $("#main_alert > span.msg").html("");
                        $("#main_alert")
                            .addClass("alert-danger")
                            .removeClass("alert-success");

                        jQuery.each(json["message"], function(i, val) {
                            $("#main_alert > span.msg").append(
                                "<i class='fas fa-exclamation'></i> " + val
                            );
                        });
                        $("#main_alert").css("display", "block");
                    } else {
                        $.toast({
                            text: json["message"],
                            showHideTransition: 'slide',
                            icon: 'error',
                            position: 'top-right'
                        });
                    }
                }
            },
        });
    });

    //Update Invoice Template
    $(document).on("click", "#update_invoice_template", function() {
        var body_code = get_body_code();
        var editor_code = get_editor_code();

        $.ajax({
            url: $("#action").val(),
            method: "PATCH",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                name: $("#template_name").val(),
                template_type: $("#template_type").val(),
                body: body_code,
                editor: editor_code,
                custom_css: $("#custom-css").val(),
            },
            beforesend: function() {},
            success: function(data) {
                var json = JSON.parse(JSON.stringify(data));
                if (json["result"] == "success") {
                    $.toast({
						text: json["message"],
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right'
					});
                } else {
                    if (Array.isArray(json["message"])) {
                        $("#main_alert > span.msg").html("");
                        $("#main_alert")
                            .addClass("alert-danger")
                            .removeClass("alert-success");

                        jQuery.each(json["message"], function(i, val) {
                            $("#main_alert > span.msg").append(
                                '<i class="far fa-times-circle"></i> ' +
                                val +
                                "<br>"
                            );
                        });
                        $("#main_alert").css("display", "block");
                    } else {
                        $.toast({
                            text: json["message"],
                            showHideTransition: 'slide',
                            icon: 'error',
                            position: 'top-right'
                        });
                    }
                }
            },
        });
    });
})(jQuery);

function new_droppable(elem) {
    $(elem).droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        greedy: true,
        drop: function(event, ui) {
            var droppable = $(this);
            var draggable = ui.draggable;
            var element = draggable.data("element");
            if (typeof element !== "undefined") {
                $.ajax({
                    url: _admin_url + "/invoice_templates/element/" + element,
                    beforesend: function() {
                        $("#preloader").fadeIn();
                    },
                    success: function(data) {
                        $("#preloader").fadeOut();
                        var json = JSON.parse(data);
                        var option_fields = json["option_fields"];

                        $(droppable).append(json["element"]);
                        var item = $(droppable).children().last();

                        new_droppable(item);

                        $(item).find('div').each(function(index, childItem) {
                            if ($(childItem).data('drop') == true) {
                                new_droppable(childItem);
                            }
                        });

                        $(item).append(option_fields);
                        $(item).attr("data-element-type", element);

                        if ($(item).data("sort") == true) {
                            $(item).sortable({
                                connectWith: ".dot-element",
                            }).disableSelection();
                        }
                        $(document).trigger('updateInvoiceCanvas');
                    },
                });
            } else if (draggable.data("element-type") !== "undefined") {
                //$(droppable).append(draggable);
            }
        },
    });
}

function reset_ui(){
    $( "#invoice-canvas div").each(function( key, value ) {
		$(this).droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			greedy: true,
			drop: function (event, ui) {
				var droppable = $(this);
				var draggable = ui.draggable;
				var element = draggable.data('element');
				if(typeof element  !== "undefined"){
					$.ajax({
						url: _admin_url + '/invoice_templates/element/' + element,
						beforesend: function(){
							$("#preloader").fadeIn();
						},success: function(data){
							$("#preloader").fadeOut();
							var json = JSON.parse(data);
							var option_fields = json['option_fields'];
							
							$(droppable).append(json['element']);
							var item = $(droppable).children().last();

							new_droppable(item);

							$(item).find('div').each(function(index, childItem) {
								if ($(childItem).data('drop') == true) {
									new_droppable(childItem);
								}
							});

							$(item).append(option_fields);
							$(item).attr("data-element-type", element);

							if ($(item).data("sort") == true) {
								$(item).sortable({
									connectWith: ".dot-element",
								}).disableSelection();
							}

							$(item).find('div').each(function(index, childItem) {
								if ($(childItem).data('sort') == true) {
									$(childItem).sortable({
										connectWith: ".dot-element",
									}).disableSelection();
								}
							});

							$(document).trigger('updateInvoiceCanvas');
					
						}
					});
				}else if(draggable.data('element-type') !== "undefined"){
					//$(droppable).append(draggable);
				}  	
			}
		});
	});

	$('.ui-sortable').sortable({
		connectWith: ".dot-element",
		update: function( event, ui ) {
            $(document).trigger('updateInvoiceCanvas');
        }
	}).disableSelection();
}

function get_body_code() {
    $("#invoice-canvas div").removeClass("toggle-preview");
    $("#invoice-canvas i.far").removeAttr("style");
    var canvas = $("#invoice-canvas").clone();
    $(canvas).find('div').removeClass("ui-droppable-hover ui-state-hover ui-droppable ui-sortable ui-sortable-handle");
    canvas.find('#invoice-item-table thead').html('<!--$invoice_items_header-->');
    canvas.find('#invoice-item-table tbody').html('<!--$invoice_items-->');
    canvas.find('.element-invoice-summary-table tbody').html('<!--$invoice_summary-->');
    canvas.find('.element-payment-history-table tbody').html('<!--$invoice_payment_history-->');
    canvas.find('.element-logo').after('<!--$company_logo-->');
    canvas.find('.element-logo').remove();
    canvas.find("form").remove();
    canvas.find(".fa-trash-alt").remove();
    canvas.find(".fa-edit").remove();
    canvas.find(".fa-clone").remove();
    return $.trim(canvas.html());
}

function get_editor_code() {
    $("#invoice-canvas div").removeClass("toggle-preview");
    $("#invoice-canvas i.far").removeAttr("style");
    $("#invoice-canvas div").removeClass("ui-droppable-hover ui-state-hover");
    var canvas = $("#invoice-canvas");
    return canvas.html();
}