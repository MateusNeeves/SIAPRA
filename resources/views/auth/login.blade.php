<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username  -->
        <div>
            <x-input-label for="username" :value="__('Usuário')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            {{-- <x-input-error :messages="$errors->get('username')" class="mt-2" /> --}}
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            {{-- <x-input-error :messages="$errors->get('password')" class="mt-2" /> --}}
        </div>
        
        <x-input-error :messages="$errors->get('username')" class="mt-2" />

        <div class="flex items-center justify-between gap-3 mt-4">
            <a class="whitespace-nowrap underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">Esqueci minha senha</a>

            <x-primary-button class="btn-orange">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
