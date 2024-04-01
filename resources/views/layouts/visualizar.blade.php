@yield('variables')
<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$title}}
                </div>
                {{-- <button type="button" class="btn btn-dark bg-gradient mb-4" data-bs-toggle="modal" data-bs-target="#newModal">
                    {{"Novo"}}
                </button>
                <a class="btn btn-dark bg-gradient mb-4">
                    {{"Editar"}}
                </a>
                <a class="btn btn-dark bg-gradient mb-4" >
                    {{"Deletar"}}
                </a> --}}
                <div class="container overflow-auto">
                    <table id="myTable" class="table table-bordered table-striped">
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
                                    @foreach ($indexes as $index)
                                        <td class="text-start"> {{$info[$index]}}</td>
                                    @endforeach
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
                            @yield('content')
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Cadastrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      
    <!-- Modal DELETAR-->
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        {{"Tem certeza que quer deletar?"}}
                        <input type="hidden" name="id" id="id" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar </button>
                        <button type="submit" class="btn btn-danger">Deletar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
