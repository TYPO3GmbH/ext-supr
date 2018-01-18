define(['jquery', 'TYPO3/CMS/Backend/Notification'], function ($, Notification) {
	var Widget = {
		identifier: {
			widgetContainer: '.t3js-formengine-supr-widget-preview',
			loadSpinner: '.t3js-load-spinner'
		}
	};

	Widget.initialize = function() {
		$(document).find(Widget.identifier.widgetContainer).each(function() {
			var $container = $(this);
			var $field = $container.closest('.t3js-formengine-field-item').find('.form-wizards-element select');

			$field.on('change', Widget.renderSelected);
		});
	};

	Widget.renderSelected = function() {
		var $field = $(this);
		var $container = $field.closest('.t3js-formengine-field-item').find(Widget.identifier.widgetContainer);

		$.ajax({
			url: TYPO3.settings.ajaxUrls['supr_render_widget'],
			method: 'GET',
			data: {
				widgetId: $field.val()
			},
			dataType: 'html',
			beforeSend: function() {
				$container.html($(Widget.identifier.loadSpinner).html());
			},
			success: function(response) {
				$container.html(response);
			},
			error: function(response) {
				$container.html('');

				var responseText = JSON.parse(response.responseText);
				Notification.error('', responseText.exception);
			}
		});
	};

	$(Widget.initialize);
});