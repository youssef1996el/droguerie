<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Carbon\Carbon;
use DB;
use App\Models\Order;
use Illuminate\Support\Facades\Crypt;
use DataTables;
class BordereauController extends Controller
{
    public function index()
    {
        // check Company is create
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page bordereau journalier de production')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CountInfo          = DB::table('infos as f')->join('company as c','c.id','=','f.idcompany')->where('c.status','=','Active')->count();
        if($CountInfo == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page sbordereau journalier de production')
            ->with('body',"Parce qu'il n'y a pas de information");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('Bordereau.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive);

    }

    public function GetMyBordereau(Request $request)
    {
        if($request->ajax())
        {
            $query = Order::select(
                'orders.id',
                'orders.total AS totalvente',
                DB::raw('SUM(reglements.total) AS totalpaye'),
                DB::raw('(orders.total - SUM(reglements.total)) AS reste'),
                DB::raw('CONCAT(clients.nom, " ", clients.prenom) AS client'),
                'company.title AS company',
                'users.name AS user',
                'orders.idfacture',
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") as created_at_formatted')
            )
            ->join('reglements', 'reglements.idorder', '=', 'orders.id')
            ->join('paiements', 'paiements.idreglement', '=', 'reglements.id')
            ->join('modepaiement', 'modepaiement.id', '=', 'paiements.idmode')
            ->join('clients', 'clients.id', '=', 'orders.idclient')
            ->join('company', 'company.id', '=', 'orders.idcompany')
            ->join('users', 'users.id', '=', 'orders.iduser')
            ->leftJoin('factures', 'factures.id', '=', 'orders.idfacture')
            ->where('company.status','=','Active')
            ->groupBy('orders.id');
            // Check if startDate and endDate are provided
            if ($request->filled('startDate') && $request->filled('endDate')) {

                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');

                // Filter orders between startDate and endDate
                $query->whereDate('orders.created_at', '>=', $startDate)
                    ->whereDate('orders.created_at', '<=', $endDate);
            }

            // Execute the query
            $orders = $query->get();

            $orders->transform(function ($order) {
                $order->encryptedId = Crypt::encrypt($order->id);
                return $order;
            });

            // Prepare DataTables response
            return DataTables::of($orders)
                    ->addIndexColumn()
                    ->addColumn('encrypted_id', function ($order) {
                        return $order->encryptedId;
                    })
                    ->rawColumns(['encrypted_id'])
                    ->make(true);
           /*  return DataTables::of($orders)->addIndexColumn()->make(true); */


        }
    }
}

