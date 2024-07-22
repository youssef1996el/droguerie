<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use DB;
use App\Models\Categorys;
use Illuminate\Support\Facades\Crypt;
use Auth;
use Illuminate\Support\Facades\Validator;
use DataTables;
use App\Models\DetailCategorys;
class CategoryController extends Controller
{
    public function index()
    {

        // check Company is create
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page catégorie')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('Categorys.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function FetchCategoryByCompanyActive(Request $request)
    {
        $Categorys = DB::table('categorys as ca')
        ->join('company as c','c.id','=','ca.idcompany')
        ->where('c.status','=','Active')
        ->where('ca.name','!=','Solde de départ')
        ->select('ca.name','c.title','ca.id')
        ->get();
        return DataTables::of($Categorys)->addIndexColumn()->addColumn('action', function ($row)
        {
            $encryptedId = Crypt::encrypt($row->id);

            $btn = '<div class="action-btn d-flex">';

            if (auth()->user()->can('catégorie-modifier')) {
                $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                            <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier le client"></i>
                        </a>';
            }

            if (auth()->user()->can('catégorie-supprimer')) {
                $btn .= '<a href="#" class="text-light trash" value="' . $row->id . '">
                            <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer le client"></i>
                        </a>';
            }

            $btn .= '</div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    public function StoreCategory(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'                     =>'required',
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
            $data['name']          = ucfirst(strtolower($request->name));
            $Categorys = Categorys::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Catégorie créée avec succès',
            ]);
        }
    }
    public function UpdateCategory(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'                     =>'required',
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

            $data['name']          = ucfirst(strtolower($request->name));

            $Categorys = Categorys::where('id',$data['id'])->update([
                'name' => $data['name'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Catégorie modifier avec succès',
            ]);
        }
    }

    public function TrashCategory(Request $request)
    {
        // check category inside product
        $check   = DB::table('categorys as c')
        ->join('products as p','p.idcategory','=','c.id')
        ->count();
        if($check == 0)
        {
            $data = $request->all();
            $Category = Categorys::where('id',$data['id'])->delete();
            return response()->json([
                'status'   => 200,
                'message'  => 'Catégorie supprimier avec succès',
            ]);
        }
        else
        {
            return response()->json([
                'status'    => 400,
                'message'   => 'Cette catégorie contient le produit qui ne peut pas être supprimé',
            ]);
        }
    }








}
