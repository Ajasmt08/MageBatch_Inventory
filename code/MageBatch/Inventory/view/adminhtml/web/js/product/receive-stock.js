define([
    'jquery',
    'Magento_Ui/js/form/components/button',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'domReady!'
], function ($, Button, $t, alert) {
    'use strict';

    return Button.extend({
        defaults: {
            ajaxUrl: '',
        },

        receiveStock: function () {
            var self = this,
                fieldset = $('[data-index="batch_stock"]'),
                data = {};

            if (!fieldset.length) {
                alert({content: $t('Could not find form fields.')});
                return;
            }

            data.form_key = window.FORM_KEY;
            data.source_code = fieldset.find('select').val() || '';
            data.batch_number = fieldset.find('input[name="batch_number"]').val() || '';
            data.qty = fieldset.find('input[name="qty"]').val() || '';
            data.expiry_date = fieldset.find('input[name="expiry_date"]').val() || '';
            data.manufacturing_date = fieldset.find('input[name="manufacturing_date"]').val() || '';
            data.supplier = fieldset.find('input[name="supplier"]').val() || '';
            data.purchase_order = fieldset.find('input[name="purchase_order"]').val() || '';
            data.cost_price = fieldset.find('input[name="cost_price"]').val() || '';
            data.notes = fieldset.find('textarea').val() || '';

            if (!data.batch_number || !data.qty || !data.expiry_date) {
                alert({content: $t('Please fill in Batch Number, Quantity, and Expiry Date.')});
                return;
            }

            $.ajax({
                url: self.ajaxUrl,
                type: 'POST',
                data: data,
                dataType: 'json',
                showLoader: true,
                success: function (response) {
                    if (response.success) {
                        alert({content: $t(response.message), title: $t('Success')});
                        fieldset.find('input[name="batch_number"], input[name="qty"], input[name="expiry_date"], input[name="manufacturing_date"], input[name="supplier"], input[name="purchase_order"], input[name="cost_price"], textarea').val('');
                    } else {
                        alert({content: $t(response.message), title: $t('Error')});
                    }
                },
                error: function () {
                    alert({content: $t('An error occurred while receiving stock.'), title: $t('Error')});
                }
            });
        }
    });
});
