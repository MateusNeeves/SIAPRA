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