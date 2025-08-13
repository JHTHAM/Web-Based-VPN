<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NetworkController extends Controller
{
    // List networks
    public function index()
    {
        $networks = Network::withCount('routers')->get();
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
        return view('devices_in_networks', [
            'network' => $network->load('routers'),
            'allRouters' => $allRouters
        ]);
    }

    public function fetchStatus()
    {
        $remoteFile = '/etc/openvpn/openvpn-status.log';
        $sshCommand = "ssh utar@10.8.0.1 \"cat $remoteFile\"";
        $output = shell_exec($sshCommand);

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
        foreach (\App\Models\Router::all() as $router) {
            $statuses[$router->id] = in_array($router->name, $activeRouters) ? 'online' : 'offline';
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
        return redirect()->route('network.devices', $network->id)->with('success', 'Device added successfully!');
    }

    // Remove a device from the network
    public function removeDevice(Network $network, Router $router)
    {
        $network->routers()->detach($router->id);
        return redirect()->route('network.devices', $network->id)->with('success', 'Device removed successfully!');
    }
}
