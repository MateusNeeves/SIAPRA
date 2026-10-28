# Verificação das pendências 01–04

## Segurança e execução reproduzível

Não executar `artisan test` com a configuração de banco do `.env`. Os testes novos
usam uma conexão explícita para `127.0.0.1:55438`, usuário `siapra_test`, banco
`postgres`, com `search_path = pg_temp`, transação e tabelas temporárias. Não usam
migrations nem credenciais do banco da aplicação. `SIAPRA_TEST_PGSQL=1` habilita
esses testes; sem a variável, os testes PostgreSQL são explicitamente ignorados.

Para preparar uma instância descartável, em PowerShell na raiz do projeto:

```powershell
# Somente para um diretório novo. Não apontar para uma instância real.
initdb -D C:/dev/SIAPRA/storage/framework/testing/pendencias-pg -U siapra_test -A trust --encoding=UTF8 --locale=C
pg_ctl -D C:/dev/SIAPRA/storage/framework/testing/pendencias-pg -l C:/dev/SIAPRA/storage/framework/testing/pendencias-pg/server.log -o "-p 55438 -h 127.0.0.1" -w start
```

Após verificar que a porta corresponde à instância descartável:

```powershell
$env:SIAPRA_TEST_PGSQL='1'
$env:TOTAL_ESTOQUE_PGSQL='1'
$env:DB_CONNECTION='sqlite'
$env:DB_HOST='127.0.0.1'
$env:DB_PORT='55438'
$env:DB_USERNAME='siapra_test'
$env:DB_PASSWORD=''
$env:DB_DATABASE='postgres'
$env:DB_URL=''
php -d extension=pdo_sqlite vendor/bin/phpunit tests/Unit/QuantidadeTest.php tests/Unit/QuarentenaTest.php tests/Feature/QuantidadeProdutoTest.php tests/Feature/InventarioRelatorioTest.php tests/Feature/ItensAVencerRelatorioTest.php tests/Feature/TotalEstoqueTest.php tests/Feature/StatusLoteTest.php --do-not-cache-result
```

O PHP local já possui a DLL `pdo_sqlite`; `-d extension=pdo_sqlite` a habilita
somente nesse processo, sem editar `php.ini`. Se a extensão já estiver habilitada,
omitir essa opção. Os testes StatusLote usam SQLite em memória. O teste TotalEstoque
usa sua opção PostgreSQL existente, apontada explicitamente para a instância acima.

Para verificar o ordenador original da versão utilizada pelo layout, sem adicionar
dependências ao projeto:

```powershell
Invoke-WebRequest -Uri 'https://cdn.datatables.net/2.0.3/js/dataTables.js' -OutFile storage/framework/testing/datatables-2.0.3.js
$env:DATATABLES_SOURCE='storage/framework/testing/datatables-2.0.3.js'
node --test tests/status-lote.test.cjs tests/produtos-modais.test.cjs tests/quantidade-datatables.test.cjs
```

Sem `DATATABLES_SOURCE`, apenas o teste de ordenação é ignorado. Ele executa o
detector/ordenador originais com a configuração decimal real, sem simular um DOM
ou alegar interação em navegador. A configuração global do DataTables não mudou.

## Evidência obtida

- PHP: **252 testes, 2.335 asserções, nenhuma falha** no conjunto acima.
- Node: **16 testes aprovados**, incluindo a função existente `atualizarDescricao()`.
- Sintaxe: `php -l` nos 11 arquivos PHP alterados/criados; `node --check` no novo JS.
- `git diff --check`: sem erros de whitespace.
- A primeira execução de StatusLote falhou por falta do driver ativo; passou após
  habilitação transitória da DLL já instalada.
- A regressão TotalEstoque identificou uma fixture que inseria ID explícito sem
  avançar SERIAL. A fixture agora usa o ID gerado (continua sendo 1), sem alterar
  nenhuma expectativa de estoque, aprovação ou quarentena.
- Os testes de PDF capturam o HTML do controller real e executam mPDF real em
  subprocesso. FPDI reabre os arquivos e verifica a quantidade mínima de páginas.
  Artefatos ignorados pelo Git: `storage/app/p01-p04-qa/{itens-0,itens-1,itens-120,inventario}.pdf`.
