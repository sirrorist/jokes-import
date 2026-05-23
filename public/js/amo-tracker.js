/**
 * amo-tracker.js — embeddable visit counter for third-party sites.
 *
 * IP and city are NOT available reliably in the browser (no permission, NAT, VPN).
 * This script sends url/device_type/visitor_id; the Laravel backend reads IP from the
 * request and resolves city via a geo service.
 *
 * Usage:
 * <script src="https://your-domain.test/js/amo-tracker.js" data-endpoint="https://your-domain.test/api/analytics/visit" defer></script>
 */
(function (window, document) {
    'use strict';

    var currentScript = document.currentScript;
    var endpoint = currentScript && currentScript.getAttribute('data-endpoint');

    if (!endpoint) {
        return;
    }

    var storageKey = 'amo_visitor_id';

    function getVisitorId() {
        try {
            var existing = window.localStorage.getItem(storageKey);
            if (existing) {
                return existing;
            }
            var generated = window.crypto && window.crypto.randomUUID
                ? window.crypto.randomUUID()
                : String(Date.now()) + '-' + Math.random().toString(16).slice(2);
            window.localStorage.setItem(storageKey, generated);
            return generated;
        } catch (error) {
            return null;
        }
    }

    function detectDeviceType() {
        var ua = navigator.userAgent || '';
        if (/bot|crawl|spider/i.test(ua)) {
            return 'bot';
        }
        if (/mobile|android|iphone|ipod/i.test(ua)) {
            return 'mobile';
        }
        if (/ipad|tablet/i.test(ua)) {
            return 'tablet';
        }
        return 'desktop';
    }

    function buildPayload() {
        return {
            url: window.location.href,
            device_type: detectDeviceType(),
            visited_at: new Date().toISOString(),
            visitor_id: getVisitorId(),
        };
    }

    function sendVisit() {
        var payload = JSON.stringify(buildPayload());

        if (navigator.sendBeacon) {
            var blob = new Blob([payload], { type: 'application/json' });
            if (navigator.sendBeacon(endpoint, blob)) {
                return;
            }
        }

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: payload,
            keepalive: true,
            mode: 'cors',
        }).catch(function () {
            /* silent fail — analytics must not break the host page */
        });
    }

    if (document.readyState === 'complete') {
        sendVisit();
    } else {
        window.addEventListener('load', sendVisit, { once: true });
    }
})(window, document);
