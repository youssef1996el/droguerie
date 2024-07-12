<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Tva;
use DataTables;
use App\Models\Company;
use DB;
use Illuminate\Support\Facades\Validator;

class TvaController extends Controller
{
    public function index()
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page tva')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }

        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();
        return view('Tva.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function StoreTva(Request $request)
    {

        $validator=validator::make($request->all(),[
            'name'                     =>'required',

        ]);
         // Override default error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
        ];
        $validator->setAttributeNames([
            'name'       => 'tva',

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
            //$checkTVA = Tva::count();
            $checkTVA = DB::table('tva as t')
            ->join('company as c','c.id','=','t.idcompany')
            ->where('c.status','=','Active')
            ->count();
            if($checkTVA != 0)
            {
                return response()->json([
                    'status'   => 450,
                    'message'  => 'Il n\'est pas possible d\'ajouter plus que la tva'
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
                $data['name']          =$data['name'].' %';

                $Tva = Tva::create($data);
                return response()->json([
                    'status' => 200,
                    'message' => 'Tva créée avec succès',
                ]);
            }

        }
    }


    public function getTva(Request $request)
    {
        if($request->ajax())
        {
            $data = DB::table('tva as t')
            ->join('company as c','c.id','=','t.idcompany')
            ->join('users as u','u.id','=','t.iduser')
            ->where('c.status','=','Active')
            ->select('t.name as tva',DB::raw('date(t.created_at) as date_creer'),'c.title','u.name','t.id')
            ->get();

            return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($row)
            {
                $btn = '<div class="action-btn d-flex">';

                // Edit button with permission check
                if (auth()->user()->can('tva-modifier')) {
                    $btn .= '<a href="#" class="text-light edit" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier la TVA"></i>
                            </a>';
                }

                // Delete button with permission check
                if (auth()->user()->can('tva-supprimer')) {
                    $btn .= '<a href="#" class="text-light trash ms-2" value="' . $row->id . '">
                                <i class="ti ti-trash-x fs-5 border rounded-2 bg-danger p-1" title="Supprimer la TVA"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
    }

    public function trashTva(Request $request)
    {
        $TrashTva= Tva::where('id',$request->id)->delete();
        return response()->json([
            'status'   => 200,
            'message'  => 'Tva supprimer avec succès'
        ]);
    }

    public function UpdateTva(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'                     =>'required',

        ]);
         // Override default error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
        ];
        $validator->setAttributeNames([
            'name'       => 'tva',

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
            $data['name']          =$data['name'].' %';

            $UpdateTva= Tva::where('id',$data['id'])->update([
                'name'     => $data['name'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Tva modifier avec succès',
            ]);
        }
    }
}
