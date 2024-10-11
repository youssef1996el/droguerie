<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Charge;
use App\Models\Company;
use DB;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Auth;
use Carbon\Carbon;
class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page charge')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        if($request->ajax())
        {
            $data = DB::table('charge as ca')
            ->join('company as c','c.id','=','ca.idcompany')
            ->join('users as u','u.id','=','ca.iduser')
            ->select('ca.id','ca.name','ca.total','c.title','u.name as user',DB::raw('date(ca.created_at) as created_at_formatted'))
            ->where('c.status','=','Active')
            ->get();
            return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($row)
            {


                $btn = '<div class="action-btn d-flex">';

                // Edit button with permission check
                if (auth()->user()->can('charge-modifier')) {
                    $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier charge"></i>
                            </a>';
                }

                // Delete button with permission check
                if (auth()->user()->can('charge-supprimer')) {
                    $btn .= '<a href="#" class="text-light trash" value="' . $row->id . '">
                                <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer charge"></i>
                            </a>';
                }
                $btn .='<a href="#" class="text-light ChangeDate" value="' . $row->id . '">
                                <i class="ti ti-calendar-due fs-5 border rounded-2 bg-info p-1" title="Supprimer charge"></i>
                            </a>';

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
        return view('Charge.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function StoreCharge(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'                     =>'required',
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

            $Charge = Charge::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'charge créée avec succès',
            ]);
        }
    }

    public function updateCharge(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'                     =>'required',
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

            $data['name']          = ucfirst(strtolower($request->name));

            $Charge = Charge::where('id',$data['id'])->update([
                'name' => $data['name'],
                'total' =>$data['total'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'charge modifier avec succès',
            ]);
        }
    }

    public function TrashCharge(Request $request)
    {
        $data = $request->all();

        // Retrieve the Charge based on the provided ID
        $charge = Charge::find($data['id']);
        if (!$charge) {
            return response()->json([
                'status'   => 404,
                'message'  => 'Charge introuvable',
            ]);
        }
        // Get the creation date of the charge
        $creationDate = Carbon::parse($charge->created_at)->format('Y-m-d');

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
            $charge->delete();
            return response()->json([
                'status'   => 200,
                'message'  => 'Charge supprimé avec succès',
            ]);
        }
    }
    
    public function ChangeDateCharge(Request $request)
    {
       

        Charge::where('id', $request->id)->update([
            'created_at' => Carbon::parse($request->date)->setTimeFromTimeString(Carbon::now()->toTimeString()), // Current time
            'updated_at' => Carbon::parse($request->date)->setTimeFromTimeString(Carbon::now()->toTimeString()), // Current time
        ]);

        return redirect()->to('Charge');
    }
}
