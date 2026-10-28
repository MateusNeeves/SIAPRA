const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const script = fs.readFileSync(require('node:path').join(__dirname, '../public/js/produtos-modais.js'), 'utf8');

function ambiente(ids) {
    let dono;
    const avisos = [];
    const document = { activeElement: null };
    const instancias = [];
    function elemento(id) {
        const classes = new Set();
        const listeners = {};
        return { id, style: {}, attrs: {}, inert: false, isConnected: true, visivel: true, focusable: false,
            focus() { document.activeElement = this; this.onFocus?.(); },
            contains(outro) { return outro === this || Boolean(outro?.parent && this.contains(outro.parent)); },
            matches(seletor) { return seletor === ':disabled' ? Boolean(this.disabled) : this.focusable; },
            closest() { return this.inert || this.hidden || this.attrs['aria-hidden'] === 'true' ? this : this.parent?.closest(); },
            getClientRects() { return this.visivel ? [{}] : []; },
            classList: { contains: c => classes.has(c), add: c => classes.add(c), remove: c => classes.delete(c) },
            addEventListener: (e, cb) => { listeners[e] = cb; },
            emitir(e) { listeners[e]?.(); },
            setAttribute(k, v) { this.attrs[k] = v; },
            removeAttribute(k) { delete this.attrs[k]; },
        };
    }
    const elementos = Object.fromEntries(ids.map(id => [id, elemento(id)]));
    const body = elemento('body');
    class Modal {
        constructor(el, config) {
            this.el = el;
            this.config = config;
            this._focustrap = {
                _isActive: false,
                activate() {
                    if (this._isActive) return;
                    el.focus(); // autofocus real ocorre antes de registrar os listeners.
                    dono = undefined; // off(document, '.bs.focustrap') global
                    dono = el.id; // dois listeners (focusin / keydown.tab)
                    this._isActive = true;
                },
                deactivate() {
                    if (!this._isActive) return;
                    this._isActive = false;
                    dono = undefined; // remove inclusive listeners registrados por outro trap
                },
            };
            instancias.push(this);
        }
        show() { this.el.emitir('show.bs.modal'); }
        iniciarTransicao() { this.el.classList.add('show'); }
        terminarTransicao() {
            if (this.config.focus) this._focustrap.activate();
            this.el.emitir('shown.bs.modal');
        }
        hide() {
            this._focustrap.deactivate();
            this.el.classList.remove('show');
            body.classList.remove('modal-open');
            body.style.overflow = '';
            this.el.emitir('hidden.bs.modal');
        }
    }
    Object.assign(document, { querySelector: id => elementos[id], body });
    const contexto = { document, bootstrap: { Modal }, console: { warn: (...args) => avisos.push(args) },
        getComputedStyle: el => ({ visibility: el.style.visibility || 'visible' }) };
    vm.runInNewContext(script, contexto);
    contexto.abrirModaisProdutos(ids);
    return { elementos, instancias, body, document, avisos, dono: () => dono,
        campo(modal) { return Object.assign(elemento('campo'), { parent: elementos[modal], focusable: true }); },
        listeners: () => dono ? 2 : 0 };
}

for (const superior of ['#viewMovModal', '#makeMovModal', '#movInModal', '#movOutSelectModal', '#movOutModal']) {
    for (const ordem of [[0, 1], [1, 0]]) {
        test(`${superior}: transições ${ordem}, fechamento e reabertura`, () => {
            const a = ambiente(['#viewModal', superior]);
            a.instancias.forEach(i => { assert.equal(i.config.focus, false); i.iniciarTransicao(); });
            a.instancias[ordem[0]].terminarTransicao();
            assert.equal(a.dono(), ordem[0] === 1 ? superior : undefined);
            a.instancias[ordem[1]].terminarTransicao();
            assert.equal(a.dono(), superior);
            assert.equal(a.elementos['#viewModal'].inert, true);
            assert.equal(a.elementos[superior].inert, false);
            a.instancias[1].hide();
            assert.equal(a.dono(), '#viewModal');
            assert.equal(a.elementos['#viewModal'].inert, false);
            assert.equal(a.elementos['#viewModal'].attrs['aria-modal'], 'true');
            assert.equal(a.elementos['#viewModal'].style.zIndex, 1051);
            assert.equal(a.body.style.overflow, 'hidden');
            a.instancias[1].show();
            assert.equal(a.dono(), undefined);
            a.instancias[1].iniciarTransicao();
            a.instancias[1].terminarTransicao();
            assert.equal(a.dono(), superior);
            a.instancias[1].hide();
            a.instancias[0].hide();
            assert.equal(a.dono(), undefined);
            assert.equal(a.body.classList.contains('modal-open'), false);
        });
    }
}

