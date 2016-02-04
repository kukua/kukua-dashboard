(function(chart) {
    'use strict';

    chart.onDomReady = function() {
        //No need to do anything on domReady
    };

    chart.temp = function(container, jsonUrl, options) {
        if ($(container).length >= 1) {

            //get dates from daterangepicker
            var selectedDate = kukua.getDateRangePicker()
            var country = kukua.getGraphCountry().val()
            var postdata = {
                'from': selectedDate.data('daterangepicker').startDate.startOf('day').format('X'),
                'to': selectedDate.data('daterangepicker').endDate.endOf('day').format('X'),
                'country': country
            }

            var call = $.ajax({
                type: 'POST',
                url: jsonUrl,
                data: postdata,
                dataType: 'json'
            })

            call.done(function(request) {
                var result = new Array()

                $.each(request, function(id, station) {

                    var data  = new Object()
                    data.name = "N.E. Tanzania"
                    data.data = []
                    $.each(station.values, function(key, value) {
                        data.data.push(value)
                    })
                    result.push(data)
                })

                //Add data points to the given options
                options.series = result

                //Combine given options with default options
                var opt = $.extend({}, chart.getTempOptions(), options)

                //render
                $(container).highcharts(opt)
            })
        }
    };

    chart.render = function(container, jsonUrl, options) {
        if ($(container).length >= 1) {

            //get dates from daterangepicker
            var graphType    = kukua.getGraphType()
            var selectedDate = kukua.getDateRangePicker()
            var interval     = kukua.getGraphInterval()
            var country = kukua.getGraphCountry().val()

            var postdata = {
                'from': selectedDate.data('daterangepicker').startDate.startOf('day').format('X'),
                'to': selectedDate.data('daterangepicker').endDate.endOf('day').format('X'),
                'interval': interval.val(),
                'country': country
            }

            var call = $.ajax({
                type: 'POST',
                url: jsonUrl,
                data: postdata,
                dataType: 'json',
                beforeSend: function() {
                    $(container).html("<div class='loading'></div>");
                }
            })

            call.done(function(request) {
                var result = new Array()
                var call2 = $.ajax({
                    type: 'POST',
                    url: '/graph/get/forecast/' + graphType.val(),
                    data: postdata,
                    dataType: 'json'
                })

                call2.done(function(req2) {
                    if (req2 != null) {
                        $.each(req2, function(id, values) {
                            var data = new Object()
                            data.name = "Forecast"
                            data.data = []
                            $.each(values.values, function(k, v) {
                                data.data.push(v)
                            });
                            result.push(data)
                        })
                    }

                    if (request != null) {
                        $.each(request, function(id, station) {
                            var data   = new Object()
                            data.name = station.name
                            data.data  = []
                            $.each(station.values, function(key, value) {
                                data.data.push(value)
                            })
                            result.push(data)
                        })
                    }

                    //Add data points to the given options
                    options.series = result

                    //Combine given options with default options
                    var opt = $.extend({}, chart.getOptions(), options)

                    //render
                    $(container).highcharts(opt)
                })
            })
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

                        //Set daterangepicker object
                        kukua.getDateRangePicker().daterangepicker({
                            ranges: kukua.getDatePickerRanges(),
                            startDate: moment(extremes.min),
                            endDate: moment(extremes.max)
                        }, kukua.datePickerCallback)

                        //Rebind
                        kukua.datePickerChange()
                        kukua.datePickerCallback(moment(extremes.userMin), moment(extremes.max));

                    }
                },
                alternateGridColor: "#f7f7f7"
            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            legend: {
                align: 'center',
                verticalAlign: 'bottom',
                layout: 'horizontal',
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            chart: {
                zoomType: 'x'
            },
            plotOptions: {
                series: {
                    cropTreshhold: 5000,
                    states: {
                        hover: {
                            enabled: false
                        }
                    }
                },
                line: {
                    turboThreshold: 5000,
                    lineWidth: 1
                }
            },
            credits: {
                enabled: false
            }
        });

        return options;
    };

    chart.getTempOptions = function() {
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
                alternateGridColor: "#f7f7f7"
            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            legend: {
                align: 'center',
                verticalAlign: 'bottom',
                layout: 'horizontal'
            },
            yAxis: {
                title: {
                    text: ''
                },
                min: 0,
                max: 50
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
                },
                events: {
                    afterSetExtremes: function(event){

                    }
                }
            },
            credits: {
                enabled: false
            },
        });

        return options;
    };
})(window.chart = window.chart || {});
$(document).ready(chart.onDomReady);
