<nav x-data="{ open: false }" class="bg-white border-b border-gray fixed-top">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div> --}}

                <!-- Navigation Links -->

                @php
                    $route = \Request::route()->getName();

                    if (in_array($route, ['fabricantes', 'fornecedores', 'tipos_produtos', 'produtos', 'dest_produtos']))
                        $route = "almoxarifado";
                    else if (in_array($route, ['fracionamentos', 'parametros', 'planejamentos']))
                        $route = "producao";
                    
                @endphp
                <style>
                    #{{$route}}{
                        border-bottom: solid 2px #f2714b;
                    }
                    #{{$route}} button{
                        color: rgb(17 24 39);
                    }
                </style>

                <div class="hidden sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link id="dashboard" class=" text-decoration-none" :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <img class="pe-4" src="{{ asset('images/logo-crcn-ne-horizontal-principal.png') }}" alt="Logo" width="100">
                        {{ 'Início'}}
                    </x-nav-link>
                </div>

                <!-- Almoxarifado -->
                @if (array_intersect(['Admin', 'Almoxarife', 'Visualizador', 'Farmacêutico'], Auth::user()->getClassNamesAttribute()))
                    <div class="hidden sm:flex sm:items-center sm:ms-10" id="almoxarifado">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger" class="h-100">
                                <button class="inline-flex items-center text-sm font-medium text-gray-500 bg-white hover:text-gray-700">
                                    <div>{{ __('Almoxarifado') }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>

                            </x-slot>

                            <x-slot name="content">
                                @if (array_intersect(['Admin', 'Almoxarife', 'Visualizador'], Auth::user()->getClassNamesAttribute()))
                                    <x-dropdown-link class=" text-decoration-none" :href="route('fabricantes')">
                                        {{ __('Fabricantes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link class=" text-decoration-none" :href="route('fornecedores')">
                                        {{ __('Fornecedores') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link class=" text-decoration-none" :href="route('produtos')">
                                        {{ __('Produtos') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link class=" text-decoration-none" :href="route('tipos_produtos')">
                                        {{ __('Tipos de Produtos') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link class=" text-decoration-none" :href="route('dest_produtos')">
                                        {{ __('Destinos dos Produtos') }}
                                    </x-dropdown-link>
                                @if (array_intersect(['Admin', 'Visualizador', 'Farmacêutico'], Auth::user()->getClassNamesAttribute()))
                                    <x-dropdown-link class=" text-decoration-none" :href="route('quarentena')">
                                        {{ __('Quarentena') }}
                                    </x-dropdown-link>
                                @endif
                                    <x-dropdown-link class=" text-decoration-none" :href="route('relatorios')">
                                        {{ __('Relatórios') }}
                                    </x-dropdown-link>
                                @endif
                                
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Clientes -->
                @if (array_intersect(['Admin', 'Visualizador', 'Produção'], Auth::user()->getClassNamesAttribute()))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link id="clientes" class=" text-decoration-none" :href="route('clientes')" :active="request()->routeIs('clientes')">
                            {{ __('Clientes') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Pedidos -->
                @if (array_intersect(['Admin', 'Visualizador', 'Produção'], Auth::user()->getClassNamesAttribute()))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex ">
                        <x-nav-link id="pedidos" class=" text-decoration-none" :href="route('pedidos')" :active="request()->routeIs('pedidos')">
                            {{ __('Pedidos') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Produção -->
                @if (array_intersect(['Admin', 'Visualizador', 'Produção'], Auth::user()->getClassNamesAttribute()))
                    <div class="hidden sm:flex sm:items-center sm:ms-10" id="producao">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger" class="h-100">
                                <button class="inline-flex items-center text-sm font-medium text-gray-500 bg-white hover:text-gray-700">
                                    <div>{{ __('Produção') }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>

                            </x-slot>

                            <x-slot name="content" >
                                <x-dropdown-link class=" text-decoration-none" :href="route('fracionamentos')">
                                    {{ __('Fracionamento') }}
                                </x-dropdown-link>
                                <x-dropdown-link class=" text-decoration-none" :href="route('parametros')">
                                    {{ __('Parâmetros') }}
                                </x-dropdown-link>
                                <x-dropdown-link class=" text-decoration-none" :href="route('planejamentos')">
                                    {{ __('Planejamento') }}
                                </x-dropdown-link>
                                <x-dropdown-link class=" text-decoration-none" :href="route('registros_lote')">
                                    {{ __('Registros de Lote') }}
                                </x-dropdown-link>
                            </x-slot>

                        </x-dropdown>
                    </div>
                @endif

                <!-- Usuários -->
                @if (array_intersect(['Admin', 'Visualizador'], Auth::user()->getClassNamesAttribute()))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex ">
                        <x-nav-link id="usuarios" class=" text-decoration-none" :href="route('usuarios')" :active="request()->routeIs('usuarios')">
                            {{ __('Usuários') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Logs -->
                @if (array_intersect(['Admin'], Auth::user()->getClassNamesAttribute()))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link id="logs" class=" text-decoration-none" :href="route('logs')" :active="request()->routeIs('logs')">
                            {{ __('Logs') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>



            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->username }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link class=" text-decoration-none" :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" >
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

        
        </div>
    </div>
</nav>

@if (!Session::has('modal'))
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info', 'dark'] as $msg)
            @if(Session::has('alert-' . $msg))
                <div class="position-absolute w-100 py-16">
                    <p class="alert alert-{{ $msg }}">
                        {!! Session::get('alert-' . $msg) !!}
                        <a href="" style="text-decoration: none;" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    </p>
                </div>
            @endif
        @endforeach
    </div>
@endif

