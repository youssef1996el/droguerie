<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Client;
use DB;
use DataTables;
use App\Models\Order;
use App\Models\ModePaiement;
use App\Models\Reglements;
use Carbon\Carbon;
use App\Models\Paiements;
use Auth;
class RecouverementController extends Controller
{
    public function index()
    {

        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page Recouverement')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CountInfo          = DB::table('infos as f')->join('company as c','c.id','=','f.idcompany')->where('c.status','=','Active')->count();
        if($CountInfo == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page Recouverement')
            ->with('body',"Parce qu'il n'y a pas de information");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        $Clients               = DB::table('clients as c')
                                ->join('company as ca','ca.id','=','c.idcompany')
                                ->where('ca.status','=','Active')
                                ->select(DB::raw('concat(c.nom," ",c.prenom) as client'),'c.id')
                                ->get();
        $ModePaiement          = DB::table("modepaiement as m")
                                ->join('reglements as r','r.idmode','=','m.id')
                                ->join('company as ca','ca.id','=','m.idcompany')
                                ->select('m.id','m.name')
                                ->groupBy('m.id')
                                ->get();
        return view('Recouverement.index')

        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('Clients'                 ,$Clients)
        ->with('ModePaiement'            ,$ModePaiement)

        ;
    }

    public function GetRecouvementClient(Request $request)
    {
        if($request->ajax())
        {

            $Recouvement = Order::select(
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
            ->where('clients.id','=',$request->idclient)
            ->where('company.status','=','Active')
            ->havingRaw('reste > 0')
            ->groupBy('orders.id')
            ->get();

            return DataTables::of($Recouvement)->addIndexColumn()->make(true);
        }
    }

    public function GetDataSelectedRecouvement(Request $request)
    {
        if($request->ajax())
        {
            $ids = $request->id;
            $Recouvement = Order::select(
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
            ->whereIn('orders.id', $ids) // Use whereIn to match multiple IDs
            ->havingRaw('reste > 0')
            ->groupBy('orders.id')
            ->get();

            return DataTables::of($Recouvement)->addIndexColumn()->make(true);
        }
    }

    public function StoreRecouvement(Request $request)
    {
        try
        {

            foreach ($request['ModePaiement'] as $item)
            {
                // extract id mode paiement credit
                $IdCredit = ModePaiement::where('name','crédit')->select('id')->first();
                // extract reglement

                $Reglements = Reglements::where('idorder',$item['idorder'])
                                        ->where('idmode' ,$IdCredit->id)
                                        ->select('id','total','idclient','idcompany')
                                        ->first();

                if ($Reglements && intval($Reglements->total) == $item['prix'])
                {
                    // Update reglement with mode paiement
                    $updateReglementModePaiement = Reglements::where('id', $Reglements->id)->update([
                        'datepaiement' => Carbon::now()->format('Y-m-d'),
                        'idmode'       => $item['mode'],
                    ]);

                    // Create Paiements record
                    $Paiements = Paiements::create([
                        'total'       => $item['prix'],
                        'idmode'      => $item['mode'],
                        'idreglement' => $Reglements->id,
                        'idcompany'   => $Reglements->idcompany,
                        'iduser'      => Auth::user()->id,
                    ]);
                }
                else if(intval($Reglements->total > $item['prix']))
                {
                    $updateReglementModePaiement = Reglements::where('id',$Reglements->id)->update([
                        'total'           => $item['prix'],
                        'datepaiement'    => Carbon::now()->format('Y-m-d'),
                        'idmode'          => $item['mode'],
                    ]);


                    // create new reglement
                    $CreateReglementCredit = Reglements::create([
                        'total'            => $item['reste'] - $item['prix'],
                        'idclient'         => $Reglements->idclient,
                        'idorder'          => $item['idorder'],
                        'idmode'           => $IdCredit->id,
                        'idcompany'        => $Reglements->idcompany,
                        'iduser'           => Auth::user()->id,
                    ]);

                    // create Paiements
                    $Paiements = Paiements::create([
                        'total'      => $item['prix'],
                        'idmode'     => $item['mode'],
                        'idreglement'=> $Reglements->id,
                        'idcompany'  => $Reglements->idcompany,
                        'iduser'     => Auth::user()->id,
                    ]);


                }
            }

            return response()->json([
                'status'        => 200,
            ]);
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
    }
}
