<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Client;
use App\Models\TmpLineOrder;
use App\Models\Categorys;
use App\Models\Stock;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Tva;
use App\Models\ModePaiement;
use App\Models\Paiements;
use App\Models\Lineorder;
use App\Models\Order;
use App\Models\Info;
use App\Models\Reglements;
use App\Models\Facture;
use App\Models\Cheques;
use App\Models\User;
use DB;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Auth;
class AvoirController extends Controller
{
    public function index()
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page vente')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CountInfo          = DB::table('infos as f')->join('company as c','c.id','=','f.idcompany')->where('c.status','=','Active')->count();
        if($CountInfo == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page stock')
            ->with('body',"Parce qu'il n'y a pas de information");
        }

        $CountTva = DB::table('tva as t')
        ->join('company as c','c.id','=','t.idcompany')
        ->where('c.status','Active')
        ->count();
        if($CountTva == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page vente')
            ->with('body',"Parce qu'il n'y a pas tva");
        }

        $ModePaiement = DB::table('modepaiement as m')
        ->join('company as c','c.id','=','m.idcompany')
        ->where('c.status','Active')
        ->count();
        if($ModePaiement == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page vente')
            ->with('body',"Parce qu'il n'y a pas mode paiement");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();

        $Clients = DB::table('clients as cl')
                    ->join('company as c','c.id',"=","cl.idcompany")
                    ->where('cl.idcompany',$CompanyIsActive->id)
                    ->select("cl.nom","cl.prenom","cl.cin","cl.adresse","cl.ville","cl.phone","cl.plafonnier","c.title","cl.id")
                    ->get();


        $Product = DB::table('products as p')
        ->join('stock as s', 'p.id', '=', 's.idproduct')
        ->join('company as c', 'p.idcompany', '=', 'c.id')
        ->where('c.status', 'Active')
        ->where('p.name','!=','Solde de départ')
        ->groupBy('p.id')
        ->select('p.name')
        ->get();

        $Tva                    = DB::table('tva as t')
        ->join('company as c','c.id','=','t.idcompany')
        ->where('c.status','Active')
        ->first();

        $ModePaiement           = DB::table('modepaiement as m')
        ->join('company as c','c.id','=','m.idcompany')
        ->where('c.status','Active')
        ->select('m.id','m.name')
        ->get();


        $Cheques = DB::table('cheques as c')
        ->join('orders as o','o.id','=','c.idorder')
        ->join('company as co','co.id','=','o.idcompany')
        ->where('co.status','Active')
        ->select('c.*')
        ->get();
        $users = User::all();
        $idCompany  = Company::where('status','Active')->value('id');
        return view('Avoir.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('Clients'                 ,$Clients)
        ->with('Product'                 ,$Product)
        ->with('tva'                     ,$Tva->name)
        ->with('ModePaiement'            ,$ModePaiement);
    }
    public function GetDataTmpAvoirByClient(Request $request)
    {
        if ($request->ajax())
        {

            $data = DB::table('products as p')
            ->join('stock as s', 's.idproduct', '=', 'p.id')
            ->join('tmpavoir as t', 't.idproduct', '=', 'p.id')
            ->leftjoin('setting as se','se.id','=','t.idsetting')
            ->where('t.idcompany', $request->idcompany)
            ->where('t.idclient' , $request->idclient)
            ->where('t.iduser'   , Auth::user()->id)
            ->select('t.id', 't.qte','t.price',DB::raw('t.total + t.accessoire as total'), 'p.name', 'se.type','t.accessoire')
            ->groupBy('t.id')
            ->orderBy('t.id','desc')
            ->get();

            return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($row)
            {
                $encryptedId = Crypt::encrypt($row->id);

                $btn =  '<div class="action-btn d-flex">
                            <a href="#" class="text-light trash ms-2"  value="' . $row->id . '">
                                <i class="ti ti-trash-x fs-5 border rounded-2 bg-danger p-1" title="Supprimer le produit du panier"></i>
                            </a>
                        </div>';
                return $btn;
            })->rawColumns(['action'])->make(true);

        }
    }

