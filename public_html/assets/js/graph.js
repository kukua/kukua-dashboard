(function(kukua) {
    'use strict';
    kukua.onDomReady = function() {
        //Initializations
        kukua.datePickerInit();

        //Actions
        kukua.swapPanel();
        kukua.swapDatePicker();
        kukua.swapDashboard();

        kukua.refreshed();
    };

    kukua.refreshed = function() {
        if (window.location.hash == "#refresh") {
            var dashboard = kukua.getDashboard();
            var panelId   = kukua.getPanelId();
            var dates     = kukua.getDates();
            kukua.reloadGraph(dashboard, panelId, dates.from, dates.to);
        }
    };

    kukua.datePickerInit = function() {
        var input_from = kukua.getDatePickerFrom();
        var input_to =   kukua.getDatePickerTo();
        var datePickerOptions = {
            format: "YYYY/MM/DD",
            useCurrent: false,
            viewMode: 'years',
            ignoreReadonly: true
        };
        input_from.datetimepicker(datePickerOptions);
        input_to.datetimepicker(datePickerOptions);
    };

    kukua.swapPanel = function() {
        $("#js-graph-type-swap").on('change', function() {
            var dashboard = kukua.getDashboard();
            var dates     = kukua.getDates();
            var panelId   = $(this).val();
            kukua.reloadGraph(dashboard, panelId, dates.from, dates.to);
        })
    };

    kukua.swapDatePicker = function() {
        kukua.getDatePickerFrom().on("dp.change", function (e) {
            kukua.getDatePickerTo().data("DateTimePicker").minDate(e.date);
            var dashboard = kukua.getDashboard();
            var panelId   = kukua.getPanelId();
            var min       = $(this).val();
            var max       = kukua.getDatePickerTo().val();
            kukua.reloadGraph(dashboard, panelId, min, max);
        })
        kukua.getDatePickerTo().on("dp.change", function (e) {
            kukua.getDatePickerFrom().data("DateTimePicker").maxDate(e.date);
            var dashboard = kukua.getDashboard();
            var panelId   = kukua.getPanelId();
            var min       = kukua.getDatePickerFrom().val();
            var max       = $(this).val();
            kukua.reloadGraph(dashboard, panelId, min, max);
        })
    };

    kukua.swapDashboard = function () {
        $("#js-graph-location-swap").on("change", function() {
            var dates = kukua.getDates();
            var panelId = kukua.getPanelId();
            var dashboard = $(this).val();
            kukua.reloadGraph(dashboard, panelId, dates.from, dates.to);
        })
    };

    kukua.getDatePickerFrom = function() {
        return $("#js-datetimepicker-min");
    };
    kukua.getDatePickerTo = function() {
        return $("#js-datetimepicker-max");
    };
    kukua.getDashboard = function() {
        return $("#js-graph-location-swap").val();
    };
    kukua.getPanelId = function() {
        return $("#js-graph-type-swap").val();
    };
    kukua.getDates = function() {
        var dateFrom    = moment().subtract("8", "days").format("YYYYMMDD");
        var dateTo      = moment().subtract("1", "day").format("YYYYMMDD");
        var min         = $("#js-datetimepicker-min").val();
        var max         = $("#js-datetimepicker-max").val();
        if (min != "" && max != "") {
            dateFrom = min;
            dateTo = max;
        }
        return {"from": dateFrom, "to": dateTo};
    };

    kukua.reloadGraph = function (dashboard, panelId, dateFrom, dateTo) {
        var from = dateFrom.replace(/\//gi,"");
        var to   = dateTo.replace(/\//gi,"");

        if (dashboard != "") {
            var dashboard = "_" + dashboard;
        }
        var hostname      = "dashboard.kukua.cc";
        var pre_dashboard = $("#js-graph").data("user");
        var frameUrl      = "http://" + hostname + ":9000/dashboard-solo/db/" + pre_dashboard + dashboard + "?panelId=" + panelId + "&fullscreen&edit&from=" + from + "&to=" + to + "&theme=light";
        $("#js-graph").attr("src", function(i, val) { return frameUrl });
    };

})(window.kukua = window.kukua || {});
$(document).ready(kukua.onDomReady);
