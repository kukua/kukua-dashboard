(function(helpers) {
    'use strict';

    helpers.onDomReady = function() {
        helpers.forecast();

        helpers.confirmDelete()
        helpers.confirmDisable()
        helpers.confirmRevoke()
        helpers.confirmGrant()
        helpers.feedbackDisplay()
        helpers.feedbackPost()
        helpers.locationPost()
    };

    helpers.forecast = function() {
        $("#js-forecast-country").on('change', function () {
            var postCountry = {
                "country": $(this).val()
            }
            var call = $.ajax({
                type: 'POST',
                url: '/forecast/get/',
                data: postCountry,
                dataType: 'json'
            })

            call.done(function(request) {
                $(".js-iframe").html('<iframe src="' + request.url + '" frameborder="0" width="802px" height="802px"></iframe>');
            })
        }).trigger("change");
    }

    /**
     * Display confirm box on delete
     */
    helpers.confirmDelete = function() {
        $(".js-confirm-delete").unbind("click");
        $(".js-confirm-delete").on("click", function() {
            var txt = "Are you sure you want to delete this?";
            if ($(this).data("text")) {
                txt = $(this).data("text");
            }
            var x = confirm(txt)
            if (x == true) {
                return true
            }
            return false
        })
    };

    /**
     * Display confirm box on disable
     */
    helpers.confirmDisable = function() {
        $(".js-confirm-disable").on("click", function() {
            var x = confirm("Are you sure you want to disable this?")
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

    helpers.locationPost = function() {
        var countrySelect = $("#js-location-post");
        countrySelect.on("change", function() {
            $(".input-country-id").attr("value", $(this).val());
            $.ajax({
                type: "POST",
                url: "/locations/get",
                data: {country: countrySelect.val()},
                postData: "json",
                beforeSend: function() {
                    $(".js-table-result").html("<tr><td class='loading' colspan='2'></td></tr>");
                },
                success: function(data) {
                    $(".js-table-result").html("");
                    var result = JSON.parse(data)
                    $.each(result, function(key, value) {
                        var statusClass = ""
                        if (value.active == 0) {
                            statusClass = "bg-info";
                            var enable_or_disable = "<a href='/locations/enable/" + value.id + "' class='text-info'><i class='glyphicon glyphicon-eye-open'></i></a> "
                        } else {
                            var enable_or_disable = "<a href='/locations/disable/" + value.id + "' class='js-confirm-disable text-info'><i class='glyphicon glyphicon-eye-close'></i></a> "
                        }
                        $(".js-table-result").append(
                            "<tr class='" + statusClass + "'>" +
                                "<td>" + value.name + "</td>" +
                                "<td>" + value.station_id + "</td>" +
                                "<td class='text-right'>" +
                                    enable_or_disable +
                                    "<span data-station_id='" + value.id + "' class='js-remove-station text-danger pointer'><i class='glyphicon glyphicon-remove'></i></span> " +
                                "</td>" +
                            "</tr>"
                        )
                    })
                    helpers.confirmDisable()
                    helpers.removeStation()
                }
            });
        }).trigger("change");
    }

    helpers.removeStation = function() {
        var item = $(".js-remove-station");
        item.on("click", function(e) {
            var $this = $(this);
            var x = confirm("Are you sure you want to delete this station?")
            if (x == true) {
                $.ajax({
                    type: "POST",
                    url: "/locations/delete_station/" + item.data("station_id"),
                    data: $("#stationForm").serialize(),
                    postData: "json",
                    success: function(data) {
                        $this.parent("td").parent("tr").fadeOut()
                    }
                });
            }
            return false
        })
    }
})(window.helpers = window.helpers || {});
$(document).ready(helpers.onDomReady);
