function get_purchase_product(productId, selectType, quantity = 1) {
    if (productId == null) {
        return;
    }

    let customer_id = $('.customer_id_field').val();

    if (customer_id == '') {
        alert('Before selecting a product, you need to select the customer');
        $("#products").val(null).trigger("change");
        return;
    }

    $.ajax({
        url: _admin_url + "/products/getProduct/" + productId,
        data: {
            'customer_id': customer_id,
        },
        beforeSend: function () {
            $("#preloader").css("display", "block");
        },
        success: function (data) {
            $("#preloader").css("display", "none");
            var json = JSON.parse(JSON.stringify(data));
            var row = $("#copy-line").clone().removeAttr("id");

            // Check Stock is not available
            if (selectType == 'sell') {
                if (
                    json["product"]["type"] == "product" &&
                    json["product"]["stock_management"] == "1"
                ) {
                    var available_quantity = parseFloat(
                        json["product"]["stock"]
                    );
                    var invoice_quantity = 0;

                    $("#invoice-table > tbody > tr").each(function (
                        index,
                        elem
                    ) {
                        invoice_quantity += parseFloat(
                            $(elem).find(".quantity").val()
                        );
                    });

                    if (invoice_quantity >= available_quantity) {
                        Swal.fire({
                            text: $lang_stock_out,
                            icon: "warning",
                        });

                        $("#products").val(null).trigger("change");
                        return;
                    }
                    $(row).find(".quantity").prop("max", available_quantity);
                    if (available_quantity == 1) {
                        $(row).find(".quantity").prop("readonly", true);
                    }
                }
            }

            $(row)
                .find(".product_taxes")
                .attr("name", `taxes[${json["product"]["id"]}][]`);

            if (selectType == 'sell') {
                var sellingPrice = json["product"]["selling_price"]; // Selling Price
            } else {
                var sellingPrice = json["product"]["purchase_cost"]; // Purchase Price
            }

            decimalPlace = json["decimal_place"];

            $(row).find(".product_id").val(json["product"]["id"]);
            $(row).find(".product_type").val(json["product"]["type"]);
            $(row).find(".product_name").val(json["product"]["name"]);
            $(row).find(".description").text(json["product"]["descriptions"]);
            $(row).find(".quantity").val(quantity);
            $(row).find(".unit_cost").val(parseFloat(sellingPrice).toFixed(decimalPlace));
            $(row).find(".sub_total").val(parseFloat(sellingPrice * 1).toFixed(decimalPlace));

            //Append Row
            $("#invoice-table tbody").append(row);

            $.fn.calculateTotal();

            $("#products").val(null).trigger("change");
        },
    });
}

(function ($) {
    "use strict";

    var decimalPlace = 2; //Default Decimal Places

    $(document).on("change", "#products", function (e) {
        var productId = $(this).val();
        var selectType = $(this).data("type");

        get_purchase_product(productId, selectType);

    });


    $(document).on("keyup change", ".unit_cost", function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }
        if (typeof parseFloat($(this).val()) == "number") {
            $.fn.calculateTotal();
        }
    });

    $(document).on("keyup change", ".quantity", function () {
        if ($(this).val() == "") {
            $(this).val(1);
        }
        if (typeof parseFloat($(this).val()) == "number") {
            $.fn.calculateTotal();
        }
    });

    $(document).on("paste", ".quantity", function (e) {
        e.preventDefault();
    });

    //Select Tax
    $(document).on("change", ".product_taxes", function (event) {
        var elem = $(this);
        $(".tax-field").remove();

        $("#invoice-table > tbody > tr").each(function (index, elem) {
            $.each(
                $(elem).find(".product_taxes").val(),
                function (index, value) {
                    var tax_name = $(elem)
                        .find('option[value="' + value + '"]')
                        .data("tax-name");
                    var tax_rate = $(elem)
                        .find('option[value="' + value + '"]')
                        .data("tax-rate");

                    if (!$("#tax-" + value).length) {
                        $("#after-tax")
                            .before(`<div class="form-group row tax-field">
                        <label class="col-md-6 col-form-label">${tax_name}</label>						
                        <div class="col-md-6">
                            <input type="text" class="form-control text-md-right tax-input-field" name="tax_amount[${value}]" id="tax-${value}" value="0" readonly>
                        </div>
                    </div>`);
                    }
                }
            );
        });

        $.fn.calculateTotal();
    });

    $(document).on("keyup", "#discount_value", function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }
        if (typeof parseFloat($(this).val()) == "number") {
            $.fn.calculateTotal();
        }
    });

    $(document).on("change", "#discount_type", function () {
        $.fn.calculateTotal();
    });

    $(document).on("click", ".btn-remove-row", function () {
        $(this)
            .closest("tr")
            .fadeOut("normal", function () {
                $(this).remove();
                $(".product_taxes").trigger("change");
                $.fn.calculateTotal();
            });
    });

    $.fn.calculateTotal = function () {
        var subTotal = 0;
        var taxAmount = 0;
        var discountAmount = 0;
        var grandTotal = 0;
        $(".tax-input-field").val(0);

        $("#invoice-table > tbody > tr").each(function (index, elem) {
            //Calculate Sub Total
            var line_qnt = parseFloat($(elem).find(".quantity").val());
            var line_unit_cost = parseFloat($(elem).find(".unit_cost").val());
            var line_total = parseFloat(line_qnt * line_unit_cost);
            $(elem).find(".sub_total").val(line_total.toFixed(decimalPlace));

            //Show Sub Total
            subTotal = parseFloat(subTotal + line_total);

            //Calculate Taxes
            $.each($(elem).find(".product_taxes").val(), function (index, value) {
                    var tax_rate = $(elem)
                        .find('option[value="' + value + '"]')
                        .data("tax-rate");
                    var product_tax = (line_total / 100) * tax_rate;

                    //Find Tax Field
                    if ($("#tax-" + value).length) {
                        var existingTaxAmount = parseFloat($("#tax-" + value).val());
                        var newTaxAmount = existingTaxAmount + product_tax;
                        $("#tax-" + value).val(newTaxAmount.toFixed(decimalPlace));
                        taxAmount = newTaxAmount;
                    }
                }
            );

            //Calculate Discount
            if ($("#discount_type").val() == "0") {
                discountAmount =
                    (subTotal / 100) * parseFloat($("#discount_value").val());
            } else if ($("#discount_type").val() == "1") {
                discountAmount = parseFloat($("#discount_value").val());
            }

            //Calculate Grand Total
            grandTotal = (subTotal + taxAmount) - discountAmount;
        });

        $("#sub_total").val(subTotal.toFixed(decimalPlace));
        $("#discount").val(parseFloat(discountAmount).toFixed(decimalPlace));
        $("#grand_total").val(parseFloat(grandTotal).toFixed(decimalPlace));
    };
})(jQuery);