test('modal inferior não ganha foco enquanto o superior aguarda o backdrop', () => {
    const a = ambiente(['#viewModal', '#movInModal']);
    a.instancias[0].iniciarTransicao();
    a.instancias[0].terminarTransicao();
    assert.equal(a.dono(), undefined);
    a.instancias[1].iniciarTransicao();
    a.instancias[1].terminarTransicao();
    assert.equal(a.dono(), '#movInModal');
});

test('edição isolada mantém o foco e libera ao fechar', () => {
    const a = ambiente(['#editModal']);
    a.instancias[0].iniciarTransicao();
    a.instancias[0].terminarTransicao();
    assert.equal(a.dono(), '#editModal');
    a.instancias[0].hide();
    assert.equal(a.dono(), undefined);
});

test('fechar o superior antes de terminar a transição inferior restaura o foco ao concluir', () => {
    const a = ambiente(['#viewModal', '#movInModal']);
    a.instancias.forEach(i => i.iniciarTransicao());
    a.instancias[1].terminarTransicao();
    a.instancias[1].hide();
    assert.equal(a.dono(), undefined);
    a.instancias[0].terminarTransicao();
    assert.equal(a.dono(), '#viewModal');
    assert.equal(a.elementos['#viewModal'].inert, false);
});

for (const ordem of [[0, 1], [1, 0]]) {
    test(`campo focado durante a animação permanece focado: ${ordem}`, () => {
        const a = ambiente(['#viewModal', '#movInModal']);
        a.instancias.forEach(i => i.iniciarTransicao());
        const campo = a.campo('#movInModal');
        campo.focus();
        a.instancias[ordem[0]].terminarTransicao();
        a.instancias[ordem[1]].terminarTransicao();
        assert.equal(a.document.activeElement, campo);
        assert.equal(a.dono(), '#movInModal');
        assert.equal(a.listeners(), 2);
        a.instancias[1].hide();
        assert.equal(a.document.activeElement, a.elementos['#viewModal']);
        a.instancias[1].show();
        a.instancias[1].iniciarTransicao();
        campo.focus();
        a.instancias[1].terminarTransicao();
        assert.equal(a.document.activeElement, campo);
        assert.equal(a.listeners(), 2);
    });
}

for (const caso of ['nenhum', 'externo', 'removido', 'oculto', 'invisivel', 'desabilitado', 'removido-no-autofocus']) {
    test(`não restaura foco inválido: ${caso}`, () => {
        const a = ambiente(['#viewModal', '#movInModal']);
        a.instancias.forEach(i => i.iniciarTransicao());
        const campo = a.campo(caso === 'externo' ? '#viewModal' : '#movInModal');
        if (caso !== 'nenhum') campo.focus();
        if (caso === 'removido') campo.isConnected = false;
        if (caso === 'oculto') campo.visivel = false;
        if (caso === 'invisivel') campo.style.visibility = 'hidden';
        if (caso === 'desabilitado') campo.disabled = true;
        if (caso === 'removido-no-autofocus') a.elementos['#movInModal'].onFocus = () => { campo.isConnected = false; };
        a.instancias[0].terminarTransicao();
        a.instancias[1].terminarTransicao();
        assert.equal(a.document.activeElement, a.elementos['#movInModal']);
        assert.equal(a.dono(), '#movInModal');
    });
}

