<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Company;
use App\Models\Categorys;
use App\Models\DetailCategorys;
use App\Models\Setting;
use App\Models\BonEntre;
use Auth;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use DB;
use Illuminate\Support\Collection;
class StockController extends Controller
{
    public function index()
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page stock')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CountInfo          = DB::table('infos as f')->join('company as c','c.id','=','f.idcompany')->where('c.status','=','Active')->count();
        if($CountInfo == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page stock')
            ->with('body',"Parce qu'il n'y a pas de information");
        }

        $CountCategorys          = Categorys::count();
        if($CountCategorys == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page stock')
            ->with('body',"Parce qu'il n'y a pas de catégorie");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();

        $CategoryCompanyActive = DB::table('categorys as ca')
                                    ->join('company as c','c.id','=','ca.idcompany')
                                    ->where('c.status','=','Active')
                                    ->select('ca.name','c.title','ca.id')
                                    ->get();

        $Product             =  DB::table('setting as s')
                                    ->join('company as c','c.id','=','s.idcompany')
                                    ->where('c.status','=','Active')
                                    ->select('s.name_product')
                                    ->groupBy('s.name_product')
                                    ->get();



        return view('Stock.index')
        ->with('CompanyIsActive'                        ,$CompanyIsActive)
        ->with('CategoryCompanyActive'                  ,$CategoryCompanyActive)

        ->with('Product'                                ,$Product)
        ;
    }


