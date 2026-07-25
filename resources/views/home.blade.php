@extends('layouts.app')

@section('content')
<div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-gray-800 border border-gray-700 shadow-xl overflow-hidden sm:rounded-lg">
    <div class="mb-6 border-b border-gray-700 pb-4">
        <h2 class="text-2xl font-bold text-white">Dashboard</h2>
        <p class="text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}!</p>
    </div>
    
    <div class="text-gray-300">
        <p>You are successfully logged in to FlowHub.</p>
        <p class="mt-4 text-gray-400">From here, you will be able to manage your automations, connect your third-party services, and view your execution history.</p>
        
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 border rounded-md bg-indigo-900/30 border-indigo-700/50">
                <h3 class="font-semibold text-indigo-300">Automations</h3>
                <p class="text-sm text-indigo-400 mt-1">0 Active</p>
            </div>
            <div class="p-4 border rounded-md bg-emerald-900/30 border-emerald-700/50">
                <h3 class="font-semibold text-emerald-300">Connected Services</h3>
                <p class="text-sm text-emerald-400 mt-1">0 Services</p>
            </div>
        </div>
    </div>
</div>
@endsection
