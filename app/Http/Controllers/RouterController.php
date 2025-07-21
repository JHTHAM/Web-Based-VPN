<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class RouterController extends Controller
{
    // Show all routers
    public function index()
    {
        $routers = Router::all();
        return view('routers', compact('routers'));
    }

    // Show dashboard
    public function dashboard()
    {
        $routers = Router::all();
        return view('dashboard', compact('routers'));
    }

    
    // Store a new router
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:routers,name',
        ]);

        $name = $request->name;

        // Step 1: Create router record
        $router = Router::create([
            'name' => $name,
            'ip_address' => null,
            'status' => 'offline',
        ]);

        // Step 2: SSH command to run remote script
        $clientName = escapeshellarg($name);
        $remoteScript = "bash /opt/shared_vpn/client-configs/generate_ovpn.sh $clientName";
        $sshCommand = "ssh utar@vpn.novaplus.my \"$remoteScript\"";

        Log::info("Running SSH command: $sshCommand");
        $sshOutput = shell_exec($sshCommand);
        Log::info("SSH Output: $sshOutput");

        // Extract the assigned IP (last line of output)
        $lines = explode("\n", trim($sshOutput));
        $assignedIp = end($lines);

        // Step 3: SCP back .ovpn file from remote server
        $remoteFile = "utar@vpn.novaplus.my:/opt/shared_vpn/client-configs/files/{$name}.ovpn";
        $localDir = storage_path('app/ovpn');
        $localPath = "$localDir/{$name}.ovpn";

        if (!file_exists($localDir)) {
            mkdir($localDir, 0755, true);
        }

        $scpCommand = "scp $remoteFile \"$localPath\"";
        \Log::info("Running SCP Command: $scpCommand");
        $scpOutput = shell_exec($scpCommand);
        \Log::info("SCP Output: " . $scpOutput);

        // Step 4: Save result if successful
        if (file_exists($localPath)) {
            $router->ip_address = $assignedIp;
            $router->ovpn_path = $localPath;
            $router->save();
            \Log::info("✅ .ovpn fetched and saved for router: $name");
        } else {
            \Log::error("❌ Failed to fetch .ovpn for router: $name");
        }

        return redirect()->back()->with('success', 'Router created and .ovpn generated from remote server.');
    }

    // Delete router
    public function destroy($id)
    {
        $router = Router::findOrFail($id);
        $name = $router->name;

        // Step 1: Build remote script command
        $clientName = escapeshellarg($name);
        $remoteScript = "bash /opt/shared_vpn/client-configs/delete_ovpn.sh $clientName";
        $sshCommand = "ssh utar@vpn.novaplus.my \"$remoteScript\"";

        // Step 2: Run SSH command and capture output
        \Log::info("Running SSH command for deletion: $sshCommand");
        $sshOutput = shell_exec($sshCommand);
        \Log::info("Delete SSH Output for $name: $sshOutput");

        // Step 3: Delete router from database after remote deletion
        $router->delete();

        // Step 4: Return with status message
        return redirect()->back()->with('success', "Router '$name' deleted and remote cleanup completed.");
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

    // Download OVPN
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
