(function(chart) {
    'use strict';

    chart.onDomReady = function() {
        //No need to do anything on domReady
    };

    chart.temp = function(container, jsonUrl, options) {
        if ($(container).length >= 1) {

            //get dates from daterangepicker
            var selectedDate = kukua.getDateRangePicker()
            var postdata = {
                'from': selectedDate.data('daterangepicker').startDate.startOf('day').format('X'),
                'to': selectedDate.data('daterangepicker').endDate.endOf('day').format('X')
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

                        //Handle data differently per chart
                        switch(options.chart.type) {
                            case 'column':
                                data.data.push(value)
                                break
                            case 'line':
                                var points = new Object()
                                points.x  = value[0]
                                points.y  = value[1]
                                data.data.push(points)
                                break
                        }
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
            var selectedDate = kukua.getDateRangePicker()
            var postdata = {
                'from': selectedDate.data('daterangepicker').startDate.startOf('day').format('X'),
                'to': selectedDate.data('daterangepicker').endDate.endOf('day').format('X')
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
                    var data   = new Object()
                    data.name = chart.convertName(station.name)
                    data.data  = []
                    $.each(station.points, function(key, value) {

                        //Handle data differently per chart
                        switch(options.chart.type) {
                            case 'column':
                                data.data.push(value)
                                break
                            case 'line':
                                var points = new Object()
                                points.x  = value[0]
                                points.y  = value[1]
                                data.data.push(points)
                                break
                        }
                    })
                    result.push(data)
                })
                console.log(result)
                //Add data points to the given options
                options.series = result

                //Combine given options with default options
                var opt = $.extend({}, chart.getOptions(), options)

                //render
                $(container).highcharts(opt)
            })
        }
    };

    chart.convertName = function(id) {
        var text = '';
        switch(id) {
            case 'sivad_ndogo_a5e4d2c1':
                text = 'Mwangoi'
                break
            case 'sivad_ndogo_a687dcd8':
                text = 'Mavumo'
                break
            case 'sivad_ndogo_a468d67c':
                text = 'Migambo'
                break
            case 'sivad_ndogo_9f113b00':
                text = 'Mshizii'
                break
            case 'sivad_ndogo_890d85ba':
                text = 'Baga'
                break
            case 'sivad_ndogo_1e2e607e':
                text = 'Makuyuni'
                break
            case 'sivad_ndogo_9f696fb0':
                text = 'Rauya'
                break
            case 'sivad_ndogo_841d300b':
                text = 'Mandakamnono'
                break
            case 'sivad_ndogo_7aa19521':
                text = 'Sanyo'
                break
            case 'sivad_ndogo_fab23419':
                text = 'Ibadan'
                break
        }
        return text;
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
