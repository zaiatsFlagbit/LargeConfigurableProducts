/**
 * Copyright © Flagbit, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['jquery'], function ($) {
        'use strict';

        let mixin = {
            /**
             /* @TODO - get endpoint from php setup
             /* admin user must pick a DataSet himself,
             /* from an input
             */
            defaults: {
                assocProdIDs: 'data.assoc_prod_ids',
                pageSize: 5,
                endpoint: '/rest/all/V1/product-matrix',
                dataSet: 100, // move abstract config
                realPages: 1
            },

            /**
             * Filtering data and calculates the quantity of pages
             *
             * fake and real page
             * @param {Array} data
             * @returns void
             */
            parsePagesData: function (data) {
                let fakePages;

                this.relatedData = this.deleteProperty ?
                    _.filter(data, function (elem) {
                        return elem && elem[this.deleteProperty] !== this.deleteValue;
                    }, this) : data;

                this.realPages = Math.ceil(this.relatedData.length / this.pageSize) || 1;
                fakePages = Math.ceil(this.source.get('data.assoc_prod_total') / this.pageSize) || 1;

                this.pages(fakePages);
            },

            /**
             * async GET paginated variations from server
             *
             * @param {Object} data
             * @returns {XMLHttpRequest}
             */
            getNextVariations: function (data) {
                return $.ajax({
                    url: this.endpoint,
                    type: 'GET',
                    data,
                    dataType: 'json'
                })
            },

            /**
             * Add loaded variations to dataSource
             *
             * @param {Array} nextVariations - product matrix paginated from server
             * @returns void
             */
            addBulkVariations: function (nextVariations) {
                let variation, underlyingRecords = this.recordData();
                for (let i = 0, j = nextVariations.length; i < j; i++) {
                    variation = nextVariations[i];
                    underlyingRecords.push(variation);
                }
                this.recordData.valueHasMutated();
            },

            /**
             * Generate associated products
             *
             * @returns void
             */
            generateAssociatedProducts: function () {
                let productsIds = this.source.get(`${this.assocProdIDs}`);
                this.source.set(this.dataScopeAssociatedProduct, productsIds);
            },

            /**
             * Change page
             *
             * @param {Number} page - current page
             * @returns {Boolean}
             */
            changePage: function (page) {
                this.clear();
                page = parseInt(page, 10);

                if (page === 1 && !this.recordData().length) {
                    return false;
                }

                if (page > this.pages()) {
                    this.currentPage(this.pages());

                    return false;
                } else if (page < 1) {
                    this.currentPage(1);

                    return false;
                }

                if ((page - this.realPages) > 0) {
                    let variationsMissing = (page * this.pageSize) - this.recordData().length;
                    let data = this._getData();
                    data['limit'] = variationsMissing > this.dataSet ? variationsMissing : this.dataSet;
                    this.showNextVariations(
                        this.getNextVariations(data),
                        page
                    );
                }

                this.initChildren();

                return true;
            },

            /**
             * Change page to next
             * @returns void
             */
            nextPage: function () {
                let page = ~~this.currentPage() + 1;

                if (!this._isBoundPage()) {
                    this.currentPage(page);
                } else {
                    this.showNextVariations(
                        this.getNextVariations(
                            this._getData()
                        ),
                        page
                    );
                }
            },

            /**
             * Awaits ajax reponse from server if page load new variations needed
             * sets currentPage observable if success
             *
             * @param {XMLHttpRequest} request
             * @param {Number} toPage - destination page
             * @returns void
             */
            showNextVariations: function (request, toPage) {
                this.clear();
                this.showSpinner(true);
                /*@TODO might be a non-blocking, but what would user do without data I wonder*/
                $.when(request)
                    .then(
                        data => {
                            if (data && data.length > 0) {
                                this.addBulkVariations(data);
                                this.currentPage(parseInt(toPage, 10));
                                this.initChildren();
                            }
                        },
                        error => {
                            alert("Request error: " + JSON.stringify(error.message));
                            this.showSpinner(false);
                        }
                    );
            },

            /**
             * Check if page load new variations needed
             * @returns {Boolean}
             */
            _isBoundPage: function () {
                return !this._isSourceFull() && this.currentPage() === this.realPages;
            },

            /**
             * Get variations limit for a request
             * @returns {Object}
             */
            _getData: function () {
                let dataLimit,
                    recordsLeft = this.source.get('data.assoc_prod_total') - this.getRecordCount(),
                    limitReached = this.getRecordCount() > recordsLeft;

                if (limitReached) {
                    dataLimit = recordsLeft;
                } else {
                    dataLimit = this.dataSet
                }

                return {
                    sku: this.source.get('data.product.sku'),
                    offset: this.getRecordCount(),
                    limit: dataLimit
                };
            },

            /**
             * Check if all variations loaded
             * @returns {Boolean}
             */
            _isSourceFull: function () {
                return this.getRecordCount() === this.source.get('data.assoc_prod_total');
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);
