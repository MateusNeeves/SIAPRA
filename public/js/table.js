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
        language:{
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
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


