@extends('layouts.app')

@section('content')
<div class="w-full max-w-4xl mx-auto mt-6 px-6 py-8">
    <div class="mb-8 border-b border-gray-700 pb-4">
        <h2 class="text-3xl font-bold text-white">Mi Perfil</h2>
        <p class="text-gray-400 mt-2">Gestiona la seguridad de tu cuenta.</p>
    </div>

    <!-- 2FA Section -->
    <div class="bg-gray-800 border border-gray-700 shadow-xl overflow-hidden sm:rounded-lg p-6">
        <h3 class="text-xl font-semibold text-white mb-4">Autenticación de Dos Factores (2FA)</h3>
        
        <p class="text-gray-300 text-sm mb-6">
            Agrega seguridad adicional a tu cuenta requiriendo un código de 6 dígitos cada vez que inicies sesión.
        </p>

        @if(!isset(auth()->user()->two_factor_secret) || !auth()->user()->two_factor_secret)
            <!-- Activar 2FA -->
            <form method="POST" action="{{ route('2fa.enable') }}">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 transition">
                    Activar 2FA
                </button>
            </form>
        @elseif(!auth()->user()->two_factor_confirmed)
            <!-- Confirmar QR -->
            <div class="bg-gray-900/50 border border-gray-700 p-6 rounded-md mb-6 flex flex-col md:flex-row gap-6 items-center">
                <div class="bg-white p-3 rounded-md">
                    @if(isset($qrCodeUrl))
                        <div id="qrcode"></div>
                        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
                        <script>
                            new QRCode(document.getElementById("qrcode"), {
                                text: "{{ $qrCodeUrl }}",
                                width: 160,
                                height: 160,
                                colorDark : "#000000",
                                colorLight : "#ffffff",
                                correctLevel : QRCode.CorrectLevel.L
                            });
                        </script>
                    @endif
                </div>
                
                <div class="flex-1">
                    <h4 class="text-white font-semibold mb-2">Termina de configurar tu 2FA</h4>
                    <p class="text-sm text-gray-400 mb-4">Abre Google Authenticator, escanea el código QR y digita el código generado abajo para verificar tu dispositivo.</p>
                    
                    <form method="POST" action="{{ route('2fa.confirm') }}" class="flex gap-2">
                        @csrf
                        <input id="code" class="w-32 rounded-md shadow-sm bg-gray-800 border-gray-600 text-white focus:border-indigo-400 focus:ring-indigo-400 py-2 px-3 border text-center font-mono tracking-widest" type="text" name="code" placeholder="000000" maxlength="6" required />
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-400 transition">
                            Verificar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Boton para cancelar la activacion a medias -->
            <form method="POST" action="{{ route('2fa.disable') }}" class="border-t border-gray-700 pt-4 mt-4">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600/20 border border-transparent rounded-md font-semibold text-xs text-red-500 uppercase tracking-widest hover:bg-red-600/30 transition">
                    Cancelar configuración
                </button>
            </form>
        @else
            <!-- 2FA Ya esta Activo y Confirmado -->
            <div class="bg-emerald-900/30 border border-emerald-700/50 p-4 rounded-md mb-6 flex items-center">
                <svg class="w-6 h-6 text-emerald-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-emerald-300 font-semibold">2FA Activado</h4>
                    <p class="text-sm text-emerald-400/80">Tu cuenta está protegida con autenticación de dos factores.</p>
                </div>
            </div>

            <!-- Desactivar 2FA -->
            <form method="POST" action="{{ route('2fa.disable') }}" class="border-t border-gray-700 pt-4 mt-4">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition">
                    Desactivar 2FA
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
