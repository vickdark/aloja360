<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configuraciones = Configuracion::all()->groupBy('group');
        return view('configuracion.index', compact('configuraciones'));
    }

    public function update(Request $request)
    {
        // Manejar la subida del logo si existe
        if ($request->hasFile('app_logo_image')) {
            $path = $request->file('app_logo_image')->store('app', 'public');
            
            // Opcional: Eliminar el logo anterior si existe
            $oldLogo = setting('app_logo_image');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            Configuracion::updateOrCreate(
                ['key' => 'app_logo_image'],
                ['value' => $path, 'group' => 'app']
            );
        }

        $data = $request->except('_token', '_method', 'app_logo_image');

        foreach ($data as $key => $value) {
            Configuracion::where('key', $key)->update(['value' => $value]);
        }

        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Configuración actualizada correctamente');
    }
}