test('repara flags ativas com listeners globais removidos por outro trap', () => {
    const a = ambiente(['#viewModal', '#movInModal']);
    a.instancias.forEach(i => { i.iniciarTransicao(); i.terminarTransicao(); });
    const [inferior, superior] = a.instancias.map(i => i._focustrap);
    inferior.activate(); // Ambos têm _isActive=true; os listeners agora são do inferior.
    inferior.deactivate(); // Remove listeners globais, mas não muda _isActive do superior.
    assert.equal(superior._isActive, true);
    assert.equal(a.listeners(), 0);
    superior.activate(); // Bootstrap retorna cedo: não repara o namespace.
    assert.equal(a.listeners(), 0);
    a.elementos['#viewModal'].emitir('shown.bs.modal');
    assert.equal(a.dono(), '#movInModal');
    assert.equal(inferior._isActive, false);
    assert.equal(superior._isActive, true);
    assert.equal(a.listeners(), 2);
});

for (const ausente of [null, {}, { activate() {} }]) {
    test(`estrutura interna ausente/incompleta não causa exceção: ${JSON.stringify(ausente)}`, () => {
        const a = ambiente(['#viewModal', '#movInModal']);
        a.instancias.forEach(i => i.iniciarTransicao());
        a.instancias[1]._focustrap = ausente;
        assert.doesNotThrow(() => a.instancias[0].terminarTransicao());
        assert.doesNotThrow(() => a.elementos['#movInModal'].emitir('shown.bs.modal'));
        assert.equal(a.dono(), undefined); // Nunca entrega foco ao inferior ainda coberto.
        assert.equal(a.avisos.length, 1);
        a.elementos['#movInModal'].classList.remove('show');
        a.elementos['#movInModal'].emitir('hidden.bs.modal');
        assert.equal(a.dono(), '#viewModal');
        assert.equal(a.avisos.length, 1);
    });
}

test('instância destruída e elemento desconectado permitem restauração do inferior', () => {
    const a = ambiente(['#viewModal', '#movInModal']);
    a.instancias.forEach(i => { i.iniciarTransicao(); i.terminarTransicao(); });
    a.instancias[1]._focustrap.deactivate();
    a.instancias[1]._focustrap = null; // Modal.dispose() limpa as propriedades.
    a.elementos['#movInModal'].isConnected = false;
    a.elementos['#movInModal'].emitir('hidden.bs.modal');
    assert.equal(a.dono(), '#viewModal');
    assert.equal(a.listeners(), 2);
});

test('inicialização mantém a instância jQuery do Select2 mesmo após troca do global', () => {
    const blade = fs.readFileSync(require('node:path').join(__dirname, '../resources/views/produtos/layout.blade.php'), 'utf8');
    const inicializacao = [...blade.matchAll(/<script>([\s\S]*?)<\/script>/g)]
        .map(match => match[1]).find(code => code.includes('abrirModaisProdutos'))
        .replace(/\{!! json_encode\(Session::get\('modal'\)\) !!\}/, '["#editModal"]');
    let carregar;
    let abertos;
    const chamadas = [];
    const select = {};
    const modal = {};
    const janela = {};
    const jquery = alvo => {
        if (alvo === janela) return { on: (evento, cb) => { carregar = cb; } };
        if (alvo === '.modal .multiple-select') return { each: cb => cb.call(select) };
        assert.equal(alvo, select);
        return { data: () => true, closest: () => modal, select2: config => chamadas.push(config) };
    };
    janela.jQuery = jquery;
    const contexto = { window: janela, abrirModaisProdutos: ids => { abertos = ids; } };
    vm.runInNewContext(inicializacao, contexto);
    janela.jQuery = () => { throw new Error('Outro jQuery sem Select2'); };
    carregar();
    assert.equal(chamadas[0], 'destroy');
    assert.equal(chamadas[1].theme, 'bootstrap-5');
    assert.equal(chamadas[1].dropdownParent, modal);
    assert.equal(abertos[0], '#editModal');
});