    public function GetTotalByClientCompanyaVoir(Request $request)
    {
        $sumTotal = TmpLineOrder::where('iduser', Auth::user()->id)
        ->where('idcompany', $request->idcompany)
        ->where('idclient', $request->idclient)
        ->sum(DB::raw('total + accessoire'));

        $Tva    = Tva::first();
        $remove_character = preg_replace('/[^\d]/', '', $Tva->name);
        $Calcul_Tva = ($sumTotal * $remove_character) / 100;

        $Total_TTC = $Calcul_Tva + $sumTotal;

        $ModePaiement = ModePaiement::where('name','crédit')->select('id')->first();
        // extract plafonnier client
        $Client       = Client::where('id',$request->idclient)->select('nom','prenom','plafonnier')->first();
        // extract total credit by client
        $creditClient = Reglements::where('idclient',$request->idclient)->where('idmode',$ModePaiement->id)->sum('total');


        return response()->json([
            'sumTotal' => $sumTotal,
            'Calcul_Tva' => $Calcul_Tva,
            'TotalTTC'   =>$Total_TTC,
            'TotalCredit' => $creditClient,
            'Plafonnier' => $Client->plafonnier,
        ]);
    }


    public function GetOrderClient(Request $request)
    {
        if($request->ajax())
        {
            $IdsCredit  = DB::table('modepaiement as m')
            ->join('company as c','c.id','m.idcompany')
            ->where('m.name','crédit')
            ->pluck('m.id')
            ->implode(',');

            $idclient = $request->idclient;

            // Subquery 1
            $subQuery1 = DB::table('orders as o')
                ->join('clients as c', 'o.idclient', '=', 'c.id')
                ->join('company as co', 'o.idcompany', '=', 'co.id')
                ->join('users as u', 'o.iduser', '=', 'u.id')
                ->where('o.idclient', $idclient)
                ->select(
                    'o.id',
                    DB::raw('CONCAT(c.nom, " ", c.prenom) as client'),
                    'co.title',
                    'u.name as user',
                    DB::raw('DATE(o.created_at) as creer_le'),
                    DB::raw('IF(o.total = 0, CAST((SELECT SUM(total) FROM reglements WHERE idclient = ' . $idclient . ' AND status = "SD") AS CHAR), o.total) as montantOrder'),
                    DB::raw('0 as totalpaye'),'o.idfacture'
                );

            // Subquery 2
            $subQuery2 = DB::table('orders as o')
                ->join('reglements as r', 'o.id', '=', 'r.idorder')
                ->join('clients as c', 'o.idclient', '=', 'c.id')
                ->join('company as co', 'o.idcompany', '=', 'co.id')
                ->join('users as u', 'o.iduser', '=', 'u.id')
                ->whereNotIn('r.idmode', explode(',', $IdsCredit))
                ->where('r.idclient', $idclient)
                ->select(
                    'r.idorder as id',
                    DB::raw('CONCAT(c.nom, " ", c.prenom) as client'),
                    'co.title',
                    'u.name as user',
                    DB::raw('DATE(o.created_at) as creer_le'),
                    DB::raw('0 as montantOrder'),
                    DB::raw('SUM(r.total) as totalpaye'),'o.idfacture'
                )
                ->groupBy('r.idorder');

            // Combine subqueries
            $combinedQuery = $subQuery1->unionAll($subQuery2);

            // Final query
            $result = DB::table(DB::raw("({$combinedQuery->toSql()}) as t"))
                ->mergeBindings($combinedQuery)
                ->select(
                    'id','client','title','user','creer_le',DB::raw('SUM(montantOrder) as montantOrder'),DB::raw('SUM(totalpaye) as totalpaye'),
                    DB::raw('SUM(montantOrder - totalPaye) as reste'),'idfacture'
                )
                ->groupBy('id')
                ->orderBy('id','desc')
                ->get();
                return DataTables::of($result)
                ->addIndexColumn() // This adds the index column to DataTables
                ->rawColumns([]) // No raw columns to set
                ->make(true);

        }
    }

    public function checkClientHasOrder(Request $request)
    {
        $Check = DB::table('orders as o')
        ->join('clients as c','c.id','=','o.idclient')
        ->join('company as co','co.id','=','o.idcompany')
        ->where('o.total','!=',0)
        ->where('co.status','Active')
        ->count();
        if($Check >0)
        {
            return response()->json([
                'status'    => 200,
            ]);
        }
        else
        {
            return response()->json([
                'status'   =>442,
                'message'  => 'Ce client n\'a pas de vente'
            ]);
        }
    }
}