    public function StoreStock(Request $request)
    {
         // Define validation rules
        $rules =
        [
            'numero_bon'        => 'required',
            'date'              => 'required',
            'numero'            => 'required',
            'commercial'        => 'required',
            'matricule'         => 'required',
            'chauffeur'         => 'required',
            ''
        ];
        if (empty($request->input('name'))) {
            return response()->json([
                'status' => 422,
                'errors' => ['name' => 'Le champ nom produit est requis.'],
            ]);
        }
        // Loop through array fields and add validation rules for each element
        foreach ($request->input('name', []) as $key => $value)
        {
            $rules["name.{$key}"]               = 'required|string';
            $rules["DropDownCategory.{$key}"]   = 'required|not_in:0';
            $rules["qte.{$key}"]                = 'required|integer';
            $rules["price.{$key}"]              = 'required|numeric';
            $rules["qte_company.{$key}"]        = 'required|integer';
            $rules["qte_notification.{$key}"]   = 'required|integer';
        }

        $customMessages =
        [
            'required' => 'Le champ :attribute est requis.',
            'not_in' => 'Veuillez sélectionner une catégorie pour le champ :attribute.',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        $validator->setAttributeNames([
            'DropDownCategory.*' => 'catégorie',
            'idbonentre' => 'N° Bon',
            'qte_company.*' => 'quantité société',
            'qte.*' => 'quantité',
            'qte_notification.*' => 'quantité min de stock',
        ]);

        if ($validator->fails())
        {

            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }

        $dataProduct = $request->all();
       // $dataProduct = array_map('trim', $dataProduct);

        $CompanyIsActive = Company::where('status', 'Active')->select('id')->first();
        $dataProduct['idcompany'] = $CompanyIsActive->id;
        $dataProduct['iduser'] = Auth::user()->id;

        // First, add Bon Entre
        $BonEntre = BonEntre::create([
            'numero_bon'                => $dataProduct['numero_bon'],
            'date'                      => $dataProduct['date'],
            'numero'                    => $dataProduct['numero'],
            'commercial'                => $dataProduct['commercial'],
            'mode_paiement'             => $dataProduct['modePaiement'] == "" ? null : $dataProduct['modePaiement'],
            'matricule'                 => $dataProduct['matricule'],
            'chauffeur'                 => $dataProduct['chauffeur'],
            'cin'                       => $dataProduct['cin'] == "" ? null : $dataProduct['cin'],
            'idcompany'                 => $dataProduct['idcompany'],
            'iduser'                    => $dataProduct['iduser'],
        ]);

        // Loop through each product and save
        foreach ($dataProduct['name'] as $index => $name)
        {
            $check = Product::where(['name' => $name, 'idcompany' => $dataProduct['idcompany']])->count();
            $Product = null;

            if ($check == 0) {
                $Product = Product::create([
                    'name'          => $name,
                    'idcategory'    => $dataProduct['DropDownCategory'][$index],
                    'idcompany'     => $dataProduct['idcompany'],
                    'iduser'        => $dataProduct['iduser'],
                ]);
            }
            else
            {
                $Product = Product::where(['name' => $name, 'idcompany' => $dataProduct['idcompany']])->first();
            }

            if ($Product)
            {
                $dataStock = [
                    'idbonentre'            => $BonEntre->id,
                    'idproduct'             => $Product->id,
                    'qte'                   => $dataProduct['qte'][$index],
                    'price'                 => $dataProduct['price'][$index],
                    'qte_company'           => $dataProduct['qte_company'][$index],
                    'idcategory'            => $dataProduct['DropDownCategory'][$index],
                    'idcompany'             => $dataProduct['idcompany'],
                    'iduser'                => $dataProduct['iduser'],
                    'qte_notification'      => $dataProduct['qte_notification'][$index],
                ];

                Stock::create($dataStock);
            }
            else
            {
                return response()->json([
                    'status' => 400,
                    'message' => 'Contactez le support',
                ]);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Stock créée avec succès',
        ]);


        /* $validator = Validator::make($request->all(), [
            'numero_bon'        => 'required',
            'date'              => 'required',
            'numero'            => 'required',
            'commercial'        => 'required',
            'matricule'         => 'required',
            'chauffeur'         => 'required',


            'name'              => 'required|string',
            'idcategory'        => 'required|not_in:0',
            'qte'               => 'required|integer',
            'price'             => 'required|numeric',
            'qte_company'       => 'required|integer',
            'idbonentre'        => 'required|not_in:0',
        ]);


        $customMessages =
        [
            'required' => 'Le champ :attribute est requis.',
            'not_in'   => 'Veuillez sélectionner une catégorie pour le champ :attribute.',
        ];

        $validator->setAttributeNames([
            'idcategory'             => 'catégorie',
            'idbonentre'             => 'N° Bon',
            'qte_company'            => 'quantité société',
            'qte'                    => 'quantité ',
        ]);

        $validator->setCustomMessages($customMessages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }
        else
        {
            $dataProduct = $request->all();

            $dataProduct = array_map('trim', $dataProduct);

            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();

            $dataProduct['idcompany'] = $CompanyIsActive->id;

            $dataProduct['iduser'] = Auth::user()->id;

            // first add bon entrée
            $BonEntre = BonEntre::create([
               'numero_bon'                 => $dataProduct['numero_bon'],
               'date'                       => $dataProduct['date'],
               'numero'                     => $dataProduct['numero'],
               'commercial'                 => $dataProduct['commercial'],
               'mode_paiement'              => $dataProduct['modePaiement'] == "" ? null : $dataProduct['modePaiement'],
               'matricule'                  => $dataProduct['matricule'],
               'chauffeur'                  => $dataProduct['chauffeur'],
               'cin'                        => $dataProduct['cin'] == "" ? null : $dataProduct['cin'],
               'idcompany'                  => $dataProduct['idcompany'],
               'iduser'                     => $dataProduct['iduser'],
            ]);

            // check name product existes in table product
            foreach($dataProduct as $item)
            {
                $check  = Product::where(['name' => $item['name'] , 'idcompany' => $item['idcompany'] ])->count();
                $Product = null;
                if($check == 0)
                {
                    $Product = Product::create($dataProduct);
                }
                else
                {
                    $Product = Product::where(['name' => $item['name'] , 'idcompany' => $item['idcompany'] ])->first();
                }
                if($Product)
                {
                    $dataProduct['idproduct']       = $Product->id;
                    $dataStock                      = $dataProduct;
                    $dataStock['idbonentre']        = $BonEntre->id;

                    $Stock = Stock::create($dataStock);
                    return response()->json([
                        'status' => 200,
                        'message'=> 'Stock créée avec succès'
                    ]);
                }
                else
                {
                    return response()->json([
                        'status'   => 400,
                        'message'  => 'Contactez le support'
                    ]);
                }
            }







        } */


    }


    public function getStock(Request $request)
    {
        if ($request->ajax())
        {



            $DataStock = DB::table('bonentres as b')
        ->join('stock as s', 'b.id', '=', 's.idbonentre')
        ->join('products as p', 's.idproduct', '=', 'p.id')
        ->join('company as c', 'b.idcompany', '=', 'c.id')
        ->join('users as u', 'b.iduser', '=', 'u.id')
        ->where('c.status', 'Active')
        ->select(
            'b.id as idbon', 'b.numero_bon', 'b.date', 'b.numero', 'b.commercial',
            'b.mode_paiement', 'b.matricule', 'b.chauffeur', 'b.cin', 'b.created_at',
            'c.title as title_company', 's.qte as qte_stock', 's.qte_company', 's.price',
            'u.name', 'p.name as product',DB::raw('IFNULL(s.qte_notification, 0) as qte_notification')
        )
        ->get();

    $mergedData = new Collection();

    // Group and merge data by idbon
    $groupedData = $DataStock->groupBy('idbon');

    foreach ($groupedData as $idbon => $items) {
        $mergedItem = $items->reduce(function ($merged, $item) {
            // Initialize properties if not already set
            if (!isset($merged->product)) {
                $merged->product = [];
            }

            // Merge fields for items with the same idbon
            $merged->idbon = $item->idbon;
            $merged->name = $item->name;
            $merged->numero_bon = $item->numero_bon;
            $merged->date = $item->date;
            $merged->numero = $item->numero;
            $merged->commercial = $item->commercial;
            $merged->mode_paiement = $item->mode_paiement;
            $merged->matricule[] = $item->matricule;
            $merged->chauffeur[] = $item->chauffeur;
            $merged->cin = $item->cin;
            $merged->created_at = $item->created_at;
            $merged->title_company = $item->title_company;
            $merged->qte_stock[] = $item->qte_stock; // Assign qte_stock directly
            $merged->qte_company[] = $item->qte_company; // Assign qte_company directly
            $merged->price[] = $item->price;
            $merged->product[] = $item->product;
            $merged->qte_notification[] = $item->qte_notification;
            return $merged;
        }, new \stdClass()); // Start with an empty stdClass object

        $mergedData->push($mergedItem);
    }




            return DataTables::of($mergedData)->addIndexColumn()->addColumn('action', function ($row)
            {
                $encryptedId = Crypt::encrypt($row->idbon);

                $btn = '<div class="action-btn d-flex">';

                // Edit button with permission check
                if (auth()->user()->can('stock-modifier')) {
                    $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->idbon . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier le stock"></i>
                            </a>';
                }

                // Delete button with permission check
                if (auth()->user()->can('stock-supprimer')) {
                    $btn .= '<a href="#" class="text-light trash" value="' . $row->idbon . '">
                                <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer le stock"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
    }
    public function GetRowSelectedByTable(Request $request)
    {
        $DataStock = DB::table('bonentres as b')
        ->join('stock as s', 'b.id', '=', 's.idbonentre')
        ->join('products as p', 's.idproduct', '=', 'p.id')
        ->join('categorys as ca','ca.id','=','p.idcategory')
        ->join('company as c', 'b.idcompany', '=', 'c.id')
        ->join('users as u', 'b.iduser', '=', 'u.id')
        ->where('c.status', 'Active')
        ->where('b.id','=',$request->idBon)
        ->select(
            'b.id as idbon', 'b.numero_bon', 'b.date', 'b.numero', 'b.commercial',
            'b.mode_paiement', 'b.matricule', 'b.chauffeur', 'b.cin', 'b.created_at',
            'c.title as title_company', 's.qte as qte_stock', 's.qte_company', 's.price',
            'u.name', 'p.name as product','b.mode_paiement','ca.id as idcategory','ca.name as titleCategory','s.qte_notification'
        )
        ->get();



        return response()->json([
            'status'   => 200,
            'data'     => $DataStock,
        ]);
    }

    public function UpdateStock(Request $request)
    {
        $rules =
        [
            'numero_bon'        => 'required',
            'date'              => 'required',
            'numero'            => 'required',
            'commercial'        => 'required',
            'matricule'         => 'required',
            'chauffeur'         => 'required',
        ];

        // Loop through array fields and add validation rules for each element
        foreach ($request->input('name', []) as $key => $value)
        {
            $rules["name.{$key}"]               = 'required|string';
            $rules["DropDownCategory.{$key}"]   = 'required|not_in:0';
            $rules["qte.{$key}"]                = 'required|integer';
            $rules["price.{$key}"]              = 'required|numeric';
            $rules["qte_company.{$key}"]        = 'required|integer';
            $rules["qte_notification.{$key}"]        = 'required|integer';
        }
        $customMessages =
        [
            'required' => 'Le champ :attribute est requis.',
            'not_in' => 'Veuillez sélectionner une catégorie pour le champ :attribute.',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        $validator->setAttributeNames([
            'DropDownCategory.*' => 'catégorie',
            'idbonentre' => 'N° Bon',
            'qte_company.*' => 'quantité société',
            'qte.*' => 'quantité',
            'qte_notification.*' => 'quantité min de stock',
        ]);




        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }
        else
        {

            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
            $request['idcompany'] = $CompanyIsActive->id;
            $request['iduser'] = Auth::user()->id;
            // update bon Entree
            $upDateBonEntree = BonEntre::where('id',$request->idbon)->update([
                'numero_bon'                => $request['numero_bon'],
                'date'                      => $request['date'],
                'numero'                    => $request['numero'],
                'commercial'                => $request['commercial'],
                'mode_paiement'             => $request['mode_paiement'] == "" ? null : $request['mode_paiement'],
                'matricule'                 => $request['matricule'],
                'chauffeur'                 => $request['chauffeur'],
                'cin'                       => $request['cin'] == "" ? null : $dataProduct['cin'],
                'idcompany'                 => $request['idcompany'],
                'iduser'                    => $request['iduser'],
            ]);
            // delete stock by bon entree
            $DeleteStockByBon = Stock::where('idbonentre',$request->idbon)->delete();
            foreach ($request['name'] as $index => $name)
            {
                $check = Product::where(['name' => $name, 'idcompany' => $request['idcompany']])->count();
                $Product = null;

                if ($check == 0) {
                    $Product = Product::create([
                        'name'          => $name,
                        'idcategory'    => $request['DropDownCategory'][$index],
                        'idcompany'     => $request['idcompany'],
                        'iduser'        => $request['iduser'],
                    ]);
                }
                else
                {
                    $Product = Product::where(['name' => $name, 'idcompany' => $request['idcompany']])->first();
                }
                if ($Product)
                {
                    $dataStock = [
                        'idbonentre'            => $request->idbon,
                        'idproduct'             => $Product->id,
                        'qte'                   => $request['qte'][$index],
                        'price'                 => $request['price'][$index],
                        'qte_company'           => $request['qte_company'][$index],
                        'idcategory'            => $request['DropDownCategory'][$index],
                        'idcompany'             => $request['idcompany'],
                        'iduser'                => $request['iduser'],
                        'qte_notification'      => $request['qte_notification'][$index],
                    ];

                    Stock::create($dataStock);
                }
                else
                {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Contactez le support',
                    ]);
                }
            }
            $Stock = Stock::where('idproduct',$request->id)->update([
                'qte'       => $request->qte,
                'price'     => $request->price,
            ]);

            $Product  = Product::where('id',$request->id)->update([
                'name'      => $request->name,
                'price'     => $request->price,
            ]);
            return response()->json([
                'status' => 200,
                'message'=> 'Stock modifier avec succès'
            ]);
        }
    }

    public function TrashStock(Request $request)
    {
        // check all products in stock not use
        $Stock = Stock::where('idbonentre',$request->id)->get();
        $StockUse = false;
        foreach($Stock as $item)
        {
            if($item->status != "waiting")
            {
                $StockUse = true;
            }
        }
        if(!$StockUse)
        {
            // delete stock by id bon
            $Delete_Stock = Stock::where('idbonentre',$request->id)->delete();

            // delete bon entre

            $Delete_Bon_entre = BonEntre::where('id',$request->id)->delete();
            return response()->json([
                'status'   => 200,
            ]);
        }
        else
        {
            return response()->json([
                'status'   => 400,
                'message'  => 'Vous ne pouvez pas supprimer cet stock déjà utilisé'
            ]);
        }
    }



}
