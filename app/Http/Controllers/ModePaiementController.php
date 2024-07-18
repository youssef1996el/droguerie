<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Auth;
use App\Models\Company;
use App\Models\ModePaiement;
use Illuminate\Support\Facades\Validator;

class ModePaiementController extends Controller
{
    public function index()
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page Mode Paiement')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('ModePaiement.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function StoreModePaiement(Request $request)
    {
        try
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

                $data['iduser'] = Auth::user()->id;


                $data = array_map('trim', $data);
                $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
                $data['idcompany']     = $CompanyIsActive->id;

                // Create Client
                $ModePaiement = ModePaiement::create($data);
                return response()->json([
                    'status' => 200,
                    'message' => 'Mode paiement créée avec succès',
                ]);
            }
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
    }


    public function FetchModePaiementByCompanyActive(Request $request)
    {
        $modepaiement = DB::table('modepaiement as m')
        ->join('company as c','c.id','=','m.idcompany')
        ->join('users as u','u.id','=','m.iduser')
        ->where('c.status','=','Active')
        ->select('m.name','c.title','m.id','u.name as creerpar','m.created_at')
        ->get();
        return DataTables::of($modepaiement)->addIndexColumn()->addColumn('action', function ($row)
        {
            $btn = '<div class="action-btn d-flex">';
            // Edit button with permission check
            if (auth()->user()->can('mode paiement-modifier')) {
                $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                            <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier le mode de paiement"></i>
                        </a>';
            }
            // Delete button with permission check
            if (auth()->user()->can('mode paiement-supprimer')) {
                $btn .= '<a href="#" class="text-light trash" value="' . $row->id . '">
                            <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer le mode de paiement"></i>
                        </a>';
            }
            $btn .= '</div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    public function UpdateModePaiement(Request $request)
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

            $ModePaiement = ModePaiement::where('id',$data['id'])->update([
                'name' => $data['name'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Mode paiement modifier avec succès',
            ]);
        }
    }

    public function TrashModePaiement(Request $request)
    {
        // check ModePaiement inside product
        $check   = DB::table('modepaiement as m')
        ->join('paiements as p','p.idmode','=','m.id')
        ->count();
        if($check == 0)
        {
            $data = $request->all();
            $ModePaiement = ModePaiement::where('id',$data['id'])->delete();
            return response()->json([
                'status'   => 200,
                'message'  => 'Mode paiement supprimier avec succès',
            ]);
        }
        else
        {
            return response()->json([
                'status'    => 400,
                'message'   => 'Cette mode paiement contient le reglement qui ne peut pas être supprimé',
            ]);
        }
    }
}
