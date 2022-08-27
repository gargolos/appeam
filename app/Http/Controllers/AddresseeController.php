<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  Illuminate\Support\Facades\Validator;

use App\Addressees;

class AddresseeController extends Controller
{

    public function index()
    {
        $destinatarios = Addressees::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'ciudades' => $destinatarios
        ]);
        
    }

    public function show($id)
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
