/* SPDX-License-Identifier: AGPL-3.0-only */
(function () {
    'use strict';

    function initTabs() {
        document.querySelectorAll('[data-sg-directory-tab]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = button.getAttribute('data-sg-directory-tab');
                document.querySelectorAll('[data-sg-directory-panel]').forEach(function (panel) {
                    panel.hidden = panel.getAttribute('data-sg-directory-panel') !== target;
                });
                document.querySelectorAll('[data-sg-directory-tab]').forEach(function (item) {
                    var active = item === button;
                    item.setAttribute('aria-selected', active ? 'true' : 'false');
                    item.classList.toggle('sg-button-secondary', !active);
                });
                if (target === 'map') {
                    document.querySelector('[data-sg-circle-map]')?.dispatchEvent(new Event('sg:shown'));
                }
            });
        });
    }

    function initMap(map) {
        var graph;
        try { graph = JSON.parse(map.dataset.graph || '[]'); } catch (_) { return; }
        if (!graph.length) { return; }
        var world = map.querySelector('.sg-map-world');
        var svg = map.querySelector('.sg-map-links');
        var nodes = {};
        graph.forEach(function (node) { nodes[node.id] = node; });
        var stepX = 230, stepY = 180, bubbleWidth = 190, bubbleHeight = 120;
        var maxX = Math.max.apply(null, graph.map(function (node) { return node.x; }));
        var maxY = Math.max.apply(null, graph.map(function (node) { return node.depth; }));
        var worldWidth = Math.max(340, (maxX + 1) * stepX + 70);
        var worldHeight = Math.max(260, (maxY + 1) * stepY + 80);
        world.style.width = worldWidth + 'px';
        world.style.height = worldHeight + 'px';
        svg.setAttribute('width', String(worldWidth));
        svg.setAttribute('height', String(worldHeight));
        graph.forEach(function (node) {
            var bubble = map.querySelector('[data-sg-node-id="' + node.id + '"]');
            if (!bubble) { return; }
            node.cx = node.x * stepX + 20 + bubbleWidth / 2;
            node.cy = node.depth * stepY + 20 + bubbleHeight / 2;
            bubble.style.left = (node.cx - bubbleWidth / 2) + 'px';
            bubble.style.top = (node.cy - bubbleHeight / 2) + 'px';
        });
        graph.forEach(function (node) {
            if (!node.parentId || !nodes[node.parentId]) { return; }
            var parent = nodes[node.parentId];
            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', String(parent.cx));
            line.setAttribute('y1', String(parent.cy + bubbleHeight / 2));
            line.setAttribute('x2', String(node.cx));
            line.setAttribute('y2', String(node.cy - bubbleHeight / 2));
            svg.appendChild(line);
        });

        var scale = 1, tx = 0, ty = 0, positioned = false;
        function apply() {
            var transform = 'translate(' + tx + 'px,' + ty + 'px) scale(' + scale + ')';
            world.style.transform = transform;
            svg.style.transform = transform;
        }
        function position() {
            if (positioned || map.offsetWidth === 0) { return; }
            var selected = graph.filter(function (node) { return node.focus; });
            var focus = selected.length ? selected : graph.filter(function (node) { return !node.parentId; });
            if (!focus.length) { focus = graph; }
            var cx = focus.reduce(function (sum, node) { return sum + node.cx; }, 0) / focus.length;
            var cy = focus.reduce(function (sum, node) { return sum + node.cy; }, 0) / focus.length;
            scale = Math.min(1, Math.max(0.55, map.clientWidth / Math.max(360, focus.length * stepX + 120)));
            tx = map.clientWidth / 2 - cx * scale;
            ty = Math.max(24, map.clientHeight / 2 - cy * scale);
            positioned = true;
            apply();
        }
        map.addEventListener('sg:shown', position);
        map.addEventListener('wheel', function (event) {
            event.preventDefault();
            var rect = map.getBoundingClientRect();
            var beforeX = (event.clientX - rect.left - tx) / scale;
            var beforeY = (event.clientY - rect.top - ty) / scale;
            scale = Math.max(0.45, Math.min(1.6, scale * (event.deltaY < 0 ? 1.12 : 0.88)));
            tx = event.clientX - rect.left - beforeX * scale;
            ty = event.clientY - rect.top - beforeY * scale;
            positioned = true;
            apply();
        }, {passive: false});
        var drag = null;
        map.addEventListener('pointerdown', function (event) {
            if (event.target.closest('a')) { return; }
            drag = {x: event.clientX, y: event.clientY, tx: tx, ty: ty};
            map.setPointerCapture(event.pointerId);
        });
        map.addEventListener('pointermove', function (event) {
            if (!drag) { return; }
            tx = drag.tx + event.clientX - drag.x;
            ty = drag.ty + event.clientY - drag.y;
            positioned = true;
            apply();
        });
        map.addEventListener('pointerup', function () { drag = null; });
        position();
    }

    function init() {
        initTabs();
        document.querySelectorAll('[data-sg-circle-map]').forEach(initMap);
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
}());
