(function(helpers) {
    'use strict';

    helpers.onDomReady = function() {
        helpers.confirmDelete()
        helpers.confirmRevoke()
        helpers.confirmGrant()
        helpers.feedbackDisplay()
        helpers.feedbackPost()
    };

    /**
     * Display confirm box on user delete
     */
    helpers.confirmDelete = function() {
        $(".js-confirm-delete").on("click", function() {
            var x = confirm("Are you sure you want to delete this?")
            if (x == true) {
                return true
            }
            return false
        })
    };

    /**
     * Display confirm box on user access revoke
     */
    helpers.confirmRevoke = function() {
        $(".js-confirm-revoke").on("click", function() {
            var x = confirm("Are you sure you want to revoke these rights?")
            if (x == true) {
                return true
            }
            return false
        })
    };

    /**
     * Display confirm box on user access grant
     */
    helpers.confirmGrant = function() {
        $(".js-confirm-grant").on("click", function() {
            var x = confirm("Are you sure you want to grant these rights?")
            if (x == true) {
                return true
            }
            return false
        })
    };

    /**
     * Display feedback box
     */
    helpers.feedbackDisplay = function() {
        $(".js-feedback").on("click", function(e) {
            e.preventDefault();
            if ($(this).parent().hasClass("open")) {
                $(".js-feedback").parent().removeClass("open");
            } else {
                $(".js-feedback").parent().addClass("open");
            }
        })
    };

    /**
     * Feedback post ajax handle
     */
    helpers.feedbackPost = function() {
        var form = $('.js-post-feedback');
        form.on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url:  "/feedback/create",
                data: form.serialize(),
                postData: "json",
                success: function(data) {
                    var result = JSON.parse(data)
                    if (result.success == true) {
                        $(".js-feedback-result").html("<i class='glyphicon glyphicon-ok'></i> Thanks for your feedback!")
                        $(".js-feedback-submit").attr("disabled", "disabled");
                    } else {
                        $(".js-feedback-result").html(result.message)
                    }
                }
            });
        });
    };

})(window.helpers = window.helpers || {});
$(document).ready(helpers.onDomReady);
