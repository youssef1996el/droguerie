<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\SoldeCaisse;
use DB;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
class SoldecaisseController extends Controller
{
    public function index()
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page Solde de départ caisse')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('SoldeCaisse.index')
        ->with('CompanyIsActive'                  , $CompanyIsActive);
    }

    public function StoreSoldeCaisse(Request $request)
    {
        $SoldeCaisse = SoldeCaisse::create([
            'total'       => $request->total,
            'idcompany'   => Company::where('status','Active')->value('id'),
            'iduser'      => Auth::user()->id,
        ]);


        return response()->json([
            'status'    => 200,
        ]);



    }

    public function getSoldeCaisse()
    {
        $Solde_De_Caisse = DB::table('soldecaisse as s')
        ->join('company as c','c.id','=','s.idcompany')
        ->join('users as u','u.id','=','s.iduser')
        ->where('c.status','Active')
        ->select('s.total','u.name','c.title','s.id',DB::raw('date(s.created_at) as created'))
        ->get();

        return DataTables::of($Solde_De_Caisse)->addIndexColumn()->addColumn('action', function ($row)
        {
            $encryptedId = Crypt::encrypt($row->id);

            $btn = '<div class="action-btn d-flex">';

            // Edit button with permission check
            if (auth()->user()->can('Solde-modifier')) {
                $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                            <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier solde de caisse"></i>
                        </a>';
            }

            // Delete button with permission check
            if (auth()->user()->can('Solde-supprimer')) {
                $btn .= '<a href="#" class="text-light trash" value="' . $row->id . '">
                            <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer solde de caisse"></i>
                        </a>';
            }

            $btn .= '</div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    public function UpdateSoldeCaisse(Request $request)
    {
        $update_Solde_Caisse = SoldeCaisse::where('id',$request->id)->update([
            'total'   => $request->total,
        ]);

        return response()->json([
            'status'    => 200,
        ]);
    }

    public function TrashSoldeCaisse(Request $request)
    {
        SoldeCaisse::where('id',$request->id)->delete();
        return response()->json([
            'status'    => 200,
        ]);
    }
}
