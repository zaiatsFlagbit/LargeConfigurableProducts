/**
 * Copyright © Flagbit, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// NOT CONNECTED. IN CASE configurable-products-matrix is really needed.
define([
    'underscore',
    'jquery',
], function (_, $) {
    'use strict';

    let mixin = {
        defaults: {
            endpoint: '/rest/all/V1/product-matrix'
        },

        _get: function(key) {
            return this.source.get(`data.${key}`);
        },

        _set: function(data, key, prop = null) {
            _.each(data, (record) => {
                this._get(key).push(prop ? record[`${prop}`] : record)
            });
        },

        /**
         * async GET paginated variations from server
         *
         * @param {Object} data
         * @returns {XMLHttpRequest}
         */
        /*
        * @TODO Abstract. same as in dynamic-rows-optimized.
        * Take into account added or deleted product, changing request offset - which is not correct
        */
        getMissingAssociatedProducts: function (data) {
            return $.ajax({
                url: this.endpoint,
                type: 'GET',
                showLoader: true,
                data,
                dataType: 'json'
            })
        },

        /*
        * @TODO Abstract. same as in dynamic-rows-optimized.
         */
        /**
         * Get variations limit for a request
         * @returns {Object}
         */
        _getData: function () {
            let recordsLoaded = this._get('configurable-matrix').length;
            let recordsLeft = this._get('assoc_prod_total') - recordsLoaded;

            return {
                sku: this._get('product.sku'),
                offset: recordsLoaded,
                limit: recordsLeft
            };
        },

        /*
        * @TODO DRY. simplify.
        */
        /**
         * Check if all associated products loaded
         * @returns {Boolean}
         */
        _isSourceFull: function () {
            return this._get('configurable-matrix').length === this._get('assoc_prod_total');
        },

        /*
        * @TODO DRY this code. Parent method call, super throws exception
        *  use a wrapper instead.
       */
        save: function (){
            this.formElement().validate();

            if (this.formElement().source.get('params.invalid') === false) {
                this.serializeData();
            }

            if (this.checkForNewAttributes()) {
                this.formSaveParams = arguments;
                this.attributeSetHandlerModal().openModal();
            } else {
                if (this.validateForm(this.formElement())) {
                    this.clearOutdatedData();
                }
                this.formElement().save(arguments[0], arguments[1]);

                if (this.formElement().source.get('params.invalid')) {
                    this.unserializeData();
                }
            }
        },

        /**
         * @TODO DRY this up.
         * Chose action for the form save button
         */
        saveFormHandler: function () {
            if (!this._isSourceFull()) {
                let payload = this._getData();
                let request = this.getMissingAssociatedProducts(payload);
                $.when(request)
                    .then(
                        data => {
                            if (data && data.length > 0) {
                                this._set(data, 'configurable-matrix');
                                this._set(data, 'associated_product_ids', 'id' );
                                this.save();
                            }
                        },
                        error => {
                            alert("Request error: " + JSON.stringify(error.message));
                        }
                    );
            } else {
                this.save();
            }
        },
    };

    return function (target) {
        return target.extend(mixin);
    };

});
