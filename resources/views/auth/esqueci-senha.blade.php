<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-800">Recuperar acesso</h2>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-4">
        @csrf

        <div>
            <x-input-label for="username" value="Usuário" />
            <x-text-input id="username" class="block mt-1 w-full" name="username" type="text" required autofocus autocomplete="username" :value="old('username')" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3 mt-4">
            <a class="whitespace-nowrap underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">Voltar ao login</a>

            <x-primary-button class="btn-orange">Solicitar recuperação</x-primary-button>
        </div>
    </form>
</x-guest-layout>
