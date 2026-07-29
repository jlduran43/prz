<?php

namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\TipoCliente;

class ReservaWizardController extends Controller
{
    public function cliente()
    {
        $tiposCliente = TipoCliente::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $regiones = Region::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('reservas.paso1-cliente', [
            'paso' => 1,
            'tiposCliente' => $tiposCliente,
            'regiones' => $regiones,
            'datosCliente' => session('reserva.cliente', []),
        ]);
    }

    public function guardarCliente(Request $request)
    {
        $datos = $request->validate([
            'tipo_cliente_id' => [
                'required',
                'exists:tipos_cliente,id',
            ],
        ]);

        $tipoCliente = TipoCliente::findOrFail(
            $datos['tipo_cliente_id']
        );

        $datos['codigo_tipo_cliente'] =
            $tipoCliente->codigo;

        session([
            'reserva.cliente' => $datos,
        ]);

        return redirect()->route('reservas.datos');
    }

    public function guardarReserva(Request $request)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required'],
            'hora_fin' => ['required'],
        ]);

        session([
            'reserva.datos' => $datos,
        ]);

        return redirect()->route('reservas.confirmacion');
    }

    public function confirmacion()
    {
        if (
            ! session()->has('reserva.cliente') ||
            ! session()->has('reserva.datos')
        ) {
            return redirect()->route('reservas.cliente');
        }

        return view('reservas.paso3-confirmacion', [
            'paso' => 3,
            'cliente' => session('reserva.cliente'),
            'reserva' => session('reserva.datos'),
        ]);
    }

    public function finalizar()
    {
        /*
         * Aquí se guardarán definitivamente
         * el cliente y la reserva en la base de datos.
         */

        session()->forget('reserva');

        return redirect()
            ->route('reservas.index')
            ->with('success', 'Reserva registrada correctamente.');
    }

    public function comunasPorRegion(Region $region)
    {
        $comunas = $region->comunas()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ]);

        return response()->json($comunas);
    }

    public function reserva()
    {
        if (! session()->has('reserva.cliente')) {
            return redirect()->route('reservas.cliente');
        }

        $categoriasServicio = CategoriaServicio::query()
            ->where('activo', true)
            ->with([
                'servicios' => function ($query) {
                    $query
                        ->where('activo', true)
                        ->orderBy('nombre');
                },
            ])
            ->whereHas('servicios', function ($query) {
                $query->where('activo', true);
            })
            ->orderBy('nombre')
            ->get();

        return view('reservas.paso2-reserva', [
            'paso' => 2,
            'datosCliente' => session('reserva.cliente'),
            'datosReserva' => session('reserva.reserva', []),
            'categoriasServicio' => $categoriasServicio,
        ]);
    }
}
