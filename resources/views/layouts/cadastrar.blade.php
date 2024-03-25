<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    @yield('title')
                </div>
                <form method="POST" action=@yield('path') >
                    @csrf
                    @yield('content')
                    <button class="ms-auto d-block">
                        <a class="btn btn-dark mt-4 mb-4" >
                            {{ __('Cadastrar') }}
                        </a>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>