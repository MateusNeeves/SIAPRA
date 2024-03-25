@yield('variables')
<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$title}}
                </div>

                <a class="btn btn-dark mb-4" href={{$path}} >
                    {{"Cadastrar Novo"}}
                </a>
                <div class="container">
                    <table id="myTable" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                @foreach ($columns as $column)
                                    <th class="table-dark text-start" scope="col"> {{$column}} </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($infos as $info)
                                <tr>
                                    @foreach ($indexes as $index)
                                        <td class="text-start"> {{$info[$index]}}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                @foreach ($columns as $column)
                                    <th > {{$column}} </th>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mb-4"></div>
            </div>
        </div>
    </div>
</x-app-layout>
