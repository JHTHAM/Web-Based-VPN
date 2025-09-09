<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Router;
use App\Models\StandaloneClient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NetworkController extends Controller
{
    // List networks
    public function index()
    {
        $networks = Network::withCount(['routers', 'standaloneClients'])->get();
        return view('networks', compact('networks'));
    }

    // Store a new network
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:networks,name'],
        ['name.unique' => 'This network name is already taken. Please choose another.']);
        Network::create(['name' => $request->name]);
        return redirect()->route('networks.index')->with('success', 'Network created successfully!');
    }

    // Delete a network
    public function destroy(Network $network)
    {
        $network->delete();
        return redirect()->route('networks.index')->with('success', 'Network deleted successfully!');
    }

    // Show devices for a specific network
    public function devices(Network $network)
    {
        $allRouters = Router::all();
        $allStandaloneClients = StandaloneClient::all();
        return view('devices_in_networks', [
            'network' => $network->load(['routers', 'standaloneClients']),
            'allRouters' => $allRouters,
            'allStandaloneClients' => $allStandaloneClients,
        ]);
    }

    public function networkDevices(Network $network)
    {
        $allRouters = Router::all();
        $allStandaloneClients = StandaloneClient::all();
        return view('network_devices', [
            'network' => $network->load(['routers', 'standaloneClients']),
            'allRouters' => $allRouters,
            'allStandaloneClients' => $allStandaloneClients,
        ]);
    }

    public function fetchStatus()
    {
        $statusFile = '/etc/openvpn/openvpn-status.log';
        $output = @file_get_contents($statusFile);
        
        $activeRouters = [];

        if ($output) {
            foreach (explode("\n", $output) as $line) {
                if (str_starts_with($line, 'CLIENT_LIST')) {
                    $fields = str_getcsv($line);
                    if (isset($fields[1]) && !empty($fields[1])) {
                        $clientName = trim($fields[1]);
                        $activeRouters[] = $clientName;
                    }
                }
            }
        }

        $statuses = [];
        // Router
        foreach (\App\Models\Router::all() as $router) {
            $statuses["router-{$router->id}"] = in_array($router->name, $activeRouters)
                ? 'online'
                : 'offline';
        }
        
        // Standalone Client
        foreach (\App\Models\StandaloneClient::all() as $client) {
            $statuses["client-{$client->id}"] = in_array($client->name, $activeRouters)
                ? 'online'
                : 'offline';
        }

        return response()->json($statuses);
    }

    // Add devices (routers) to a network
    public function addDevices(Request $request, Network $network)
    {
        $request->validate(['router_ids' => 'required|array',
        'router_ids.*' => ['integer',
            Rule::exists('routers', 'id'),
            function ($attribute, $value, $fail) use ($network) {
                if ($network->routers()->where('routers.id', $value)->exists()) {
                    $fail("The selected router is already in this network.");
                }
            }
        ]], ['router_ids.required' => 'Please select at least one router.']);

        $network->routers()->syncWithoutDetaching($request->router_ids);
        return redirect()->route('network.networkDevices', $network->id)->with('success', 'Router added successfully!');
    }

    // Remove a device from the network
    public function removeDevice(Network $network, Router $router)
    {
        $network->routers()->detach($router->id);
        return redirect()->route('network.networkDevices', $network->id)->with('success', 'Router removed successfully!');
    }

    // Add standalone clients to a network
    public function addStandaloneDevices(Request $request, Network $network)
    {
        $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => ['integer',
                Rule::exists('standalone_clients', 'id'),
                function ($attribute, $value, $fail) use ($network) {
                    if ($network->standaloneClients()->where('standalone_clients.id', $value)->exists()) {
                        $fail("The selected standalone client is already in this network.");
                    }
                }
            ]
        ], [
            'client_ids.required' => 'Please select at least one standalone client.'
        ]);

        $network->standaloneClients()->syncWithoutDetaching($request->client_ids);
        return redirect()->route('network.networkDevices', $network->id)->with('success', 'Standalone client added successfully!');
    }

    // Remove standalone client from the network 
    public function removeStandaloneDevice(Network $network, StandaloneClient $client)
    {
        $network->standaloneClients()->detach($client->id);
        return redirect()->route('network.networkDevices', $network->id)->with('success', 'Standalone client removed successfully!');
    }
}
