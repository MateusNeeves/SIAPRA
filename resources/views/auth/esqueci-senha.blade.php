<x-guest-layout>
    <h1>Recuperar acesso</h1>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-input-label for="username" value="Login" />
        <x-text-input id="username" name="username" type="text" required autofocus autocomplete="username" :value="old('username')" />
        <x-input-error :messages="$errors->get('username')" />
        <x-primary-button class="btn-orange mt-4">Solicitar recuperação</x-primary-button>
    </form>
    <a href="{{ route('login') }}">Voltar ao login</a>
</x-guest-layout>
