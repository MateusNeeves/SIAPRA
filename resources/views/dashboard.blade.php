<x-app-layout>
    <div style="padding-top:100px">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 h3 text-center">
                    {{"Seja Bem Vindo, " . Auth::user()->username}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
