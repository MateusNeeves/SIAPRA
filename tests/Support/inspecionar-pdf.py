"""QA opcional com PyMuPDF: texto, fontes, limites, cabeçalhos e rasterização.

Uso: py tests/Support/inspecionar-pdf.py storage/app/findings-qa/f6-f7-*.pdf
PyMuPDF pode ficar em diretório temporário indicado por PYTHONPATH.
"""
import glob
import json
import re
import sys
from html.parser import HTMLParser
from pathlib import Path

import pymupdf


class Celulas(HTMLParser):
    def __init__(self):
        super().__init__()
        self.tag = None
        self.atual = []
        self.valores = []
        self.cabecalhos = []

    def handle_starttag(self, tag, attrs):
        if tag in ('td', 'th'):
            self.tag, self.atual = tag, []

    def handle_data(self, data):
        if self.tag:
            self.atual.append(data)

    def handle_endtag(self, tag):
        if tag == self.tag:
            (self.cabecalhos if tag == 'th' else self.valores).append(''.join(self.atual))
            self.tag = None


def compacto(texto):
    return re.sub(r'\s+', '', texto)


for argumento in sys.argv[1:]:
    for arquivo in glob.glob(argumento):
        caminho = Path(arquivo)
        doc = pymupdf.open(caminho)
        html = Celulas()
        html.feed(caminho.with_suffix('.html').read_text(encoding='utf-8'))
        texto = compacto(''.join(p.get_text() for p in doc))
        ausentes = [v for v in html.valores if compacto(v) not in texto]
        assert not ausentes, f'Texto ausente em {arquivo}: {ausentes[:2]}'
        # Colunas estreitas + overflow: wrap podem partir datas ("29/10/202" + "6").
        linhas = {''.join(s['text'] for s in l['spans']).strip() for p in doc
                  for b in p.get_text('dict')['blocks'] if 'lines' in b for l in b['lines']}
        datas = [v for v in html.valores if re.fullmatch(r'\d{2}/\d{2}/\d{4}', v.strip())]
        partidas = [d for d in datas if d.strip() not in linhas]
        assert not partidas, f'Data quebrada em {arquivo}: {partidas[:2]}'
        palavras = {p for linha in linhas for p in linha.split()}
        cortadas = [p for h in html.cabecalhos for p in h.split() if p not in palavras]
        assert not cortadas, f'Palavra de cabeçalho partida em {arquivo}: {cortadas[:3]}'
        tamanhos, cores, fora, sobreposicoes = [], set(), [], []
        for indice, pagina in enumerate(doc):
            conteudo = compacto(pagina.get_text())
            assert all(compacto(h) in conteudo for h in html.cabecalhos), f'Cabeçalho ausente: página {indice + 1}'
            spans = [s for b in pagina.get_text('dict')['blocks'] if 'lines' in b for l in b['lines'] for s in l['spans'] if s['text'].strip()]
            for i, span in enumerate(spans):
                tamanhos.append(span['size'])
                caixa = pymupdf.Rect(span['bbox'])
                if not pagina.rect.contains(caixa):
                    fora.append([indice + 1, span['text'], list(caixa)])
                for outro in spans[i + 1:]:
                    intersecao = caixa & pymupdf.Rect(outro['bbox'])
                    if not intersecao.is_empty and intersecao.width > 1 and intersecao.height > 1:
                        sobreposicoes.append([indice + 1, span['text'], outro['text']])
            for desenho in pagina.get_drawings():
                if desenho['fill']:
                    cores.add(tuple(round(c * 255) for c in desenho['fill']))
            if indice in (0, len(doc) - 1):
                pagina.get_pixmap(matrix=pymupdf.Matrix(1.3, 1.3)).save(caminho.with_name(caminho.stem + f'-p{indice + 1}.png'))
        assert not fora, f'Texto fora da página: {fora[:2]}'
        assert not sobreposicoes, f'Texto sobreposto: {sobreposicoes[:2]}'
        assert min(tamanhos) >= 8, f'Fonte pequena: {min(tamanhos)}'
        assert (247, 247, 247) in cores, 'Zebrado não encontrado no PDF'
        if 'itens' in caminho.stem:
            assert (253, 236, 234) in cores, 'Destaque de estoque baixo não encontrado'
        print(json.dumps({'arquivo': str(caminho), 'paginas': len(doc), 'fonte_min': min(tamanhos),
            'fonte_max': max(tamanhos), 'celulas_preservadas': len(html.valores),
            'cores': sorted(cores), 'fora_da_pagina': len(fora), 'sobreposicoes': len(sobreposicoes)}))
