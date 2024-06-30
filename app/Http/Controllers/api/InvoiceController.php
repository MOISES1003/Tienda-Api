<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Compania;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function send(Request $request)
    {
        $company = Compania::first();
    
        // Asegúrate de que la ruta al archivo de certificado sea correcta
        $certPath = $company->cert_path;
    
        if (!Storage::exists($certPath)) {
            return response()->json(['message' => 'El archivo de certificado no existe en la ruta especificada.'], 404);
        }
    
        // Leer el contenido del certificado usando el sistema de almacenamiento de Laravel
        $Certificado = Storage::get($certPath);
    
        $see = new See();
        $see->setCertificate($Certificado); // Aquí ya no necesitas usar file_get_contents
        $see->setService(SunatEndpoints::FE_BETA);
        $see->setClaveSOL($company->ruc, $company->sol_user, $company->sol_pass);
    
        // Cliente
        $client = (new Client())
            ->setTipoDoc('6')
            ->setNumDoc('20000000001')
            ->setRznSocial('EMPRESA X');
    
        // Emisor
        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento('LIMA')
            ->setProvincia('LIMA')
            ->setDistrito('LIMA')
            ->setUrbanizacion('-')
            ->setDireccion('Av. Villa Nueva 221')
            ->setCodLocal('0000'); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.
    
        $companyData = (new Company())
            ->setRuc('20123456789')
            ->setRazonSocial('GREEN SAC')
            ->setNombreComercial('GREEN')
            ->setAddress($address);
    
        // Venta
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta - Catalog. 51
            ->setTipoDoc('01') // Factura - Catalog. 01 
            ->setSerie('F001')
            ->setCorrelativo('1')
            ->setFechaEmision(new DateTime('2020-08-24 13:05:00-05:00')) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda('PEN') // Sol - Catalog. 02
            ->setCompany($companyData)
            ->setClient($client)
            ->setMtoOperGravadas(100.00)
            ->setMtoIGV(18.00)
            ->setTotalImpuestos(18.00)
            ->setValorVenta(100.00)
            ->setSubTotal(118.00)
            ->setMtoImpVenta(118.00);
    
        $item = (new SaleDetail())
            ->setCodProducto('P001')
            ->setUnidad('NIU') // Unidad - Catalog. 03
            ->setCantidad(2)
            ->setMtoValorUnitario(50.00)
            ->setDescripcion('PRODUCTO 1')
            ->setMtoBaseIgv(100)
            ->setPorcentajeIgv(18.00) // 18%
            ->setIgv(18.00)
            ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
            ->setTotalImpuestos(18.00) // Suma de impuestos en el detalle
            ->setMtoValorVenta(100.00)
            ->setMtoPrecioUnitario(59.00);
    
        $legend = (new Legend())
            ->setCode('1000') // Monto en letras - Catalog. 52
            ->setValue('SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES');
    
        $invoice->setDetails([$item])
            ->setLegends([$legend]);
    
        $result = $see->send($invoice);
    
        // Verificamos que la conexión con SUNAT fue exitosa.
        if (!$result->isSuccess()) {
            // Mostrar error al conectarse a SUNAT.
            return response()->json([
                'Codigo Error' => $result->getError()->getCode(),
                'Mensaje Error' => $result->getError()->getMessage(),
            ], 400);
        }
    
        $cdr = $result->getCdrResponse();
        $code = (int)$cdr->getCode();
    
        if ($code === 0) {
            $response = ['ESTADO' => 'ACEPTADA'];
            if (count($cdr->getNotes()) > 0) {
                $response['OBSERVACIONES'] = $cdr->getNotes();
            }
        } else if ($code >= 2000 && $code <= 3999) {
            $response = ['ESTADO' => 'RECHAZADA'];
        } else {
            // Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción.
            $response = ['ESTADO' => 'Excepción'];
        }
    
        $response['Descripcion'] = $cdr->getDescription();
    
        return response()->json($response);
    }
    
}
