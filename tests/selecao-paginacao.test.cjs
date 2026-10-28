const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const read = file => fs.readFileSync(path.join(__dirname, '..', file), 'utf8');

// Harness sem dependências: simula destacamento de linhas fora da página e
// eventos da API. Não substitui a homologação do DOM/CSS do DataTables.
function ambiente(total = 11) {
    const rows = Array.from({ length: total }, (_, i) => ({ id: String(100 + i), idx: String(i), qtd: '4', selected: false }));
    let filtered = rows.slice();
    let start = 0;
    let config;
    const handlers = {};
    const values = {};
    const disabled = {};
    const document = {};
    const ready = [];
    const clicks = [];
    const searches = [];
    const visible = () => filtered.slice(start, start + (config.pageLength ?? 10));
    const emit = event => (handlers[event] || []).forEach(fn => fn());
    const api = {
        on(events, fn) { events.split(' ').forEach(event => (handlers[event] ||= []).push(fn)); return this; },
        rows() { return { deselect() { rows.forEach(row => { row.selected = false; }); } }; },
        columns() { return { eq() { return { each(fn) { fn(0); } }; } }; },
        column() { return { search(value) {
            filtered = rows.filter(row => row.id.includes(value));
            start = 0;
            emit('search.dt');
            return { draw() {} };
        } }; },
    };
    function $(selector) {
        if (selector === document) return {
            ready(fn) { ready.push(fn); },
            on(event, fn) { assert.equal(event, 'click'); clicks.push(fn); },
        };
        const selected = selector.includes('.selected') ? visible().filter(row => row.selected) : [];
        return {
            0: {}, length: selected.length,
            each() {},
            DataTable(options) { if (selector === '#myTableSelect') config = options; return api; },
            on(event, fn) { if (selector === 'input') searches.push(fn); },
            prop(key, value) { selector.split(', ').forEach(id => { disabled[id] = value; }); },
            text(value) {
                if (value !== undefined) { values[selector] = value; return; }
                const field = selector.endsWith('.idx') ? 'idx' : selector.endsWith('.qtd') ? 'qtd' : 'id';
                return selected.map(row => row[field]).join('');
            },
            val(value) { values[selector] = value; },
            html(value) { values[selector] = value; },
            css() {},
            modal() {},
        };
    }
    $.fn = { dataTable: { Buttons: { defaults: { dom: { button: {} } } } } };
    const context = vm.createContext({ $, document, console: { log() {} } });
    vm.runInContext(read('public/js/tables.js'), context);
    ready.forEach(fn => fn());
    return {
        config, context, values, disabled, visible, rows,
        select(index) { rows.forEach(row => { row.selected = false; }); visible()[index].selected = true; clicks.forEach(fn => fn()); },
        page(index) { start = index * (config.pageLength ?? 10); emit('page.dt'); },
        search(text) { searches.at(-1).call({ value: text }); },
    };
}

test('configuração executada mantém paginação, contador, português e dez linhas', () => {
    const a = ambiente();
    assert.notEqual(a.config.paging, false);
    assert.equal(a.config.pageLength ?? 10, 10);
    assert.equal(a.config.select, true);
    assert.equal(a.config.ordering, false);
    assert.equal(a.config.layout.bottomStart, 'info');
    assert.equal(a.config.layout.bottomEnd, 'paging');
    assert.equal(a.config.language.paginate.next, 'Próximo');
    assert.equal(a.config.language.paginate.previous, 'Anterior');
    assert.equal(a.config.language.paginate.first, 'Primeiro');
    assert.equal(a.config.language.paginate.last, 'Último');
    const info = (start, end) => a.config.language.info.replace('_START_', start).replace('_END_', end).replace('_TOTAL_', 11);
    assert.equal(info(1, 10), 'Mostrando de 1 até 10 de 11 registros');
    assert.equal(info(11, 11), 'Mostrando de 11 até 11 de 11 registros');
    assert.equal(a.config.language.infoEmpty, 'Nenhum registro encontrado');
    assert.equal(a.config.language.infoFiltered.replace('_MAX_', 11), '(Filtrados de 11 registros)');
    assert.equal(a.visible().length, 10);
    a.page(1);
    assert.deepEqual(a.visible().map(row => row.id), ['110']);
});

