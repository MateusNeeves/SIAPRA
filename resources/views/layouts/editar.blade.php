@yield('variables')
<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$title}}
                </div>
                <form method="POST" action="{{route($path . '.update', $id)}}" >
                    @csrf
                    @method('PUT')
                    @yield('content')
                    <button type="submit" class="btn btn-dark ms-auto mt-4 mb-4">
                        {{ __('Salvar Mudanças') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>