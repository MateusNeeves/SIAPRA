// TABLES GERAIS
$(document).ready(function(){
    $('#myTable .filters .filter').each( function () {
        let input = document.createElement('input');
        input.placeholder = this.textContent;
        input.style.width = '100%';
        input.style.padding = '3px';
        input.style.boxSizing = 'border-box';

        this.replaceChildren(input);
    });


    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
    
    var table = $('#myTable').DataTable({
        layout: {
            topStart: {
                buttons: [
                    {
                        text: 'Novo',
                        className: 'btn btn-dark bg-gradient',
                        action: function () {
                            $('#newModal').modal('show')
                        }
                    },
                    {
                        text: 'Editar',
                        className: 'btn btn-dark bg-gradient',
                        action: function () {
                            let id = table.rows( { selected: true } ).data()[0][0];
                            let modal = $('#editModal');

                            let data = document.querySelectorAll('#myTable .selected .get');
                            let inputs = document.querySelectorAll('#editModal .modal-body .input');

                            for (let i = 0, j = 0; i < inputs.length; i++){
                                if (inputs[i].classList.contains('select') || inputs[i].type == 'text')
                                    inputs[i].value = data[i].textContent.substring(1);
                                else if (inputs[i].type == 'date')    
                                    inputs[i].value = data[i].textContent.substring(1).split('/').reverse().join('-');
                                else if (inputs[i].type == 'datetime-local'){
                                    let date = data[i].textContent.substring(1).split(" ")[0];
                                    let time = data[i].textContent.substring(1).split(" ")[1];
                                    inputs[i].value = date.split('/').reverse().join('-') + ' ' + time;
                                }   
                                else if (inputs[i].type == 'number')
                                    inputs[i].value = Number(data[i].textContent);
                            }

                            modal.find('.modal-body #id').val(id);
                            modal.modal('show');
                        }
                    },
                    {
                        text: 'Deletar',
                        className: 'btn btn-dark bg-gradient',
                        action: function () {
                            let modal = $('#deleteModal');
                            
                            let id = table.rows( { selected: true } ).data()[0][0];
                                                        
                            modal.find('.modal-body #id').val(id);
                            modal.modal('show');
                        }
                    }
                ]
            },
            topEnd: {
                buttons: [
                    {
                        className: 'btn btn-dark bg-gradient',
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        filename: document.location.href.substring(document.location.href.lastIndexOf('/') + 1),
                        title: function(){
                            let text = '';
                            let inputs = document.querySelectorAll('.filters input');
                            for (let i = 0; i < inputs.length; i++) {
                                if (inputs[i].value.length)
                                    text += inputs[i].placeholder + "= " + inputs[i].value + '\n';   
                            }
        
                            return text.length ? 'Filtros: \n' + text : 'Sem Filtros';
                        },
                        customize: function(doc) {
                            doc.styles.title = {
                            alignment: 'left',
                            fontSize: '10',
                            }   
                        },
                        exportOptions: {
                            columns: ':not(:last)',
                        }
                    },
                    {
                        className: 'btn btn-dark bg-gradient',
                        extend: 'csvHtml5',
                        text: 'PLANILHA',
                        filename: document.location.href.substring(document.location.href.lastIndexOf('/') + 1),
                        customize: function(csv){
                            let csvSplit = csv.split('\n');
                            let headers = csvSplit[0];
                            let inputs = document.querySelectorAll('.filters input');
                            let newLine = [];
                            for (i = 0; i < inputs.length; i++)
                                newLine[i] = inputs[i].value;
                            csvSplit[0] = newLine;
                            return headers + csvSplit.join('\n');
                        },
                        exportOptions: {
                            columns: ':not(:last)',
                        }
                    }
                ],
            },
            bottomStart: 'info',
            bottomEnd: 'paging',

        },
        select: true,
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

// TABLE PARAMETROS
$(document).ready(function(){

    var paramTable = $('#paramTable').DataTable({
        ordering: false,
        layout: {
            topStart: {
                buttons: [
                    {
                        text: 'Editar',
                        className: 'btn btn-dark bg-gradient',
                        action: function () {
                            let modal = $('#editModal');

                            let data = document.querySelectorAll('#paramTable tbody td');
                            let inputs = document.querySelectorAll('#editModal .modal-body input');
                            for (let i = 0, j = 0; i < inputs.length; i++){
                                if ( inputs[i].type == 'time')
                                    inputs[i].value = data[i].textContent.substring(1);         
                                else if (inputs[i].type == 'number')
                                    inputs[i].value = Number(data[i].textContent);
                            }

                            modal.modal('show');
                        }
                    }
                ]
            },
            topEnd: {},
            bottomStart: {},
            bottomEnd: {}
        },
    });
});