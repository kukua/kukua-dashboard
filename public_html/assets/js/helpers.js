(function(helpers) {
	'use strict';

	helpers.onDomReady = function() {
		helpers.forecast();

		helpers.confirmDelete()
		helpers.confirmDisable()
		helpers.confirmRevoke()
		helpers.confirmGrant()
		helpers.feedbackDisplay()
		helpers.feedbackPost()

		helpers.tableRowClick()
	};

	helpers.forecast = function() {
		$("#js-forecast-country").on('change', function () {
			var postCountry = {
				"country": $(this).val()
			}
			var call = $.ajax({
				type: 'POST',
				url: '/forecast/get/',
				data: postCountry,
				dataType: 'json'
			})

			call.done(function(request) {
				$(".js-iframe").html('<iframe src="' + request.url + '" frameborder="0" width="802px" height="802px"></iframe>');
			})
		}).trigger("change");
	}

	/**
	 * Display confirm box on delete
	 */
	helpers.confirmDelete = function() {
		$(".js-confirm-delete").unbind("click");
		$(".js-confirm-delete").on("click", function() {
			var txt = "Are you sure you want to delete this?";
			if ($(this).data("text")) {
				txt = $(this).data("text");
			}
			var x = confirm(txt)
			if (x == true) {
				return true
			}
			return false
		})
	};

	/**
	 * Display confirm box on disable
	 */
	helpers.confirmDisable = function() {
		$(".js-confirm-disable").unbind("click");
		$(".js-confirm-disable").on("click", function() {
			var x = confirm("Are you sure you want to disable this?")
			if (x == true) {
				return true
			}
			return false
		})
	};

	/**
	 * Display confirm box on user access revoke
	 */
	helpers.confirmRevoke = function() {
		$(".js-confirm-revoke").on("click", function() {
			var x = confirm("Are you sure you want to revoke these rights?")
			if (x == true) {
				return true
			}
			return false
		})
	};

	/**
	 * Display confirm box on user access grant
	 */
	helpers.confirmGrant = function() {
		$(".js-confirm-grant").on("click", function() {
			var x = confirm("Are you sure you want to grant these rights?")
			if (x == true) {
				return true
			}
			return false
		})
	};

	/**
	 * Display feedback box
	 */
	helpers.feedbackDisplay = function() {
		$(".js-feedback").on("click", function(e) {
			e.preventDefault();
			if ($(this).parent().hasClass("open")) {
				$(".js-feedback").parent().removeClass("open");
			} else {
				$(".js-feedback").parent().addClass("open");
			}
		})
	};

	/**
	 * Feedback post ajax handle
	 */
	helpers.feedbackPost = function() {
		var form = $('.js-post-feedback');
		form.on("submit", function(e) {
			e.preventDefault();
			$.ajax({
				type: "POST",
				url:  "/feedback/create",
				data: form.serialize(),
				postData: "json",
				success: function(data) {
					var result = JSON.parse(data)
					if (result.success == true) {
						$(".js-feedback-result").html("<i class='glyphicon glyphicon-ok'></i> Thanks for your feedback!")
						$(".js-feedback-submit").attr("disabled", "disabled");
					} else {
						$(".js-feedback-result").html(result.message)
					}
				}
			});
		});
	};

	helpers.removeStation = function() {
		var item = $(".js-remove-station");
		item.on("click", function(e) {
			var $this = $(this);
			var x = confirm("Are you sure you want to delete this station?")
			if (x == true) {
				$.ajax({
					type: "GET",
					url: "/locations/delete_station/" + item.data("station_id"),
					success: function(data) {
						$this.parent("td").parent("tr").fadeOut()
					}
				});
			}
			return false
		})
	}

	helpers.tableRowClick = function() {
		$(".js-row-link td").on("click", function() {
			if ($(this).children("a").length > 1) {
				return;
			} else {
				window.document.location = $(this).parent('tr').data("href");
			}
		})
	}
})(window.helpers = window.helpers || {});
$(document).ready(helpers.onDomReady);
