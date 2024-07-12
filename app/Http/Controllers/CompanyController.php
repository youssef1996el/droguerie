<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\Crypt;
use DB;

class CompanyController extends Controller
{
    public function index()
    {
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('Company.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function getCompany()
    {
        $Company = Company::all();
        return DataTables::of($Company)->addIndexColumn()->addColumn('action', function ($row)
        {
            $encryptedId = Crypt::encrypt($row->id);

            $btn =  '<div class="action-btn d-flex">

                        <a href="#" class="text-dark edit ms-2"  value="' . $row->id . '">
                            <i class="ti ti-edit fs-6 text-success " title="Modifier le Compagnie"></i>
                        </a>
                    </div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    public function StoreCompany(Request $request)
    {
        $validator=validator::make($request->all(),[
            'title'                     =>'required',
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
            $CountCompany   = Company::count();
            if($CountCompany == 0)
            {
                $data['status'] = 'Active';
            }
            else
            {
                $data['status'] = 'Désactivé';
            }

            $data = array_map('trim', $data);


            // Create Client
            $Company = Company::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Compagnie créée avec succès',
            ]);
        }
    }

    public function ShowCompany(Request $request)
    {

        $Company = Company::find($request->IdCompany);
        if($Company)
        {
            return response()->json([
                'status'  => 200,
                'data'    => $Company
            ]);
        }
    }

    public function EditCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'status' => 'required|in:Active,Désactivé',
        ], [
            'required' => 'Le champ :attribute est requis.',
        ]);

        $validator->after(function ($validator) use ($request) {
            // Check if the status is "désactivé"
            if ($request->status === 'Désactivé')
            {
                // count array company
                $CountCompany = Company::count();

                if($CountCompany == 1)
                {
                    $validator->errors()->add('status', 'Au moins un doit être active');
                }
                else
                {
                    // Count active records in the company table
                    $activeCompaniesCount = Company::where('status', 'Active')->count();

                    // If no active companies exist, add a custom error
                    if ($activeCompaniesCount === 0)
                    {
                        $validator->errors()->add('status', 'Au moins un doit être active');
                    }
                }

            }
        });

        // Set custom messages after the after hook
        $validator->setCustomMessages([
            'required' => 'Le champ :attribute est requis.',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }
        else
        {
            $data = $request->all();
            $data['iduser'] = Auth::user()->id;
            $data = array_map('trim', $data);
            // update Everything to Désacativé
            $CheckIsCompanieActive = DB::select('select count(*) as c from company where status = "Active"');
            if($CheckIsCompanieActive[0]->c !=0)
            {
                DB::select('update company set status = "Désactivé" where status = "Active" ');
            }
            $Company  = Company::where('id',$request->IdCompany)->update([
                'title'       => $data['title'],
                'status'      => $data['status'],
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Compagnie modifier avec succès',
            ]);
        }
    }
}
