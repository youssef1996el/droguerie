<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\GetMoney;
use DB;
use DataTables;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class GetMoneyController extends Controller
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
            $data = DB::table('getmoney as g')
            ->join('company as c','c.id','=','g.idcompany')
            ->join('users as u','u.id','=','g.iduser')
            ->select('g.id','g.friend','g.total','c.title','u.name as user',DB::raw('date(g.created_at) as created_at_formatted'))
            ->where('c.status','=','Active')
            ->get();
            return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($row)
            {


                $btn = '<div class="action-btn d-flex">';

                // Edit button with permission check
                if (auth()->user()->can('Renenus-modifier')) {
                    $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier versement"></i>
                            </a>';
                }

                // Delete button with permission check
                if (auth()->user()->can('Renenus-supprimer')) {
                    $btn .= '<a href="#" class="text-light trash" value="' . $row->id . '">
                                <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer versement"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
        return view('Revenus.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function StoreRevenus(Request $request)
    {
        $validator=validator::make($request->all(),[
            'friend'                 =>'required',
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

            $Charge = GetMoney::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Revenus créée avec succès',
            ]);
        }
    }

    public function updateRevenus(Request $request)
    {

        $validator=validator::make($request->all(),[
            'friend'                     =>'required',
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

            $data['friend']          = ucfirst(strtolower($request->friend));

            $Charge = GetMoney::where('id',$data['id'])->update([
                'friend' => $data['friend'],
                'total' =>$data['total'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Revenus modifier avec succès',
            ]);
        }
    }

    public function TrashRevenus(Request $request)
    {
        $data = $request->all();

        // Retrieve the Charge based on the provided ID
        $GetMoney = GetMoney::find($data['id']);
        if (!$GetMoney) {
            return response()->json([
                'status'   => 404,
                'message'  => 'Revenus introuvable',
            ]);
        }
        // Get the creation date of the charge
        $creationDate = Carbon::parse($GetMoney->created_at)->format('Y-m-d');

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
            $GetMoney->delete();
            return response()->json([
                'status'   => 200,
                'message'  => 'Revenus supprimé avec succès',
            ]);
        }
    }
}
