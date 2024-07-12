<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Info;
use App\Models\Company;
use DB;
use Auth;
use DataTables;
use Illuminate\Support\Facades\Validator;
class InfoController extends Controller
{
    public function index()
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page etat')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();
        return view('Info.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function FetchInformation(Request $request)
    {
        if($request->ajax())
        {
            $Data = DB::table('infos as i')
            ->join('company as c','c.id','=','i.idcompany')
            ->where('c.status','=','Active')
            ->select('i.*','c.title as title_company')
            ->get();
            return DataTables::of($Data)->addIndexColumn()->addColumn('action', function ($row)
            {
                $btn = '<div class="action-btn d-flex">';

                // Edit button with permission check
                if (auth()->user()->can('information-modifier')) {
                    $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier les informations"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
    }

    public function StoreInformation(Request $request)
    {
        $validator = validator::make($request->all(), [
            'title' => 'required',
            'ice' => 'required|numeric',
            'phone' => 'required|numeric',
            'fix' => 'required|numeric',
            'cnss' => 'required|numeric',
            'rc' => 'required|numeric',
            'if' => 'required|numeric',
            'address' => 'required',
        ]);

        // Custom error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
            'numeric' => 'Le champ :attribute doit être numérique.',
        ];

        $validator->setCustomMessages($customMessages);

        // Custom attribute names
        $customAttributes = [
            'title'             => 'titre',
            'ice'               => 'ICE',
            'phone'             => 'téléphone',
            'fix'               => 'fixe',
            'cnss'              => 'CNSS',
            'rc'                => 'RC',
            'if'                => 'IF',
            'address'           => 'adresse',
        ];

        $validator->setAttributeNames($customAttributes);

        // Check if validation fails
        if ($validator->fails())
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

            $Info = Info::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Info créée avec succès',
            ]);
        }
    }
    public function UpdateInformation(Request $request)
    {

        $validator = validator::make($request->all(), [
            'title' => 'required',
            'ice' => 'required|numeric',
            'phone' => 'required|numeric',
            'fix' => 'required|numeric',
            'cnss' => 'required|numeric',
            'rc' => 'required|numeric',
            'if' => 'required|numeric',
            'address' => 'required',
        ]);

        // Custom error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
            'numeric' => 'Le champ :attribute doit être numérique.',
        ];

        $validator->setCustomMessages($customMessages);

        // Custom attribute names
        $customAttributes = [
            'title'             => 'titre',
            'ice'               => 'ICE',
            'phone'             => 'téléphone',
            'fix'               => 'fixe',
            'cnss'              => 'CNSS',
            'rc'                => 'RC',
            'if'                => 'IF',
            'address'           => 'adresse',
        ];

        $validator->setAttributeNames($customAttributes);

        // Check if validation fails
        if ($validator->fails())
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


            $Info = Info::where('id',$data['id'])->update([
                'title'         => $data['title'],
                'ice'           => $data['ice'],
                'cnss'          => $data['cnss'],
                'rc'            => $data['rc'],
                'if'            => $data['if'],
                'address'       => $data['address'],
                'phone'         => $data['phone'],
                'fix'           => $data['fix'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Info modifier avec succès',
            ]);
        }
    }
}
