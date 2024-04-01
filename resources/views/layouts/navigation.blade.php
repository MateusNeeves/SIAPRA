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
                <div class="hidden sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Início') }}
                    </x-nav-link>
                </div>

                <!-- Almoxarifado -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger" class="h-100">
                            <button class="btn inline-flex items-center pt-2 text-sm font-medium text-gray-500 bg-white">
                                <div>{{ __('Almoxarifado') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>

                        </x-slot>

                        <x-slot name="content" >
                            <x-dropdown-link :href="route('fabricantes')">
                                {{ __('Fabricantes') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('fornecedores')">
                                {{ __('Fornecedores') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('tipos_produtos')">
                                {{ __('Tipos de Produtos') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('produtos')">
                                {{ __('Produtos') }}
                            </x-dropdown-link>
                        </x-slot>

                    </x-dropdown>
                </div>

                <!-- Clientes -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex ">
                    <x-nav-link :href="route('clientes')" :active="request()->routeIs('clientes')">
                        {{ __('Clientes') }}
                    </x-nav-link>
                </div>
                
                <!-- Usuários -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex ">
                    <x-nav-link :href="route('usuarios')" :active="request()->routeIs('usuarios')">
                        {{ __('Usuários') }}
                    </x-nav-link>
                </div>

                <!-- Pedidos -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex ">
                    <x-nav-link :href="route('pedidos')" :active="request()->routeIs('pedidos')">
                        {{ __('Pedidos') }}
                    </x-nav-link>
                </div>
            
                <!-- Produção -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger" class="h-100">
                            <button class="btn inline-flex items-center pt-2 text-sm font-medium text-gray-500 bg-white">
                                <div>{{ __('Produção') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>

                        </x-slot>

                        <x-slot name="content" >
                            <x-dropdown-link :href="route('fracionamentos')">
                                {{ __('Fracionamento') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('parametros')">
                                {{ __('Parâmetros') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('planejamentos')">
                                {{ __('Planejamento') }}
                            </x-dropdown-link>
                        </x-slot>

                    </x-dropdown>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

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

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

        
        </div>
    </div>
</nav>

<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info', 'dark'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="position-absolute w-100 py-16">
                <p class="alert alert-{{ $msg }}">
                    {{ Session::get('alert-' . $msg) }} 
                    <a href="" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </p>
            </div>
        @endif
    @endforeach
</div>
