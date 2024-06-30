<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IngresosController extends Controller
{
    protected $fillable = [
        "ingreso_id",
        "venta_id",
        "total_ganancia",
        "fecha_ingreso"
    ];

    //CON ESTO INDICAMOS EL NOMBRE DE LA TABLA DE LA BASE DE DATOS QUE TENEMOS CREADA
    protected $table = 'ingresos';

    //al trabajar con token ponemos esto para decir que así se llama el campo id de nuestra tabla
    //lo mismo hacemos cuando usamos request
    protected $primaryKey = 'ingreso_id';


    //insertar dentro de la lista los campos a ocultar
    protected $hidden = [
        // "contrasena",
    ];


    //decimos que no estamos utilizando fechas en nuestra tabla, ponemos en false para que al momeento de actualizar no
    //sea necesario esos campos
    public $timestamps = false;
}
