<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Categorys;
use App\Models\Product;
use App\Models\BonEntre;
use App\Models\Stock;
use App\Models\Lineorder;
use App\Models\Reglements;
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
            /* 'phone'                   =>'required', */
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
            /* 'phone'                   =>'required', */
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
        
        $IdCredit = DB::table('modepaiement as m')
                ->join('company as c','c.id','=','m.idcompany')
                ->where('c.status','Active')
                ->where('m.name','crédit')
                ->select('m.id')
                ->first();
                
        $orders = DB::select('select id, client,  IF(totalvente = 0, "Solde de départ", concat(totalvente," ","DH")) AS totalvente,
                                IF(totalvente = 0 , (select sum(total) from reglements where idclient = ? and idmode !=?  ) , sum(totalpaye) ) as totalpaye ,
                                IF(totalvente = 0 , (select sum(total) from reglements where idclient = ? and idmode = ?  ) , totalvente - sum(totalpaye) ) as reste ,
                                IF(idfacture IS NULL, "Bon", "Facture") AS type ,title,name,created_at

                                from (

                                    select o.id,concat(c.nom ," ",c.prenom) as client,o.total as totalvente,0 as totalpaye, o.idfacture,co.title,u.name, DATE_FORMAT(o.created_at, "%Y-%m-%d") AS created_at
                                    from clients c,orders o  ,company co ,users u

                                    where c.id = o.idclient and c.idcompany = co.id and c.id = ? and c.iduser = u.id and co.status = "Active"
                                    group by o.id

                                union all

                                    select r.idorder,concat(c.nom ," ",c.prenom) as client,0 as totalvente ,sum(r.total) as totalpaye , o.idfacture ,co.title,u.name,DATE_FORMAT(o.created_at, "%Y-%m-%d") AS created_at
                                    from reglements r, orders o  , clients c , company co , users u
                                    where r.idorder = o.id and c.id = r.idclient and c.idcompany = co.id and c.iduser = u.id and co.status = "Active" and r.idclient = ?
                                    group by r.idorder) as t group by id;
                            ',[$id,$IdCredit->id ,$id,$IdCredit->id   ,$id,$id]);

                            

        $has_Solde = false;
        $Solde_Client = Reglements::where('idclient',$id)->count();
        
        if($Solde_Client > 0)
        {
            $has_Solde = true;
        }
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
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('has_Solde'               ,$has_Solde);
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

    public function StoreSolde(Request $request)
    {

        $name_product = "Solde de départ";

        $CompanyActive = Company::where('status','Active')->first();

        // create category
        $check_category = Categorys::where('name',$name_product)->count();
        $Category = null;
        if($check_category == 0)
        {
            $Category = Categorys::create([
                'name'          => $name_product,
                'idcompany'     => $CompanyActive->id,
                'iduser'        => Auth::user()->id,
            ]);
        }
        else
        {
            $Category = Categorys::where('name', $name_product)->where('idcompany' , $CompanyActive->id,)->first();
        }

        $product = null;
        $check_product      = Product::where('name',$name_product)->count();
        
        if($check_product == 0)
        {
            // create product
            $product  = Product::create([
                'name'          => $name_product,
                'idcompany'     => $CompanyActive->id,
                'idcategory'    => $Category->id,
                'iduser'        => Auth::user()->id,
            ]);
        }
        else
        {
            // create product
            $product  = Product::where('name', $name_product)->first();
            
        }
        $BonEntre = null;
        $check_BonEntre = BonEntre::where('numero_bon','Bon-Solde-Depart')->where('idcompany',$CompanyActive->id)->count();
        if($check_BonEntre == 0)
        {
            // create bon entre
            $BonEntre  = BonEntre::create([
                'numero_bon'      => 'Bon-Solde-Depart',
                'date'            => Carbon::now()->format('Y-m-d'),
                'numero'          => 'Bon-Solde-Depart-01',
                'commercial'      => null,
                'matricule'       => null,
                'chauffeur'       => null,
                'cin'             => null,
                'idcompany'       => $CompanyActive->id,
                'iduser'          => Auth::user()->id,
            ]);
        }
        else
        {
            $BonEntre  = BonEntre::where('numero_bon','Bon-Solde-Depart')->where('idcompany',$CompanyActive->id)->first();
        }
        $Stock = null;
        $check_Stock = Stock::where('idbonentre',$BonEntre->id)->where('idcompany',$CompanyActive->id)->count();
       
        if($check_Stock == 0)
        {
            // create stock
            $Stock      = Stock::create([
                'qte'               => 0,
                'qte_comapny'       => 0,
                'qte_notification'  => 0,
                'price'             => 0,
                'status'            => 'waiting',
                'idproduct'         => $product->id,
                'idcompany'         => $CompanyActive->id,
                'iduser'            => Auth::user()->id,
                'idbonentre'        => $BonEntre->id,
            ]);
        }
        else
        {
            $Stock      = Stock::where('idbonentre',$BonEntre->id)->where('idcompany',$CompanyActive->id)->first();
        }

        // create order
        $Order   = Order::create([
            'total'     => /* $request->montant */ 0.00,
            'idfacture' => null,
            'idcompany' => $CompanyActive->id,
            'idclient'  => $request->id,
            'iduser'    => Auth::user()->id,
        ]);

        // create line order
        $LineOrder = Lineorder::create([
            'qte'       => 0,
            'price'     => $request->montant,
            'total'     => $request->montant,
            'accessoire'=> 0.00,
            'idsetting' => null,
            'idstock'   => $Stock->id,
            'idproduct' => $product->id,
            'idorder'   => $Order->id,
        ]);

        // extract id mode paiement credit by company
        $ModePaiementCredit = DB::table('modepaiement as m')
        ->join('company as c', 'c.id','=','m.idcompany')
        ->where('c.status','Active')
        ->where('m.name','crédit')
        ->select('m.id')
        ->groupBy('m.name')
        ->first();

        // create reglement
        $Reglement = Reglements::create([
            'total'         => $request->montant,
            'datepaiement'  => null,
            'idclient'      => $request->id,
            'idorder'       => $Order->id,
            'idmode'        => $ModePaiementCredit->id,
            'idcompany'     => $CompanyActive->id,
            'iduser'        => Auth::user()->id,
            'status'        => 'SD',
        ]);

        return response()->json([
            'status'        => 200,
        ]);



    }

}