for (const [name, file, button, field] of [
    ['quarentena', 'resources/views/quarentena/visualizar.blade.php', 'remove_button', '#id_lote'],
    ['saída', 'resources/views/produtos/layout.blade.php', 'selectButton', '#id_lote'],
    ['vencidos', 'resources/views/produtos/layout.blade.php', 'exp_button', '#id_exp'],
    ['logs', 'resources/views/logs/pesquisar.blade.php', 'details_button', '#detailsLogModal .modal-body #id_log'],
]) {
    test(`${name}: handler real usa a linha da página 2 e exige nova seleção após navegar/filtrar`, () => {
        const a = ambiente();
        const blade = read(file);
        const scripts = [...blade.matchAll(/<script>([\s\S]*?)<\/script>/g)].map(match => match[1]);
        let script = scripts.find(code => code.includes(`$('#${button}').prop`));
        assert.ok(script, 'handler de habilitação real');
        script = script.replace('@json($logs)', JSON.stringify(a.rows.map(row => ({ id_log: row.id, descricao: 'Teste' }))));
        vm.runInContext(script, a.context);
        const tag = [...blade.matchAll(/<button\b[^>]*>/g)].map(match => match[0]).find(tag => tag.includes(`id="${button}"`));
        const action = tag.match(/onclick="([^"]+)"/)[1];
        function click() {
            if (a.disabled['#' + button]) return false;
            vm.runInContext(action, a.context);
            return true;
        }
        a.select(0);
        a.page(1);
        assert.equal(a.disabled['#' + button], true);
        assert.equal(a.rows.some(row => row.selected), false);
        assert.equal(click(), false);
        a.select(0);
        assert.equal(click(), true);
        assert.equal(a.values[field], '110');
        if (name === 'saída') assert.equal(a.values['#qtd_estoque_lote'], '4');
        a.page(0);
        assert.equal(click(), false);
        assert.equal(a.rows.some(row => row.selected), false);
        a.select(2);
        assert.equal(click(), true);
        assert.equal(a.values[field], '102');
        a.search('110');
        assert.equal(click(), false);
        assert.equal(a.rows.some(row => row.selected), false);
        assert.deepEqual(a.visible().map(row => row.id), ['110']);
        a.select(0);
        assert.equal(click(), true);
        assert.equal(a.values[field], '110');
    });
}

test('tabelas pequenas, vazias e grandes mantêm todos os registros acessíveis', () => {
    for (const total of [0, 3, 10, 12, 105]) {
        const a = ambiente(total);
        const seen = [];
        for (let page = 0; page < Math.ceil(total / 10); page++) {
            a.page(page);
            assert.ok(a.visible().length <= 10);
            seen.push(...a.visible().map(row => row.id));
        }
        assert.deepEqual(seen, a.rows.map(row => row.id));
    }
});

test('motor original de paginação DataTables 2.0.3 alcança o 11º registro', {
    skip: !process.env.DATATABLES_SOURCE && 'Defina DATATABLES_SOURCE com o JS 2.0.3 da CDN do layout.',
}, () => {
    const source = fs.readFileSync(process.env.DATATABLES_SOURCE, 'utf8');
    assert.match(source, /DataTables 2\.0\.3/);
    const fn = source.match(/function _fnPageChange \([\s\S]*?\n\t\}/)[0];
    const events = [];
    const context = vm.createContext({ _fnCallbackFire: (_, __, event) => events.push(event), _fnDraw() {} });
    vm.runInContext(fn, context);
    for (const total of [3, 11, 105]) {
        const settings = { _iDisplayStart: 0, _iDisplayLength: 10, fnRecordsDisplay: () => total };
        context._fnPageChange(settings, 'next', true);
        assert.equal(settings._iDisplayStart, total > 10 ? 10 : 0);
        context._fnPageChange(settings, 'last', true);
        assert.equal(settings._iDisplayStart, Math.floor((total - 1) / 10) * 10);
        context._fnPageChange(settings, 'first', true);
        assert.equal(settings._iDisplayStart, 0);
    }
    assert.ok(events.includes('page'));
});
