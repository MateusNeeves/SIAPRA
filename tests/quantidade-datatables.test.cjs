const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

// Sem dependência nova no projeto: apontar para o JS 2.0.3 servido pela CDN do layout.
const fonte = process.env.DATATABLES_SOURCE;
test('DataTables 2.0.3 detecta e ordena quantidades com a configuração real', {
    skip: !fonte && 'Defina DATATABLES_SOURCE com o arquivo dataTables.js 2.0.3 da CDN.',
}, () => {
    const source = fs.readFileSync(fonte, 'utf8');
    assert.match(source, /DataTables 2\.0\.3/);
    const tables = fs.readFileSync(path.join(__dirname, '../public/js/tables.js'), 'utf8');
    assert.match(tables, /"decimal":\s*","/);
    const contexto = {
        _re_dic: {},
        _fnEscapeRegex: texto => texto.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'),
        DataTable: { type(nome, tipo) { contexto.tipo = tipo; } },
    };
    // Executa o detector e o ordenador originais, incluindo a normalização decimal.
    for (const nome of ['_empty', '_numToDecimal', '_isNumber', '__numericReplace']) {
        const bloco = source.match(new RegExp('var ' + nome + ' = function[\\s\\S]*?\\n\\t};'));
        assert.ok(bloco, nome);
        vm.runInNewContext(bloco[0], contexto);
    }
    vm.runInNewContext(source.match(/DataTable\.type\('num', \{[\s\S]*?\n\t\}\);/)[0], contexto);
    const configuracao = { oLanguage: { sDecimal: ',' } };
    const valores = ['1500', '10', '2', '1,5', '0,125', '9999999,999', '2,75', '-1,5'];
    for (const valor of valores) assert.equal(contexto.tipo.detect(valor, configuracao), 'num');
    const ordenar = valor => contexto.tipo.order.pre(valor, configuracao);
    assert.deepEqual(valores.sort((a, b) => ordenar(a) - ordenar(b)),
        ['-1,5', '0,125', '1,5', '2', '2,75', '10', '1500', '9999999,999']);
    assert.equal(ordenar('9999999,999'), 9999999.999);
});
