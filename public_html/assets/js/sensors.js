(function(sensors) {
	'use strict'

	/**
	 * Execute on dom ready
	 */
	sensors.onDomReady = function() {
		if ($('#sensor-chart').length > 0) {
			sensors.init()
		}
	}

	/**
	 * Initialize when div available
	 */
	sensors.init = function() {

		/* on change fill station measurements */
		sensors.getStation().on("change", function() {
			sensors.getMeasurements($(this).val()).done(function(request) {
				kukua.getGraphType().html("");
				$.each(request, function(key, measurement) {
					kukua.getGraphType().append('<option value="' + measurement.id + '">' + measurement.name + '</option>')
				})

				if (kukua.getGraphType().children().length <= 0) {
					kukua.getGraphType().html('<option disabled="disabled">Nothing to display</option>');
				}

				/* bind functions after trigger */
				sensors.graph();
				kukua.formChanges();
			})
		}).trigger('change')

		return
	}

	/**
	 * Render graph
	 */
	sensors.graph = function() {
		var graphType = kukua.getGraphType().find(":selected")

		var options = chart.getOptions()
		options.chart.zoomType = 'x'
		options.title.text = graphType.text()

		options.chart.type = 'line'
		options.yAxis.title.text = graphType.text()
		options.yAxis.min = null
		options.yAxis.max = null
		options.tooltip.valueSuffix = ''

		chart.render("#sensor-chart", "/api/sensordata/get/", options)
	}

	/**
	 * Measurements call
	 * @return $.ajax
	 */
	sensors.getMeasurements = function(stationId) {
		var call = $.ajax({
			type: 'GET',
			url: '/graph/getStationMeasurements/' + stationId,
			dataType: 'json',
			beforeSend: function() {
				$("#sensor-chart").html("<div class='loading'></div>")
			}
		})
		return call
	}

	/**
	 * Get station select element
	 */
	sensors.getStation = function() {
		return $("#js-graph-station")
	}

})(window.sensors = window.sensors || {});
$(document).ready(sensors.onDomReady);
