@yield('variables')
<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$title}}
                </div>
                {{-- <a class="btn btn-dark bg-gradient mb-4" href="{{route($path . '.register')}}" >
                    {{"Novo"}}
                </a> --}}
                <div class="container overflow-auto mb-4">
                    <table id="myTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                @foreach ($columns as $column)
                                    <th class="table-dark text-start" scope="col"> {{$column}} </th>
                                @endforeach
                                {{-- <th class="table-dark last"> Ações</th> --}}
                            </tr>
                        </thead>
                        <thead class="filters">
                            <tr>
                                @foreach ($columns as $column)
                                    <td class="filter"> {{$column}} </td>
                                @endforeach
                                {{-- <td></td> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($infos as $info)
                                <tr>
                                    @for ($i = 0, $e = 0; $i < count($indexes); $i++)
                                        @if ($indexes[$i] == $editables[$e])
                                            <td class="get text-start"> {{$info[$indexes[$i]]}}</td>  
                                            @php $e++; @endphp
                                        @else
                                            <td class="text-start"> {{$info[$indexes[$i]]}}</td>   
                                        @endif
                                    @endfor
                                    {{-- <td>
                                        <div class="text-center">
                                            <form class="mb-4" action="{{route($path . ".edit", ['id' => $info['id']])}}" method="get">
                                                <button class="btn btn-primary" type="submit">
                                                    {{"Editar"}}
                                                </button>
                                            </form>
                                            <button class="btn btn-danger" data-id={{$info['id']}} data-bs-toggle="modal" data-bs-target="#delete">
                                                {{"Deletar"}}
                                            </button>
                                        </div>
                                    </td> --}}
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
                $('{{Session::get('modal')}}').modal('show');
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
                                                @foreach(explode('<br>', Session::get('alert-' . $msg)) as $text)
                                                {!! $text . ($loop->last ? "" : '<br>') !!}
                                                @endforeach 
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
                                                @foreach(explode('<br>', Session::get('alert-' . $msg)) as $text)
                                                {!! $text . ($loop->last ? "" : '<br>') !!}
                                                @endforeach 
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <input type="hidden" name="id" id="id" value="{{old('id')}}">
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
                                                @foreach(explode('<br>', Session::get('alert-' . $msg)) as $text)
                                                {!! $text . ($loop->last ? "" : '<br>') !!}
                                                @endforeach
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            {{-- {{"Deseja Desativar esse registro ao invés de Deletar?"}}
                            <br>
                            {{"Você pode restaurá-lo futuramente, caso necessário."}} --}}
                            <input type="hidden" name="id" id="id" value="{{old('id')}}">
                            <input type="hidden" name="soft" id="soft" value="true">
                        @else
                            {{"Tem certeza que quer deletar?"}}
                            <input type="hidden" name="id" id="id" value="{{old('id')}}">
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
