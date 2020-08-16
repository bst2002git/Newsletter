/**
 * @api
 */
define([
    'jquery',
    'mage/storage',
], function ($, storage) {
    'use strict';

    return function (email, deferred) {
        deferred = deferred || $.Deferred();

        return storage.get(
            'rest/V1/majidiannewsletter/subscribe?email=' + email,
            false
        ).done(function (response) {
            deferred.resolve(response);
        }).error(function (response) {
            deferred.reject();
        }).always(function () {
        });
    };
});
