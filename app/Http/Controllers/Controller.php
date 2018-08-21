<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use nusoap_client;
use App\transaction_data;
use Carbon\Carbon;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected $identificador;

    protected $tranKey;

    protected $seed;
    
    protected $cliente;

    protected $bancos;
    
    protected $transaction_data_default;

    /**
    * Función constructora
    */
    public function __construct()
    {
    	date_default_timezone_set('America/Bogota');
        $this->identificador = '6dd490faf9cb87a9862245da41170ff2';
        $this->tranKey = '024h1IlD';
        $this->seed = date('c');
        $this->startCliente();
        $this->verificarBancos();
        $this->setDefaultDataTransaction();
    }

    /**
    * 
    * Función encargada de conectar la aplicación con los servicios
    * 
    */
    public function startCliente()
    {
        $this->cliente = new nusoap_client('https://test.placetopay.com/soap/pse/?wsdl', true);
    }

    public function verificarBancos()
    {
    	$transaction_info = transaction_data::orderBy('created_at')->first();
    	$date_actual = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

    	if (!$transaction_info || !$transaction_info->created_at->isSameDay($date_actual)) {
    		$bancos = $this->cliente->call('getBankList',$this->getAuthData())['getBankListResult'];
    		$collection_bancos = collect($bancos['item']);

    		$collection_bancos->transform(function ($item, $key) {
			    return ['bankCode' => $item['bankCode'], 'bankName' => utf8_encode($item['bankName'])];
			});

    		$newBancos = new transaction_data();
    		$newBancos->bancos = json_encode($collection_bancos);
    		$newBancos->save();

    		$transaction_info = transaction_data::orderBy('created_at')->first();
    	}

    	$this->bancos = json_decode($transaction_info->bancos);
    }

    public function getAuthData()
    {
        $parametros = ['login' => $this->identificador, 'tranKey' => SHA1($this->seed . $this->tranKey, false),'seed' => $this->seed, 'additional' => []];
        return ['auth' => $parametros];
    }

    public function setDefaultDataTransaction()
    {
    	$total = 100000000;
    	$total_impuesto = 1900000;
    	$total_base = 50000;

    	$data_transaction = [
    		'bankCode' => null,
    		'bankInterface' => null,
    		'returnURL' => null,
    		'reference' => random_int(000000000000001, 999999999999999),
    		'description' => 'Venta por medio de la pasarela - pruebas PTP',
    		'language' => 'ES',
    		'currency' => 'COP',
    		'totalAmount' => $total,
    		'taxAmount' => $total_impuesto,
    		'devolutionBase' => $total_base,
    		'tipAmount' => 0,
    		'payer' => null,
    		'buyer' => $this->getCompradorData(),
    		'shipping' => $this->getCompradorData(),
    		'ipAddress' => null,
    		'userAgent' => null,
    		'additionalData' => [],
    	];

    	$this->transaction_data_default = $data_transaction;
    }

    public function getCompradorData()
    {
    	return [
    		'document' => '1036956105',
    		'documentType' => 'CC',
    		'firstName' => 'Esneider',
    		'lastName' => 'Mejia Ciro',
    		'company' => 'Place To Pay',
    		'emailAddress' => 'esneider.m12@gmail.com',
    		'address' => 'Calle 88B #66...',
    		'city' => 'Medellin',
    		'province' => 'Antioquia',
    		'country' => 'CO',
    		'phone' => '6064973',
    		'mobile' => '3218074451'
    	];
    }
}
