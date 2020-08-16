define([
    'ko',
    'jquery',
    'uiComponent',
    'Majidian_Newsletter/js/action/subscribe',
    'mage/validation'
], function (ko, $, Component, subscribeAction) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Majidian_Newsletter/newsletter',
        },
        validateForm: function (form) {
            return $(form).validation() && $(form).validation('isValid');
        },
        initObservable: function () {
            this._super();
            this.email = ko.observable('Enter Email Address');
            return this;
        },
        subscribe: function () {
            var self = this;
            var body = $('body').loader();
            body.loader('show');

            //validate form
            if (!this.validateForm('#majidiannewsletter')) {
                body.loader('hide');
                return;
            }
            $.when(
                subscribeAction(self.email())
            ).done(
                function (response) {
                    body.loader('hide');
                    if (response === '1') {
                        alert('Thank you for subscribing!');
                    } else if (response === '2') {
                        alert('Already subscribed!');
                    } else {
                        alert('Something went wrong. Please try again!');
                    }
                }
            ).fail(
                function () {
                    body.loader('hide');
                }
            );
        }
    });
});
