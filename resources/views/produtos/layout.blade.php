@yield('variables')
<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$title[0]}}
                </div>

                <div class="flex mb-2">
                    <a class="btn btn-dark bg-gradient me-2 ms-3" href="{{route($path. '.register')}}"> Novo </a>

                    <form method="post" action="{{route($path. '.view')}}">
                        @csrf
                        <input hidden name="id_view" id="id_view" value="{{old('id_view')}}">
                        <button disabled id="view_button" onclick="$('#id_view').val($('#myTable .selected .id').text())" class="btn btn-dark bg-gradient me-2" > Visualizar </button>
                    </form>

                    <form method="post" action="{{route($path. '.edit')}}">
                        @csrf
                        <input hidden name="id_edit" id="id_edit" value="{{old('id_edit')}}">
                        <button disabled id="edit_button" onclick="$('#id_edit').val($('#myTable .selected .id').text())" class="btn btn-dark bg-gradient me-2" > Editar </button>
                    </form>
    
                    <button disabled id="delete_button" onclick="$('#id_delete').val($('#myTable .selected .id').text()); $('#deleteModal').modal('show')" class="btn btn-dark bg-gradient me-2"> Deletar </button>
                </div>

                <script>
                    $(document).on('click', function() {
                        if ($('#myTable .selected').length){
                            $('#edit_button').prop('disabled', false);
                            $('#view_button').prop('disabled', false);
                            $('#delete_button').prop('disabled', false);
                        }
                        else{
                            $('#edit_button').prop('disabled', true);
                            $('#view_button').prop('disabled', true);
                            $('#delete_button').prop('disabled', true);
                        }
                    });
                </script>

                <div class="container overflow-auto mb-4">
                    <table id="myTable" class="table table-bordered table-hover text-sm">
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
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Cadastrar {{$title[1]}}</h1>
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

    <!-- Modal VISUALIZAR-->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-block">
                    <div class="d-flex">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Visualizar {{$title[1]}}</h1>
                        <div class="ms-auto">
                            <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="d-flex mt-3">
                        <form method="post" action="{{route($path. '.register_lote')}}">
                            @csrf
                            <input hidden name="id_view" id="id_view" value="{{old('id_view', Session::get('id_view_backup') ?? '')}}">
                            <button id="lote_button" onclick="$('#id_view').val($('#myTable .selected .id').text())" class="btn btn-dark bg-gradient me-2" > Novo Lote </button>
                        </form>
                        <form method="post" action="{{route($path. '.make_mov')}}">
                            @csrf
                            <input hidden name="id_view" id="id_view" value="{{old('id_view', Session::get('id_view_backup') ?? '')}}">
                            <button id="mov_button" onclick="$('#id_view').val($('#myTable .selected .id').text())" class="btn btn-dark bg-gradient me-2" > Movimentar </button>
                        </form>
                        <form method="get" action="{{route($path. '.view_print')}}">
                            <input hidden name="id_view" id="id_view" value="{{old('id_view', Session::get('id_view_backup') ?? '')}}">
                            <button id="print_button" onclick="$('#id_view').val($('#myTable .selected .id').text())" class="btn btn-dark bg-gradient me-2" > Imprimir Rótulo </button>
                        </form>
                    </div>
                </div>
                <div class="modal-body">
                    @if (Session::has('modal')  && Session::get('modal') == '#viewModal')
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
                    @yield('visualizar')
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal EDITAR-->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Editar {{$title[1]}}</h1>
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
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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

    <!-- Modal LOTE-->
    <div class="modal fade" id="loteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Cadastrar Novo Lote</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route($path. '.store_lote')}}">
                    @csrf
                    <div class="modal-body">
                        @if (Session::has('modal') && Session::get('modal') == '#loteModal')
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
                        <input hidden name="id_view" id="id_view" value="{{old('id_view')}}">
                        @yield('novo_lote')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal SELECIONAR LOTE-->
    <div class="modal fade" id="selecLoteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-block">
                    <div class="d-flex">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">{{Session::get('title_modal')}}</h1>
                        <div class="ms-auto">
                            <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <form method="get" action="{{route(Session::get('route_modal', 'dashboard'))}}">

                    <div class="modal-body">
                        @if (Session::has('modal')  && Session::get('modal') == '#selecLoteModal')
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
                        @yield('selecionar_lote')
                    </div>
                    <div class="modal-footer">
                        <button disabled id="selectButton" type="submit" class="btn btn-dark">Selecionar</button>
                    </div>

                    <script>
                        $(document).on('click', function() {
                            if ($('#myTableSelect .selected').length)
                                $('#selectButton').prop('disabled', false);
                            else
                                $('#selectButton').prop('disabled', true);
                        });
                    </script>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>