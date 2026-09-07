/* SPDX-License-Identifier: AGPL-3.0-only */
(function () {
    'use strict';

    function element(name, className, text) { var item = document.createElement(name); if (className) { item.className = className; } if (text !== undefined) { item.textContent = text; } return item; }
    function initials(name) { return String(name || '?').trim().split(/\s+/).slice(0, 2).map(function (part) { return part.charAt(0).toUpperCase(); }).join('') || '?'; }
    function personAvatar(person, compact) {
        var avatar = element('span', compact ? 'sg-map-person-avatar is-compact' : 'sg-map-person-avatar'); avatar.setAttribute('aria-hidden', 'true');
        if (person.avatarUrl) { var image = document.createElement('img'); image.src = person.avatarUrl; image.alt = ''; image.loading = 'lazy'; avatar.appendChild(image); } else { avatar.textContent = initials(person.name); }
        return avatar;
    }
    function personRow(person, compact) {
        var safeRole = ['leader', 'delegate', 'facilitator', 'secretary'].indexOf(person.roleKey) !== -1 ? ' sg-role-' + person.roleKey : '';
        var link = document.createElement('a'); link.className = (compact ? 'sg-map-link-person is-compact' : 'sg-map-link-person') + safeRole; link.href = person.url; link.appendChild(personAvatar(person, compact));
        if (!compact) { var copy = element('span', 'sg-map-person-copy'); copy.appendChild(element('strong', '', person.name)); copy.appendChild(element('span', '', person.roles || 'Kreismitglied')); link.appendChild(copy); }
        return link;
    }
    function createNode(node) {
        var safeColor = ['teal', 'moss', 'terracotta', 'plum', 'ochre', 'blue'].indexOf(node.color) !== -1 ? node.color : 'teal', size = Math.max(220, Math.min(290, Number(node.diameter) || 240));
        if (!node.parentId) { size = 310; }
        var card = element('article', 'sg-map-node sg-tone-' + safeColor + (node.focus ? ' is-focus' : '') + (!node.parentId ? ' sg-map-root' : '')); card.dataset.sgNodeId = String(node.id); card.style.width = size + 'px'; card.style.height = size + 'px';
        var header = element('header', 'sg-map-node-header'), title = document.createElement('a'); title.className = 'sg-map-node-title'; title.href = node.url; title.textContent = node.name; header.appendChild(title); header.appendChild(element('span', 'sg-map-member-count', String(node.members.length) + ' Mitglied' + (node.members.length === 1 ? '' : 'er'))); card.appendChild(header);
        card.appendChild(element('p', 'sg-map-node-mandate', node.mandate || 'Mandat noch nicht beschrieben.'));
        var actions = element('div', 'sg-map-node-actions'), detailsButton = element('button', 'sg-map-node-action', 'Infos'), membersButton = element('button', 'sg-map-node-action', 'Mitglieder'); detailsButton.type = membersButton.type = 'button'; actions.append(detailsButton, membersButton); card.appendChild(actions);
        var details = element('section', 'sg-map-details'); details.hidden = true; details.appendChild(element('h3', '', 'Kreisprofil'));
        if (node.purpose) { details.appendChild(element('h4', '', 'Zweck')); details.appendChild(element('p', '', node.purpose)); }
        details.appendChild(element('h4', '', 'Mandat')); details.appendChild(element('p', '', node.mandate || 'Noch nicht beschrieben.'));
        if (node.roles.length) { details.appendChild(element('h4', '', 'Rollen')); var roles = element('div', 'sg-map-role-list'); node.roles.forEach(function (role) { roles.appendChild(personRow(role, false)); }); details.appendChild(roles); } card.appendChild(details);
        var members = element('section', 'sg-map-members'); members.hidden = true; members.appendChild(element('h3', '', 'Mitglieder'));
        if (node.members.length) { var list = element('div', 'sg-map-member-list'); node.members.forEach(function (member) { list.appendChild(personRow(member, false)); }); members.appendChild(list); } else { members.appendChild(element('p', 'sg-muted', 'Keine aktiven Mitglieder sichtbar.')); } card.appendChild(members);
        function toggle(panel, button) { var willOpen = panel.hidden; panel.hidden = !willOpen; button.setAttribute('aria-expanded', willOpen ? 'true' : 'false'); card.dispatchEvent(new CustomEvent('sg:node-size-changed', {bubbles: true})); }
        detailsButton.setAttribute('aria-expanded', 'false'); membersButton.setAttribute('aria-expanded', 'false'); detailsButton.addEventListener('click', function () { toggle(details, detailsButton); }); membersButton.addEventListener('click', function () { toggle(members, membersButton); }); return card;
    }
    function initTabs() {
        document.querySelectorAll('[data-sg-directory-tab]').forEach(function (button) {
            if (button.dataset.sgDirectoryInitialized) { return; }
            button.dataset.sgDirectoryInitialized = '1'; button.addEventListener('click', function (event) {
            event.preventDefault();
            var target = button.getAttribute('data-sg-directory-tab'); document.querySelectorAll('[data-sg-directory-panel]').forEach(function (panel) { panel.hidden = panel.getAttribute('data-sg-directory-panel') !== target; });
            document.querySelectorAll('[data-sg-directory-tab]').forEach(function (item) { var active = item === button; item.setAttribute('aria-selected', active ? 'true' : 'false'); item.classList.toggle('sg-button-secondary', !active); });
            if (target === 'map') { document.querySelectorAll('[data-sg-circle-map]').forEach(function (map) { initMap(map); map.dispatchEvent(new Event('sg:shown')); }); }
        }); });
    }
    function initMap(map) {
        if (map.dataset.sgMapInitialized) { return; }
        var graph; try { graph = JSON.parse(map.dataset.graph || '{}'); } catch (_) { map.replaceChildren(element('p', 'sg-note', 'Die Karte konnte nicht geladen werden. Bitte die Seite neu laden.')); return; } var nodes = graph.nodes || [], links = graph.links || []; if (!nodes.length) { map.replaceChildren(element('p', 'sg-note', 'Für die Karte sind keine sichtbaren Arbeitskreise vorhanden.')); return; }
        map.dataset.sgMapInitialized = '1';
        map.classList.remove('sg-map-fallback-mode');
        var byId = {}; nodes.forEach(function (node) { byId[node.id] = node; });
        var controls = element('div', 'sg-map-controls'); controls.setAttribute('aria-label', 'Kartensteuerung'); var zoomIn = element('button', 'sg-map-control', 'Vergrößern'), zoomOut = element('button', 'sg-map-control', 'Verkleinern'), reset = element('button', 'sg-map-control', 'Ansicht zentrieren'); zoomIn.type = zoomOut.type = reset.type = 'button'; controls.append(zoomIn, zoomOut, reset);
        var world = element('div', 'sg-map-world'), overlay = element('div', 'sg-map-overlay'), lineLayer = element('div', 'sg-map-lines'), badges = element('div', 'sg-map-link-badges'); overlay.append(lineLayer, badges); map.replaceChildren(controls, overlay, world);
        var maxWidth = 340, worldWidth = 520, worldHeight = 440;
        nodes.forEach(function (node) { node.card = createNode(node); world.appendChild(node.card); });
        function radialLayout() {
            // a = rᵢ + rⱼ + s + 2m.  The 140 px free distance consists of
            // an 80 px visible arrow corridor (s) plus 30 px protection margin
            // around each circle (m).
            var root = nodes.filter(function (node) { return !node.parentId; })[0] || nodes[0], children = {}, seen = {}, arrowLength = 80, circleMargin = 30, minimumClearance = arrowLength + circleMargin * 2, margin = 120;
            function byName(left, right) { return String(left.name || '').localeCompare(String(right.name || ''), 'de'); }
            function clampAngle(angle, start, end) {
                if (end - start >= Math.PI * 2 - 0.001) { return angle; }
                var middle = (start + end) / 2;
                while (angle < middle - Math.PI) { angle += Math.PI * 2; }
                while (angle > middle + Math.PI) { angle -= Math.PI * 2; }
                return Math.max(start, Math.min(end, angle));
            }
            function cartesian(node) { node.cx = Math.cos(node.angle) * node.distance; node.cy = Math.sin(node.angle) * node.distance; }
            function constrain(node) {
                node.angle = clampAngle(node.angle, node.sectorStart, node.sectorEnd);
                node.distance = Math.max(node.minDistance, node.distance);
                cartesian(node);
            }
            nodes.forEach(function (node) { children[node.id] = []; node.radius = Math.max(node.card.offsetWidth || 240, node.card.offsetHeight || 240) / 2; });
            nodes.forEach(function (node) { if (node.parentId && children[node.parentId]) { children[node.parentId].push(node); } });
            Object.keys(children).forEach(function (id) { children[id].sort(byName); });

            root.cx = 0; root.cy = 0; root.radius = Math.max(root.card.offsetWidth || 240, root.card.offsetHeight || 240) / 2;
            root.sectorStart = -Math.PI; root.sectorEnd = Math.PI; root.angle = 0; root.distance = 0; seen[root.id] = true;

            // The first level gets evenly sized 360° sectors. Every later level
            // inherits and subdivides its parent's sector, so descendants stay on
            // their side of the diagram and links cannot cross into another theme.
            function seed(parent, sectorStart, sectorEnd, depth) {
                var list = children[parent.id] || [], count = list.length, span = sectorEnd - sectorStart;
                list.forEach(function (child, index) {
                    var childStart = sectorStart + span * index / count, childEnd = sectorStart + span * (index + 1) / count, center = (childStart + childEnd) / 2;
                    seen[child.id] = true; child.depth = depth; child.parentNode = parent; child.sectorStart = childStart; child.sectorEnd = childEnd; child.angle = center;
                    child.minDistance = Math.hypot(parent.cx, parent.cy) + parent.radius + child.radius + minimumClearance;
                    child.distance = child.minDistance + 28; child.weight = depth === 1 ? 2.4 : 1.4; cartesian(child);
                    seed(child, childStart, childEnd, depth + 1);
                });
            }
            seed(root, -Math.PI * 1.5, Math.PI * 0.5, 1);

            // Visible circles without a visible parent are treated as additional
            // root themes; this keeps the map usable with partially visible trees.
            var orphans = nodes.filter(function (node) { return !seen[node.id]; }).sort(byName);
            orphans.forEach(function (node, index) {
                var count = orphans.length, start = -Math.PI * 1.5 + Math.PI * 2 * index / count, end = -Math.PI * 1.5 + Math.PI * 2 * (index + 1) / count;
                node.parentNode = root; node.depth = 1; node.sectorStart = start; node.sectorEnd = end; node.angle = (start + end) / 2; node.minDistance = root.radius + node.radius + minimumClearance; node.distance = node.minDistance + 28; node.weight = 2.4; seen[node.id] = true; cartesian(node); seed(node, start, end, 2);
            });

            // Constraint relaxation: push overlapping cards the minimum amount
            // apart, then pull linked cards back toward their parent. The root is
            // fixed. Repeating both steps yields a compact, non-overlapping graph.
            function resolveOverlaps() {
                var largestOverlap = 0;
                for (var leftIndex = 0; leftIndex < nodes.length; leftIndex++) {
                    for (var rightIndex = leftIndex + 1; rightIndex < nodes.length; rightIndex++) {
                        var left = nodes[leftIndex], right = nodes[rightIndex], dx = right.cx - left.cx, dy = right.cy - left.cy, actual = Math.hypot(dx, dy), required = left.radius + right.radius + minimumClearance;
                        if (actual >= required) { continue; }
                        if (actual < 0.001) { dx = Math.cos((leftIndex + rightIndex + 1) * 1.618); dy = Math.sin((leftIndex + rightIndex + 1) * 1.618); actual = 1; }
                        var correction = (required - actual) / (left === root || right === root ? 1 : 2), ux = dx / actual, uy = dy / actual;
                        if (left !== root) { left.cx -= ux * correction; left.cy -= uy * correction; left.angle = Math.atan2(left.cy, left.cx); left.distance = Math.hypot(left.cx, left.cy); constrain(left); }
                        if (right !== root) { right.cx += ux * correction; right.cy += uy * correction; right.angle = Math.atan2(right.cy, right.cx); right.distance = Math.hypot(right.cx, right.cy); constrain(right); }
                        largestOverlap = Math.max(largestOverlap, required - actual);
                    }
                }
                return largestOverlap;
            }
            function weightedVisibleLength() {
                return nodes.reduce(function (sum, node) {
                    if (!node.parentNode || !node.parentId || !byId[node.parentId]) { return sum; }
                    var parent = node.parentNode, distance = Math.hypot(node.cx - parent.cx, node.cy - parent.cy);
                    return sum + (node.weight || 1) * Math.max(0, distance - node.radius - parent.radius);
                }, 0);
            }
            var previousScore = Infinity, stableRounds = 0;
            for (var iteration = 0; iteration < 260; iteration++) {
                resolveOverlaps();
                nodes.forEach(function (node) {
                    if (node === root || !node.parentNode) { return; }
                    var parent = node.parentNode, dx = node.cx - parent.cx, dy = node.cy - parent.cy, actual = Math.max(0.001, Math.hypot(dx, dy)), target = parent.radius + node.radius + minimumClearance;
                    if (actual <= target) { return; }
                    var pull = Math.min((actual - target) * 0.18 * node.weight, 10);
                    node.cx -= dx / actual * pull; node.cy -= dy / actual * pull; node.angle = Math.atan2(node.cy, node.cx); node.distance = Math.hypot(node.cx, node.cy); constrain(node);
                });
                var remainingOverlap = resolveOverlaps(), score = weightedVisibleLength();
                stableRounds = remainingOverlap < 0.05 && Math.abs(previousScore - score) < 0.05 ? stableRounds + 1 : 0;
                previousScore = score;
                if (stableRounds >= 6 && iteration > 25) { break; }
            }

            var extentX = Math.max.apply(null, nodes.map(function (node) { return Math.abs(node.cx) + node.radius; })), extentY = Math.max.apply(null, nodes.map(function (node) { return Math.abs(node.cy) + node.radius; })), minX = -extentX, maxX = extentX, minY = -extentY, maxY = extentY;
            nodes.forEach(function (node) { node.cx += margin - minX; node.cy += margin - minY; node.card.style.left = (node.cx - node.radius) + 'px'; node.card.style.top = (node.cy - node.radius) + 'px'; });
            worldWidth = maxX - minX + margin * 2; worldHeight = maxY - minY + margin * 2; world.style.width = worldWidth + 'px'; world.style.height = worldHeight + 'px';
        }
        radialLayout();
        function edgePoint(rect, target) { var center = {x: rect.left + rect.width / 2, y: rect.top + rect.height / 2}, dx = target.x - center.x, dy = target.y - center.y, halfWidth = rect.width / 2, halfHeight = rect.height / 2, factor = 1 / Math.max(Math.abs(dx) / halfWidth, Math.abs(dy) / halfHeight); return {x: center.x + dx * factor, y: center.y + dy * factor}; }
        function addBadge(person, x, y, role, label, childId) { if (!person) { return; } var badge = element('div', 'sg-map-link-badge sg-map-link-' + role), badgeScale = Math.max(0.42, Math.min(1, scale || 1)); badge.style.left = x + 'px'; badge.style.top = y + 'px'; badge.style.transform = 'translate(-50%,-50%) scale(' + badgeScale + ')'; badge.dataset.sgLinkChild = String(childId); badge.setAttribute('aria-label', label + ': ' + person.name); badge.appendChild(personRow(person, true)); badges.appendChild(badge); }
        function addLine(role, start, end, childId) { var dx = end.x - start.x, dy = end.y - start.y, distance = Math.max(1, Math.sqrt(dx * dx + dy * dy)), line = element('div', 'sg-map-line sg-map-line-' + role); line.dataset.sgLinkChild = String(childId); line.style.left = start.x + 'px'; line.style.top = start.y + 'px'; line.style.width = distance + 'px'; line.style.transform = 'rotate(' + Math.atan2(dy, dx) + 'rad)'; lineLayer.appendChild(line); }
        function drawLinks() {
            lineLayer.replaceChildren(); badges.replaceChildren();
            var mapRect = map.getBoundingClientRect(); if (!mapRect.width || !mapRect.height) { return; }
            links.forEach(function (link) {
                var parent = byId[link.parentId], child = byId[link.childId]; if (!parent || !child) { return; }
                var parentRect = parent.card.getBoundingClientRect(), childRect = child.card.getBoundingClientRect(), parentCenter = {x: parentRect.left + parentRect.width / 2, y: parentRect.top + parentRect.height / 2}, childCenter = {x: childRect.left + childRect.width / 2, y: childRect.top + childRect.height / 2}, parentEdge = edgePoint(parentRect, childCenter), childEdge = edgePoint(childRect, parentCenter), dx = childEdge.x - parentEdge.x, dy = childEdge.y - parentEdge.y, distance = Math.max(1, Math.sqrt(dx * dx + dy * dy)), separation = Math.max(4, 12 * Math.min(1, scale || 1)), perpendicular = {x: -dy / distance * separation, y: dx / distance * separation};
                var leaderStart = {x: parentEdge.x + perpendicular.x, y: parentEdge.y + perpendicular.y}, leaderEnd = {x: childEdge.x + perpendicular.x, y: childEdge.y + perpendicular.y}, delegateStart = {x: childEdge.x - perpendicular.x, y: childEdge.y - perpendicular.y}, delegateEnd = {x: parentEdge.x - perpendicular.x, y: parentEdge.y - perpendicular.y};
                [leaderStart, leaderEnd, delegateStart, delegateEnd].forEach(function (point) { point.x -= mapRect.left; point.y -= mapRect.top; });
                addLine('leader', leaderStart, leaderEnd, child.id);
                addLine('delegate', delegateStart, delegateEnd, child.id);
                addBadge(link.leader, leaderStart.x + (leaderEnd.x - leaderStart.x) * 0.46, leaderStart.y + (leaderEnd.y - leaderStart.y) * 0.46, 'leader', 'Kreisleitung → Tochterkreis', child.id);
                addBadge(link.delegate, delegateStart.x + (delegateEnd.x - delegateStart.x) * 0.46, delegateStart.y + (delegateEnd.y - delegateStart.y) * 0.46, 'delegate', 'Delegation ← Tochterkreis', child.id);
            });
        }
        function showPath(node, active) {
            var path = {}, current = node;
            while (current && current.parentId && byId[current.parentId]) { path[current.id] = true; current = byId[current.parentId]; }
            lineLayer.querySelectorAll('[data-sg-link-child]').forEach(function (line) { line.classList.toggle('is-path', active && !!path[line.dataset.sgLinkChild]); });
            badges.querySelectorAll('[data-sg-link-child]').forEach(function (badge) { badge.classList.toggle('is-path', active && !!path[badge.dataset.sgLinkChild]); });
        }
        nodes.forEach(function (node) {
            node.card.addEventListener('mouseenter', function () { showPath(node, true); });
            node.card.addEventListener('mouseleave', function () { showPath(node, false); });
            node.card.addEventListener('focusin', function () { showPath(node, true); });
            node.card.addEventListener('focusout', function (event) { if (!node.card.contains(event.relatedTarget)) { showPath(node, false); } });
        });
        var scale = 1, tx = 0, ty = 0, positioned = false;
        function apply() { world.style.transform = 'translate(' + tx + 'px,' + ty + 'px) scale(' + scale + ')'; window.requestAnimationFrame(drawLinks); }
        function position() {
            if (map.offsetWidth === 0) { return; }
            scale = Math.min(1, Math.max(0.12, Math.min((map.clientWidth - 88) / worldWidth, (map.clientHeight - 88) / worldHeight))); tx = (map.clientWidth - worldWidth * scale) / 2; ty = (map.clientHeight - worldHeight * scale) / 2; positioned = true; apply(); drawLinks();
        }
        function changeZoom(factor) { scale = Math.max(0.42, Math.min(1.7, scale * factor)); positioned = true; apply(); }
        function keepCardVisible(card) {
            var cardRect = card.getBoundingClientRect(), mapRect = map.getBoundingClientRect(), padding = 24, dx = 0, dy = 0;
            if (cardRect.left < mapRect.left + padding) { dx = mapRect.left + padding - cardRect.left; } else if (cardRect.right > mapRect.right - padding) { dx = mapRect.right - padding - cardRect.right; }
            if (cardRect.top < mapRect.top + padding) { dy = mapRect.top + padding - cardRect.top; } else if (cardRect.bottom > mapRect.bottom - padding) { dy = mapRect.bottom - padding - cardRect.bottom; }
            if (dx || dy) { tx += dx; ty += dy; apply(); }
        }
        zoomIn.addEventListener('click', function () { changeZoom(1.18); }); zoomOut.addEventListener('click', function () { changeZoom(0.85); }); reset.addEventListener('click', position); map.addEventListener('sg:shown', position); map.addEventListener('sg:node-size-changed', function (event) { window.requestAnimationFrame(function () { drawLinks(); keepCardVisible(event.target); }); });
        map.addEventListener('wheel', function (event) { event.preventDefault(); var rect = map.getBoundingClientRect(), beforeX = (event.clientX - rect.left - tx) / scale, beforeY = (event.clientY - rect.top - ty) / scale; changeZoom(event.deltaY < 0 ? 1.12 : 0.89); tx = event.clientX - rect.left - beforeX * scale; ty = event.clientY - rect.top - beforeY * scale; apply(); }, {passive: false});
        map.addEventListener('keydown', function (event) { if (event.key === '+' || event.key === '=') { event.preventDefault(); changeZoom(1.18); } if (event.key === '-') { event.preventDefault(); changeZoom(0.85); } if (event.key === 'Home') { event.preventDefault(); position(); } });
        var drag = null; map.addEventListener('pointerdown', function (event) { if (event.target.closest('a, button')) { return; } drag = {x: event.clientX, y: event.clientY, tx: tx, ty: ty}; map.setPointerCapture(event.pointerId); }); map.addEventListener('pointermove', function (event) { if (!drag) { return; } tx = drag.tx + event.clientX - drag.x; ty = drag.ty + event.clientY - drag.y; positioned = true; apply(); }); map.addEventListener('pointerup', function () { drag = null; }); window.requestAnimationFrame(function () { if (!positioned) { position(); } });
    }
    function init() { initTabs(); document.querySelectorAll('[data-sg-circle-map]').forEach(initMap); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
    if (window.jQuery) { window.jQuery(document).on('humhub:ready', init); }
}());
