<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Cheques;
use Auth;
use DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
class ChequeController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $Data_Cheque = DB::table('cheques as c')
                ->join('orders as o', 'o.id', '=', 'c.idorder')
                ->join('company as co', 'co.id', '=', 'o.idcompany')
                ->where('co.status', 'Active')
                ->select('c.*');

            if ($request->filled('startDate') && $request->filled('endDate'))
            {
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');

                $Data_Cheque->whereDate('c.created_at', '>=', $startDate)
                            ->whereDate('c.created_at', '<=', $endDate);
            }

            $Data_Cheque = $Data_Cheque->get();

            $Data_Cheque->transform(function ($cheque) {
                $cheque->encryptedId = Crypt::encrypt($cheque->idorder);
                return $cheque;
            });

            return DataTables::of($Data_Cheque)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="action-btn d-flex">';

                    if (auth()->user()->can('clients-voir')) {
                        $btn .= '<a href="' . url("ShowOrder/{$row->encryptedId}") . '" class="text-light view" value="' . $row->id . '">
                                    <i class="ti ti-eye fs-5 border rounded-2 bg-info p-1" title="Voir le client"></i>
                                </a>';
                    }

                    if (auth()->user()->can('clients-modifier')) {
                        $btn .= '<a href="#" class="text-light edit ms-2" value="' . $row->id . '">
                                    <i class="ti ti-edit fs-5 border rounded-2 bg-success p-1" title="Modifier le client"></i>
                                </a>';
                    }



                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $CompanyIsActive = Company::where('status', 'Active')->select('title')->first();
        return view('Cheque.index')
            ->with('CompanyIsActive', $CompanyIsActive);
    }

    public function ChangeStatus(Request $request)
    {
        $Update_Cheque = Cheques::where('id',$request->id)->update([
            'status'      => $request->status,
        ]);
        return response()->json([
            'status'    => 200,
            'message'   => 'Chèque modifier avec succès'
        ]);
    }
}
