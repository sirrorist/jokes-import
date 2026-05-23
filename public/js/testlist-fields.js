/**
 * testlist-fields.js
 *
 * Algorithm:
 * 1. Find the first <select> on the page (field "Тип").
 * 2. Collect all elements that have a `name` attribute (inputs, selects, textareas).
 * 3. On change of the type select, show only fields whose `name` contains the selected option value.
 * 4. Run once on DOMContentLoaded for the initial state.
 *
 * Alternatives considered:
 * - jQuery: rejected — one event listener is enough in vanilla JS.
 * - Hard-coded #field1 IDs: rejected — breaks when markup changes.
 * - MutationObserver: rejected — unnecessary for a static form.
 */
(function () {
    'use strict';

    function getTypeSelect() {
        return document.querySelector('select');
    }

    function getNamedFields(root, typeSelect) {
        if (!root) {
            return [];
        }

        return Array.from(root.querySelectorAll('[name]')).filter(function (element) {
            return element.name && element !== typeSelect;
        });
    }

    function findFieldContainer(field) {
        return field.closest('label, p, div, tr, li') || field;
    }

    function applyVisibility(typeSelect, typeValue) {
        var fields = getNamedFields(document, typeSelect);

        if (!typeValue) {
            fields.forEach(function (field) {
                findFieldContainer(field).style.display = '';
            });
            return;
        }

        fields.forEach(function (field) {
            var visible = field.name.indexOf(typeValue) !== -1;
            findFieldContainer(field).style.display = visible ? '' : 'none';
        });
    }

    function init() {
        var typeSelect = getTypeSelect();

        if (!typeSelect) {
            return;
        }

        var update = function () {
            applyVisibility(typeSelect, typeSelect.value);
        };

        typeSelect.addEventListener('change', update);
        update();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
