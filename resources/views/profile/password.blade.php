<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Trocar senha
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 text-green-600">
                        Senha alterada com sucesso.
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Senha atual</label>
                        <input
                            type="password"
                            name="current_password"
                            class="w-full border rounded px-3 py-2"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Nova senha</label>
                        <input
                            type="password"
                            name="password"
                            class="w-full border rounded px-3 py-2"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Confirmar nova senha</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full border rounded px-3 py-2"
                            required
                        >
                    </div>

                    <button 
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded transition"
                    >
                        Alterar senha
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
