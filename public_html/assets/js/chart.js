(function(chart) {
    'use strict';

    chart.onDomReady = function() {
        //No need to do anything on domReady
    };

    chart.render = function(container, jsonUrl, options) {
        if ($(container).length >= 1) {
            var call = $.ajax({
                type: 'POST',
                url: jsonUrl,
                data: $('#js-submit').serialize(),
                dataType: 'json'
            });

            call.done(function(request) {
                var result = new Array()

                $.each(request, function(id, station) {
                    var data   = new Object()
                    data.data  = []
                    $.each(station.points, function(key, value) {

                        //Handle data differently per chart
                        switch(options.chart.type) {
                            case 'column':
                                data.data.push(value);
                                break;
                            case 'line':
                                var points = new Object()
                                points.x  = value[0]
                                points.y  = value[1]
                                data.data.push(points)
                                break;
                        }
                    })
                    result.push(data)
                });

                //Add data points to the given options
                options.series = result;

                //Combine given options with default options
                var opt = $.extend({}, chart.getOptions(), options)

                //render
                $(container).highcharts(opt);
            });
        }
    };

    chart.getOptions = function() {
        var options = new Object({
            title: {
                text: ""
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    rotation: -45,
                    align: 'right',
                },
                title: {
                    text: 'Date/Time'
                },
                crosshair: true,
                events: {
                    afterSetExtremes: function(event){
                        var extremes = this.getExtremes();
                        $('#js-datetimepicker-max').val(moment(extremes.max).format("YYYY/MM/DD"));
                        $('#js-datetimepicker-min').val(moment(extremes.userMin).format("YYYY/MM/DD"));
                    }
                },
                alternateGridColor: "#f7f7f7"
            },
            tooltip: {
                shared: true
            },
            legend: {
                align: 'right',
                verticalAlign: 'top',
                layout: 'vertical',
                x: 0,
                y: 20
            },
            yAxis: {
                title: {
                    text: 'Temperature (°C)'
                }
            },
            chart: {
                zoomType: 'x'
            },
            plotOptions: {
                series: {
                    states: {
                        hover: {
                            enabled: false
                        }
                    }
                },
                line: {
                    turboThreshold: 100000,
                    lineWidth: 1
                }
            }
        });

        return options;
    };

})(window.chart = window.chart || {});
$(document).ready(chart.onDomReady);