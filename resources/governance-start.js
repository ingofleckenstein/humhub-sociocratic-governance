// SPDX-License-Identifier: AGPL-3.0-only
(function () {
    'use strict';
    function initializeStartNavigation() {
        document.querySelectorAll('.sg-start-navigation').forEach(function (navigation) {
            if (navigation.dataset.initialized) { return; }
            navigation.dataset.initialized = 'true';
            if (window.matchMedia('(max-width: 767.98px)').matches) { navigation.open = false; }
        });
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initializeStartNavigation, {once: true}); }
    else { initializeStartNavigation(); }
    if (window.jQuery) { window.jQuery(document).on('humhub:ready', initializeStartNavigation); }
}());
