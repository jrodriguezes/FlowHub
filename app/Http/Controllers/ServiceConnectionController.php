<?php

namespace App\Http\Controllers;

use App\Models\ServiceConnection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\ConnectionStatus;

class ServiceConnectionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ServiceConnection::class);

        $connections = $request->user()
            ->serviceConnections()
            ->latest()
            ->get();

        return view('connections.index', ['connections' => $connections]);
    }

    public function destroy(ServiceConnection $serviceConnection)
    {
        $this->authorize('delete', $serviceConnection);

        $serviceConnection->update([
            'status' => ConnectionStatus::REVOKED,
            'access_token' => null,
            'refresh_token' => null,
            'revoked_at' => now(),
        ]);

        return redirect()->route('connections.index')->with('success', 'Cuenta desconectada exitosamente.');
    }
}
