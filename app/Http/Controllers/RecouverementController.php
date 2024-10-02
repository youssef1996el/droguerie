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
use App\Models\Cheques;
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
                               /*  ->join('reglements as r','r.idmode','=','m.id') */
                                ->join('company as ca','ca.id','=','m.idcompany')
                                ->select('m.id','m.name')
                                ->where('ca.status','Active')
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
            $CompanyIsActive       = Company::where('status','Active')->select('id')->first();
            $idModePaiement        = DB::table('modepaiement as m')
            ->join('company as c','c.id','=','m.idcompany')
            ->where('c.id',$CompanyIsActive->id)
            ->where('m.name','=','crédit')
            ->value('m.id');
            /* $Recouvement = Order::select(
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
            ->get(); */
            $Recouvement = DB::table('reglements as r')
            ->join('orders as o', 'r.idorder', '=', 'o.id')
            ->join('clients as c', 'o.idclient', '=', 'c.id')
            ->join('company as co', 'c.idcompany', '=', 'co.id')
            ->join('users as u', 'r.iduser', '=', 'u.id')
            ->select(
                'o.id',
                'o.total AS totalvente',
                DB::raw("
                    CASE
                        WHEN
                            CASE
                                WHEN (o.total - SUM(r.total)) < 0 THEN (o.total + SUM(r.total))
                                ELSE (o.total - SUM(r.total))
                            END = r.total THEN 0
                        ELSE
                            CASE
                                WHEN (o.total - SUM(r.total)) < 0 THEN (o.total + SUM(r.total))
                                ELSE (o.total - SUM(r.total))
                            END
                    END AS totalpaye
                "),
                DB::raw('SUM(r.total) as reste'),
                DB::raw("CONCAT(c.nom, ' ', c.prenom) as client"),
                'o.idfacture',
                'u.name as user',
                'co.title as company',
                DB::raw("DATE_FORMAT(o.created_at, '%Y-%m-%d') as created_at_formatted")
            )
            ->where('co.status', 'Active')
            ->where('r.idclient', $request->idclient)
            ->where('r.idmode', $idModePaiement)
            ->groupBy('o.id', )
            ->having('reste', '>', 0)
            ->get();
            return DataTables::of($Recouvement)->addIndexColumn()->make(true);
        }
    }

    public function GetDataSelectedRecouvement(Request $request)
    {
        if($request->ajax())
        {
            $ids = $request->id;
            /* $Recouvement = Order::select(
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
            ->whereIn('orders.id', $ids)
            ->havingRaw('reste > 0')
            ->groupBy('orders.id')
            ->get(); */

            $IdCredit = DB::table('modepaiement as m')
            ->join('company as c','c.id','=','m.idcompany')
            ->where('c.status','Active')
            ->where('m.name','crédit')
            ->select('m.id')
            ->first();

            $Recouvement = DB::table('reglements as r')
            ->join('orders as o', 'r.idorder', '=', 'o.id')
            ->join('clients as c', 'o.idclient', '=', 'c.id')
            ->join('company as co', 'c.idcompany', '=', 'co.id')
            ->join('users as u', 'r.iduser', '=', 'u.id')
            ->select(
                'o.id',
                'o.total AS totalvente',
                DB::raw("
                    CASE
                        WHEN
                            CASE
                                WHEN (o.total - SUM(r.total)) < 0 THEN (o.total + SUM(r.total))
                                ELSE (o.total - SUM(r.total))
                            END = r.total THEN 0
                        ELSE
                            CASE
                                WHEN (o.total - SUM(r.total)) < 0 THEN (o.total + SUM(r.total))
                                ELSE (o.total - SUM(r.total))
                            END
                    END AS totalpaye
                "),
                DB::raw('SUM(r.total) as reste'),
                DB::raw("CONCAT(c.nom, ' ', c.prenom) as client"),
                'o.idfacture',
                'u.name as user',
                'co.title as company',
                DB::raw("DATE_FORMAT(o.created_at, '%Y-%m-%d') as created_at_formatted")
            )
            ->whereIn('o.id', $ids)
            ->where('r.idmode', $IdCredit->id)
            ->havingRaw('reste > 0')
            ->groupBy('o.id')
            ->get();



            return DataTables::of($Recouvement)->addIndexColumn()->make(true);
        }
    }

    public function StoreRecouvement(Request $request)
    {
        try
        {

            $hasCheque = false;
            foreach($request['ModePaiement'] as $item)
            {
                $ModePaiement = ModePaiement::where('id',$item['mode'])->select('name')->get();
                foreach($ModePaiement as $item1)
                {
                    if($item1->name === "chèque")
                    {
                        $hasCheque = true;
                    }
                }
            }
            
            
    
            foreach ($request['ModePaiement'] as $item)
            {
                // extract id mode paiement credit
                //$IdCredit = ModePaiement::where('name','crédit')->select('id')->first();
                $IdCredit = DB::table('modepaiement as m')
                ->join('company as c','c.id','=','m.idcompany')
                ->where('c.status','Active')
                ->where('m.name','crédit')
                ->select('m.id')
                ->first();
                // extract reglement

                $Reglements = Reglements::where('idorder',$item['idorder'])
                                        ->where('idmode' ,$IdCredit->id)
                                        ->select('id','total','idclient','idcompany')
                                        ->first();
                                        $Reglements = Reglements::selectRaw('reglements.*, SUM(total) as total')
                                        ->where('idorder', $item['idorder'])
                                        ->where('idmode', $IdCredit->id)
                                        ->first();


                if ($Reglements && floatval($Reglements->total) == $item['prix'])
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

                    // extract status reglement if has solde de départ or not
                    $Status                = Reglements::where('id',$Reglements->id)->value('status');
                    // create new reglement
                    $CreateReglementCredit = Reglements::create([
                        'total'            => $item['reste'] - $item['prix'],
                        'idclient'         => $Reglements->idclient,
                        'idorder'          => $item['idorder'],
                        'idmode'           => $IdCredit->id,
                        'idcompany'        => $Reglements->idcompany,
                        'iduser'           => Auth::user()->id,
                        'status'           => $Status,
                    ]);

                    // create Paiements
                    $Paiements = Paiements::create([
                        'total'      => $item['prix'],
                        'idmode'     => $item['mode'],
                        'idreglement'=> $Reglements->id,
                        'idcompany'  => $Reglements->idcompany,
                        'iduser'     => Auth::user()->id,
                    ]);

                    if($hasCheque)
                    {
                        // insert cheque
                        $Cheques = Cheques::create([
                            'numero'                 => $request->numero,
                            'datecheque'             => $request->datecheque,
                            'datepromise'            => $request->datepromise,
                            'montant'                => $request->montant,
                            'type'                   => $request->type,
                            'name'                   => $request->name,
                            'bank'                   => $request->bank,
                            'idorder'                => $item['idorder'],
                        ]);
                    }


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


    public function Suivirecouverement(Request $request)
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page Suivi Recouverement')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        if($request->ajax())
        {
            $Recouvement           = DB::table('clients as c')
            ->join('reglements as r', 'c.id', '=', 'r.idclient')
            ->join('paiements as p', 'r.id', '=', 'p.idreglement')
            ->join('company as co', 'p.idcompany', '=', 'co.id')
            ->select('r.id',DB::raw('concat(c.nom, " ", c.prenom) as client'),DB::raw('sum(p.total) as total'),
                    DB::raw('date(p.created_at) as date_paye'),DB::raw('date(r.created_at) as date_credit'),'co.title')
            ->where('co.status', 'Active')
            ->whereRaw('r.datepaiement != date(r.created_at)')
            ->groupBy('p.id')
            ->get();

            return DataTables::of($Recouvement)->addIndexColumn()->make(true);
        }

        return view('Recouverement.suivi')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }
}
