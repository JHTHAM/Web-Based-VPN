<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Models\StandaloneClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StandaloneClientController extends Controller
{
    public function fetchStatus()
    {
        $statusFile = '/etc/openvpn/openvpn-status.log';
        $output = @file_get_contents($statusFile); // Local read instead of SSH

        $activeClients = [];
        if ($output) {
            foreach (explode("\n", $output) as $line) {
                if (str_starts_with($line, 'CLIENT_LIST')) {
                    $fields = str_getcsv($line);
                    if (isset($fields[1]) && $fields[0] === 'CLIENT_LIST') {
                        $clientName = trim($fields[1]);
                        if ($clientName !== '') {
                            $activeClients[] = $clientName;
                        }
                    }
                }
            }
        }

        $statuses = [];
        foreach (StandaloneClient::all() as $client) {
            $statuses[$client->id] = in_array($client->name, $activeClients)
                ? 'online'
                : 'offline';
        }

        return response()->json($statuses);
    }

    public function index()
    {
        $this->fetchStatus();
        $clients = StandaloneClient::all();
        return view('standalone_clients', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Check in routers
                    if (Router::where('name', $value)->exists()) {
                        $fail("The $attribute '$value' is already taken (router)!");
                    }

                    // Check in standalone clients
                    if (StandaloneClient::where('name', $value)->exists()) {
                        $fail("The $attribute '$value' is already taken (standalone client)!");
                    }
                },
            ],
        ], [
            'name.required' => 'A name is required.',
        ]);

        $name = $request->name;

        $client = StandaloneClient::create([
            'name'       => $name,
            'ip_address' => null,
            'status'     => 'offline',
        ]);

        $clientName = escapeshellarg($name);
        $scriptPath = "/opt/vpn/client-configs/generate_client_ovpn.sh";

        Log::info("Running local script: $scriptPath $clientName");
        $output = shell_exec("sudo -u utar {$scriptPath} {$clientName} 2>&1");
        Log::info("Script output: $output");

        $lines = explode("\n", trim($sshOutput));
        $assignedIp = end($lines);

        $remoteFile = "/opt/vpn/client-ovpn/{$name}.ovpn";
        $localDir = storage_path('app/ovpn');
        $localPath = "{$localDir}/{$name}.ovpn";

        if (!file_exists($localDir)) {
            mkdir($localDir, 0755, true);
        }

        if (file_exists($remoteFile)) {
            copy($remoteFile, $localPath);
            $client->ip_address = $assignedIp;
            $client->ovpn_path  = $localPath;
            $client->save();
            Log::info("✅ .ovpn fetched and saved for standalone client: $name");
        } else {
            Log::error("❌ Failed to fetch .ovpn for standalone client: $name");
        }

        return redirect()->back()->with('success', 'Standalone client created and .ovpn generated successfully!');
    }

    public function destroy($id)
    {
        $client = StandaloneClient::findOrFail($id);
        $name   = $client->name;

        $clientName = escapeshellarg($name);
        $deleteScript = "/opt/vpn/client-configs/delete_ovpn.sh";
        
        Log::info("Running local delete script: $deleteScript $clientName");
        $output = shell_exec("sudo -u utar {$deleteScript} {$clientName} 2>&1");
        Log::info("Delete output: $output");

        $client->delete();

        return redirect()->back()->with('success', "Standalone client '$name' deleted successfully!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
        ]);

        $client = StandaloneClient::findOrFail($id);
        $client->label = $request->label ?? '';
        $client->save();

        return redirect()->back()->with('success', 'Standalone client updated successfully!');
    }

    public function downloadOvpn($id)
    {
        $client = StandaloneClient::findOrFail($id);
        $file   = $client->ovpn_path;

        if (!file_exists($file)) {
            abort(404, 'OVPN file not found.');
        }

        return response()->download($file, $client->name . '.ovpn');
    }
}
