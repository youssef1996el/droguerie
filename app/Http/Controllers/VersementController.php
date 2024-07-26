<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Versement;
use Auth;
use DB;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class VersementController extends Controller
{
    public function index(Request $request)
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page client')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }

        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        if($request->ajax())
        {
            $data = DB::table('versement as v')
            ->join('company as c','c.id','=','v.idcompany')
            ->join('users as u','u.id','=','v.iduser')
            ->select('v.id','v.comptable','v.total','c.title','u.name as user',DB::raw('date(v.created_at) as created_at_formatted'))
            ->where('c.status','=','Active')
            ->get();
            return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($row)
            {


                $btn = '<div class="action-btn d-flex">';

                // Edit button with permission check
                if (auth()->user()->can('Versement-modifier')) {
                    $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier versement"></i>
                            </a>';
                }

                // Delete button with permission check
                if (auth()->user()->can('Versement-supprimer')) {
                    $btn .= '<a href="#" class="text-light trash" value="' . $row->id . '">
                                <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer versement"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
        return view('Versement.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);

    }

    public function StoreVersement(Request $request)
    {
        $validator=validator::make($request->all(),[
            'comptable'                 =>'required',
            'total'                     =>'required',

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

            $Charge = Versement::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Versement créée avec succès',
            ]);
        }
    }

    public function updateVersement(Request $request)
    {

        $validator=validator::make($request->all(),[
            'comptable'                     =>'required',
            'total'                     =>'required',
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

            $data['comptable']          = ucfirst(strtolower($request->comptable));

            $Charge = Versement::where('id',$data['id'])->update([
                'comptable' => $data['comptable'],
                'total' =>$data['total'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Versement modifier avec succès',
            ]);
        }
    }
    public function TrashVersement(Request $request)
    {
        $data = $request->all();

        // Retrieve the Charge based on the provided ID
        $Versement = Versement::find($data['id']);
        if (!$Versement) {
            return response()->json([
                'status'   => 404,
                'message'  => 'Versement introuvable',
            ]);
        }
        // Get the creation date of the charge
        $creationDate = Carbon::parse($Versement->created_at)->format('Y-m-d');

        // Get today's date
        $today = Carbon::now()->format('Y-m-d');

        // Check if the charge was created today
        if ($creationDate != $today) {
            return response()->json([
                'status'   => 400,
                'message'  => 'Les frais ne peuvent pas être supprimés le jour même de leur création',
            ]);
        }
        if ($creationDate == $today)
        {
            $Versement->delete();
            return response()->json([
                'status'   => 200,
                'message'  => 'Versement supprimé avec succès',
            ]);
        }
    }
}
