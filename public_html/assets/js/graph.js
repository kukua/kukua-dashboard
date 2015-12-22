(function(kukua) {
    'use strict';
    kukua.onDomReady = function() {

        //Initializations
        kukua.datePickerInit()
        kukua.datePickerCallback(moment(), moment())

        //onChange reload graph
        kukua.formChanges()

        //Render first onDomReady
        kukua.graph()
        kukua.forecast()
    };

    kukua.datePickerInit = function() {
        kukua.getDateRangePicker().daterangepicker({
            ranges: kukua.getDatePickerRanges()
        }, kukua.datePickerCallback)
    };

    kukua.getDatePickerRanges = function() {
        return {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    };

    kukua.datePickerCallback = function(start, end) {
        var startDate = start.startOf('day')
        var endDate   = end.endOf('day')

        kukua.getDateRangePickerSpan().html(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'))
        $('input#dateFrom').val(startDate.format("X"))
        $('input#dateTo').val(endDate.format("X"))
    };

    kukua.forecast = function() {
        var graphType       = kukua.getGraphType()
        var graphTypeText   = kukua.getGraphTypeText()
        var graphInterval   = kukua.getGraphInterval()

        var options = chart.getOptions()
        options.chart.zoomType = 'x'
        options.title.text = graphTypeText

        switch(graphType.val()) {
            case 'temp':
                options.chart.type = "line"
                options.yAxis.title.text = graphTypeText + " (°C)"
                break
            case 'rain':
                options.chart.type = "column"
                options.yAxis.title.text = graphTypeText + " (mm)"
                break
        }
        chart.temp("#chart-forecast", "/graph/forecast/" + graphType.val(), options)
    };

    kukua.graph = function() {
        var graphType       = kukua.getGraphType()
        var graphTypeText   = kukua.getGraphTypeText()
        var graphInterval   = kukua.getGraphInterval()

        var options = chart.getOptions()
        options.chart.zoomType = 'x'
        options.title.text = graphTypeText

        switch(graphType.val()) {
            case 'temp':
                options.chart.type = "line"
                options.yAxis.title.text = graphTypeText + " (°C)"
                break
            case 'rain':
                options.chart.type = "column"
                options.yAxis.title.text = graphTypeText + " (mm)"
                break
        }
        chart.render("#chart", "/graph/build/" + graphType.val() + "/" + graphInterval.val() + "/", options)
    };

    kukua.datePickerChange = function() {
        //Date range select
        kukua.getDateRangePicker().on("apply.daterangepicker", function(ev, picker) {
            kukua.graph()
        })
    };

    kukua.formChanges = function() {
        kukua.datePickerChange();
        kukua.getGraphInterval().on("change", function() {
            kukua.graph()
        })
        kukua.getGraphType().on("change", function() {
            kukua.graph()
        })
    };

    kukua.getGraphInterval = function() {
        return $('#js-graph-show-per')
    };
    kukua.getGraphType = function() {
        return $("#js-graph-type-swap")
    };
    kukua.getGraphTypeText = function() {
        var input = kukua.getGraphType()
        return input.find("option:selected").text()
    };
    kukua.getDateRangePicker = function() {
        return $("#reportrange")
    };
    kukua.getDateRangePickerSpan = function() {
        return $("#reportrange span")
    };

})(window.kukua = window.kukua || {});
$(document).ready(kukua.onDomReady);
