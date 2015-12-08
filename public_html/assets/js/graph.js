(function(kukua) {
    'use strict';
    kukua.onDomReady = function() {

        //Initializations
        kukua.datePickerInit();

        //onChange reload graph
        kukua.formChanges();

        //Render first onDomReady
        kukua.graph();
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

    kukua.graph = function() {
        var graphType       = kukua.getGraphType();
        var graphTypeText   = kukua.getGraphTypeText();
        var graphDate       = kukua.getDates();

        var options = chart.getOptions();
        options.chart.zoomType = 'x';
        options.title.text = graphTypeText

        switch(graphType.val()) {
            case 'temp':
                options.chart.type = "line";
                options.yAxis.title.text = graphTypeText + " (°C)"
                break;
            case 'rain':
                options.chart.type = "column";
                options.yAxis.title.text = graphTypeText + " (mm)"
                break;
        }
        chart.render("#chart", "/graph/build/" + graphType.val() + "/5m", options);
    };

    kukua.formChanges = function() {
        kukua.getDatePickerFrom().on("dp.change", function() {
            kukua.graph()
        });
        kukua.getDatePickerTo().on("dp.change", function() {
            kukua.graph()
        });
        $("#js-graph-type-swap").on("change", function() {
            kukua.graph()
        });
    };

    //Get values
    kukua.getDatePickerFrom = function() {
        return $("#js-datetimepicker-min")
    };
    kukua.getDatePickerTo = function() {
        return $("#js-datetimepicker-max")
    };
    kukua.getGraphType = function() {
        return $("#js-graph-type-swap")
    };
    kukua.getGraphTypeText = function() {
        var input = kukua.getGraphType()
        return input.find("option:selected").text()
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
})(window.kukua = window.kukua || {});
$(document).ready(kukua.onDomReady);
