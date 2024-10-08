<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Company;
use App\Models\TmpDevis;
use App\Models\Client;
use App\Models\Tva;
use App\Models\ModePaiement;
use App\Models\Reglements;
use App\Models\Stock;
use App\Models\Setting;
use App\Models\Product;
use App\Models\LineDevis;
use App\Models\Devis;
use Illuminate\Support\Facades\Crypt;
use DataTables;
use Auth;
use Carbon\Carbon;
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
class DevisController extends Controller
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
        return view('Devis.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('Clients'                 ,$Clients)
        ->with('Product'                 ,$Product)
        ->with('tva'                     ,$Tva->name);
    }

    public function GetDataTmpDevisByClient(Request $request)
    {
        if ($request->ajax())
        {

            $data = DB::table('products as p')
            ->join('stock as s', 's.idproduct', '=', 'p.id')
            ->join('tmpdevis as t', 't.idproduct', '=', 'p.id')
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

    public function GetMyDevis(Request $request)
    {
        if($request->ajax())
        {


            $subQuery1 = DB::table('devis as d')
            ->select([
                'd.id',
                'd.total as totalvente',
                DB::raw('0 as totalpaye'),
                DB::raw('concat(c.nom, " ", c.prenom) as client'),
                'u.name as user', // alias user
                'co.title as company',
                'd.type',
                DB::raw('DATE_FORMAT(d.created_at, "%Y-%m-%d") as created_at_formatted'),'d.created_at as created_With_Time_Zone'
            ])
            ->join('clients as c', 'd.idclient', '=', 'c.id')
            ->join('users as u', 'd.iduser', '=', 'u.id')
            ->join('company as co', 'd.idcompany', '=', 'co.id')
            ->where('co.status', 'Active')
            ->where('d.total', '>', 0)
            ->groupBy('d.id');

            $subQuery2 = DB::table('devis as d')
    ->select([
        'd.id',
        DB::raw('0 as totalvente'),
        DB::raw('sum(p.total) as totalpaye'),
        DB::raw('concat(c.nom, " ", c.prenom) as client'),
        'u.name as user', // alias user
        'co.title as company',
        'd.type',
        DB::raw('DATE_FORMAT(d.created_at, "%Y-%m-%d") as created_at_formatted'),'d.created_at as created_With_Time_Zone'

    ])
    ->join('clients as c', 'd.idclient', '=', 'c.id')
    ->join('users as u', 'd.iduser', '=', 'u.id')
    ->join('company as co', 'd.idcompany', '=', 'co.id')
    ->join('reglements as r', 'd.id', '=', 'r.idorder')
    ->join('paiements as p', 'r.id', '=', 'p.idreglement')
    ->where('co.status', 'Active')
    ->where('d.total', '>', 0)
    ->groupBy('r.idorder');

            $Devis = DB::table(DB::raw("({$subQuery1->toSql()} UNION ALL {$subQuery2->toSql()}) as t"))
            ->mergeBindings($subQuery1)
            ->mergeBindings($subQuery2)
            ->select([
                'id',
                DB::raw('sum(totalvente) as total'),
                DB::raw('sum(totalpaye) as totalpaye'),
                DB::raw('sum(totalvente - totalpaye) as reste'),
                'client',
                'user', // matching alias
                'company',
                'type',
                'created_at_formatted',
                'created_With_Time_Zone'
            ])
                ->groupBy('id')
                ->orderBy('id', 'desc')
                ->get();


            return DataTables::of($Devis)->addIndexColumn()->addColumn('action', function ($row)
            {

                $encryptedId = Crypt::encrypt($row->id);
                $btn = '<div class="action-btn d-flex">';

                // View button with permission check
                if (auth()->user()->can('Devis-voir')) {
                    $btn .= '<a href="' . url('ShowDevis/' . $encryptedId) . '" class="text-light view ms-2" target="_blank" value="' . $row->id . '">
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
                if (auth()->user()->can('Devis-imprimer')) {
                    $btn .= '<a href="' . url('invoicesDevis/' . $row->id) . '" class="text-light ms-2" target="_blank" value="' . $row->id . '">
                                <i class="ti ti-file-invoice fs-5 border rounded-2 bg-success p-1" title="Imprimer bon ou facture"></i>
                            </a>';
                }


                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);

        }
    }

    public function checkTableTmpHasDataNotThisClientDevis(Request $request)
    {
        // check table tmp has data not this client
        $TmpLineOrder = TmpDevis::where('idclient','!=',$request->idclient)->count();

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
            $TmpLineOrders = TmpDevis::where('idclient', '!=', $request->idclient)->groupBy('idclient')->get();
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

    public function GetTotalByClientCompanyDevis(Request $request)
    {
        $sumTotal = TmpDevis::where('iduser', Auth::user()->id)
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

    public function checkQteProductDevis(Request $request)
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

            $Qte_Stock = Stock::where('idproduct',$idproduct)->where('id',$request->idstock)->value('qte');

            $Qte_Stock = floatval(str_replace(',', '.', $Qte_Stock));

            $checkPorductInTableTmp = TmpDevis::where(['idclient' => $idclient , 'idproduct' => $idproduct ])->count();

            if($checkPorductInTableTmp == 0)
            {
                $value_array = [];

                foreach($Setting as $item)
                {

                    $value_array[$item->id] = round($Qte_Stock / $item->convert ,2);
                }
                $TmpLineDevis = TmpDevis::where(['idclient' => $idclient , 'idproduct' => $idproduct ])->get();
                if($TmpLineDevis->isEmpty())
                {
                    return response()->json(['status' => 200]);
                }
                $value_qte_tmp = [];

                foreach($TmpLineDevis as $item)
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

                $DataTmp = DB::table('tmpdevis as t')
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

                $TmpLineDevis = TmpDevis::where(['idclient' => $idclient , 'idproduct' => $idproduct ])->get();

                $value_qte_tmp = [];

                foreach($TmpLineDevis as $item)
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
            $check = TmpDevis::where('idproduct', $idproduct)->count();

            if ($check == 0) {
                return response()->json(['status' => 200]);
            }

            // Extract quantities
            $qteStock = Stock::where('idproduct', $idproduct)->value('qte');
            $qteTmpDevis = TmpDevis::where('idproduct', $idproduct)->value('qte');

            if ($qteTmpDevis && $qteTmpDevis == $qteStock) {
                return response()->json([
                    'status' => 422,
                    'message' => 'La quantité maximale de produit est: ' . $qteStock,
                ]);
            }

            return response()->json(['status' => 200]);
        }
    }

    public function sendDataToTmpDevis(Request $request)
    {
        // function send to table tmp
        if ($request->ajax()) {
            $idUser = Auth::user()->id;
            $idClient = $request->idclient;
            $idProduct = $request->idproduct;
            $idCompany = $request->idcompany;
            $idSetting = $request->typeVente;
            $idStock   = $request->idstock;

            $check_Product_In_TmpLineOrder = TmpDevis::where([
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
                TmpDevis::create([
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
                $Old_Data = TmpDevis::where([
                    'idproduct' => $idProduct,
                    'iduser' => $idUser,
                    'idcompany' => $idCompany,
                    'idclient' => $idClient,
                    'idsetting' => $idSetting,
                    'idstock'   => $request->idstock,
                ])->first();

                TmpDevis::where([
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
                $check_Product_In_TmpLineOrder = TmpDevis::where('idclient',$request->idclient)
                ->where('idproduct',$request->idproduct)
                ->where('iduser',Auth::user()->id)
                ->where('idcompany',$request->idcompany)
                ->count();
            }
            else
            {

                $check_Product_In_TmpLineOrder = TmpDevis::where('idclient',$request->idclient)
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
                $TmpLineOrder = TmpDevis::create([
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
                $data_product_old = TmpDevis::where('idproduct'     ,$request->idproduct)
                                                    ->where('iduser'    ,Auth::user()->id)
                                                    ->where('idcompany' ,$request->idcompany)
                                                    ->where('idclient'  ,$request->idclient)
                                                    ->first();
                if (is_null($request->typeVente))
                {

                    $TmpLineOrder = TmpDevis::where('idproduct'     ,$request->idproduct)
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

                    $TmpLineOrder = TmpDevis::where('idproduct'     ,$request->idproduct)
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

    public function TrashTmpDevis(Request $request)
    {

        $data = $request->all();

        // Ensure that we only delete if more than one row exists
        if (TmpDevis::count() > 0) {
            TmpDevis::where('id', $data['id'])->delete();
            return response()->json([
                'status' => 200,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Cannot delete the last remaining item.'
        ]);

    }

    public function ChangeQteTmpMinusDevis(Request $request)
    {
        //extract id product from tmp with idRow
        $Product   = TmpDevis::where('id',$request->id)->select('idproduct')->first();
        $IdProduct = $Product->idproduct;


        if(intval($request->qte) == 1)
        {
            $price      = TmpDevis::where('id',$request->id)->value('price');
            $UpDateRow  = TmpDevis::where('id',$request->id)
            ->update([
                'qte'    => 1,
                'total'  => $price * 1,
            ]);

            return response()->json([
                'status'      => 200,
            ]);
        }
        $price      = TmpDevis::where('id',$request->id)->value('price');
        $UpDateRow  = TmpDevis::where('id',$request->id)
        ->update([
            'qte'    => $request->qte,
            'total'  => $price * $request->qte,
        ]);

        return response()->json([
            'status'      => 200,
        ]);
    }

    public function ChangeQteTmpPlusDevis(Request $request)
    {
        $Client = Client::where('id', $request->idclient)
            ->select('nom', 'prenom', 'plafonnier')
            ->first();

        if (!$Client) {
            return response()->json(['status' => 404, 'message' => 'Client not found']);
        }

        $creditClient           = $this->getClientCredit($request->idclient);
        $totalTmpByClient       = TmpDevis::where('idclient', $request->idclient)->sum('total');
        $Price                  = TmpDevis::where('id',$request->id)->value('price');
        $totalCreditByClient    = $totalTmpByClient + $Price + $creditClient;

        if ($Client->plafonnier > 0 && $totalCreditByClient >= $Client->plafonnier)
        {

            return response()->json([
                'status' => 500,
                'message' => 'Le client ' . $Client->nom . " " . $Client->prenom . ' a atteint le montant maximum de ' . $Client->plafonnier . ' dirhams'
            ]);
        }

        $tmpLineOrder = TmpDevis::find($request->id);
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

    public function changeAccessoireTmpDevis(Request $request)
    {
        $updateAccessoire = TmpDevis::where('id',$request->id)
        ->update([
            'accessoire'   => $request->accessoire,
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }

    public function ChangeQteByPressDevis(Request $request)
    {
        $Client = Client::where('id', $request->idclient)
            ->select('nom', 'prenom', 'plafonnier')
            ->first();

        if (!$Client) {
            return response()->json(['status' => 404, 'message' => 'Client not found']);
        }

        $creditClient           = $this->getClientCredit($request->idclient);
        $totalTmpByClient       = TmpDevis::where('idclient', $request->idclient)->sum('total');
        $Price                  = TmpDevis::where('id',$request->id)->value('price');
        $totalCreditByClient    = $totalTmpByClient + $Price + $creditClient;

        if ($Client->plafonnier > 0 && $totalCreditByClient >= $Client->plafonnier)
        {

            return response()->json([
                'status' => 500,
                'message' => 'Le client ' . $Client->nom . " " . $Client->prenom . ' a atteint le montant maximum de ' . $Client->plafonnier . ' dirhams'
            ]);
        }

        $tmpLineOrder = TmpDevis::find($request->id);
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

    public function StoreDevis(Request $request)
    {
        $data = DB::table('products as p')
        ->join('stock as s', 's.idproduct', '=', 'p.id')
        ->join('tmpdevis as t', 't.idproduct', '=', 'p.id')
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
        $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
        $Devis = Devis::create([
            'total'             => $request->total,
            'idcompany'         => $CompanyIsActive->id,
            'iduser'            => Auth::user()->id,
            'idclient'          => $request->idclient,
            'type'              => $request->facture == true ? 'Facture' : 'Bon',
        ]);
        foreach ($data as $item)
        {
            $LineDevis = LineDevis::create([
                'qte'       => $item->qte_converted,
                'price'     => $item->price,
                'total'     => $item->total,
                'iddevis'   => $Devis->id,
                'idproduct' => $item->idproduct,
                'idsetting' => $item->idsetting,
                'idstock'   => $item->idstock,
                'accessoire'=> $item->accessoire,
            ]);
        }

        $TmpDevis = TmpDevis::where('idclient',$request->idclient)->where('iduser',Auth::user()->id)->delete();

        // here add notification if $item->qtestock < = $item->qte_notification
        return response()->json([
            'status'        => 200,

        ]);
    }

    public function ShowDevis($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);

        $DataLineDevis = DB::table('linedevis as l')
            ->join('products as p', 'l.idproduct', '=', 'p.id')
            ->join('devis as d', 'l.iddevis', '=', 'd.id')
            ->leftJoin('setting as s', 'l.idsetting', '=', 's.id')
            ->where('d.id', $id)
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
                                ->join('devis as d','d.idclient','=','c.id')
                                ->where('d.id',$id)
                                ->select('c.*')
                                ->first();
        $Tva                  = DB::table('tva as t')
                                ->join('company as c','c.id','=','t.idcompany')
                                ->where('c.status','=','Active')
                                ->select('t.name')
                                ->first();
        // is facutre or not
        $CheckFacutreOrBon    = Devis::where('id',$id)->select('type')->first();
        return view('Devis.lineDevis')
        ->with('DataLineDevis',$DataLineDevis)
        ->with('CompanyIsActive',$CompanyIsActive)
        ->with('client',$client)
        ->with('id',$id)
        ->with('Tva',$Tva)
        ->with('CheckFacutreOrBon',$CheckFacutreOrBon)

        ;
    }

    public function invoicesDevis($id)
    {
        $invoice = Devis::findOrFail($id);
        // extract client from order
        $IdClient = Devis::where('id',$id)->select('idclient')->first();
        $Client   = Client::where('id',$IdClient->idclient)->first();



        // extract line order from id order


        $DataLine = DB::table('linedevis as l')
            ->join('products as p', 'l.idproduct', '=', 'p.id')
            ->join('devis as d', 'l.iddevis', '=', 'd.id')
            ->leftJoin('setting as s', 'l.idsetting', '=', 's.id')
            ->where('d.id', $id)
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


        $Devis    = Devis::findOrFail($id);
        // check is facture or bon

        $id = $Devis->id;
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
        $pdf->loadView('Devis.PrintDevis',
        compact('Client', 'DataLine', 'Devis',  'Info', 'Tva', 'formattedId',  'imageData'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Devis.pdf');


    }

    public function TrashDevis(Request $request)
    {
        $LineDevis = LineDevis::where('iddevis',$request->id)->delete();
        $Devis     = Devis::where('id',$request->id)->delete();
        if($Devis)
        {
            return response()->json([
                'status'      => 200,
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
}
