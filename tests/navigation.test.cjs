// SPDX-License-Identifier: AGPL-3.0-only
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require('node:path').join(__dirname, '../resources/governance-start.js'), 'utf8');
for (const mobile of [false, true]) {
    const navigation = {dataset: {}, open: true};
    vm.runInNewContext(source, {document: {readyState: 'complete', querySelectorAll: () => [navigation]}, window: {matchMedia: () => ({matches: mobile})}});
    assert.equal(navigation.open, !mobile);
    navigation.open = !navigation.open;
    const toggled = navigation.open;
    vm.runInNewContext(source, {document: {readyState: 'complete', querySelectorAll: () => [navigation]}, window: {matchMedia: () => ({matches: mobile})}});
    assert.equal(navigation.open, toggled, 'Reinitialization preserves the user toggle');
}
console.log('PASS: sidebar starts expanded on desktop, collapsed on mobile, and preserves user toggles.');
