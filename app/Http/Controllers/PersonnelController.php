<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\Company;
use App\Models\Personnel;
use App\Models\ReglementPersonnel;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class PersonnelController extends Controller
{
    public function index() 
    {
        // check Company is create
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page personnel')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        $personnel = DB::table('personnels as p')
                        ->join('company as c','c.id',"=","p.idcompany")
                        ->where('c.status','=','Active')
                        ->select("p.nom","p.prenom","p.id")
                        ->get();
        return view('Personnel.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('Personnel'              ,$personnel)

        ;
    }

    public function StorePersonnel(Request $request)
    {
        $validator=validator::make($request->all(),[
            'nom'                     =>'required',
            'prenom'                  =>'required',
            'telephone'               =>'required',
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

            $Personnel = Personnel::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Personnel créée avec succès',
            ]);
        }
    }

    public function getFichePersonnel(Request $request)
    {
        if ($request->ajax())
        {
            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
            $personnel = DB::table('personnels as p')
                        ->join('company as c','c.id',"=","p.idcompany")
                        ->join('users as u','u.id',"=","p.iduser")
                        ->where('p.idcompany',$CompanyIsActive->id)
                        ->select("p.nom","p.prenom","p.cin","p.adresse","p.ville","p.telephone","c.title","p.id","u.name",DB::raw('date(p.created_at) as date_created'))
                        ->get();


            return DataTables::of($personnel)->addIndexColumn()->addColumn('action', function ($row)
            {
                $encryptedId = Crypt::encrypt($row->id);
                $btn = '<div class="action-btn d-flex">';

                // View button with permission check
                if (auth()->user()->can('personnel-voir')) {
                    $btn .= '<a href="' . url('SuiviPersonnel/' . $encryptedId) . '" class="text-primary view" value="' . $row->id . '">
                                <i class="ti ti-eye fs-5" title="Voir le personnel"></i>
                            </a>';
                }

                // Edit button with permission check
                if (auth()->user()->can('personnel-modifier')) {
                    $btn .= '<a href="#" class="text-dark edit ms-2" value="' . $row->id . '">
                                <i class="ti ti-edit fs-5" title="Modifier le personnel"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
        }
    }

    public function UpdatePersonnel(Request $request)
    {
        $validator=validator::make($request->all(),[
            'nom'                     =>'required',
            'prenom'                  =>'required',
            'telephone'               =>'required',
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

            $Personnel = Personnel::where('id',$data['id'])->update([
                'nom'               => $data['nom'],
                'prenom'            => $data['prenom'],
                'adresse'           => $data['adresse'],
                'cin'               => $data['cin'],
                'ville'             => $data['ville'],
                'telephone'         => $data['telephone'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Personnel modifier avec succès',
            ]);
        }
    }
    public function SuiviPersonnel($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        $personnel             = Personnel::where('id',$id)->get();
        $dataLine              = DB::table('personnels as p')
        ->join('reglementspersonnels as r','r.idpersonnel','=','p.id')
        ->where('p.id',$id)
        ->select('p.*','r.total',DB::raw('date(r.created_at) as datereglement'))
        ->get();
        return view('Personnel.suivi')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('id'                      ,$id)
        ->with('personnel'               ,$personnel)
        ->with('dataLine'               ,$dataLine)

        ;
    }

    public function SuiviPersonnelWithoutID()
    {
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        $personnel = DB::table('personnels as p')
                        ->join('company as c','c.id',"=","p.idcompany")
                        ->where('c.status','=','Active')
                        ->select("p.nom","p.prenom","p.id")
                        ->get();
        return view('Personnel.suivi')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('personnel'               ,$personnel)
        ;
    }

    public function getFichePersonnelByPersonnel(Request $request)
    {
        if($request->ajax())
        {
            $dataLine              = DB::table('personnels as p')
            ->join('reglementspersonnels as r','r.idpersonnel','=','p.id')
            ->where('p.id',$request->IdPersonnel)
            ->select('p.*','r.total',DB::raw('date(r.created_at) as datereglement'),'r.created_at as created_With_Time_Zone','r.id as idreglementpersonnel')
            ->get();
            return DataTables::of($dataLine)->addIndexColumn()->addColumn('action', function ($row)
            {
                $today = Carbon::today();
                $createdOrder = Carbon::parse($row->created_With_Time_Zone);
                $btn = '<div class="action-btn d-flex">';
                if ($createdOrder->isSameDay($today)) 
                {
                    $btn .= '<a href="#" class="text-light ms-2 Trash" value="' . $row->idreglementpersonnel . '">
                                <i class="ti ti-shopping-cart-off fs-5 border rounded-2 bg-danger p-1" title="Annuler regelement"></i>
                            </a>';
                            
                } 
                $btn .= '</div>';
                return $btn;
            })->rawColumns(['action'])->make(true);
            //return DataTables::of($dataLine)->addIndexColumn()->rawColumns([])->make(true);

        }
    }
    public function deletereglementpersonnel(Request $request)
    {
        
        $ReglementPersonnel = ReglementPersonnel::where('id',$request->id)->delete();
        if($ReglementPersonnel)
        {
            return response()->json([
                'status'   => 200,
            ]);
        }
        else
        {
            return response()->json([
                'status'   => 404,
            ]);
        }
    }

    public function StorePaiementPersonnel(Request $request)
    {
        $validator=validator::make($request->all(),[
            'idpersonnel'            =>'required',
            'total'                  =>'required',

        ]);
         // Override default error messages
        $customMessages = [
            'required' => 'Le champ :attribute est requis.',
        ];
        $validator->setAttributeNames([
            'idpersonnel' => 'personnel'
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

            // Sanitize inputs if necessary
            $data = $request->all();

            $data = array_map('trim', $data);

            $ReglementPersonnel = ReglementPersonnel::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Paiement créée avec succès',
            ]);
        }
    }
}
