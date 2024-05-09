@yield('variables')
<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$title}}
                </div>

                <div class="flex mb-2">
                    <button onclick="$('#newModal').modal('show')" class="btn btn-dark bg-gradient me-2"> Novo </button>
                    
                    <form method="post" action="{{route($path. '.edit')}}">
                        @csrf
                        <input hidden name="id_edit" id="id_edit" value="{{old('id_edit')}}">
                        <button disabled id="edit_button" onclick="$('#id_edit').val($('#myTable .selected .id').text())" class="btn btn-dark bg-gradient me-2" > Editar </button>
                    </form>
    
                    <button disabled id="delete_button" onclick="$('#id_delete').val($('#myTable .selected .id').text()); $('#deleteModal').modal('show')" class="btn btn-dark bg-gradient me-2"> Deletar </button>
                </div>

                <script>
                    $(document).on('click', function() {
                        if ($('.selected').length){
                            $('#edit_button').prop('disabled', false);
                            $('#delete_button').prop('disabled', false);
                        }
                        else{
                            $('#edit_button').prop('disabled', true);
                            $('#delete_button').prop('disabled', true);
                        }
                    });
                </script>

                <div class="container overflow-auto mb-4">
                    <table id="myTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                @foreach ($columns as $column)
                                    <th class="table-dark text-start" scope="col"> {{$column}} </th>
                                @endforeach
                            </tr>
                        </thead>
                        <thead class="filters">
                            <tr>
                                @foreach ($columns as $column)
                                    <td class="filter"> {{$column}} </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($infos as $info)
                                <tr>
                                    @for ($i = 0, $e = 0; $i < count($indexes); $i++)
                                        <td class="{{$indexes[$i]}} text-start">{!!$info[$indexes[$i]]!!}</td>   
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if (Session::has('modal'))
        <script>
            $(window).on('load', function() {
                $("{{Session::get('modal')}}").modal('show');
            });
        </script>        
    @endif

    <!-- Modal NOVO-->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Cadastrar {{$title}}</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route($path. '.store')}}">
                    @csrf
                    <div class="modal-body">
                        @if (Session::has('modal') && Session::get('modal') == '#newModal')
                            <div class="flash-message">
                                @foreach (['danger', 'warning', 'success', 'info', 'dark'] as $msg)
                                    @if(Session::has('alert-' . $msg))
                                        <div class="w-100">
                                            <p class="alert alert-{{ $msg }}">
                                                {!! Session::get('alert-' . $msg) !!}
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        @yield('content')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal EDITAR-->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Editar {{$title}}</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route($path. '.update')}}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        @if (Session::has('modal')  && Session::get('modal') == '#editModal')
                            <div class="flash-message">
                                @foreach (['danger', 'warning', 'success', 'info', 'dark'] as $msg)
                                    @if(Session::has('alert-' . $msg))
                                        <div class="w-100">
                                            <p class="alert alert-{{ $msg }}">
                                                {!! Session::get('alert-' . $msg) !!}
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <input hidden name="id_edit" id="id_edit" value="{{old('id_edit')}}">
                        @yield('content')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal DELETAR-->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar Deleção</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route($path. '.destroy')}}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        @if (Session::has('modal') && Session::get('modal') == '#deleteModal')
                            <div class="flash-message">
                                @foreach (['danger', 'warning', 'success', 'info', 'dark'] as $msg)
                                    @if(Session::has('alert-' . $msg))
                                        <div class="w-100">
                                            <p class="alert alert-{{ $msg }}">
                                                {!! Session::get('alert-' . $msg) !!}
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <input hidden name="id_delete" id="id_delete" value="{{old('id_delete')}}">
                            <input type="hidden" name="soft" id="soft" value="true">
                        @else
                            {{"Tem certeza que quer deletar?"}}
                            <input hidden name="id_delete" id="id_delete" value="{{old('id_delete')}}">
                            <input type="hidden" name="soft" id="soft" value="false">
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar </button>
                        @if (Session::has('modal') && Session::get('modal') == '#deleteModal')
                            <button type="submit" class="btn btn-danger">Desativar</button>
                        @else
                            <button type="submit" class="btn btn-danger">Deletar</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>