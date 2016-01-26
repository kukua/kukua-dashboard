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
           'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(30, 'days'), moment()]
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
        var graphCountry    = kukua.getGraphCountry()
        var graphType       = kukua.getGraphType()
        var graphTypeText   = kukua.getGraphTypeText()
        var graphInterval   = kukua.getGraphInterval()

        var options = chart.getTempOptions()
        options.chart.zoomType = 'x'

        var graphTypeValue = graphType.val();
        switch(graphTypeValue) {
            case 'rain':
                options.title.text = graphTypeText
                options.chart.type = "column"
                options.yAxis.title.text = graphTypeText + " (mm)"
                options.yAxis.min = null
                options.yAxis.max = null
                options.colors = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']
                options.tooltip.valueSuffix = ' mm'
                break
            //if other than rain
            default:
                options.title.text = "Temperature"
                graphTypeValue = 'temp'
                options.chart.type = "arearange"
                options.yAxis.title.text = "Temperature (°C)"
                options.yAxis.min = 0
                options.yAxis.max = 50
                options.tooltip.valueSuffix = '°C'
                break;
        }
        chart.temp("#chart-forecast", "/graph/get/forecast_t/" + graphTypeValue + "_ten", options)
    };

    kukua.graph = function() {
        var graphCountry    = kukua.getGraphCountry()
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
                options.yAxis.min = 0
                options.yAxis.max = 50
                options.tooltip.valueSuffix = '°C'
                break
            case 'rain':
                options.chart.type = "column"
                options.yAxis.title.text = graphTypeText + " (mm)"
                options.yAxis.min = null
                options.yAxis.max = null
                options.tooltip.valueSuffix = ' mm'
                break
            case 'wind':
                options.chart.type = "line"
                options.yAxis.title.text = graphTypeText + " (km/h)"
                options.yAxis.min = null
                options.yAxis.max = null
                options.tooltip.valueSuffix = ' km/h'
                break;
        }
        chart.render("#chart", "/graph/get/history/" + graphType.val(), options)
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
            kukua.forecast()
        })
        kukua.getGraphCountry().on("change", function() {
            kukua.graph()
        })
    };

    kukua.getGraphCountry = function() {
        return $("#js-graph-country")
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
