<x-guest-layout>
    <h1>Defina sua nova senha</h1>
    <p>Para continuar, escolha uma senha com pelo menos oito caracteres, diferente da senha temporária.</p>
    <form method="POST" action="{{ route('password.forced.update') }}">
        @csrf
        <x-input-label for="password" value="Nova senha" />
        <x-text-input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" />
        <x-input-label for="password_confirmation" value="Confirme a nova senha" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />
        <x-primary-button class="btn-orange mt-4">Salvar nova senha</x-primary-button>
    </form>
    <form method="POST" action="{{ route('logout') }}">@csrf <button type="submit">Sair</button></form>
</x-guest-layout>