- Não foi feita inspeção visual dos PDFs; Poppler não está disponível no PATH.
  Geração/reabertura não comprovam ausência de cortes ou repetição visual correta.
- Não foi executada a suíte inteira de autenticação/perfil nem usado banco real.

## Decisões e limites

P01 usa operações de string, sem arredondamento ou conversão a float. Inputs
normalizam somente o fallback do banco, mantendo `old()` intacto. A integração
submete o formulário renderizado ao controller real e confirma igualdade exata
dos valores `NUMERIC(10,3)` antes e depois, em tabelas temporárias.

P02 calcula início e fim em uma CTE PostgreSQL, usando um binding inteiro e
`NOW() + CAST(? AS integer) * INTERVAL '1 month'`. O mesmo limite governa ambos os
filtros e o texto do PDF, inclusive no resultado vazio. Preservam-se a comparação
com timestamp, a regra por produto e os critérios da consulta principal, que
continuam permitindo lotes com saldo zero, reprovados ou em quarentena.

P03 mantém o saldo atual dos lotes recebidos até a referência. Não representa
estoque histórico. Nenhuma dependência interna do nome antigo do PDF foi encontrada.

P04 foi fundamentada no código do Bootstrap **5.3.3**, carregado pela CDN do layout:

- [Modal](https://github.com/twbs/bootstrap/blob/v5.3.3/js/src/modal.js)
- [FocusTrap](https://github.com/twbs/bootstrap/blob/v5.3.3/js/src/util/focustrap.js)

As transições automáticas não ativam foco. O script local ativa apenas o FocusTrap
do modal superior pronto, respeitando também modais superiores ainda em transição.
Ao fechar, restaura foco, atributos de acessibilidade e estado visual do inferior.
O uso de `_focustrap` é interno ao Bootstrap e precisa ser reavaliado ao trocar sua
versão. Não há `focus()` arbitrário no campo Lote Fabricante.

Select2 permanece dentro do seu modal via `dropdownParent`. A inicialização captura
a instância jQuery que possui o plugin, pois o layout existente também carrega
outro jQuery com `defer`. Essa duplicação global não foi alterada.

Os testes Node verificam a coordenação de eventos com doubles que reproduzem a
ativação/desativação do namespace compartilhado. **Não reproduzem o defeito
intermitente em um navegador e não comprovam clique, cursor ou Tab reais.**

## Roteiro manual obrigatório antes de homologar P04

Usar ambiente de homologação, sem registrar movimentações em dados reais:

1. Abrir visualização e entrada. Clicar em Lote Fabricante e digitar imediatamente
   durante a abertura; repetir após a transição. Verificar cursor e texto contínuos.
2. Navegar com Tab e Shift+Tab, inclusive nas extremidades do formulário. O modal
   inferior não deve receber foco enquanto o superior estiver aberto.
3. Trocar fabricante e Status Lote; verificar a descrição condicional. Interagir
   com preço, quantidade, selects e demais campos.
4. Fechar o modal superior pelo botão disponível. Conferir foco/navegação no modal
   inferior, backdrop e rolagem. Fechar o inferior e conferir a rolagem da página.
5. Reabrir, fechar e repetir rapidamente. Repetir alternando de aba/janela durante
   abertura e fechamento, inclusive mantendo a página em segundo plano.
6. Repetir em zoom 80%, 100% e 125%.
7. Testar visualização de movimentações, seleção de movimentação, seleção de lote
   para saída e saída; conferir quantidades, selects e fechamento de cada fluxo.
8. Abrir edição isolada. Testar os Select2 com mouse e teclado, pesquisa, seleção,
   fechamento e reabertura, sem perder os valores selecionados.
9. Verificar no navegador a ordenação das quantidades `2`, `10`, `1500` e frações.

## Roteiro manual dos PDFs

Abrir os quatro artefatos acima. Conferir títulos e acentos, legenda condicional,
asteriscos, coluna Status de Baixa vazia, bordas e contraste. No arquivo com 120
linhas, conferir repetição de cabeçalhos, quebras, última linha e ausência de cortes.
Verificar também impressão/visualização em escala de cinza: o asterisco deve
identificar a condição independentemente da cor.

Ao terminar os testes, encerrar somente a instância descartável:

```powershell
pg_ctl -D C:/dev/SIAPRA/storage/framework/testing/pendencias-pg -m fast -w stop
```
