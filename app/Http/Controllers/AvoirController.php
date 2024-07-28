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
use App\Models\Tmplineavoir;

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
                    ->join('orders as o','o.idclient','=','cl.id')
                    ->where('cl.idcompany',$CompanyIsActive->id)
                    ->where('o.total','>',0)
                    ->select(DB::raw('concat(cl.nom," ",cl.prenom) as client'),"cl.cin","cl.adresse","cl.ville","cl.phone","cl.plafonnier","c.title","cl.id")
                    ->groupBy('cl.id')
                    ->orderBy('cl.nom','asc')
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
            $orders = DB::table('orders as o')
                ->select(
                    'o.id',
                    DB::raw('o.total as totalvente'),
                    DB::raw('0 as totalpaye'),
                    DB::raw('CONCAT(c.nom, " ", c.prenom) as client'),
                    'u.name as user', // Alias u.name as user
                    'co.title',
                    'o.idfacture',
                    DB::raw('DATE_FORMAT(o.created_at, "%Y-%m-%d") as created_at_formatted')
                )
                ->join('clients as c', 'o.idclient', '=', 'c.id')
                ->join('users as u', 'o.iduser', '=', 'u.id')
                ->join('company as co', 'o.idcompany', '=', 'co.id')
                ->where('co.status', 'Active')
                ->where('o.total', '>', 0)
                ->where('o.idclient', $idclient)
                ->groupBy('o.id');

            // Second query (payments with totalpaye)
            $payments = DB::table('orders as o')
                ->select(
                    'o.id',
                    DB::raw('0 as totalvente'),
                    DB::raw('SUM(p.total) as totalpaye'),
                    DB::raw('CONCAT(c.nom, " ", c.prenom) as client'),
                    'u.name as user', // Alias u.name as user
                    'co.title',
                    'o.idfacture',
                    DB::raw('DATE_FORMAT(o.created_at, "%Y-%m-%d") as created_at_formatted')
                )
                ->join('clients as c', 'o.idclient', '=', 'c.id')
                ->join('users as u', 'o.iduser', '=', 'u.id')
                ->join('company as co', 'o.idcompany', '=', 'co.id')
                ->join('reglements as r', 'o.id', '=', 'r.idorder')
                ->join('paiements as p', 'r.id', '=', 'p.idreglement')
                ->where('co.status', 'Active')
                ->where('o.total', '>', 0)
                ->where('o.idclient', $idclient)
                ->groupBy('r.idorder');

            // Combine both queries using unionAll
            $combined = $orders->unionAll($payments);

            // Wrap the unioned query in a subquery and perform the final grouping
            $results = DB::table(DB::raw("({$combined->toSql()}) as t"))
                ->mergeBindings($combined) // Merge bindings to avoid SQL injection
                ->select(
                    'id',
                    DB::raw('SUM(totalvente) as totalvente'),
                    DB::raw('SUM(totalpaye) as totalpaye'),
                    DB::raw('SUM(totalvente - totalpaye) as reste'),
                    'client',
                    'user', // Select user
                    'title',
                    'idfacture',
                    'created_at_formatted'
                )
                ->groupBy('id')
                ->orderBy('id', 'desc')
                ->get();

                return DataTables::of($results)
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

    public function GetProductByOrderClient(Request $request)
    {

        $Products = DB::select('SELECT o.idcompany as idcompany,o.idclient,l.id as idline,b.numero_bon,IF(idsetting is not null, round(l.qte / s.convert),l.qte )  as qte_convert,l.price,(l.total + l.accessoire) as total,l.accessoire, p.name,p.id as idproduct,s.type,s.convert,l.idsetting,st.id as idstock,IF(idsetting is not null, round(l.qte / s.convert),l.qte )  as qte,o.id as idorder
            FROM lineorder l
            LEFT JOIN setting s ON l.idsetting = s.id  -- Add this LEFT JOIN
            JOIN stock st ON l.idstock = st.id
            JOIN bonentres b ON st.idbonentre = b.id
            JOIN products p ON l.idproduct = p.id
            JOIN orders o ON l.idorder = o.id

            WHERE l.idorder = ?
            GROUP BY l.id',[$request->idorder]);

        return DataTables::of($Products)
            ->addIndexColumn() // This adds the index column to DataTables
            ->rawColumns([]) // No raw columns to set
            ->make(true);
    }

    public function StoreTmpAvoir(Request $request)
    {

        $check_product_has_table_tmp = DB::table('tmpavoir')
        ->where('idproduct' ,$request->idproduct)
        ->where('idclient'  ,$request->idclient)
        ->where('idsetting' ,$request->idsetting)
        ->where('idstock'   ,$request->idstock)
        ->count();
        if($check_product_has_table_tmp !=0)
        {
            return response()->json([
                'status'   => 422,
                'message'  => 'Le produit est déjà dans le tableau',
            ]);
        }
        else
        {
            $data            = $request->all();
            $data['iduser']  = Auth::user()->id;

            $TmpLineOrder = Tmplineavoir::create($data);
            if($TmpLineOrder)
            {
                return response()->json([
                    'status'        => 200,
                    'message'       => 'Le Produit créer avec succès',
                    'Data'          => $TmpLineOrder,
                ]);
            }
            else
            {
                return response()->json([
                    'status'        => 400,
                    'message'       => 'Veuillez contacter le support'
                ]);
            }
        }

    }

    public function DisplayProductsTableTmpAvoir(Request $request)
    {
        if($request->ajax())
        {
            $data = DB::table('products as p')
                ->join('stock as s', 's.idproduct', '=', 'p.id')
                ->join('tmpavoir as t', 't.idproduct', '=', 'p.id')
                ->leftjoin('setting as se','se.id','=','t.idsetting')
                ->where('t.idcompany', $request->idcompany)
                ->where('t.idclient' , $request->idclient)
                ->where('t.iduser'   , Auth::user()->id)
                ->select('t.id', 't.qte','t.price',DB::raw('t.total + t.accessoire as total'), 'p.name', 'se.type','t.accessoire','se.convert','t.idorder','t.idproduct')
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

    public function CheckQteChangeNotSuperQteOrderAndUpdateQte(Request $request)
    {

        $convert        = $request->convert;
        $Qte_Order      = $request->qte;
        $Qte_Enter      = $request->newValue;
        $IdProduct      = $request->idproduct;
        // extract qte vendus par product
        $Qte_LineOrder = DB::table('orders as o')
        ->join('lineorder as l', 'l.idorder', '=', 'o.id')
        ->leftJoin('setting as s', 's.id', '=', 'l.idsetting')
        ->where('o.id', $request->idorder)
        ->where('l.idproduct', $request->idproduct)
        ->where('l.price', $request->price)
        ->value(DB::raw('IF(l.idsetting IS NOT NULL, round(l.qte / s.convert), l.qte) as Qte_LineOrder'));



        if($Qte_Enter > $Qte_LineOrder)
        {
            return response()->json([
                'status'        => 422,
                'message'       => 'La quantité vendue est supérieure à la quantité disponible à l\'échange',
            ]);
        }
        else
        {
            if(!is_null($convert))
            {
                // convert QTE VENDUE to KG
                $Qte_Vendue_KG = $convert * $request->qte;

                // Convert QTE Changé to KG
                $QTE_Change    = $convert * $Qte_Enter;

                // update tmpAvoir
                $UpdateTmpAvoir = Tmplineavoir::where('id',$request->id)
                ->update([
                    'qte'         => $Qte_Enter,
                    'total'       => $Qte_Enter * $request->price,
                ]);

                return response()->json([
                    'status'        => 200,
                ]);

                // extract id ligneorder



                // update linetmpavoir qte with Qte_enter  and get total tmp this achange
                // 1 - update lineorder product qte with qte change where idorder and idproduct
                // 2 - update total order
                // 3 - update total reglement
                // 4 - update total paiement if not credit

            }
            else
            {

            }
        }

    }

    public function TotalTmpAvoir(Request $request)
    {
        $Total_HT  = DB::table('tmpavoir')
        ->where('idclient', $request->idclient)
        ->sum('total');

        $TVA  = DB::table('tva as t')
            ->join('company as c', 'c.id', '=', 't.idcompany')
            ->where('c.status', 'Active')
            ->value(DB::raw('CAST(REGEXP_REPLACE(name, "[^0-9]", "") AS UNSIGNED)'));

        $Total_TTC = $Total_HT + ($Total_HT * $TVA / 100);
        $Total_TVA = $Total_HT * $TVA;

        return response()->json([
            'status'   => 200,
            'Total_HT' => floatval($Total_HT),
            'TVA'      => floatval($Total_TVA),
            'Total_TTC'=> floatval($Total_TTC),
        ]);

    }
}
