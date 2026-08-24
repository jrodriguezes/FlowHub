@extends('layouts.app')

@php
    $automationsCount = \App\Models\Automation::where('user_id', Auth::id())->count();
    $connectionsCount = \App\Models\ServiceConnection::where('user_id', Auth::id())->count();
    $executionsCount = \App\Models\AutomationExecution::where('user_id', Auth::id())->count();
@endphp

@section('content')
<div class="space-y-8 animate-fade-in-up">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-emerald-400">
                Welcome back, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </h2>
            <p class="text-gray-400 mt-2 text-sm">Here's what's happening with your automations today.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('automations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-indigo-500/30 transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Automation
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Automations Stat -->
        <div class="relative overflow-hidden bg-[#111827] border border-white/5 rounded-2xl p-6 group hover:border-indigo-500/30 transition-colors">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Active Automations</p>
                    <p class="text-4xl font-bold text-white mt-2">{{ $automationsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm relative z-10">
                <a href="{{ route('automations.index') }}" class="text-indigo-400 hover:text-indigo-300 font-medium inline-flex items-center">
                    Manage automations <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Connections Stat -->
        <div class="relative overflow-hidden bg-[#111827] border border-white/5 rounded-2xl p-6 group hover:border-emerald-500/30 transition-colors">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Connected Services</p>
                    <p class="text-4xl font-bold text-white mt-2">{{ $connectionsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm relative z-10">
                <a href="{{ route('connections.index') }}" class="text-emerald-400 hover:text-emerald-300 font-medium inline-flex items-center">
                    Add new service <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Executions Stat -->
        <div class="relative overflow-hidden bg-[#111827] border border-white/5 rounded-2xl p-6 group hover:border-fuchsia-500/30 transition-colors">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-fuchsia-500/10 rounded-full blur-2xl group-hover:bg-fuchsia-500/20 transition-all"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Total Executions</p>
                    <p class="text-4xl font-bold text-white mt-2">{{ $executionsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-fuchsia-500/20 rounded-xl flex items-center justify-center text-fuchsia-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm relative z-10">
                <a href="{{ route('executions.index') }}" class="text-fuchsia-400 hover:text-fuchsia-300 font-medium inline-flex items-center">
                    View execution logs <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Start Guide -->
    <div class="bg-gradient-to-r from-[#111827] to-[#1a2333] border border-white/5 rounded-2xl p-8 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute right-0 top-0 w-64 h-full opacity-10 pointer-events-none">
            <svg viewBox="0 0 100 100" class="w-full h-full text-indigo-400" fill="currentColor">
                <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="2"></circle>
                </pattern>
                <rect width="100" height="100" fill="url(#dots)"></rect>
            </svg>
        </div>

        <div class="relative z-10 max-w-2xl">
            <h3 class="text-xl font-bold text-white mb-2">Ready to automate your workflow? 🚀</h3>
            <p class="text-gray-400 mb-6 leading-relaxed">
                You're just a few steps away from putting your tasks on autopilot. FlowHub connects GitHub, Google, and your favorite tools together in real-time.
            </p>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold border border-indigo-500/30 text-sm">1</div>
                    <div class="ml-4">
                        <h4 class="text-white font-medium">Connect your services</h4>
                        <p class="text-sm text-gray-400 mt-1">Head over to the <a href="{{ route('connections.index') }}" class="text-indigo-400 hover:underline">Connections tab</a> and link your Google and GitHub accounts securely.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold border border-indigo-500/30 text-sm">2</div>
                    <div class="ml-4">
                        <h4 class="text-white font-medium">Create an Automation</h4>
                        <p class="text-sm text-gray-400 mt-1">Define triggers (e.g. "When a GitHub issue is opened") and actions (e.g. "Send me an email").</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold border border-indigo-500/30 text-sm">3</div>
                    <div class="ml-4">
                        <h4 class="text-white font-medium">Watch the magic happen</h4>
                        <p class="text-sm text-gray-400 mt-1">Our background queue will automatically execute your rules. Check the <a href="{{ route('executions.index') }}" class="text-indigo-400 hover:underline">History tab</a> to see the logs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
