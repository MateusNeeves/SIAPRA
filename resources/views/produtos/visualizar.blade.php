@extends('produtos.layout')

@section('variables')
    @php
        $title = ['Produtos', 'Produto'];
        $path = 'produtos';
        $columns = ['#', 'Nome', 'Descrição', 'Tipo', 'Fabricantes', 'Fornecedores', 'Quantidade Aceitável', 'Quantidade Mínima'];
        $indexes = ['id', 'nome', 'descricao', 'tipo', 'fabricantes', 'fornecedores', 'qtd_aceitavel', 'qtd_minima'];
        $infos = $produtos;
    @endphp
@endsection

@section('content')
    @php
        $produto = Session::get('produto') ?? null;
        $fabs = Session::get('fabricantes') ?? null;
        $forns = Session::get('fornecedores') ?? null;
    @endphp

    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $produto->nome ?? '')" required/>
    </div>

    <!-- Descricao -->
    <div class="mt-4">
        <x-input-label :value="__('Descricao *')" />
        <x-text-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" :value="old('descricao', $produto->descricao ?? '')" required/>
    </div>

    <!-- Tipo do Produto -->
    <div class="mt-4">
        <x-input-label :value="__('Tipo do Produto *')" />
        <select id="tipo" class="block mt-1 w-full border rounded" name="tipo" required>
            <option value="" hidden></option>
            @foreach ($tipos as $tipo)
                <option value="{{$tipo['nome']}}" {{$tipo['nome'] == (old('tipo') ?? $produto->tipo ?? "") ? "selected" : ""}}> {{$tipo['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Fabricante -->
    <div class="mt-4">
        <x-input-label :value="__('Fabricantes')" />
        <select class="multiple-select block mt-1 w-full border rounded" name="fabricantes[]" multiple >
            @foreach ($fabricantes as $fabricante)
                <option value="{{$fabricante['nome']}}" {{in_array($fabricante['nome'], $fabs ?? old('fabricantes') ?? []) ? "selected" : ""}} > {{$fabricante['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Fornecedor -->
    <div class="mt-4">
        <x-input-label :value="__('Fornecedores')" />
        <select class="multiple-select block mt-1 w-full border rounded" name="fornecedores[]" multiple :value="old('fornecedores')">
            @foreach ($fornecedores as $fornecedor)
                <option value="{{$fornecedor['nome']}}" {{in_array($fornecedor['nome'], $forns ?? old('fornecedores') ?? []) ? "selected" : ""}}> {{$fornecedor['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Quantidade Aceitável -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Aceitável *')" />
        <x-text-input id="qtd_aceitavel" class="block mt-1 w-full" type="number" name="qtd_aceitavel" :value="old('qtd_aceitavel', $produto->qtd_aceitavel ?? '')" required/>
    </div>

    <!-- Quantidade Mínima -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Mínima *')" />
        <x-text-input id="qtd_minima" class="block mt-1 w-full" type="number" name="qtd_minima" :value="old('qtd_minima', $produto->qtd_minima ?? '')" required/>
    </div>

@endsection


@section('visualizar')
    @php
        $produtoV = Session::get('produtoV') ?? null;
        $fabsV = Session::get('fabricantesV') ?? [];
        $fornsV = Session::get('fornecedoresV') ?? [];
        $lotesV = Session::get('lotesV') ?? [];
    @endphp

    <!-- Nome -->
    <div>
        <x-input-label class="h6" :value="__('Nome')" />
        <x-input-label class="mt-2 text-secondary" :value="__($produtoV->nome ?? '')" />
    </div>

    <!-- Descricao -->
    <div class="mt-4">
        <x-input-label class="h6" :value="__('Descricao')" />
        <x-input-label class="mt-2 text-secondary" :value="__($produtoV->descricao ?? '')" />
    </div>

    <!-- Tipo do Produto -->
    <div class="mt-4">
        <x-input-label class="h6" :value="__('Tipo do Produto')" />
        <x-input-label class="mt-2 text-secondary" :value="__($produtoV->tipo ?? '')" />
    </div>

    <!-- Fabricante -->
    <div class="mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="table-secondary text-start" scope="col"> Lista de Fabricantes </th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach ($fabsV as $fabV)
                    <tr>
                        <td class="text-start">{{$fabV}}</td>   
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Fornecedor -->
    <div class="mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="table-secondary text-start text-dark" scope="col"> Lista de Fornecedores </th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach ($fornsV as $fornV)
                    <tr>
                        <td class="text-start">{{$fornV}}</td>   
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Quantidade Aceitável -->
    <div class="mt-4">
        <x-input-label class="h6" :value="__('Quantidade Aceitável')" />
        <x-input-label class="mt-2 text-secondary" :value="__($produtoV->qtd_aceitavel ?? '')" />
    </div>

    <!-- Quantidade Mínima -->
    <div class="mt-4">
        <x-input-label class="h6" :value="__('Quantidade Mínima')" />
        <x-input-label class="mt-2 text-secondary" :value="__($produtoV->qtd_minima ?? '')" />
    </div>

    <!-- Lista de QTD em Estoque -->
    <div class="mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="4" class="table-secondary text-start text-dark" scope="col"> Lista da Qtd em Estoque </th>
                </tr>
                <tr class="text-sm">
                    <th class="table-light text-start text-dark" scope="col"> # </th>
                    <th class="table-light text-start text-dark" scope="col"> Lote do Fabricante </th>
                    <th class="table-light text-start text-dark" scope="col"> Qtd em Estoque </th>
                    <th class="table-light text-start text-dark" scope="col"> Data de Validade </th>

                </tr>
            </thead>
            <tbody class="text-sm text-center">
               @php $total = 0; @endphp
                @foreach ($lotesV as $lote)
                    @php $total += $lote['qtd_itens_estoque']; @endphp

                    <tr>
                        <td>{{$lote['id']}}</td>
                        <td>{{$lote['lote_fabricante']}}</td>      
                        <td>{{$lote['qtd_itens_estoque']}}</td>      
                        <td>{{$lote['data_validade']}}</td>      
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="table-light text-end text-danger" scope="col"> Total: {{$total}}</th>
                </tr>
            </tfoot>
        </table>
    </div>

@endsection


@section('novo_lote')
    @php
        $fabricantes_lote = Session::get('fabricantes_lote') ?? null;
        $fornecedores_lote = Session::get('fornecedores_lote') ?? null;
    @endphp

    <!-- Fabricante -->
    <div class="mt-4">
        <x-input-label :value="__('Fabricante *')" />
        <select class="block mt-1 w-full border rounded" name="fabricante" required>
            <option></option>
            @foreach ($fabricantes_lote ?? [] as $fabricante)
                <option value="{{$fabricante['nome']}}" {{$fabricante['nome'] == old('fabricante') ? "selected" : ""}} > {{$fabricante['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Lote Fabricante-->
    <div class="mt-4">
        <x-input-label :value="__('Lote Fabricante *')" />
        <x-text-input id="lote_fabricante" class="block mt-1 w-full" type="text" name="lote_fabricante" :value="old('lote_fabricante')" required/>
    </div>
    
    <!-- Fornecedor -->
    <div class="mt-4">
        <x-input-label :value="__('Fornecedor *')" />
        <select class="block mt-1 w-full border rounded" name="fornecedor" required>
            <option></option>
            @foreach ($fornecedores_lote ?? [] as $fornecedor)
                <option value="{{$fornecedor['nome']}}" {{$fornecedor['nome'] == old('fornecedor') ? "selected" : ""}} > {{$fornecedor['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Qtd Itens Recebidos -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade de Itens Recebidos *')" />
        <x-text-input id="qtd_itens_recebidos" class="block mt-1 w-full" type="number" name="qtd_itens_recebidos" :value="old('qtd_itens_recebidos')" min="1" required/>
    </div>

    {{-- 
    <!-- Preço Unitário -->
    <div class="mt-4">
        <x-input-label :value="__('Preço Unitário *')" />
        <x-text-input id="preco_unitario" class="block mt-1 w-full" type="number" name="preco_unitario" :value="old('preco_unitario')" required/>
    </div>

    <!-- Preço Total -->
    <div class="mt-4">
        <x-input-label :value="__('Preço Total *')" />
        <x-text-input id="preco_total" class="block mt-1 w-full" type="number" name="preco_total" :value="old('preco_total')" required/>
    </div>
     --}}
     
    <!-- Preço -->
    <div class="mt-4">
        <x-input-label :value="__('Preço *')" />
        <x-text-input id="preco" class="block mt-1 w-full" type="number" name="preco" :value="old('preco')" step="0.01" required/>
    </div>
    
    <!-- Data Entrega -->
    <div class="mt-4">
        <x-input-label :value="__('Data Entrega *')" />
        <x-text-input id="data_entrega" class="block mt-1 w-full" type="datetime-local" name="data_entrega" :value="old('data_entrega')" required/>
    </div>
    
    <!-- Data Validade -->
    <div class="mt-4">
        <x-input-label :value="__('Data Validade *')" />
        <x-text-input id="data_validade" class="block mt-1 w-full" type="datetime-local" name="data_validade" :value="old('data_validade')" required/>
    </div>

@endsection

@section('imprimir')
    @php
        $lotesP = Session::get('lotesP') ?? [];
    @endphp

    <!-- Lista de QTD em Estoque -->
    <div class="mt-4">
        <table  id="myTableImprimir" class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="4" class="table-secondary text-start text-dark" scope="col"> Selecione um lote </th>
                </tr>
                <tr class="text-sm">
                    <th class="table-light text-start table-dark" scope="col"> # </th>
                    <th class="table-light text-start table-dark" scope="col"> Fabricante </th>
                    <th class="table-light text-start table-dark" scope="col"> Lote do Fabricante </th>
                    <th class="table-light text-start table-dark" scope="col"> Data de Validade </th>
                </tr>
            </thead>
            <thead class="filters">
                <tr class="text-sm">
                    <td class="filter"> # </td>
                    <td class="filter"> Fabricante </td>
                    <td class="filter"> Lote do Fabricante </td>
                    <td class="filter"> Data de Validade </td>
                </tr>
            </thead>
            <tbody class="text-sm text-center"> 
                @foreach ($lotesP as $lote)
                    <tr>
                        <td>{{$lote['id']}}</td>
                        <td>{{$lote['nome']}}</td>      
                        <td>{{$lote['lote_fabricante']}}</td>      
                        <td>{{$lote['data_validade']}}</td>      
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        // TABLES GERAIS
        $(document).ready(function(){
            $('#myTableImprimir .filters .filter').each( function () {
                let input = document.createElement('input');
                input.placeholder = this.textContent;
                input.style.width = '100%';
                input.style.padding = '3px';
                input.style.boxSizing = 'border-box';

                this.replaceChildren(input);
            });


            $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
            
            var table = $('#myTableImprimir').DataTable({
                layout: {
                    topStart: {},
                    topEnd: {},
                    bottomStart: {},
                    bottomEnd: {},
                },
                select: true,
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "todos"] ],
                language:{
                    "emptyTable": "Nenhum registro encontrado",
                    "info": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "infoFiltered": "(Filtrados de _MAX_ registros)",
                    "infoThousands": ".",
                    "loadingRecords": "Carregando...",
                    "zeroRecords": "Nenhum registro encontrado",
                    "search": "Pesquisar",
                    "paginate": {
                        "next": "Próximo",
                        "previous": "Anterior",
                        "first": "Primeiro",
                        "last": "Último"
                    },
                    "aria": {
                        "sortAscending": ": Ordenar colunas de forma ascendente",
                        "sortDescending": ": Ordenar colunas de forma descendente"
                    },
                    "select": {
                        "rows": {
                        }
                        
                    },
                    "buttons": {
                        "copySuccess": {
                            "1": "Uma linha copiada com sucesso",
                            "_": "%d linhas copiadas com sucesso"
                        },
                        "collection": "Coleção  <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
                        "colvis": "Visibilidade da Coluna",
                        "colvisRestore": "Restaurar Visibilidade",
                        "copy": "Copiar",
                        "copyKeys": "Pressione ctrl ou u2318 + C para copiar os dados da tabela para a área de transferência do sistema. Para cancelar, clique nesta mensagem ou pressione Esc..",
                        "copyTitle": "Copiar para a Área de Transferência",
                        "csv": "CSV",
                        "excel": "Excel",
                        "pageLength": {
                            "-1": "Mostrar todos os registros",
                            "_": "Mostrar %d registros"
                        },
                        "pdf": "PDF",
                        "print": "Imprimir",
                        "createState": "Criar estado",
                        "removeAllStates": "Remover todos os estados",
                        "removeState": "Remover",
                        "renameState": "Renomear",
                        "savedStates": "Estados salvos",
                        "stateRestore": "Estado %d",
                        "updateState": "Atualizar"
                    },
                    "autoFill": {
                        "cancel": "Cancelar",
                        "fill": "Preencher todas as células com",
                        "fillHorizontal": "Preencher células horizontalmente",
                        "fillVertical": "Preencher células verticalmente"
                    },
                    "lengthMenu": "Exibir _MENU_ resultados por página",
                    "searchBuilder": {
                        "add": "Adicionar Condição",
                        "button": {
                            "0": "Construtor de Pesquisa",
                            "_": "Construtor de Pesquisa (%d)"
                        },
                        "clearAll": "Limpar Tudo",
                        "condition": "Condição",
                        "conditions": {
                            "date": {
                                "after": "Depois",
                                "before": "Antes",
                                "between": "Entre",
                                "empty": "Vazio",
                                "equals": "Igual",
                                "not": "Não",
                                "notBetween": "Não Entre",
                                "notEmpty": "Não Vazio"
                            },
                            "number": {
                                "between": "Entre",
                                "empty": "Vazio",
                                "equals": "Igual",
                                "gt": "Maior Que",
                                "gte": "Maior ou Igual a",
                                "lt": "Menor Que",
                                "lte": "Menor ou Igual a",
                                "not": "Não",
                                "notBetween": "Não Entre",
                                "notEmpty": "Não Vazio"
                            },
                            "string": {
                                "contains": "Contém",
                                "empty": "Vazio",
                                "endsWith": "Termina Com",
                                "equals": "Igual",
                                "not": "Não",
                                "notEmpty": "Não Vazio",
                                "startsWith": "Começa Com",
                                "notContains": "Não contém",
                                "notStartsWith": "Não começa com",
                                "notEndsWith": "Não termina com"
                            },
                            "array": {
                                "contains": "Contém",
                                "empty": "Vazio",
                                "equals": "Igual à",
                                "not": "Não",
                                "notEmpty": "Não vazio",
                                "without": "Não possui"
                            }
                        },
                        "data": "Data",
                        "deleteTitle": "Excluir regra de filtragem",
                        "logicAnd": "E",
                        "logicOr": "Ou",
                        "title": {
                            "0": "Construtor de Pesquisa",
                            "_": "Construtor de Pesquisa (%d)"
                        },
                        "value": "Valor",
                        "leftTitle": "Critérios Externos",
                        "rightTitle": "Critérios Internos"
                    },
                    "searchPanes": {
                        "clearMessage": "Limpar Tudo",
                        "collapse": {
                            "0": "Painéis de Pesquisa",
                            "_": "Painéis de Pesquisa (%d)"
                        },
                        "count": "{total}",
                        "countFiltered": "{shown} ({total})",
                        "emptyPanes": "Nenhum Painel de Pesquisa",
                        "loadMessage": "Carregando Painéis de Pesquisa...",
                        "title": "Filtros Ativos",
                        "showMessage": "Mostrar todos",
                        "collapseMessage": "Fechar todos"
                    },
                    "thousands": ".",
                    "datetime": {
                        "previous": "Anterior",
                        "next": "Próximo",
                        "hours": "Hora",
                        "minutes": "Minuto",
                        "seconds": "Segundo",
                        "amPm": [
                            "am",
                            "pm"
                        ],
                        "unknown": "-",
                        "months": {
                            "0": "Janeiro",
                            "1": "Fevereiro",
                            "10": "Novembro",
                            "11": "Dezembro",
                            "2": "Março",
                            "3": "Abril",
                            "4": "Maio",
                            "5": "Junho",
                            "6": "Julho",
                            "7": "Agosto",
                            "8": "Setembro",
                            "9": "Outubro"
                        },
                        "weekdays": [
                            "Domingo",
                            "Segunda-feira",
                            "Terça-feira",
                            "Quarta-feira",
                            "Quinte-feira",
                            "Sexta-feira",
                            "Sábado"
                        ]
                    },
                    "editor": {
                        "close": "Fechar",
                        "create": {
                            "button": "Novo",
                            "submit": "Criar",
                            "title": "Criar novo registro"
                        },
                        "edit": {
                            "button": "Editar",
                            "submit": "Atualizar",
                            "title": "Editar registro"
                        },
                        "error": {
                            "system": "Ocorreu um erro no sistema (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Mais informações<\/a>)."
                        },
                        "multi": {
                            "noMulti": "Essa entrada pode ser editada individualmente, mas não como parte do grupo",
                            "restore": "Desfazer alterações",
                            "title": "Multiplos valores",
                            "info": "Os itens selecionados contêm valores diferentes para esta entrada. Para editar e definir todos os itens para esta entrada com o mesmo valor, clique ou toque aqui, caso contrário, eles manterão seus valores individuais."
                        },
                        "remove": {
                            "button": "Remover",
                            "confirm": {
                                "_": "Tem certeza que quer deletar %d linhas?",
                                "1": "Tem certeza que quer deletar 1 linha?"
                            },
                            "submit": "Remover",
                            "title": "Remover registro"
                        }
                    },
                    "decimal": ",",
                    "stateRestore": {
                        "creationModal": {
                            "button": "Criar",
                            "columns": {
                                "search": "Busca de colunas",
                                "visible": "Visibilidade da coluna"
                            },
                            "name": "Nome:",
                            "order": "Ordernar",
                            "paging": "Paginação",
                            "scroller": "Posição da barra de rolagem",
                            "search": "Busca",
                            "searchBuilder": "Mecanismo de busca",
                            "select": "Selecionar",
                            "title": "Criar novo estado",
                            "toggleLabel": "Inclui:"
                        },
                        "emptyStates": "Nenhum estado salvo",
                        "removeConfirm": "Confirma remover %s?",
                        "removeJoiner": "e",
                        "removeSubmit": "Remover",
                        "removeTitle": "Remover estado",
                        "renameButton": "Renomear",
                        "renameLabel": "Novo nome para %s:",
                        "renameTitle": "Renomear estado",
                        "duplicateError": "Já existe um estado com esse nome!",
                        "emptyError": "Não pode ser vazio!",
                        "removeError": "Falha ao remover estado!"
                    },
                    "infoEmpty": "Mostrando 0 até 0 de 0 registro(s)",
                    "processing": "Carregando...",
                    "searchPlaceholder": "Buscar registros"
                }
            });
            
            table.columns().eq(0).each(function(colIdx){
                $('input', $('.filters td')[colIdx]).on('keyup change', function () {
                    table.column( colIdx ).search( this.value ).draw();
                });
            });
            
        });
    </script>

@endsection