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
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Notifications\StockNotification;
use App\Notifications\ChequeNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Mpdf\Mpdf;
use ArPHP\I18N\Arabic;
class OrderController extends Controller
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
        foreach ($Cheques as $item)
        {
            $date_promise = Carbon::parse($item->datepromise);
            $date_Today   = Carbon::Today();
            if($date_Today >= $date_promise)
            {
                $existingNotification = DB::table('notifications')
                ->whereJsonContains('data->id', $item->numero)
                ->exists();
                if (!$existingNotification)
                {
                    Notification::send($users, new ChequeNotification($item->numero, $item->datecheque,$item->datepromise,
                    $item->montant, $item->name , $item->bank,$idCompany));
                }

            }
        }


        return view('Order.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('Clients'                 ,$Clients)
        ->with('Product'                 ,$Product)
        ->with('tva'                     ,$Tva->name)
        ->with('ModePaiement'            ,$ModePaiement);

    }



    public function DisplayProductStock(Request $request)
    {
        if($request->ajax())
        {



            $CompanyIsActive = Company::where('status', 'Active')->select('id')->first();
            // check product has setting
            $hasSetting = Setting::where('name_product','=',$request->prododuct)->count();
            if($hasSetting > 0)
            {
                $query = DB::table('products as p')
                ->join('stock as s'             , 'p.id'            , '='   , 's.idproduct')
                ->join('categorys as ct'        , 'ct.id'           , '='   , 'p.idcategory')
                ->join('bonentres as b'         , 'b.id'            , '='   , 's.idbonentre')
                ->join('company as co'          , 'p.idcompany'     , '='   , 'co.id')
                ->leftJoin('tmplineorder as t'  , 't.idproduct'     , '='   , 'p.id')
                ->leftJoin('setting as  set'     , 'set.id'           , '='   , 't.idsetting')
                ->where('co.status', 'Active')
                  /* ->where('s.qte','>',0) */
                ->where(DB::raw('(s.qte) - (t.qte * set.convert)') , '>' ,1)
                ->where('p.name', 'like', "%{$request->product}%");

            }
            else
            {
                $query = DB::table('products as p')
                ->join('stock as s'             , 'p.id'            , '='   , 's.idproduct')
                ->join('categorys as ct'        , 'ct.id'           , '='   , 'p.idcategory')
                ->join('bonentres as b'         , 'b.id'            , '='   , 's.idbonentre')
                ->join('company as co'          , 'p.idcompany'     , '='   , 'co.id')
                ->leftJoin('tmplineorder as t'  , 't.idproduct'     , '='   , 'p.id')
                ->leftJoin('setting as  set'     , 'set.id'           , '='   , 't.idsetting')
                ->where('co.status', 'Active')
                  ->where('s.qte','>',0)
                /* ->where(DB::raw('(s.qte) - (t.qte * set.convert)') , '>' ,1) */
                ->where('p.name', 'like', "%{$request->product}%");
            }





            $sumQteSubquery = DB::table('tmplineorder as t')
                ->leftjoin('setting as s','s.id','=','t.idsetting')
                ->select('t.idproduct', DB::raw('SUM(t.qte * s.convert) as sum_qte') ,DB::raw('SUM(t.qte) as sum_qte_without_unite'),'t.idsetting')
                ->groupBy('t.idproduct');

            $query->leftJoinSub($sumQteSubquery, 't_sum', function ($join) {
                $join->on('p.id', '=', 't_sum.idproduct');
            });

            // Add the necessary selects based on the type
            if (is_null($request->type) || $request->type === "0")
            {

                $query->addSelect(
                    'p.id', 'p.name', 'co.title', 's.price', 'p.created_at','s.id as idstock','b.numero_bon','ct.id as idcategory',


                    DB::raw('s.qte - IFNULL(t_sum.sum_qte_without_unite, 0) as qte') // Adjust stock quantity
                );

            } else {
                $query->join('categorys as c', 'p.idcategory', '=', 'c.id')
                    ->join('setting as se', 'c.id', '=', 'se.idcategory')
                    ->addSelect(
                        'p.id', 'p.name', 'co.title', DB::raw('FORMAT((s.price * se.convert), 2) as price'), 'p.created_at', 'se.convert', 'se.type',
                        's.id as idstock','b.numero_bon','c.id as idcategory',
                        DB::raw('CONCAT(ROUND(((s.qte - IFNULL(t_sum.sum_qte, 0)) / se.convert), 1), " ", se.type) as qte')
                    )
                    ->where('se.id', $request->type);
            }

            $query->groupBy('s.id');



            $products = $query->get();

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                })
                ->rawColumns(['action'])
                ->make(true);

        }
    }

    public function sendDataToTmpOrder(Request $request)
    {
        // function send to table tmp
        if ($request->ajax()) {
            $idUser = Auth::user()->id;
            $idClient = $request->idclient;
            $idProduct = $request->idproduct;
            $idCompany = $request->idcompany;
            $idSetting = $request->typeVente;
            $idStock   = $request->idstock;

            $check_Product_In_TmpLineOrder = TmpLineOrder::where([
                'idclient'          => $idClient,
                'idproduct'         => $idProduct,
                'iduser'            => $idUser,
                'idcompany'         => $idCompany,
                'idsetting'         => $idSetting,
                'idstock'           => $idStock,
            ])->count();

            $data_Product = Stock::where([
                'idproduct' => $idProduct,
                'id'        => $idStock,
                'idcompany' => $idCompany,
            ])->first();

            if ($idSetting) {
                $setting = Setting::where('id', $idSetting)->first();
                $price = $setting->convert * $data_Product->price;
            } else {
                $price = $data_Product->price;
            }

            if ($check_Product_In_TmpLineOrder == 0)
            {
                TmpLineOrder::create([
                    'qte' => 1,
                    'price' => $price,
                    'total' => $price,
                    'idproduct' => $idProduct,
                    'idclient' => $idClient,
                    'iduser' => $idUser,
                    'idsetting' => $idSetting,
                    'idcompany' => $idCompany,
                    'idstock'   => $request->idstock,
                ]);
            }
            else
            {
                $Old_Data = TmpLineOrder::where([
                    'idproduct' => $idProduct,
                    'iduser' => $idUser,
                    'idcompany' => $idCompany,
                    'idclient' => $idClient,
                    'idsetting' => $idSetting,
                    'idstock'   => $request->idstock,
                ])->first();

                TmpLineOrder::where([
                    'idproduct' => $idProduct,
                    'iduser' => $idUser,
                    'idcompany' => $idCompany,
                    'idclient' => $idClient,
                    'idsetting' => $idSetting,
                    'idstock'   => $request->idstock,
                ])->update([
                    'qte' => $Old_Data->qte + 1,
                    'total' => ($Old_Data->qte + 1) * $price,
                ]);
            }
        }


        return response()->json([
            'status'    =>200,
        ]);












        if($request->ajax())
        {
            if(is_null($request->typeVente))
            {
                $check_Product_In_TmpLineOrder = TmpLineOrder::where('idclient',$request->idclient)
                ->where('idproduct',$request->idproduct)
                ->where('iduser',Auth::user()->id)
                ->where('idcompany',$request->idcompany)
                ->count();
            }
            else
            {

                $check_Product_In_TmpLineOrder = TmpLineOrder::where('idclient',$request->idclient)
                                                            ->where('idproduct',$request->idproduct)
                                                            ->where('iduser',Auth::user()->id)
                                                            ->where('idcompany',$request->idcompany)
                                                            ->where('idsetting',$request->typeVente)
                                                            ->count();
            }


            // extract data product with company
            $data_Product = Stock::where('idproduct',$request->idproduct)
                                    ->where('idcompany',$request->idcompany)->first();

            if($check_Product_In_TmpLineOrder == 0)
            {   $setting = null;
                if (!is_null($request->typeVente))
                {
                    $setting = Setting::where('id', $request->typeVente)->first();
                }


                // Create row in TmpLineOrder table
                $TmpLineOrder = TmpLineOrder::create([
                    'qte'       => 1,
                    'price'     => $request->typeVente == null ? $data_Product->price : ($setting->convert * $data_Product->price),
                    'total'     => $request->typeVente == null ? $data_Product->price *  1 : ($setting->convert * $data_Product->price),
                    'idproduct' => $request->idproduct,
                    'idclient'  => $request->idclient,
                    'iduser'    => Auth::user()->id,
                    'idsetting' => $request->typeVente == null ? null : $request->typeVente,
                    'idcompany' => $request->idcompany,
                ]);

            }
            else
            {

                // extract qte old product déja existe in table
                $data_product_old = TmpLineOrder::where('idproduct'     ,$request->idproduct)
                                                    ->where('iduser'    ,Auth::user()->id)
                                                    ->where('idcompany' ,$request->idcompany)
                                                    ->where('idclient'  ,$request->idclient)
                                                    ->first();
                if (is_null($request->typeVente))
                {

                    $TmpLineOrder = TmpLineOrder::where('idproduct'     ,$request->idproduct)
                                                ->where('iduser'    ,Auth::user()->id)
                                                ->where('idcompany' ,$request->idcompany)
                                                ->where('idclient'  ,$request->idclient)
                                                ->update([
                                                    'qte'           => isset($request->qte) ? $request->qte                              : $data_product_old->qte + 1,
                                                    'total'         => isset($request->qte) ? ($request->qte * $data_product_old->price) : ($data_product_old->qte +1) * ($data_product_old->price),
                                                ]);
                }
                else
                {

                    $setting = Setting::where('id', $request->typeVente)->first();
                    $qte = $setting->convert;

                    $TmpLineOrder = TmpLineOrder::where('idproduct'     ,$request->idproduct)
                                                ->where('iduser'        ,Auth::user()->id)
                                                ->where('idcompany'     ,$request->idcompany)
                                                ->where('idclient'      ,$request->idclient)
                                                ->where('idsetting',$request->typeVente)
                                                ->update([
                                                    'qte'           => isset($request->qte) ? $request->qte                              : $data_product_old->qte + 1 ,
                                                    'total'         => isset($request->qte) ? ($request->qte * $data_product_old->price) : ($data_product_old->qte +1) * ($data_product_old->price)
                                                ]);
                }

            }
            return response()->json([
                'status'    =>200,
            ]);



        }
    }

    public function GetDataTmpOrderByClient(Request $request)
    {
        if ($request->ajax())
        {

            $data = DB::table('products as p')
            ->join('stock as s', 's.idproduct', '=', 'p.id')
            ->join('tmplineorder as t', 't.idproduct', '=', 'p.id')
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

    public function GetTotalByClientCompany(Request $request)
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

    public function CheckQteProduct(Request $request)
    {
        // function check qte
        $idsetting  = $request->type;
        $idproduct  = $request->idproduct;
        $idclient   = $request->idclient;

        $name_product = Product::where('id',$idproduct)->value('name');
        $Setting      = Setting::where('name_product',$name_product)->get();

        if($idsetting)
        {


            // check product has parameters
            /* $check_Product_has_parameters = Setting::where('name_product',$name_product)
            ->where('idstock',$request->idstock)
            ->count();

            if($check_Product_has_parameters == 0)
            {
                return response()->json([
                    'status'    => '550',
                    'message'   => 'Le produit n\'a aucun paramètre',
                ]);
            } */
            $Qte_Stock = Stock::where('idproduct',$idproduct)->where('id',$request->idstock)->value('qte');
          /*   dd($Qte_Stock); */
            $Qte_Stock = floatval(str_replace(',', '.', $Qte_Stock));

            $checkPorductInTableTmp = TmpLineOrder::where(['idclient' => $idclient , 'idproduct' => $idproduct ])->count();

            if($checkPorductInTableTmp == 0)
            {
                $value_array = [];
                //dd($Setting);

                foreach($Setting as $item)
                {

                    $value_array[$item->id] = round($Qte_Stock / $item->convert ,2);
                }
                $TmpLineOrder = TmpLineOrder::where(['idclient' => $idclient , 'idproduct' => $idproduct ])->get();
                if($TmpLineOrder->isEmpty())
                {
                    return response()->json(['status' => 200]);
                }
                $value_qte_tmp = [];

                foreach($TmpLineOrder as $item)
                {
                    $value_qte_tmp[$item->idsetting] = $item->qte;
                }

                foreach ($value_array as $idSetting => $value1)
                {
                    foreach($value_qte_tmp as $idSettingTmp => $value2)
                    {

                        if($idSettingTmp == $idsetting && $idSetting == $idsetting)
                        {
                            $value2 +=1;
                            if($value2 > $value1)
                            {
                                return response()->json([
                                    'status' => 422,
                                    'message' => 'La quantité maximale de produit est: ' . round($value1, 2),
                                ]);
                            }
                            else
                            {
                                return response()->json(['status' => 200]);
                            }

                        }

                    }

                }
            }
            else
            {

                $DataTmp = DB::table('tmplineorder as t')
                ->select('t.id', 't.qte', 's.type', 's.convert', DB::raw('t.qte * s.convert as qteStock'))
                ->join('products as p', 'p.id', '=', 't.idproduct')
                ->join('categorys as c', 'c.id', '=', 'p.idcategory')
                ->join('setting as s', 's.idcategory', '=', 'c.id')
                ->whereColumn('t.idsetting', 's.id')
                ->get();

                $totalQteStock = $DataTmp->sum('qteStock');

                $resteQteStock = $Qte_Stock - $totalQteStock;

                $value_array = [];
                foreach($Setting as $item)
                {
                    $value_array[$item->id] = round($resteQteStock / $item->convert ,2);
                }

                $TmpLineOrder = TmpLineOrder::where(['idclient' => $idclient , 'idproduct' => $idproduct ])->get();

                $value_qte_tmp = [];

                foreach($TmpLineOrder as $item)
                {
                    $value_qte_tmp[$item->idsetting] = $item->qte;
                }

                foreach ($value_array as $idSetting => $value1)
                {
                    if($idSetting == $idsetting)
                    {

                        if($value1 <=1)
                        {
                            return response()->json([
                                'status' => 422,
                                'message' => 'La quantité maximale de produit est: ' . $name_product
                            ]);
                        }
                        else
                        {
                            return response()->json(['status' => 200]);
                        }
                    }

                }
            }
        }
        else
        {
            // Handle case when $type is not set
            $check = TmpLineOrder::where('idproduct', $idproduct)->count();

            if ($check == 0) {
                return response()->json(['status' => 200]);
            }

            // Extract quantities
            $qteStock = Stock::where('idproduct', $idproduct)->value('qte');
            $qteTmpVente = TmpLineOrder::where('idproduct', $idproduct)->value('qte');

            if ($qteTmpVente && $qteTmpVente == $qteStock) {
                return response()->json([
                    'status' => 422,
                    'message' => 'La quantité maximale de produit est: ' . $qteStock,
                ]);
            }

            return response()->json(['status' => 200]);
        }









    }

    public function TrashTmpOrder(Request $request)
    {
        $data = $request->all();

        // Ensure that we only delete if more than one row exists
        if (TmpLineOrder::count() > 0) {
            TmpLineOrder::where('id', $data['id'])->delete();
            return response()->json([
                'status' => 200,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Cannot delete the last remaining item.'
        ]);
    }


    public function ChangeQteTmpPlus(Request $request)
    {
        $Client = Client::where('id', $request->idclient)
            ->select('nom', 'prenom', 'plafonnier')
            ->first();

        if (!$Client) {
            return response()->json(['status' => 404, 'message' => 'Client not found']);
        }

        $creditClient           = $this->getClientCredit($request->idclient);
        $totalTmpByClient       = TmpLineOrder::where('idclient', $request->idclient)->sum('total');
        $Price                  = TmpLineOrder::where('id',$request->id)->value('price');
        $totalCreditByClient    = $totalTmpByClient + $Price + $creditClient;

        if ($Client->plafonnier > 0 && $totalCreditByClient >= $Client->plafonnier)
        {

            return response()->json([
                'status' => 500,
                'message' => 'Le client ' . $Client->nom . " " . $Client->prenom . ' a atteint le montant maximum de ' . $Client->plafonnier . ' dirhams'
            ]);
        }

        $tmpLineOrder = TmpLineOrder::find($request->id);
        if (!$tmpLineOrder) {
            return response()->json(['status' => 404, 'message' => 'TmpLineOrder not found']);
        }

        $Qte_Stock = Stock::where('idproduct', $tmpLineOrder->idproduct)
            ->where('id', $tmpLineOrder->idstock)
            ->value('qte');

        $Qte_New_Tmp = $this->calculateNewQuantity($request->qte, $tmpLineOrder->idsetting);

        if ($Qte_New_Tmp > $Qte_Stock) {
            return response()->json([
                'status' => 422,
                'message' => 'Maximum quantity available is: ' . $Qte_Stock
            ]);
        }

        $price_product = $tmpLineOrder->price;
        $tmpLineOrder->update([
            'qte' => $request->qte,
            'total' => $price_product * $request->qte,
        ]);

        return response()->json(['status' => 200]);
    }

    private function getClientCredit($clientId)
    {
        $Mode_Paiement = DB::table('modepaiement as m')
            ->join('company as c', 'c.id', '=', 'm.idcompany')
            ->where('m.name', 'crédit')
            ->where('c.status','Active')
            ->select('m.id')
            ->first();

        if (!$Mode_Paiement) {
            return 0;
        }

        return Reglements::where('idclient', $clientId)
            ->where('idmode', $Mode_Paiement->id)
            ->sum('total');
    }

    private function calculateNewQuantity($quantity, $idsetting)
    {
        if ($idsetting) {
            $number_kg = Setting::where('id', $idsetting)->value('convert');
            return ($quantity * $number_kg) / $number_kg;
        }
        return $quantity;
    }

    public function ChangeQteTmpMinus(Request $request)
    {
        //extract id product from tmp with idRow
        $Product   = TmpLineOrder::where('id',$request->id)->select('idproduct')->first();
        $IdProduct = $Product->idproduct;


        if(intval($request->qte) == 1)
        {
            $price      = TmpLineOrder::where('id',$request->id)->value('price');
            $UpDateRow  = TmpLineOrder::where('id',$request->id)
            ->update([
                'qte'    => 1,
                'total'  => $price * 1,
            ]);

            return response()->json([
                'status'      => 200,
            ]);
        }
        $price      = TmpLineOrder::where('id',$request->id)->value('price');
        $UpDateRow  = TmpLineOrder::where('id',$request->id)
        ->update([
            'qte'    => $request->qte,
            'total'  => $price * $request->qte,
        ]);

        return response()->json([
            'status'      => 200,
        ]);
    }

    public function StoreOrder(Request $request)
    {

        $hasCheque = false;
        foreach($request->ModePaiement as $item)
        {
            $ModePaiement = ModePaiement::where('id',$item['mode'])->select('name')->get();
            foreach($ModePaiement as $item1)
            {
                if($item1->name === "chèque")
                {
                    $hasCheque = true;
                    if($item['totalPrix'] != $request->montant)
                    {
                        return response()->json([
                            'status' => 442,
                        ]);
                    }
                }
            }
        }


        $data = DB::table('products as p')
            ->join('stock as s', 's.idproduct', '=', 'p.id')
            ->join('tmplineorder as t', 't.idproduct', '=', 'p.id')
            ->join('company as c', 'c.id', '=', 't.idcompany')
            ->leftJoin('setting as se', 't.idsetting', '=', 'se.id')
            ->where('c.status', 'Active')
            ->where('t.idclient', $request->idclient)  // Replace 1 with $request->idclient if needed
            ->where('t.iduser', Auth::user()->id)    // Replace 1 with Auth::user()->id if needed
            ->groupBy('t.id')
            ->select('t.id', 't.qte', 't.price', 't.total', 'p.name', 't.idproduct', 't.idclient',
            't.idsetting', 'se.type', 'se.convert',
            DB::raw('CASE WHEN se.convert IS NOT NULL THEN t.qte * se.convert ELSE t.qte END AS qte_converted'),'t.idstock','t.accessoire')
            ->get();

        // create order


        $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
        $factureId = null;
        if(isset($request->isFacture))
        {
            $Facture = Facture::create([
                'total'     => $request->totalPrixPaiement ,
                'idcompany'=> $CompanyIsActive->id,
                'iduser'   => Auth::user()->id,
                'idclient' => $request->idclient,
            ]);
            $factureId = $Facture->id;
        }

        $Order = Order::create([
            'total'             => $request->totalPrixPaiement - $request->accessoire,
            'idcompany'         => $CompanyIsActive->id,
            'iduser'            => Auth::user()->id,
            'idclient'          => $request->idclient,
            'idfacture'         => $factureId,
        ]);
        if($hasCheque)
        {
            // insert cheque
            $Cheques = Cheques::create([
                'numero'                 => $request->numero,
                'datecheque'             => $request->datecheque,
                'datepromise'            => $request->datepromise,
                'montant'                => $request->montant,
                'type'                   => $request->type,
                'name'                   => $request->name,
                'bank'                   => $request->bank,
                'idorder'                => $Order->id,
            ]);
        }

        //create line order
        foreach ($data as $item)
        {
            $Lineorder = Lineorder::create([
                'qte'       => $item->qte_converted,
                'price'     => $item->price,
                'total'     => $item->total,
                'idorder'   => $Order->id,
                'idproduct' => $item->idproduct,
                'idsetting' => $item->idsetting,
                'idstock'   => $item->idstock,
                'accessoire'=> $item->accessoire,
            ]);
        }

        foreach ($request->ModePaiement as $item)
        {
            // extract name mode paiement

            $Reglement = Reglements::create([
                'total'         => $item['totalPrix'],
                'idclient'      => $request->idclient,
                'idorder'       => $Order->id,
                'idcompany'     => $CompanyIsActive->id,
                'iduser'        => Auth::user()->id,
                'datepaiement'  => null,
                'idmode'        => $item['mode']
            ]);
            $ModePaiement = ModePaiement::where('id',$item['mode'])->select('name')->get();
            foreach($ModePaiement as $item1)
            {
                if($item1->name != "crédit")
                {
                    $Paiements = Paiements::create([
                        'total'             => $item['totalPrix'],
                        'idmode'            => $item['mode'],
                        'idreglement'       => $Reglement->id,
                        'idcompany'         => $CompanyIsActive->id,
                        'iduser'            => Auth::user()->id,
                    ]);
                }
            }


        }

        // Prepare data for updating stock
        $updates = [];
        foreach ($data as $item) {
            $updates[] = [
                'idproduct' => $item->idproduct,
                'id' => $item->idstock,
                'qte' => DB::raw('qte - ' . $item->qte_converted),
                'status' => 'used',
            ];
        }

        // Update stock in bulk
        foreach ($updates as $update)
        {
            DB::table('stock')
                ->where('idproduct'         , $update['idproduct'])
                ->where('id'                , $update['id'])
                ->update([
                    'qte'                   => $update['qte'],
                    'status'                => $update['status'],
                ]);
        }
        $iduser = Auth::user()->id;
        $users = User::all();
        foreach($data as $index => $item)
        {
            // extract numero bon
            $result = DB::table('stock as s')
            ->join('products as p', 's.idproduct', '=', 'p.id')
            ->join('bonentres as b', 's.idbonentre', '=', 'b.id')
            ->select('p.name', 's.qte', 's.qte_notification', 'b.numero_bon')
            ->where('p.id', $item->idproduct)
            ->where('s.id', $item->idstock)
            ->first();

            if ($result && $result->qte <= $result->qte_notification)
            {
                $idCompany  = Company::where('status','Active')->value('id');
                Notification::send($users, new StockNotification($result->numero_bon, $result->name, $iduser,$idCompany));
            }
        }
        $TmpLineOrder = TmpLineOrder::where('idclient',$request->idclient)->where('iduser',Auth::user()->id)->delete();

        // here add notification if $item->qtestock < = $item->qte_notification
        return response()->json([
            'status'        => 200,

        ]);

    }

    public function GetMyVente(Request $request)
    {
        if($request->ajax())
        {
           /*  $orders = Order::select(
                'orders.id',
                DB::raw('orders.total  AS totalvente'),
                DB::raw('SUM(reglements.total) AS totalpaye'),
                DB::raw('(orders.total - SUM(reglements.total)) AS reste'),
                DB::raw('CONCAT(clients.nom, " ", clients.prenom) AS client'),
                'company.title AS company',
                'users.name AS user',
                'orders.idfacture',
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") as created_at_formatted')
            )
            ->join('reglements', 'reglements.idorder', '=', 'orders.id')
            ->join('paiements', 'paiements.idreglement', '=', 'reglements.id')
            ->join('modepaiement', 'modepaiement.id', '=', 'paiements.idmode')
            ->join('clients', 'clients.id', '=', 'orders.idclient')
            ->join('company', 'company.id', '=', 'orders.idcompany')
            ->join('users', 'users.id', '=', 'orders.iduser')
            ->leftJoin('factures', 'factures.id', '=', 'orders.idfacture')
            ->where('company.status','=','Active')
            ->where('reglements.status','=',null)
            ->groupBy('orders.id')
            ->orderBy('orders.id', 'desc')
            ->get(); */

            $subQuery1 = DB::table('orders as o')
            ->select([
                'o.id',
                'o.total as totalvente',
                DB::raw('0 as totalpaye'),
                DB::raw('concat(c.nom, " ", c.prenom) as client'),
                'u.name as user', // alias user
                'co.title as company',
                'o.idfacture',
                DB::raw('DATE_FORMAT(o.created_at, "%Y-%m-%d") as created_at_formatted'),'o.created_at as created_With_Time_Zone'
            ])
            ->join('clients as c', 'o.idclient', '=', 'c.id')
            ->join('users as u', 'o.iduser', '=', 'u.id')
            ->join('company as co', 'o.idcompany', '=', 'co.id')
            ->where('co.status', 'Active')
            ->where('o.total', '>', 0)
            ->groupBy('o.id');

            $subQuery2 = DB::table('orders as o')
    ->select([
        'o.id',
        DB::raw('0 as totalvente'),
        DB::raw('sum(p.total) as totalpaye'),
        DB::raw('concat(c.nom, " ", c.prenom) as client'),
        'u.name as user', // alias user
        'co.title as company',
        'o.idfacture',
        DB::raw('DATE_FORMAT(o.created_at, "%Y-%m-%d") as created_at_formatted'),'o.created_at as created_With_Time_Zone'

    ])
    ->join('clients as c', 'o.idclient', '=', 'c.id')
    ->join('users as u', 'o.iduser', '=', 'u.id')
    ->join('company as co', 'o.idcompany', '=', 'co.id')
    ->join('reglements as r', 'o.id', '=', 'r.idorder')
    ->join('paiements as p', 'r.id', '=', 'p.idreglement')
    ->where('co.status', 'Active')
    ->where('o.total', '>', 0)
    ->groupBy('r.idorder');

            $orders = DB::table(DB::raw("({$subQuery1->toSql()} UNION ALL {$subQuery2->toSql()}) as t"))
            ->mergeBindings($subQuery1)
            ->mergeBindings($subQuery2)
            ->select([
                'id',
                DB::raw('sum(totalvente) as totalvente'),
                DB::raw('sum(totalpaye) as totalpaye'),
                DB::raw('sum(totalvente - totalpaye) as reste'),
                'client',
                'user', // matching alias
                'company',
                'idfacture',
                'created_at_formatted',
                'created_With_Time_Zone'
            ])
                ->groupBy('id')
                ->orderBy('id', 'desc')
                ->get();


            return DataTables::of($orders)->addIndexColumn()->addColumn('action', function ($row)
            {

                $encryptedId = Crypt::encrypt($row->id);
                $btn = '<div class="action-btn d-flex">';

                // View button with permission check
                if (auth()->user()->can('vente-voir')) {
                    $btn .= '<a href="' . url('ShowOrder/' . $encryptedId) . '" class="text-light view ms-2" target="_blank" value="' . $row->id . '">
                                <i class="ti ti-eye fs-5 border rounded-2 bg-info p-1" title="Voir bon ou facture"></i>
                            </a>';
                }

                /* $orderCreatedTime = Carbon::parse($row->created_With_Time_Zone);
                $hoursSinceCreation = $orderCreatedTime->diffInHours(Carbon::now()); */
                $today = Carbon::today();
                $createdOrder = Carbon::parse($row->created_With_Time_Zone);
                // Display "Annuler vente" button if order was created less than 12 hours ago
                if ($createdOrder->isSameDay($today)) {
                    $btn .= '<a href="#" class="text-light ms-2 Trash" value="' . $row->id . '">
                                <i class="ti ti-shopping-cart-off fs-5 border rounded-2 bg-danger p-1" title="Annuler vente"></i>
                            </a>';
                } 
                // Print button with permission check
                if (auth()->user()->can('vente-imprimer')) {
                    $btn .= '<a href="' . url('invoices/' . $row->id) . '" class="text-light ms-2" target="_blank" value="' . $row->id . '">
                                <i class="ti ti-file-invoice fs-5 border rounded-2 bg-success p-1" title="Imprimer bon ou facture"></i>
                            </a>';
                }
                $btn .= '<div class="sticky-menu-container">
                            <div class="inner-menu closed ">
                                <ul class="menu-list w-100">
                                    <li class="menu-item  border border-white rounded-2 bg-white">
                                        <a href="#" class="item-text fs-2 py-2 text-dark text-center verifiPiement" value="'.$row->id.'">- Vérifiez la méthode de paiement</a>
                                    </li>
                                    <!--
                                    <li class="menu-item   border border-white rounded-2 bg-white">
                                        <a href="#" class="item-text fs-2 py-2 text-dark text-center" value="'.$row->id.'">- Change la méthode de paiement</a>
                                    </li> -->
                                </ul>
                            </div>
                            <div class="outer-button">
                                <div class="icon-container">
                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 92 92" enable-background="new 0 0 92 92" xml:space="preserve" class="close-icon">
                                        <path id="XMLID_732_" d="M70.7,64.3c1.8,1.8,1.8,4.6,0,6.4c-0.9,0.9-2,1.3-3.2,1.3c-1.2,0-2.3-0.4-3.2-1.3L46,52.4L27.7,70.7
                                            c-0.9,0.9-2,1.3-3.2,1.3s-2.3-0.4-3.2-1.3c-1.8-1.8-1.8-4.6,0-6.4L39.6,46L21.3,27.7c-1.8-1.8-1.8-4.6,0-6.4c1.8-1.8,4.6-1.8,6.4,0
                                            L46,39.6l18.3-18.3c1.8-1.8,4.6-1.8,6.4,0c1.8,1.8,1.8,4.6,0,6.4L52.4,46L70.7,64.3z"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" viewBox="0 0 64 64" width="64" height="64" class="arrow-icon">
                                        <path d="M32 12c1.1 0 2 .9 2 2v16h16c1.1 0 2 .9 2 2s-.9 2-2 2H34v16c0 1.1-.9 2-2 2s-2-.9-2-2V34H14c-1.1 0-2-.9-2-2s.9-2 2-2h16V14c0-1.1.9-2 2-2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>';
                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);

        }
    }

    public function viewInvoice($id)
    {
        /* $invoice = Order::findOrFail($id);
        // extract client from order
        $IdClient = Order::where('id',$id)->select('idclient')->first();
        $Client   = Client::where('id',$IdClient->idclient)->first();

        // extract id mode credit by company and extract credit this order
        $IdCredit = DB::table('modepaiement as m')
        ->join('company as c','c.id','=','m.idcompany')
        ->where('c.status','=','Active')
        ->where('m.name','=','crédit')
        ->value('m.id');
        // now extract credit
        $Credit = DB::table('reglements as r')
        ->where('idorder','=',$id)
        ->where('idmode','=',$IdCredit)
        ->value('total');

        // extract line order from id order


        $DataLine = DB::table('lineorder as l')
            ->join('products as p', 'l.idproduct', '=', 'p.id')
            ->join('orders as o', 'l.idorder', '=', 'o.id')
            ->leftJoin('setting as s', 'l.idsetting', '=', 's.id')
            ->where('o.id', $id)
            ->select(
                'p.name',
               
               DB::raw("CASE
            WHEN s.convert IS NOT NULL THEN CONCAT(ROUND(l.qte / s.convert), ' ', s.type)
            ELSE l.qte
        END AS qte"),
                'l.price',
                'l.total',
                DB::raw("CASE WHEN s.convert IS NOT NULL THEN ROUND(l.qte / s.convert) ELSE l.qte END AS qtedevision"),
                'l.accessoire',
                's.type',
                DB::raw('IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS QteConvertWithOutConcat,
                        ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) AS remise'),
                DB::raw('IF(ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
                            l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
                            l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
                        ) AS price_new'),
                DB::raw('IF(
                    ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
                    l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
                    l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
                ) * IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS totalnew')
            )
            ->get();

        // extract order
        $order    = Order::findOrFail($id);
        // check is facture or bon
        $typeOrder = false;
        $id = null;
        if(!is_null($order->idfacture))
        {
            $typeOrder = true;
            $id = $order->idfacture;
        }
        $id = $order->id;
        $formattedId = str_pad($id, 4, '0', STR_PAD_LEFT);
        $Tva                  = DB::table('tva as t')
        ->join('company as c','c.id','=','t.idcompany')
        ->where('c.status','=','Active')
        ->select('t.name')
        ->first();


        $imagePath = public_path('images/R.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $Info = DB::table('infos as f')->join('company as c', 'c.id', '=', 'f.idcompany')->where('c.status', '=', 'Active')->select('f.*')->first();
        $pdf = app('dompdf.wrapper');
        $context = stream_context_create([
            'ssl'  => [
                'verify_peer'  => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE,
            ]
        ]);
        $pdf = PDF::setOptions(['isHTML5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->getDomPDF()->setHttpContext($context);
        $pdf->loadView('Order.FactureOrBon',
        compact('Client', 'DataLine', 'order', 'typeOrder', 'Info', 'Tva', 'formattedId', 'Credit', 'imageData'));
        $pdf->setPaper('A4', 'landscape');
        if (!is_null($order->idfacture)) {
            return $pdf->download('Facture.pdf');
        } else {
            return $pdf->download('Bon.pdf');
        } */
        $invoice = Order::findOrFail($id);
        // extract client from order
        $IdClient = Order::where('id',$id)->select('idclient')->first();
        $Client   = Client::where('id',$IdClient->idclient)->first();

        // extract id mode credit by company and extract credit this order
        $IdCredit = DB::table('modepaiement as m')
        ->join('company as c','c.id','=','m.idcompany')
        ->where('c.status','=','Active')
        ->where('m.name','=','crédit')
        ->value('m.id');
        // now extract credit
        $Credit = DB::table('reglements as r')
        ->where('idorder','=',$id)
        ->where('idmode','=',$IdCredit)
        ->value('total');

        // extract line order from id order


        $DataLine = DB::table('lineorder as l')
            ->join('products as p', 'l.idproduct', '=', 'p.id')
            ->join('orders as o', 'l.idorder', '=', 'o.id')
            ->leftJoin('setting as s', 'l.idsetting', '=', 's.id')
            ->where('o.id', $id)
            ->select(
                'p.name',
               
               DB::raw("CASE
            WHEN s.convert IS NOT NULL THEN CONCAT(ROUND(l.qte / s.convert), ' ', s.type)
            ELSE l.qte
        END AS qte"),
                'l.price',
                'l.total',
                DB::raw("CASE WHEN s.convert IS NOT NULL THEN ROUND(l.qte / s.convert) ELSE l.qte END AS qtedevision"),
                'l.accessoire',
                's.type',
                DB::raw('IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS QteConvertWithOutConcat,
                        ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) AS remise'),
                DB::raw('IF(ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
                            l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
                            l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
                        ) AS price_new'),
                DB::raw('IF(
                    ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
                    l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
                    l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
                ) * IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS totalnew')
            )
            ->get();

        // extract order
        $order    = Order::findOrFail($id);
        // check is facture or bon
        $typeOrder = false;
        $id = null;
        if(!is_null($order->idfacture))
        {
            $typeOrder = true;
            $id = $order->idfacture;
        }
        $id = $order->id;
        $formattedId = str_pad($id, 4, '0', STR_PAD_LEFT);
        $Tva                  = DB::table('tva as t')
        ->join('company as c','c.id','=','t.idcompany')
        ->where('c.status','=','Active')
        ->select('t.name')
        ->first();
        $imagePath = public_path('images/R.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $Info = DB::table('infos as f')->join('company as c', 'c.id', '=', 'f.idcompany')->where('c.status', '=', 'Active')->select('f.*')->first();
        $pdf = app('dompdf.wrapper');
        $context = stream_context_create([
            'ssl'  => [
                'verify_peer'  => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE,
            ]
        ]);
        $Info = DB::table('infos as f')->join('company as c', 'c.id', '=', 'f.idcompany')->where('c.status', '=', 'Active')->select('f.*')->first();
    
        // توليد HTML باستخدام القالب وتمرر البيانات
        $html = view('Order.FactureOrBon', [
            'Client'        => $Client,
            'DataLine'      => $DataLine,
            'order'         => $order,
            'typeOrder'     => $typeOrder,
            'Tva'           => $Tva,
            'formattedId'   => $formattedId,
            'Credit'        => $Credit,
            'Info'          => $Info,
            'imageData'     => $imageData,
        ])->toArabicHTML();
    
        // تحميل HTML إلى PDF
        $pdf = Pdf::loadHTML($html)->output();
    
        // تحديد رؤوس الاستجابة
        $headers = [
            "Content-type" => "application/pdf",
        ];
        
    
        // إرجاع PDF كملف لتحميله
        if (!is_null($order->idfacture)) {
            return response()->streamDownload(
                fn() => print($pdf),
                "Facture.pdf",
                $headers
            );
        } else {
            return response()->streamDownload(
                fn() => print($pdf),
                "Bon.pdf",
                $headers
            );
        } 

       









    }

    public function Facture(Request $request)
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page facture')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CountInfo          = DB::table('infos as f')->join('company as c','c.id','=','f.idcompany')->where('c.status','=','Active')->count();
        if($CountInfo == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page facture')
            ->with('body',"Parce qu'il n'y a pas de information");
        }
        if($request->ajax())
        {
            $orders = Order::select(
                'orders.id',
                'orders.total AS totalvente',
                'reglements.total AS totalpaye',
                DB::raw('(orders.total - reglements.total) AS reste'),
                DB::raw('CONCAT(clients.nom, " ", clients.prenom) AS client'),
                'company.title AS company',
                'users.name AS user',
                'orders.idfacture',
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") as created_at_formatted')
            )
            ->join('reglements', 'reglements.idorder', '=', 'orders.id')
            ->join('paiements', 'paiements.idreglement', '=', 'reglements.id')
            ->join('modepaiement', 'modepaiement.id', '=', 'paiements.idmode')
            ->join('clients', 'clients.id', '=', 'orders.idclient')
            ->join('company', 'company.id', '=', 'orders.idcompany')
            ->join('users', 'users.id', '=', 'orders.iduser')
            ->leftJoin('factures', 'factures.id', '=', 'orders.idfacture')
            ->whereNotNull('orders.idfacture')
            ->where('company.status', '=', 'Active')
            ->groupBy('orders.id')
            ->get();

            return DataTables::of($orders)->addIndexColumn()->addColumn('action', function ($row)
            {


                $btn = '<div class="action-btn d-flex">';

                // Print button with permission check
                if (auth()->user()->can('facture-imprimer')) {
                    $btn .= '<a href="' . url('invoices/' . $row->id) . '" class="text-light trash ms-2" target="_blank" value="' . $row->id . '">
                                <i class="ti ti-file-invoice fs-5 border rounded-2 bg-success p-1" title="Imprimer bon ou facture"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;

            })->rawColumns(['action'])->make(true);

        }
        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();
        return view('Facture.index') ->with('CompanyIsActive'         ,$CompanyIsActive);
    }


    public function ShowOrder($encryptedId)
    {


        $id = Crypt::decrypt($encryptedId);

        $DataLineOrder = DB::table('lineorder as l')
            ->join('products as p', 'l.idproduct', '=', 'p.id')
            ->join('orders as o', 'l.idorder', '=', 'o.id')
            ->leftJoin('setting as s', 'l.idsetting', '=', 's.id')
            ->where('o.id', $id)
            ->select('p.name',
            DB::raw("CASE WHEN s.convert IS NOT NULL THEN CONCAT(ROUND(l.qte / s.convert), ' ', s.type) ELSE l.qte END AS qte"),
                'l.price','l.total','l.accessoire','s.type',
                DB::raw("CASE WHEN s.convert IS NOT NULL THEN ROUND(l.qte / s.convert) ELSE l.qte END AS qtedevision"),
                DB::raw('IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS QteConvertWithOutConcat,
                        ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) AS remise'),
                DB::raw('IF(ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
                            l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
                            l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
                        ) AS price_new'),
                DB::raw('IF(
                    ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
                    l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
                    l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
                ) * IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS totalnew'))
            ->get();



        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();
        $client                = DB::table('clients as c')
                                ->join('orders as o','o.idclient','=','c.id')
                                ->where('o.id',$id)
                                ->select('c.*')
                                ->first();
        $Tva                  = DB::table('tva as t')
                                ->join('company as c','c.id','=','t.idcompany')
                                ->where('c.status','=','Active')
                                ->select('t.name')
                                ->first();
        // is facutre or not
        $CheckFacutreOrBon    = Order::where('id',$id)->select('idfacture')->first();
        return view('Order.lineOrder')
        ->with('DataLineOrder',$DataLineOrder)
        ->with('CompanyIsActive',$CompanyIsActive)
        ->with('client',$client)
        ->with('id',$id)
        ->with('Tva',$Tva)
        ->with('CheckFacutreOrBon',$CheckFacutreOrBon)

        ;
    }

    public function getClientByCompany(Request $request)
    {
        $Client = DB::table('clients as c')
        ->join('company as co','co.id','=','c.idcompany')
        ->where('co.status','=','Active')
        ->select(DB::raw('concat(c.nom," ",c.prenom) as client'),'c.id')
        ->get();
        return response()->json([
            'status'    => 200,
            'data'      =>$Client,
        ]);
    }

    public function getUniteVenteByProduct(Request $request)
    {

        $products = DB::table('products as p')
            ->join('categorys as c', 'p.idcategory', '=', 'c.id')
            ->join('setting as s', 'c.id', '=', 's.idcategory')
            ->join('company as co', 's.idcompany', '=', 'co.id')
            ->select('s.id', 's.type')
            ->where('co.status', 'Active')
            ->where('s.name_product', $request->name)
            ->groupBy('s.id')
            ->get();


        if($products->isNotEmpty())
        {

            return response()->json([
                'status'   => 200,
                'data'     => $products
            ]);
        }
        else
        {

            return response()->json([
                'status'   => 500,
            ]);
        }
    }

    public function checkTableTmpHasDataNotThisClient(Request $request)
    {
        // check table tmp has data not this client
        $TmpLineOrder = TmpLineOrder::where('idclient','!=',$request->idclient)->count();

        if($TmpLineOrder == 0)
        {
            // remarque client
            $remarks = DB::select("select remark from remark where idclient = ?",[$request->idclient]);

            // Initialize the variable
            $remark = null;

            // Check if the result is not empty and assign the first remark
            if (!empty($remarks) && isset($remarks[0]->remark)) {
                $remark = $remarks[0]->remark;
            }
            return response()->json([
                'status'  => 200,
                'remark'  => $remark
            ]);

        }
        else
        {
            $TmpLineOrders = TmpLineOrder::where('idclient', '!=', $request->idclient)->groupBy('idclient')->get();
            $idClients = $TmpLineOrders->pluck('idclient')->toArray();
            $clients = [];
            foreach($idClients as $item)
            {
                $Client = Client::where('id',$item)->select(DB::raw('concat(nom," ",prenom) as name'))->first();
                if ($Client)
                {
                    // Add client name to array
                    $clients[] = $Client->name;
                }
            }
            // Prepare toastr message
            $errorMessage = 'Veuillez supprimer les produits du panier de table. Le client est: ';
            if (!empty($clients)) {
                $errorMessage .= implode(', ', $clients);
            }

           // Return JSON response
            return response()->json([
                'status' => 442,
                'errorMessage' => $errorMessage,
            ]);
        }
    }

    public function changeAccessoireTmp(Request $request)
    {
        $updateAccessoire = TmpLineOrder::where('id',$request->id)
        ->update([
            'accessoire'   => $request->accessoire,
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }
    public function changeQteByPress(Request $request)
    {
        $Client = Client::where('id', $request->idclient)
            ->select('nom', 'prenom', 'plafonnier')
            ->first();

        if (!$Client) {
            return response()->json(['status' => 404, 'message' => 'Client not found']);
        }

        $creditClient           = $this->getClientCredit($request->idclient);
        $totalTmpByClient       = TmpLineOrder::where('idclient', $request->idclient)->sum('total');
        $Price                  = TmpLineOrder::where('id',$request->id)->value('price');
        $totalCreditByClient    = $totalTmpByClient + $Price + $creditClient;

        if ($Client->plafonnier > 0 && $totalCreditByClient >= $Client->plafonnier)
        {

            return response()->json([
                'status' => 500,
                'message' => 'Le client ' . $Client->nom . " " . $Client->prenom . ' a atteint le montant maximum de ' . $Client->plafonnier . ' dirhams'
            ]);
        }

        $tmpLineOrder = TmpLineOrder::find($request->id);
        if (!$tmpLineOrder) {
            return response()->json(['status' => 404, 'message' => 'TABLEAU PANIER PAR CLIENT pas trouvé']);
        }

        $Qte_Stock = Stock::where('idproduct', $tmpLineOrder->idproduct)
            ->where('id', $tmpLineOrder->idstock)
            ->value('qte');

        $Qte_New_Tmp = $this->calculateNewQuantity($request->qte, $tmpLineOrder->idsetting);

        if ($Qte_New_Tmp > $Qte_Stock) {
            return response()->json([
                'status' => 422,
                'message' => 'Maximum quantity available is: ' . $Qte_Stock
            ]);
        }

        $price_product = $tmpLineOrder->price;
        $tmpLineOrder->update([
            'qte' => $request->qte,
            'total' => $price_product * $request->qte,
        ]);

        return response()->json(['status' => 200]);
    }

    public function TrashOrder(Request $request)
    {
        // extract line order
        $lineorder  = Lineorder::where('idorder',$request->id)->get();
        foreach($lineorder as $item)
        {
            // update stock
            DB::select("update stock set qte = qte + ? where idproduct = ? and id = ?",[$item->qte ,$item->idproduct , $item->idstock]);
        }
        // delete line order
        Lineorder::where('idorder',$request->id)->delete();

        // Extract id reglement by idorder and save in another variable
        $IdReglement = DB::table('orders as o')
        ->join('reglements as r', 'r.idorder', '=', 'o.id')
        ->where('r.idorder', $request->id)
        ->pluck('r.id');  // Do not implode here

        // Extract paiement by reglement (for debugging purposes)
        $Paiement = DB::table('paiements as p')
        ->join('reglements as r', 'r.id', '=', 'p.idreglement')
        ->whereIn('p.idreglement', $IdReglement->toArray())
        ->get();


        // Save idreglement in another variable before deletion
        $idReglementArray = $IdReglement->toArray();
        // delete reglements
        $Reglements = Reglements::where('idorder',$request->id)->delete();

        // Delete paiements associated with the reglements
        Paiements::whereIn('idreglement', $idReglementArray)->delete();

        // delete order
        $DeleteOrder = Order::where('id',$request->id)->delete();
        if($DeleteOrder)
        {
            return response()->json([
                'status'    => 200,
            ]);
        }
        else
        {
            return response()->json([
                'status'    => 400,
                'message'   => 'Contact support',
            ]);
        }

    }

    public function verifiPaiement(Request $request)
    {
        $InfoModePaiementORder = DB::table('orders as o')
        ->join('reglements as r', 'o.id', '=', 'r.idorder')
        ->join('modepaiement as m', 'r.idmode', '=', 'm.id')
        ->where('o.id', $request->idorder)
        ->select('r.total', 'm.name')
        ->get();
        return response()->json([
            'status'   => 200,
            'data'     => $InfoModePaiementORder
        ]);
    }
}
