(function(chart) {
    'use strict';

    chart.onDomReady = function() {
        //No need to do anything on domReady
    };

    chart.render = function(container, jsonUrl, options) {
        if ($(container).length >= 1) {

            //get dates from daterangepicker
            var graphType    = kukua.getGraphType()
            var selectedDate = kukua.getDateRangePicker()
            var interval     = kukua.getGraphInterval()
            var region		 = kukua.getGraphRegion()
			var stationValue = null;
			if (sensors.getStation().length > 0) {
				var stationValue = sensors.getStation().val()
			}

            var postdata = {
                'region': region.val(),
				'station': stationValue,
                'type': graphType.val(),
                'dateFrom': selectedDate.data('daterangepicker').startDate.startOf('day').format('X'),
                'dateTo': selectedDate.data('daterangepicker').endDate.endOf('day').format('X'),
                'interval': interval.val()
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
                var result = []
				options.series = []
                if (request != null && request.length > 0) {
					options.series = request;
                }

                //Combine given options with default options
                var opt = $.extend({}, chart.getOptions(), options)

                //render
                $(container).highcharts(opt)
            })
        }
    };

    chart.getOptions = function() {
        var options = new Object({
            title: {
                text: "",
				align: "left",
				style: {
					fontSize: "26px",
					fontFamily: "Asap, Trebuchet MS"
				}
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    rotation: -45,
                    align: 'right',
                },
                title: {
                    text: 'Date/Time',
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
                        kukua.datePickerCallback(moment(extremes.min), moment(extremes.max));
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
				style: {
					fontFamily: 'Trebuchet MS',
				},
                zoomType: 'x',
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

})(window.chart = window.chart || {});
$(document).ready(chart.onDomReady);
