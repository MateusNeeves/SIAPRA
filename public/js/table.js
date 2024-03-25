$(document).ready(function(){
    $('#myTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
        },
        initComplete: function () {
            this.api().columns().every(function () {
                    let column = this;
                    let title = column.footer().textContent;
     
                    // Create input element
                    let input = document.createElement('input');
                    input.placeholder = title;
                    input.style.width = '100%';
                    input.style.padding = '3px';
                    input.style.boxSizing = 'border-box';

                    column.footer().replaceChildren(input);
     
                    // Event listener for user input
                    input.addEventListener('keyup', () => {
                        if (column.search() !== this.value) {
                            column.search(input.value).draw();
                        }
                    });
                });
        },
    });
});


// $(".table-select tr").click(function(e) {
//     if (!$(this).hasClass("select")){
//         $(this).parent().find("tr.select").removeClass("select");
//         $(this).addClass("select");
//     }
// });

// let table = new DataTable('#myTable');