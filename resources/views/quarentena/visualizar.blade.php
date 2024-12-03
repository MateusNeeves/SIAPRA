<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    Quarentena
                </div>

                @if (array_intersect(['Admin', 'Farmacêutico'], Auth::user()->getClassNamesAttribute()))
                    <div class="flex mb-2">
                        <div class="me-2 ms-3">
                            <button disabled id="remove_button" onclick="$('#id_lote').val($('#myTableSelect .selected .id').text()); $('#removeModal').modal('show')" class="btn btn-orange me-2"> Retirar da Quarentena </button>
                        </div>
                    </div>

                    <script>
                        $(document).on('click', function() {
                            if ($('.selected').length){
                                $('#remove_button').prop('disabled', false);
                            }
                            else{
                                $('#remove_button').prop('disabled', true);
                            }
                        });
                    </script>
                @endif


                <div class="container overflow-auto mb-4">
                    <table id="myTableSelect" class="table table-bordered table-hover text-sm">
                        <thead>
                            <tr>
                                <th class="text-start table-orange" scope="col"> Produto </th>
                                <th class="text-start table-orange" scope="col"> # Lote </th>
                                <th class="text-start table-orange" scope="col"> Fabricante </th>
                                <th class="text-start table-orange" scope="col"> Lote do Fabricante</th>
                                <th class="text-start table-orange" scope="col"> Data de Entrega </th>
                                <th class="text-start table-orange" scope="col"> Data de Validade </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lotes as $lote)
                                <tr>
                                    <td class="text-start"> {{$lote['produto']}} </td>
                                    <td class="id text-start"> {{$lote['id_lote']}} </td>
                                    <td class="text-start"> {{$lote['fabricante']}} </td>
                                    <td class="text-start"> {{$lote['lote_fabricante']}} </td>
                                    <td class="text-start"> {{$lote['data_entrega']}} </td>
                                    <td class="text-start"> {{$lote['data_validade']}} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal REMOVER-->
    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar Retirada da Quarentena</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('quarentena.remove')}}">
                    @csrf
                    <div class="modal-body flex justify-end">
                        <input hidden name="id_lote" id="id_lote" value="{{old('id_lote')}}">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar </button>
                        <button type="submit" class="ms-2 btn btn-danger">Retirar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>