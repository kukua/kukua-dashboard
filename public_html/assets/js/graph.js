(function(kukua) {
	'use strict'
	kukua.onDomReady = function() {

		kukua.init()
		kukua.graph()
		kukua.formChanges()
	}

	/**
	 * Set variables correctly
	 *
	 * @example direct-link
	 * @example form change
	 */
	kukua.init = function() {
		kukua.datePickerInit()
		kukua.directLink()
	}

	kukua.datePickerInit = function() {
		kukua.getDateRangePicker().daterangepicker({
			ranges: kukua.getDatePickerRanges()
		}, kukua.datePickerCallback)
	}

	kukua.getDatePickerRanges = function() {
		return {
		   'Today': [moment().startOf('day'), moment().endOf('day')],
		   'Tomorrow': [moment().add(1, 'days').startOf('day'), moment().add(1, 'days').endOf('day')],
		   'Yesterday': [moment().subtract(1, 'day').startOf('day'), moment().subtract(1, 'day').endOf('day')],
		   'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
		   'Last 30 Days': [moment().subtract(30, 'days').startOf('day'), moment().endOf('day')]
		}
	}

	kukua.datePickerCallback = function(start, end) {
		var startDate = start.startOf('day')
		var endDate   = end.endOf('day')

		kukua.getDateRangePickerSpan().html(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'))
		$('input#dateFrom').val(startDate.format("X"))
		$('input#dateTo').val(endDate.format("X"))
	}

	kukua.graph = function() {
		var graphRegion		= kukua.getGraphRegion()
		var graphType		= kukua.getGraphType()
		var graphTypeText	= kukua.getGraphTypeText()
		var graphInterval	= kukua.getGraphInterval()

		var options = chart.getOptions()
		options.chart.zoomType = 'x'
		options.title.text = graphTypeText

		var item = graphType.find(":selected").val()
		switch(item) {
			case 'temp':
				options.chart.type = "line"
				options.yAxis.title.text = graphTypeText + " (°C)"
				options.yAxis.min = null
				options.yAxis.max = null
				options.tooltip.valueSuffix = '°C'
				break
			case 'humid':
				options.chart.type = "line"
				options.yAxis.title.text = graphTypeText + " (%)"
				options.yAxis.min = null
				options.yAxis.max = null
				options.tooltip.valueSuffix = ' (%)'
				break
			case 'pressure':
				options.chart.type = "line"
				options.yAxis.title.text = graphTypeText + " (hPa)"
				options.yAxis.min = null
				options.yAxis.max = null
				options.tooltip.valueSuffix = ' (hPa)'
				break
			case 'rain':
				options.chart.type = "column"
				options.yAxis.title.text = graphTypeText + " (mm)"
				options.yAxis.min = null
				options.yAxis.max = null
				options.tooltip.valueSuffix = ' mm'
				break
			case 'windSpeed':
				options.chart.type = "line"
				options.yAxis.title.text = graphTypeText + " (km/h)"
				options.yAxis.min = null
				options.yAxis.max = null
				options.tooltip.valueSuffix = ' km/h'
				break
		}
		chart.render("#chart", "/api/sensordata/get/true", options)
	}

	kukua.datePickerChange = function() {
		kukua.getDateRangePicker().on("apply.daterangepicker", function(ev, picker) {
			kukua.graph()
			sensors.graph()
		})
	}

	kukua.getUrlParam = function(name) {
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href)
		if (results == null) {
		   return null
		} else {
		   return results[1] || 0
		}
	}

	kukua.directLink = function() {
		var rawRegion	= kukua.getUrlParam('region')
		var rawGraph	= kukua.getUrlParam('graph')
		var rawDateFrom	= kukua.getUrlParam('dateFrom')
		var rawDateTo	= kukua.getUrlParam('dateTo')

		if (rawRegion) {
			kukua.getGraphRegion().val(rawRegion)
		}
		if (kukua.getUrlParam('graph')) {
			kukua.getGraphType().val(rawGraph)
		}
		if (kukua.getUrlParam('dateFrom') && kukua.getUrlParam('dateTo')) {
			kukua.datePickerCallback(moment(rawDateFrom), moment(rawDateTo))
		} else {
			kukua.datePickerCallback(moment(), moment())
		}
	}

	kukua.formChanges = function() {
		kukua.getGraphRegion().on("change", function() {
			kukua.graph()
		})
		kukua.datePickerChange()
		kukua.getGraphInterval().on("change", function() {
			kukua.graph()
			sensors.graph()
		})
		kukua.getGraphType().on("change", function() {
			kukua.graph()
			sensors.graph()
		})
	}

	kukua.getGraphRegion = function() {
		return $("#js-graph-region")
	}
	kukua.getGraphInterval = function() {
		return $('#js-graph-show-per')
	}
	kukua.getGraphType = function() {
		return $("#js-graph-type-swap")
	}
	kukua.getGraphTypeText = function() {
		var input = kukua.getGraphType()
		return input.find("option:selected").text()
	}
	kukua.getDateRangePicker = function() {
		return $("#reportrange")
	}
	kukua.getDateRangePickerSpan = function() {
		return $("#reportrange span")
	}
	kukua.getDateFrom = function() {
		return $("#dateFrom")
	}
	kukua.getDateTo = function() {
		return $("#dateTo")
	}

})(window.kukua = window.kukua || {})
$(document).ready(kukua.onDomReady);
