<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Company;
use App\Models\Categorys;
use Auth;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use DB;

class SettingController extends Controller
{
    public function index()
    {
        $CountCompany          = Company::where('status','=','Active')->count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page paramètre')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }

        $CountCategorys          = DB::table('categorys as c')
                                    ->join('company as co','co.id','=','c.idcompany')
                                    ->where('co.status','=','Active')
                                    ->count();
        if($CountCategorys == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page paramètre')
            ->with('body',"Parce qu'il n'y a pas de catégorie");

        }
        $CountStock            = DB::table('bonentres as b')
        ->join('stock as s','s.idbonentre','=','b.id')
        ->join('company as c','c.id','b.idcompany')
        ->where('c.status','=','Active')
        ->count();
        if($CountStock == 0)
        {
            return view('Errors.index')
                ->with('title','Il n\'est pas possible d\'accéder à la page paramètre')
                ->with('body',"Parce qu'il n'y a pas de stock");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();

        $CategoryCompanyActive = DB::table('categorys as ca')
        ->join('company as c','c.id','=','ca.idcompany')
        ->where('c.status','=','Active')
        ->select('ca.name','c.title','ca.id')
        ->get();

        $BonEntre = DB::table('bonentres as b')
        ->join('stock as s','s.idbonentre','=','b.id')
        ->join('company as c','c.id','b.idcompany')
        ->where('c.status','=','Active')
        ->select('numero_bon','b.id')
        ->groupBy('b.id')
        ->orderBy('b.id','desc')
        ->get();

        return view('Setting.index')
        ->with('BonEntre'         ,$BonEntre)
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('CategoryCompanyActive'         ,$CategoryCompanyActive);
    }

    public function FetchSetting(Request $request)
    {
        if($request->ajax())
        {

            $Data = DB::select("select s.id,b.numero_bon,s.name_product,s.type, s.qte,s.convert,c.title,u.name as user,date(s.created_at) as creer_le from setting s , stock st,bonentres b,company c,users u
where s.idstock = st.id and st.idbonentre = b.id and s.idcompany = c.id and s.iduser =u.id and c.status = 'Active';");


            return DataTables::of($Data)->addIndexColumn()->addColumn('action', function ($row)
            {
                $encryptedId = Crypt::encrypt($row->id);

                $btn = '<div class="action-btn d-flex">';

                // Edit button with permission check
                if (auth()->user()->can('paramètre-modifier')) {
                    $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier le paramètre"></i>
                            </a>';
                }

                // Delete button with permission check
                if (auth()->user()->can('paramètre-supprimer')) {
                    $btn .= '<a href="#" class="text-light trash" value="' . $row->id . '">
                                <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer le paramètre"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
    }

    public function StoreSetting(Request $request)
    {

        $validator=validator::make($request->all(),[
            'idcategory'                     =>'required|not_in:0',
            'name_product'                   =>'required',
            'unite'                          =>'required',
            'conversion_rate'                =>'required',
        ]);



         // Override default error messages
        $customMessages =
        [
            'required' => 'Le champ :attribute est requis.',
            'not_in'   => 'Veuillez sélectionner une catégorie pour le champ :attribute.',
        ];
        $validator->setAttributeNames([
            'idcategory' => 'catégorie',
            'name_product' => 'nom produit',
            'conversion_rate' => 'convert'
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
            $data = array_map('trim', $data);
            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
            $data['idcompany']     = $CompanyIsActive->id;
            $data['iduser']        = Auth::user()->id;
            $data['qte']           = 1;
            $data['convert']       = $data['conversion_rate'];
            $data['type']          = $data['unite'];


            $Setting               = Setting::create($data);
            return response()->json([
                'status'       => 200,
                'message'      => 'paramètre créér avec succès'
            ]);
        }
    }


    public function UpdateSetting(Request $request)
    {

        $validator=validator::make($request->all(),[
            'idcategory'                     =>'required|not_in:0',
            'name_product'                   =>'required',
            'unite'                          =>'required',
            'conversion_rate'                =>'required',
        ]);


        $customMessages =
        [
            'required' => 'Le champ :attribute est requis.',
            'not_in'   => 'Veuillez sélectionner une catégorie pour le champ :attribute.',
        ];
        $validator->setAttributeNames([
            'idcategory' => 'catégorie',
            'name_product' => 'nom produit',
            'conversion_rate' => 'convert'
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
            $data = array_map('trim', $data);

            $Setting               = Setting::where('id',$data['id'])->update([
                'convert'          =>  $data['conversion_rate'],
                'type'             =>  $data['unite'],
                'name_product'     =>  $data['name_product'],
                'idstock'          =>  $data['idstock']
            ]);
            return response()->json([
                'status'       => 200,
                'message'      => 'paramètre créér avec succès'
            ]);
        }
    }

    public function TrashSetting(Request $request)
    {
        $delete = Setting::where('id',$request->id)->delete();
        return response()->json([
            'status'       => 200,
            'message'      => 'paramètre supprimée avec succès'
        ]);
    }

    public function getNameProductByBonAndCategory(Request $request)
    {

        $Name_product = DB::table('bonentres as b')
            ->join('stock as s', 'b.id', '=', 's.idbonentre')
            ->join('products as p', 's.idproduct', '=', 'p.id')
            ->join('categorys as c', 'p.idcategory', '=', 'c.id')
            ->select('s.id as idstock', 'p.name')
            ->where('b.id', $request->idbon)
            ->where('c.id', $request->idcategory)
            ->get();
        return response()->json([
            'status'   => 200,
            'data'     => $Name_product
        ]);

    }

    public function getSettingByID(Request $request)
    {

        $Setting = DB::select('select s.idcategory,b.id as idbon from setting s , stock st, bonentres b where s.idstock = st.id and st.idbonentre = b.id and s.id = ?',[$request->id]);

        return response()->json([
            'status'   => 200,
            'data'     => $Setting
        ]);
    }


}
