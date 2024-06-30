<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Compania;
use Illuminate\Http\Request;

class companiasController extends Controller
{
    public function CreateCompania(Request $request)
    {

        $data =  $request->validate([
            'razon_social' => 'required|string',
            'ruc' => 'required|string', 'regex:/⌃(10|20)\d{9}$/', 'unique:compania,ruc',
            'direccion' => 'required|string',
            'logo' => 'nullable|image',
            'sol_user' => 'required|string',
            'sol_pass' => 'required|string',
            //extesion .pem
            'cert' => 'required|file|mimes:pem,txt',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'production' => 'nullable|boolean'
        ]);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }
        $data['cert_path'] = $request->file('cert')->store('certs');

        $company = Compania::create($data);

        return response()->json([
            'message' => 'empresa creada correctamente',
            'company' => $company
        ]);
    }
}
