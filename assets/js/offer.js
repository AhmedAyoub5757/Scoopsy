(function () {
	'use strict';

	function showOffer(offer) {
		if (!offer.classList.contains('is-in')) {
			offer.classList.add('is-in');
		}
	}

	function initOffer(offer) {
		if (!offer || offer.dataset.offerReady === 'true') {
			return;
		}

		offer.dataset.offerReady = 'true';

		if (
			document.body.classList.contains('elementor-editor-active') ||
			!('IntersectionObserver' in window)
		) {
			showOffer(offer);
			return;
		}

		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					showOffer(entry.target);
					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.25 });

		observer.observe(offer);
	}

	function initOffers(scope) {
		(scope || document).querySelectorAll('.sc-offer').forEach(initOffer);
	}

	var elementorHookReady = false;

	function boot() {
		initOffers(document);

		if (
			!elementorHookReady &&
			window.elementorFrontend &&
			window.elementorFrontend.hooks
		) {
			elementorHookReady = true;
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/scoopsy_offer.default',
				function ($scope) {
					initOffers($scope[0] || $scope);
				}
			);
		}
	}

	document.addEventListener('elementor/frontend/init', boot);

	if (window.elementorFrontend) {
		boot();
	} else {
		document.addEventListener('DOMContentLoaded', boot);
	}
}());
