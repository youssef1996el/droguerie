<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use DataTables;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class ClientController extends Controller
{
    public function index()
    {
        // check Company is create
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page client')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('Client.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function getFicheClient(Request $request)
    {
        if ($request->ajax())
        {
            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
            $clients = DB::table('clients as cl')
                        ->join('company as c','c.id',"=","cl.idcompany")
                        ->where('cl.idcompany',$CompanyIsActive->id)
                        ->select("cl.nom","cl.prenom","cl.cin","cl.adresse","cl.ville","cl.phone","cl.plafonnier","c.title","cl.id")
                        ->get();


            return DataTables::of($clients)->addIndexColumn()->addColumn('action', function ($row)
            {
                $encryptedId = Crypt::encrypt($row->id);

                $btn = '<div class="action-btn d-flex">';

                if (auth()->user()->can('clients-voir')) {
                    $btn .= '<a href="' . url("ShowClient/$encryptedId") . '" class="text-light view" value="' . $row->id . '">
                                <i class="ti ti-eye fs-5 border rounded-2 bg-info p-1" title="Voir le client"></i>
                            </a>';
                }

                if (auth()->user()->can('clients-modifier')) {
                    $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier le client"></i>
                            </a>';
                }
                if (auth()->user()->can('clients-supprimer')) {
                    $btn .= '<a href="#" class="text-light trash ms-2" value="' . $row->id . '">
                                <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer le client"></i>
                            </a>';

                }
                $btn .='</div>';


                return $btn;
            })->rawColumns(['action'])->make(true);
        }
    }

    public function StoreClient(Request $request)
    {
        $validator=validator::make($request->all(),[
            'nom'                     =>'required',
            'prenom'                  =>'required',
            'phone'                   =>'required',
        ]);
         // Override default error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
        ];

        $validator->setCustomMessages($customMessages);
        if($validator->fails())
        {
            return response()->json([
                'status'    =>422,
                'errors'    =>$validator->messages(),
            ]);
        }
        else
        {

            // Sanitize inputs if necessary
            $data = $request->all();

            $data = array_map('trim', $data);
            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
            $data['idcompany']     = $CompanyIsActive->id;
            $data['iduser']        = Auth::user()->id;

            $Client = Client::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Client créée avec succès',
            ]);
        }
    }

    public function UpdateClient(Request $request)
    {
        $validator=validator::make($request->all(),[
            'nom'                     =>'required',
            'prenom'                  =>'required',
            'phone'                   =>'required',
        ]);
         // Override default error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
        ];

        $validator->setCustomMessages($customMessages);
        if($validator->fails())
        {
            return response()->json([
                'status'    =>422,
                'errors'    =>$validator->messages(),
            ]);
        }
        else
        {

            // Sanitize inputs if necessary
            $data = $request->all();

            $data = array_map('trim', $data);
            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
            $data['idcompany']     = $CompanyIsActive->id;
            $data['iduser']        = Auth::user()->id;

            $Client = Client::where('id',$data['id'])->update([
                'nom'         => $data['nom'],
                'prenom'      => $data['prenom'],
                'cin'         => $data['cin'],
                'adresse'     => $data['adresse'],
                'ville'       => $data['ville'],
                'plafonnier'  => $data['plafonnier'],
                'phone'       => $data['phone'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Client modifier avec succès',
            ]);
        }
    }

    public function ShowClient($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        $Client                = Client::where('id',$id)->first();
        // order client
        $orders = Order::select(
            'orders.id',DB::raw('orders.total AS totalvente'),
            DB::raw('SUM(reglements.total) AS totalpaye'),DB::raw('(orders.total - SUM(reglements.total)) AS reste'),
            DB::raw('CONCAT(clients.nom, " ", clients.prenom) AS client'),'company.title AS company','users.name AS user','orders.idfacture',
            DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") as created_at_formatted')

        )->join('reglements', 'reglements.idorder', '=', 'orders.id')
        ->join('paiements', 'paiements.idreglement', '=', 'reglements.id')
        ->join('modepaiement', 'modepaiement.id', '=', 'paiements.idmode')
        ->join('clients', 'clients.id', '=', 'orders.idclient')
        ->join('company', 'company.id', '=', 'orders.idcompany')
        ->join('users', 'users.id', '=', 'orders.iduser')
        ->leftJoin('factures', 'factures.id', '=', 'orders.idfacture')
        ->where('company.status','=','Active')->where('orders.idclient','=',$id)->groupBy('orders.id')->get();
        // remarque client
        $remarks = DB::select("select remark from remark where idclient = ?",[$id]);

        // Initialize the variable
        $remark = null;

        // Check if the result is not empty and assign the first remark
        if (!empty($remarks) && isset($remarks[0]->remark)) {
            $remark = $remarks[0]->remark;
        }

        // Now $remark contains the first remark or null if no remark was found

        return view('Client.view')
        ->with('id', $id)
        ->with('idclient' ,  Crypt::decrypt($encryptedId))
        ->with('Client', $Client)
        ->with('orders', $orders)
        ->with('remark', $remark)
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }
    public function StoreRemark(Request $request)
    {
        $validator=validator::make($request->all(),[
            'remark'                  =>'required',
        ]);
        // Override default error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
        ];
        $validator->setAttributeNames([
            'remark' => 'remarque'
        ]);
        $validator->setCustomMessages($customMessages);
        if($validator->fails())
        {
            return response()->json([
                'status'    =>422,
                'errors'    =>$validator->messages(),
            ]);
        }
        else
        {
            $data = $request->all();

            // check table remark
            $check = DB::table('remark')->where('idclient', $data['idclient'])->count();
            if($check == 0)
            {
                DB::table('remark')->insert([
                    'remark'    =>  $data['remark'],
                    'idclient'  =>  $data['idclient']
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Remarque ajoute avec succès',
                ]);
            }
            else
            {
                DB::table('remark')->where('idclient', $data['idclient'])->update([
                    'remark'    =>  $data['remark'],
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Remarque modifier avec succès',
                ]);
            }

        }

    }
    public function TrashClient(Request $request)
    {
        // check client is has order
        $check = DB::table('orders as o')
        ->join('clients as c', 'c.id','=','o.idclient')
        ->where('c.id',$request->id)
        ->count();
        if($check != 0)
        {
            return response()->json([
                'status'  => 442,
            ]);
        }
        else
        {
            // delete client
            $DeleteClient = Client::where('id',$request->id)->delete();
            return response()->json([
                'status'  => 200,
            ]);
        }
    }

}
