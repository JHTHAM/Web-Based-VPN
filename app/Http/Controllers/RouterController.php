<?php 

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RouterController extends Controller
{
    public function fetchStatus()
    {
        $statusFile = '/etc/openvpn/openvpn-status.log';
        $output = @file_get_contents($statusFile); // Local read instead of SSH

        $activeRouters = [];
        if ($output) {
            foreach (explode("\n", $output) as $line) {
                if (str_starts_with($line, 'CLIENT_LIST')) {
                    $fields = str_getcsv($line);

                    // Only count valid lines where the first field is "CLIENT_LIST"
                    // and the second field (Common Name) exists
                    if (isset($fields[1]) && $fields[0] === 'CLIENT_LIST') {
                        $clientName = trim($fields[1]);
                        if ($clientName !== '') {
                            $activeRouters[] = $clientName;
                        }
                    }
                }
            }
        }

        // Build the statuses array
        $statuses = [];
        foreach (Router::all() as $router) {
            $statuses[$router->id] = in_array($router->name, $activeRouters)
                ? 'online'
                : 'offline';
        }

        return response()->json($statuses);
    }

    // show all routers
    public function index()
    {
        $this->fetchStatus();
        $routers = Router::all();
        return view('routers', compact('routers'));
    }

    // show dashboard
    public function dashboard()
    {
        $this->fetchStatus();
        $routers = Router::all();
        return view('dashboard', compact('routers'));
    }

    // store a new router
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:routers,name',
        ], ['name.unique' => 'This router name is already taken. Please choose another.']);

        $name = $request->name;

        // Create router and DB record
        $router = Router::create([
            'name' => $name,
            'ip_address' => null,
            'status' => 'offline',
        ]);

        $clientName = escapeshellarg($name);
        $scriptPath = "/opt/shared_vpn/client-configs/generate_ovpn.sh";

        Log::info("Running local script: $scriptPath $clientName");
        $output = shell_exec("sudo -u utar {$scriptPath} {$clientName} 2>&1");
        Log::info("Script output: $output");

        // Assigned IP should be last line of script output
        $lines = explode("\n", trim($output));
        $assignedIp = end($lines);

        // Copy generated file to Laravel storage
        $remoteFile = "/opt/shared_vpn/client-configs/files/{$name}.ovpn";
        $localDir = storage_path('app/ovpn');
        $localPath = "{$localDir}/{$name}.ovpn";

        if (!file_exists($localDir)) {
            mkdir($localDir, 0755, true);
        }

        // Save result if successful
        if (file_exists($remoteFile)) {
            copy($remoteFile, $localPath);
            $router->ip_address = $assignedIp;
            $router->ovpn_path = $localPath;
            $router->save();
            Log::info("✅ .ovpn fetched and saved for router: $name");
        } else {
            Log::error("❌ Failed to find .ovpn for router: $name");
        }

        return redirect()->back()->with('success', 'Router created and .ovpn generated.');
    }

    // delete router
    public function destroy($id)
    {
        $router = Router::findOrFail($id);
        $name = $router->name;

        $clientName = escapeshellarg($name);
        $deleteScript = "/opt/shared_vpn/client-configs/delete_ovpn.sh";

        Log::info("Running local delete script: $deleteScript $clientName");
        $output = shell_exec("sudo -u utar {$deleteScript} {$clientName} 2>&1");
        Log::info("Delete output: $output");

        $router->delete();

        return redirect()->back()->with('success', "Router '$name' deleted and cleanup completed.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
        ]);

        $router = Router::findOrFail($id);
        $router->label = $request->label ?? '';
        $router->save();

        return redirect()->back()->with('success', 'Router updated successfully.');
    }

    // download ovpn
    public function downloadOvpn($id)
    {
        $router = Router::findOrFail($id);
        $file = $router->ovpn_path;

        if (!file_exists($file)) {
            abort(404, 'OVPN file not found.');
        }

        return response()->download($file, $router->name . '.ovpn');
    }
}
