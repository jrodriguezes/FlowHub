@extends('layouts.app')

@section('content')
<div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-gray-800 border border-gray-700 shadow-xl overflow-hidden sm:rounded-lg mx-auto">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-white">Verificación de 2 Pasos</h2>
        <p class="text-gray-400 mt-2 text-sm">Abre tu aplicación de autenticación (Google Authenticator) e ingresa el código de 6 dígitos.</p>
    </div>

    <form method="POST" action="{{ route('2fa.challenge.verify') }}">
        @csrf

        <div>
            <label for="code" class="block font-medium text-sm text-gray-300">Código de Autenticación</label>
            <input id="code" class="block mt-1 w-full rounded-md shadow-sm bg-gray-900 border-gray-600 text-white focus:border-indigo-400 focus:ring-indigo-400 py-3 px-3 border text-center text-xl font-mono tracking-[0.5em]" type="text" inputmode="numeric" pattern="[0-9]*" name="code" autofocus autocomplete="one-time-code" maxlength="6" required />
            @error('code')
                <p class="text-red-400 text-xs mt-2 text-center">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 focus:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition ease-in-out duration-150">
                Iniciar Sesión
            </button>
        </div>
        
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-gray-300 underline">Volver al inicio de sesión</a>
        </div>
    </form>
</div>
@endsection
