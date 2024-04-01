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
                    },
                    {
                        text: 'Editar',
                        className: 'btn btn-dark bg-gradient',
                    },
                    {
                        text: 'Deletar',
                        className: 'btn btn-dark bg-gradient',
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
            bottonStart: 'info',
            bottonEnd: 'paging',

        },
        // dom: '<"row pb-3"<"col-sm-12 col-md-5 text-start" l> <"col-sm-12 col-md-7 text-end" B>> <"w-100" t> <"row pt-3"<"col-sm-12 col-md-5 text-start" i><"col-sm-12 col-md-7 text-end" p>>',
        select: true,
        // buttons: [
        //     {
        //         className: 'btn btn-dark bg-gradient',
        //         extend: 'pdfHtml5',
        //         orientation: 'landscape',
        //         filename: document.location.href.substring(document.location.href.lastIndexOf('/') + 1),
        //         title: function(){
        //             let text = '';
        //             let inputs = document.querySelectorAll('.filters input');
        //             for (let i = 0; i < inputs.length; i++) {
        //                 if (inputs[i].value.length)
        //                     text += inputs[i].placeholder + "= " + inputs[i].value + '\n';   
        //             }

        //             return text.length ? 'Filtros: \n' + text : 'Sem Filtros';
        //         },
        //         customize: function(doc) {
        //             doc.styles.title = {
        //             alignment: 'left',
        //             fontSize: '10',
        //             }   
        //         },
        //         exportOptions: {
        //             columns: ':not(:last)',
        //         }
        //     },
        //     {
        //         className: 'btn btn-dark bg-gradient',
        //         extend: 'csvHtml5',
        //         text: 'PLANILHA',
        //         filename: document.location.href.substring(document.location.href.lastIndexOf('/') + 1),
        //         customize: function(csv){
        //             let csvSplit = csv.split('\n');
        //             let headers = csvSplit[0];
        //             let inputs = document.querySelectorAll('.filters input');
        //             let newLine = [];
        //             for (i = 0; i < inputs.length; i++)
        //                 newLine[i] = inputs[i].value;
        //             csvSplit[0] = newLine;
        //             return headers + csvSplit.join('\n');
        //         },
        //         exportOptions: {
        //             columns: ':not(:last)',
        //         }
        //     }
        // ],
        language: {
            "emptyTable": "Nenhum registro encontrado",
            "info": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "loadingRecords": "Carregando...",
            "zeroRecords": "Nenhum registro encontrado",
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
                "rows": {}
            },
            "buttons": {
                "csv": "CSV",
                "excel": "Excel",
                "pdf": "PDF"
            },
            "lengthMenu": "Exibir _MENU_ resultados por página",
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
                "remove": {
                    "button": "Remover",
                    "confirm": {
                        "_": "Tem certeza que quer deletar %d linhas?",
                        "1": "Tem certeza que quer deletar 1 linha?"
                    },
                    "submit": "Remover",
                    "title": "Remover registro"
                }
            }
        }
    });
    
    table.columns().eq(0).each(function(colIdx){
        $('input', $('.filters td')[colIdx]).on('keyup change', function () {
            table.column( colIdx ).search( this.value ).draw();
        });
    });

});


// $(".table-select tr").click(function(e) {
//     if (!$(this).hasClass("select")){
//         $(this).parent().find("tr.select").removeClass("select");
//         $(this).addClass("select");
//     }
// });
