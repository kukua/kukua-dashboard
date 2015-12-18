(function(helpers) {
    'use strict';

    helpers.onDomReady = function() {
        helpers.confirmDeleteUser()
        helpers.confirmRevoke()
        helpers.confirmGrant()
    };

    /**
     * Display confirm box on user delete
     */
    helpers.confirmDeleteUser = function() {
        $(".js-confirm-delete").on("click", function() {
            var x = confirm("Are you sure you want to delete this user?")
            if (x == true) {
                return true
            }
            return false
        })
    };

    helpers.confirmRevoke = function() {
        $(".js-confirm-revoke").on("click", function() {
            var x = confirm("Are you sure you want to revoke these rights?")
            if (x == true) {
                return true
            }
            return false
        })
    };

    helpers.confirmGrant = function() {
        $(".js-confirm-grant").on("click", function() {
            var x = confirm("Are you sure you want to grant these rights?")
            if (x == true) {
                return true
            }
            return false
        })
    };

})(window.helpers = window.helpers || {});
$(document).ready(helpers.onDomReady);
