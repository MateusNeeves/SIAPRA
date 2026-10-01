const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

const blade = fs.readFileSync(path.join(__dirname, '../resources/views/produtos/visualizar.blade.php'), 'utf8');
const script = [...blade.matchAll(/<script>([\s\S]*?)<\/script>/g)]
    .map(match => match[1]).find(code => code.includes('function atualizarDescricao'));

test('descrição segue a regra renderizada para qualquer ID e ao trocar a seleção', () => {
    for (const id of ['1', '2', '3', '77']) {
        for (const exige of ['0', '1']) {
            let change;
            const select = { value: id, selectedIndex: 0, options: [{ dataset: { exigeDescricao: exige } }],
                addEventListener: (event, callback) => { change = callback; } };
            const container = { style: {} };
            const descricao = { value: 'Motivo', required: false };
            const elements = { status_lote: select, descricao_status_lote_container: container, descricao_status_lote: descricao };
            vm.runInNewContext(script, { document: {
                getElementById: id => elements[id], addEventListener: (event, callback) => callback(),
            } });
            assert.equal(descricao.required, exige === '1');
            assert.equal(container.style.display, exige === '1' ? 'block' : 'none');
            assert.equal(descricao.value, exige === '1' ? 'Motivo' : '');
            select.options = [{ dataset: {} }];
            select.value = '';
            change();
            assert.equal(descricao.required, false);
            assert.equal(container.style.display, 'none');
        }
    }
});
