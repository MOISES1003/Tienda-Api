<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permiso extends Model
{
    use HasFactory;
    protected $fillable = [
        "id_permiso",
        "id_usuario",
        "id_modulo"
    ];

    //CON ESTO INDICAMOS EL NOMBRE DE LA TABLA DE LA BASE DE DATOS QUE TENEMOS CREADA
    protected $table = 'permisos';

    //al trabajar con token ponemos esto para decir que así se llama el campo id de nuestra tabla
    //lo mismo hacemos cuando usamos request
    protected $primaryKey = 'id_permiso';


    //insertar dentro de la lista los campos a ocultar
    protected $hidden = [
        // "contrasena",
    ];


    //decimos que no estamos utilizando fechas en nuestra tabla, ponemos en false para que al momeento de actualizar no
    //sea necesario esos campos
    public $timestamps = false;
}
