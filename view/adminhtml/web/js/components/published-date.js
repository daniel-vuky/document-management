define([
    'jquery',
    'ko',
    'uiRegistry',
    'moment',
    "Magento_Ui/js/form/element/date",
], function ($, ko, uiRegistry, moment, Component) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            var self = this;
            uiRegistry.get(this.parentName + '.status', function (status) {
                self.statusChanged(status.value());
            });
            return this;
        },

        statusChanged: function (value) {
            this.disabled(Number(value) === 1);
        }
    });
});

