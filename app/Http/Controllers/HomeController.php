<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use nusoap_client;
use Validator;
use App\transaction_status;

class HomeController extends Controller
{

    /**
    * Función constructora
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    public function inPago(Request $request)
    {
        return $this->prepareForm();
    }

    private function prepareForm()
    {
        $bancos = $this->bancos;
        $select_bancos = $this->prepararDataSelectBancos($bancos);
        $input_name = $this->prepareInputName();
        $tipo_cuenta = $this->prepararDataSelectTCuenta();
        $dni_type = $this->prepareInputDniAndType();
        $CityDepartament = $this->prepareInputCityDepartament();
        $email = $this->prepareInputEmail();
        $direccion = $this->prepareInputDireccion();
        $telefonos = $this->prepareInputPhones();

        return '
        <form id="form_pasarela">
            <input type="hidden" name="_token" value="'.csrf_token().'">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8" id="espacio_errores">
                    
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$select_bancos.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$input_name.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$tipo_cuenta.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$dni_type.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$CityDepartament.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$email.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$direccion.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    '.$telefonos.'
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                <hr>
                    <center><a class="btn btn-success btn-xm" name="submit_button" role="button">Continuar</a></center>
                </div>
                <div class="col-md-4"></div>
            </div>
        </form>
        ';
    }

    private function prepareInputName()
    {
        return '
        <div class="input-group">
            <span class="input-group-addon">Nombre(s) *</span>
            <input id="nombre" type="text" class="form-control" maxlength="60" name="nombre" required autofocus>
            <span class="input-group-addon">Apellido(s) *</span>
            <input id="apellido" type="text" class="form-control" maxlength="60" name="apellido" required>
        </div>';
    }

    private function prepareInputEmail()
    {
        return '
        <div class="input-group">
            <span class="input-group-addon">Correo electronico *</span>
            <input id="email" type="text" class="form-control" maxlength="80" name="email" required>
        </div>';
    }

    private function prepareInputDireccion()
    {
        return '
        <div class="input-group">
            <span class="input-group-addon">Dirección *</span>
            <input id="direccion" type="text" class="form-control" maxlength="100" name="direccion" required>

            <span class="input-group-addon">Compañia *</span>
            <input id="compania" type="text" class="form-control" maxlength="60" name="compania" required>
        </div>';
    }

    private function prepareInputPhones()
    {
        return '
        <div class="input-group">
            <span class="input-group-addon">Telefono fijo *</span>
            <input id="fijo" type="text" class="form-control" maxlength="30" name="fijo" required>

            <span class="input-group-addon">Celular *</span>
            <input id="celular" type="text" class="form-control" maxlength="30" name="celular" required>
        </div>';
    }

    private function prepareInputDniAndType()
    {
        return '
        <div class="input-group">
            <span class="input-group-addon">Tipo *</span>
            <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                <option value="" selected>Seleccione...</option>
                <option value="CC">C.C. (Cédula de ciudadania colombiana)</option>
                <option value="CE">C.E. (Cédula de extranjería)</option>
                <option value="TI">T.I. (Tarjeta de identidad)</option>
                <option value="PPN">P.P.N. (Pasaporte)</option>
            </select>
            <span class="input-group-addon" title="Numero de documento"># *</span>
            <input id="documento" name="documento" type="text" maxlength="12" title="Numero de documento" class="form-control" aria-label="...">
        </div>';
    }

    private function prepararDataSelectTCuenta()
    {
        return "
        <div class='input-group'>
            <span class='input-group-addon'>Tipo de cuenta *</span>
            <select class='form-control' name='tipo_cuenta' required id='tipo_cuenta'>
                <option selected value=''>Seleccione...</option>
                <option value='0'>Persona</option>
                <option value='1'>Empresa</option>
            </select>
        </div>";
    }

    private function prepareInputCityDepartament()
    {
        return '
        <div class="input-group">
            <span class="input-group-addon">Ciudad *</span>
            <input id="ciudad" type="text" maxlength="50" class="form-control" name="ciudad" required>

            <span class="input-group-addon">Departamento *</span>
            <input id="provincia" type="text" maxlength="50" class="form-control" name="provincia" required>
        </div>';
    }


    private function prepararDataSelectBancos()
    {
        $bancos = $this->bancos;
        $opciones = '';
        foreach ($bancos as $banco) {
            $cod_bank = $banco->bankCode;
            $nam_bank = $banco->bankName;
            $opciones .= "<option value='$cod_bank'>$nam_bank</option>;";
        }

        $select_banco = "
        <div class='input-group'>
            <span class='input-group-addon'>Banco *</span>
            <select class='form-control' name='banco' id='banco'>
                $opciones
            </select>
        </div>";

        return $select_banco;
    }

    public function postTransact($referencia)
    {
        $transaccion = transaction_status::where('id_transaleatorio','=',$referencia)->orderBy('created_at','DESC')->first();
        if ($transaccion) {
            return view('post')->with([
                '_transaccion' => $transaccion->id_transaction,
                '_session' => $transaccion->id_session,
                '_trazabilidad' => $transaccion->trazabilidad,
                '_url' => $transaccion->url,
            ]);
        } else {
            return redirect('/');
        }
    }

    public function pasarelaStart(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'tipo_documento' => 'required|max:3',
            'documento' => 'required|max:12',

            'nombre' => 'required|max:60',
            'apellido' => 'required|max:60',
            'email' => 'required|max:80|email',
            'direccion' => 'required|max:100',
            'ciudad' => 'required|max:50',
            'provincia' => 'required|max:50',
            'fijo' => 'required|max:30',
            'celular' => 'required|max:30',

            'compania' => 'required|max:60',

            'tipo_cuenta' => 'required|max:1',
            'banco' => 'required|max:60',
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()],500);
        }

        $data_default = $this->transaction_data_default;

        $data_default['bankCode'] = $request->banco;
        $data_default['bankInterface'] = $request->tipo_cuenta;
        $data_default['payer'] = [
            'document' => $request->documento,
            'documentType' => $request->tipo_documento,
            'firstName' => $request->nombre,
            'lastName' => $request->apellido,
            'company' => $request->compania,
            'emailAddress' => $request->email,
            'address' => $request->direccion,
            'city' => $request->ciudad,
            'province' => $request->provincia,
            'country' => 'CO',
            'phone' => $request->fijo,
            'mobile' => $request->celular
        ];
        $data_default['returnURL'] = url('/post_transact/'.$this->transaction_data_default['reference']);
        $data_default['ipAddress'] = $_SERVER['REMOTE_ADDR'];
        $data_default['userAgent'] = $this->getNavegador();
        $this->transaction_data_default = $data_default;
        $auth = $this->getAuthData();
        $data = array_merge($auth, ['transaction' => $this->transaction_data_default]);
        $result = $this->cliente->call('createTransaction',$data);

        $transaction = new transaction_status();

        $transaction->id_transaction = $result['createTransactionResult']['transactionID'];
        $transaction->id_session = $result['createTransactionResult']['sessionID'];
        $transaction->trazabilidad = $result['createTransactionResult']['trazabilityCode'];
        $transaction->ip = $_SERVER['REMOTE_ADDR'];
        $transaction->url = $result['createTransactionResult']['bankURL'];
        $transaction->id_transaleatorio = $this->transaction_data_default['reference'];

        $transaction->save();

        return response()->json(['url' => $result['createTransactionResult']['bankURL']],200);
        
    }

    private function getNavegador()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(strpos($user_agent, 'MSIE') !== FALSE) {
            return 'Internet explorer';
        } elseif (strpos($user_agent, 'Edge') !== FALSE) {
            return 'Microsoft Edge';
        } elseif (strpos($user_agent, 'Trident') !== FALSE) {
            return 'Internet explorer';
        } elseif(strpos($user_agent, 'Opera Mini') !== FALSE) {
            return "Opera Mini";
        } elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE) {
            return "Opera";
        } elseif(strpos($user_agent, 'Firefox') !== FALSE) {
            return 'Mozilla Firefox';
        } elseif(strpos($user_agent, 'Chrome') !== FALSE) {
            return 'Google Chrome';
        } elseif(strpos($user_agent, 'Safari') !== FALSE) {
            return "Safari";
        } else{
            return 'No hemos podido detectar su navegador';
        }
    }

    public function pasarelaStatusTransaction(Request $request)
    {
        $transaction_id = $request->transaction_id;
        $auth = $this->getAuthData();
        $data = array_merge($auth, ['transactionID' => $transaction_id]);
        $result = $this->cliente->call('getTransactionInformation',$data);
        return response()->json(['status' => $result['getTransactionInformationResult']['responseReasonText']],200);
    }
}
