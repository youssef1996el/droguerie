<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Order;
use App\Models\Charge;
use App\Models\ModePaiement;
use App\Models\Versement;
use Carbon\Carbon;
use DB;
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
class EtatController extends Controller
{
    public function index(Request $request)
    {
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page etat')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();
        $today = Carbon::today(); // Get today's date using Carbon

        $espèce = Order::select( DB::raw('SUM(paiements.total) as totalPaye'))
        ->join('reglements', 'orders.id', '=', 'reglements.idorder')
        ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
        ->join('company' ,'company.id', '=','reglements.idcompany')
        ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
        ->where('modepaiement.name', '=', 'espèce')
        ->where('company.status','=','Active')
        ->whereDate('paiements.created_at', $today)
        ->first();


        $Charge = DB::table('charge')
                    ->join('company' ,'company.id', '=','charge.idcompany')
                    ->where('company.status','=','Active')
                    ->whereDate('charge.created_at',$today)->sum('total');

        $totalReglement = DB::table('reglements')
        ->select(DB::raw('SUM(reglements.total) as totalReglement'))
        ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
        ->join('company' ,'company.id', '=','reglements.idcompany')
        ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
        ->whereNotNull('reglements.datepaiement')
        ->where('company.status','=','Active')
        ->whereDate('reglements.datepaiement', $today)
        ->where('modepaiement.name', 'espèce')
        ->first();

        $totalRest = DB::table('modepaiement as m')
                                    ->join('reglements as r', 'm.id', '=', 'r.idmode')
                                    ->join('company' ,'company.id', '=','r.idcompany')
                                    ->join('orders as o', 'o.id', '=', 'r.idorder')
                                    ->where('m.name', '=', 'crédit')
                                    ->where('company.status','=','Active')
                                    ->whereDate('r.created_at', $today)
                                    ->selectRaw('SUM(r.total) as totalRest')
                                    ->first();


        $cheque  = Order::select( DB::raw('SUM(paiements.total) as totalPaye'))
        ->join('reglements', 'orders.id', '=', 'reglements.idorder')
        ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
        ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
        ->join('company' ,'company.id', '=','reglements.idcompany')
        ->where('modepaiement.name', '=', 'chèque')
        ->where('company.status','=','Active')
        ->whereDate('paiements.created_at', $today)
        ->first();
        $totalReglementCheque = DB::table('reglements')
                            ->select(DB::raw('SUM(reglements.total) as totalReglement'))
                            ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
                            ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
                            ->join('company' ,'company.id', '=','reglements.idcompany')
                            ->whereNotNull('reglements.datepaiement')
                            ->where('company.status','=','Active')
                            ->whereDate('reglements.created_at', $today)
                            ->where('modepaiement.name', 'chèque')
                            ->first();
        $virement  = Order::select( DB::raw('SUM(paiements.total) as totalPaye'))
                            ->join('reglements', 'orders.id', '=', 'reglements.idorder')
                            ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
                            ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
                            ->join('company' ,'company.id', '=','reglements.idcompany')
                            ->where('modepaiement.name', '=', 'virement')
                            ->where('company.status','=','Active')
                            ->whereDate('paiements.created_at', $today)
                            ->first();


        return view('Etat.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('espèce'                  ,$espèce)
        ->with('Charge'                  ,$Charge)
        ->with('totalRest'               ,$totalRest)
        ->with('cheque'                  ,$cheque)
        ->with('totalReglement'          ,$totalReglement)
        ->with('totalReglementCheque'    ,$totalReglementCheque)
        ->with('virement'                ,$virement)
        ;
    }

    public function SearchEtat(Request $request)
    {
        $dateDebut = $request->startDate;
        $datefin   = $request->endDate;
        $espèce = Order::select(DB::raw('SUM(paiements.total) as totalPaye'))
        ->join('reglements', 'orders.id', '=', 'reglements.idorder')
        ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
        ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
        ->join('company' ,'company.id', '=','reglements.idcompany')
        ->where('modepaiement.name', '=', 'espèce')
        ->where('company.status','=','Active')
        ->whereBetween(DB::raw('DATE(paiements.created_at)'), [$dateDebut, $datefin])
        ->first();


        $Charge = DB::table('charge')
        ->join('company' ,'company.id', '=','charge.idcompany')
        ->where('company.status','=','Active')
        ->whereBetween(DB::raw('DATE(charge.created_at)'), [$dateDebut, $datefin])
        ->sum('total');



        $totalRest = DB::table('modepaiement as m')
                    ->join('reglements as r', 'm.id', '=', 'r.idmode')
                    ->join('orders as o', 'o.id', '=', 'r.idorder')
                    ->join('company' ,'company.id', '=','r.idcompany')
                    ->where('m.name', '=', 'crédit')
                    ->where('company.status','=','Active')
                    ->whereBetween(DB::raw('DATE(r.created_at)'), [$dateDebut, $datefin])
                    ->selectRaw('SUM(r.total) as totalRest')
                    ->first();
        $cheque = Order::select(DB::raw('SUM(paiements.total) as totalPaye'))
                    ->join('reglements', 'orders.id', '=', 'reglements.idorder')
                    ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
                    ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
                    ->join('company' ,'company.id', '=','reglements.idcompany')
                    ->where('modepaiement.name', '=', 'chèque')
                    ->where('company.status','=','Active')
                    ->whereBetween(DB::raw('DATE(paiements.created_at)'), [$dateDebut, $datefin])
                    ->first();

        $totalReglement = DB::table('reglements')
                    ->select(DB::raw('SUM(reglements.total) as totalReglement'))
                    ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
                    ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
                    ->join('company' ,'company.id', '=','reglements.idcompany')
                    ->whereNotNull('reglements.datepaiement')
                    ->where('company.status','=','Active')
                    ->whereBetween(DB::raw('DATE(reglements.datepaiement)'), [$dateDebut, $datefin])
                    ->where('modepaiement.name', 'espèce')
                    ->first();
        $totalReglementCheque = DB::table('reglements')
                    ->select(DB::raw('SUM(reglements.total) as totalReglement'))
                    ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
                    ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
                    ->join('company' ,'company.id', '=','reglements.idcompany')
                    ->whereNotNull('reglements.datepaiement')
                    ->where('company.status','=','Active')
                    ->whereBetween(DB::raw('DATE(reglements.created_at)'), [$dateDebut, $datefin])
                    ->where('modepaiement.name', 'chèque')
                    ->first();

        $virement  = Order::select( DB::raw('SUM(paiements.total) as totalPaye'))
                    ->join('reglements', 'orders.id', '=', 'reglements.idorder')
                    ->join('paiements', 'reglements.id', '=', 'paiements.idreglement')
                    ->join('modepaiement', 'reglements.idmode', '=', 'modepaiement.id')
                    ->join('company' ,'company.id', '=','reglements.idcompany')
                    ->where('modepaiement.name', '=', 'virement')
                    ->where('company.status','=','Active')
                    ->whereBetween(DB::raw('DATE(paiements.created_at)'), [$dateDebut, $datefin])
                    ->first();
        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();
        return view('Etat.index')
        ->with('CompanyIsActive'         ,$CompanyIsActive)
        ->with('espèce'                  ,$espèce)
        ->with('Charge'                  ,$Charge)
        ->with('totalRest'               ,$totalRest)
        ->with('cheque'                  ,$cheque)
        ->with('totalReglement'          ,$totalReglement)
        ->with('totalReglementCheque'    ,$totalReglementCheque)
        ->with('virement'                ,$virement)
        ;
    }


    public function SearchEtatTable(Request $request)
    {
        if($request->ajax())
        {
            $today = Carbon::today(); // Get today's date using Carbon

            /* $etat = DB::table('lineorder as l')
            ->select(
                's.type',
                DB::raw('SUM(
                    CASE
                        WHEN l.idsetting IS NOT NULL THEN ROUND(l.qte / s.convert)
                        ELSE l.qte
                    END
                ) AS total_qte'),
                'p.name',
                DB::raw('CONCAT(c.nom, " ", c.prenom) AS client'),
                'o.created_at','o.total as montantvente','o.id'
            )
            ->leftJoin('setting as s', 's.id', '=', 'l.idsetting')
            ->join('orders as o', 'o.id', '=', 'l.idorder')
            ->join('clients as c', 'c.id', '=', 'o.idclient')
            ->join('products as p', 'p.id', '=', 'l.idproduct')
            ->where('l.qte', '>', 0)
            ->whereDate('o.created_at', '=', $today) // Correct usage of whereDate
            ->groupBy('s.type', 'o.idclient')
            ->get(); */
            $etat = DB::select("SELECT s.type,SUM(CASE  WHEN l.idsetting IS NOT NULL THEN ROUND(l.qte / s.convert) ELSE l.qte END) AS total_qte,
                        CONCAT(c.nom, ' ', c.prenom) AS client,o.created_at,o.total,o.id

                FROM lineorder l LEFT JOIN setting s ON s.id = l.idsetting JOIN orders o ON o.id = l.idorder JOIN clients c ON c.id = o.idclient

                where l.qte > 0 and Date(o.created_at) = ?
                GROUP BY s.type,o.idclient",[$today]);

        return DataTables::of($etat)
            ->addIndexColumn()
            ->addColumn('encrypted_id', function ($order) {
               /*  return $order->encrypted_id; */ // Ensure you have the correct column name
            })
            ->rawColumns(['encrypted_id'])
            ->make(true);
        }
    }

    public function EtatProduction(Request $request)
    {
        if($request->ajax())
        {
            $today = Carbon::today();
            $IdCredit = DB::table('modepaiement as m')
                ->join('company as c','c.id','=','m.idcompany')
                ->where('c.status','=','Active')
                ->where('m.name','=','crédit')
                ->value('m.id');
            $Production = DB::select('select id, sum(total) as total, sum(totalpaye) as totalpaye,sum(total - totalpaye) as reste,client,created_at from (
                                select o.id,total,0 as totalpaye,concat(c.nom," ",c.prenom) client,o.created_at from orders o , clients c  where c.id = o.idclient and date(o.created_at) =?
                                group by o.id
                                union all
                                select r.idorder,0 as totalvente,sum(r.total) as totalpaye ,concat(c.nom," ",c.prenom) client,o.created_at
                                from reglements r , orders o , clients c where o.idclient = c.id and o.id = r.idorder and date(r.created_at) = ? and r.idmode != ?
                                group by r.idorder) as t group by id',[$today,$today,$IdCredit]);

            return DataTables::of($Production)
            ->addIndexColumn()
            ->addColumn('encrypted_id', function ($order) {
                /*  return $order->encrypted_id; */ // Ensure you have the correct column name
            })
            ->rawColumns(['encrypted_id'])
            ->make(true);
        }
    }

    public function TotalUniteByDate(Request $request)
    {
        if($request->ajax())
        {
            $today = Carbon::today();
            $Data = DB::select("SELECT s.type,SUM(CASE  WHEN l.idsetting IS NOT NULL THEN ROUND(l.qte / s.convert) ELSE l.qte END) AS total_qte


                FROM lineorder l LEFT JOIN setting s ON s.id = l.idsetting JOIN orders o ON o.id = l.idorder

                where l.qte > 0 and Date(o.created_at) = ?
                GROUP BY s.type",[$today]);

                return DataTables::of($Data)
                ->addIndexColumn()
                ->addColumn('encrypted_id', function ($order) {
                    /*  return $order->encrypted_id; */ // Ensure you have the correct column name
                })
                ->rawColumns(['encrypted_id'])
                ->make(true);
        }
    }

    public function EtatByClient(Request $request)
    {
        $today = Carbon::today();
        $IdsCredit  = DB::table('modepaiement as m')
        ->join('company as c','c.id','m.idcompany')
        ->where('m.name','crédit')
        ->where('c.status','Active')
        ->value('m.id');


        $DataByClient = DB::select('SELECT CONCAT(c.nom, " ", c.prenom) AS client, p.name, l.qte, l.price,l.accessoire, l.total, l.idsetting,
        s.convert,IF(l.idsetting IS NOT NULL, CONCAT(ROUND(l.qte / s.convert), " ", s.type), l.qte) AS QteConvert,
        IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS QteConvertWithOutConcat,
            ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) AS remise,
        IF(
            ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
            l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
            l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
        ) AS price_new,
        IF(
            ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte)) < 0,
            l.price - (-1 * ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))),
            l.price + ROUND(l.accessoire / IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte))
        ) * IF(l.idsetting IS NOT NULL, ROUND(l.qte / s.convert), l.qte) AS totalnew
        FROM clients c JOIN orders o ON c.id = o.idclient
        JOIN lineorder l ON o.id = l.idorder
        JOIN products p ON l.idproduct = p.id
        LEFT JOIN setting s ON l.idsetting = s.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?',[$request->startDate,$request->endDate]);
                                   // dd($DataByClient);

        $DataByClient = collect($DataByClient)->groupBy('client')->toArray();

        // Calculate total and last row
        $TotalByClient = [];
        $LastRowByClient = [];
        foreach ($DataByClient as $client => $values) {
        $TotalByClient[$client] = array_sum(array_column($values, 'totalnew'));
        $LastRowByClient[$client] = end($values); // Get the last item
        }

        // Fetch credit data
        $DataByClientCredit = DB::select('SELECT r.total AS credit_total, CONCAT(c.nom, " ", c.prenom) AS client
                    FROM reglements r
                    JOIN clients c ON c.id = r.idclient
                    WHERE DATE(r.created_at) BETWEEN ? AND ? AND r.idmode = ?',[$request->startDate,$request->endDate,$IdsCredit]);

        $DataByClientPaye  = DB::select('select p.total as totalpaye,CONCAT(c.nom, " ", c.prenom) AS client from reglements r,paiements p,clients c
                                    where r.id = p.idreglement and c.id = r.idclient  and date(p.created_at) BETWEEN ? AND ? and r.datepaiement is null
                                     union all

                                     select p.total as totalpaye,concat(c.nom," ",c.prenom) as client from company co ,clients c,reglements r,paiements p where co.id = c.idcompany and c.id = r.idclient and r.id = p.idreglement and co.status = "Active" 

                                     and r.datepaiement between ? and ? and r.datepaiement = date(r.created_at)',[$request->startDate,$request->endDate,$request->startDate,$request->endDate]);

        $DataByClientCredit = collect($DataByClientCredit)->groupBy('client')->toArray();

        $DataByClientPaye   = collect($DataByClientPaye)->groupBy('client')->toArray();

        // Calculate total credit
        $TotalCreditByClient = [];
        // Calculate total paye
        $TotalPayeByClient = [];
        foreach($DataByClientPaye as $client => $values)
        {
            $TotalPayeByClient[$client] = array_sum(array_column($values,'totalpaye'));
        }
        foreach ($DataByClientCredit as $client => $values)
        {
            $TotalCreditByClient[$client] = array_sum(array_column($values, 'credit_total'));
        }
        $GrandTotal = array_sum($TotalByClient);

        // Calculate the sum of all TotalCreditByClient values
        $GrandTotalCredit = array_sum($TotalCreditByClient);

        $CompanyIsActive       = Company::where('status','Active')->select('title','id')->first();
        $pdf = new Dompdf();

        $DateStart = $request->startDate;
        $DateEnd   = $request->endDate;

        // Charge

        $Charge = DB::table('charge as ch')
        ->join('company as c', 'ch.idcompany', '=', 'c.id')
        ->select('ch.name', 'ch.total')
        ->where('c.status', 'Active')
        ->whereBetween(DB::raw('DATE(ch.created_at)'), [$DateStart, $DateEnd])
        ->get();
        // Versement
        $Versement = DB::table('versement as v')
        ->join('company as c','c.id','=','v.idcompany')
        ->where('c.status','=','Active')
        ->whereBetween(DB::raw('DATE(v.created_at)'), [$DateStart, $DateEnd])
        ->groupBy('v.comptable')
        ->select('v.comptable','v.total')
        ->get();

        /* ->sum('v.total') */
        /*******************************************************   Tableau Encaissement = vente sample + vente credit paye the same day *******/
        /* $TotalByModePaiement = DB::table('paiements as p')
            ->join('modepaiement as m', 'p.idmode', '=', 'm.id')
            ->join('reglements as r','r.id','=', 'p.idreglement')
            ->join('company as c', 'p.idcompany', '=', 'c.id')
            ->select(DB::raw('UPPER(m.name) as name'), DB::raw('SUM(p.total) as totalpaye'))
            ->where('c.status', 'Active')
            ->whereNull('r.datepaiement')
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$DateStart, $DateEnd])
            ->groupBy('p.idmode')
            ->get(); */

            $IdEspece    = DB::table('modepaiement as m')
            ->join('company as c','c.id','=','m.idcompany')
            ->where('c.status','Active')
            ->where('m.name','=','espèce')
            ->value('m.id');
            $VenteSample = DB::table('paiements as p')
            ->join('modepaiement as m', 'p.idmode', '=', 'm.id')
            ->join('reglements as r', 'r.id', '=', 'p.idreglement')
            ->join('company as c', 'p.idcompany', '=', 'c.id')
            ->select(DB::raw('UPPER(m.name) as name'), DB::raw('SUM(p.total) as totalpaye'))
            ->where('c.status', 'Active')
            ->whereNull('r.datepaiement')
            /* ->where('r.idmode',$IdEspece) */
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$DateStart, $DateEnd])
            ->groupBy('p.idmode');

        // Second subquery
        $Credit_Paye_The_same_day = DB::table('reglements as r')
            ->join('modepaiement as m', 'r.idmode', '=', 'm.id')
            ->join('company as c', 'r.idcompany', '=', 'c.id')
            ->select(DB::raw('UPPER(m.name) as name'), DB::raw('SUM(r.total) as totalpaye'))
            ->where('c.status', 'Active')
            ->whereRaw('r.datepaiement = DATE(r.created_at)')
            /* ->where('r.idmode',$IdEspece) */
            ->whereBetween(DB::raw('DATE(r.created_at)'), [$DateStart, $DateEnd])
            ->groupBy('r.idmode');

        // Union the subqueries
        $unionQuery = $VenteSample->unionAll($Credit_Paye_The_same_day);

        $TotalByModePaiement = DB::table(DB::raw("({$unionQuery->toSql()}) as t"))
                    ->mergeBindings($unionQuery)
                    ->select('name', DB::raw('SUM(totalpaye) as totalpaye'))
                    ->groupBy('name')
                    ->havingRaw('SUM(totalpaye) IS NOT NULL')
                    ->get();
        /*******************************************************  End Tableau Encaissement = vente sample + vente credit paye the same day *******/

        /******************************************************* Tableau Encaissement Credit  ******************************************/

        $Tableau_enccaissement_Credit = DB::table('clients as c')
        ->join('reglements as r', 'c.id', '=', 'r.idclient')
        ->join('company as co', 'co.id', '=', 'r.idcompany')
        ->select(DB::raw('concat(c.nom, " ", c.prenom) as client'), DB::raw('SUM(r.total) as total'))
        ->whereNotNull('r.datepaiement')
        ->where(DB::raw('Date(r.datepaiement)'), '!=', DB::raw('Date(r.created_at)'))
        ->where('co.status', 'Active')
        ->whereBetween(DB::raw('DATE(r.datepaiement)'), [$DateStart, $DateEnd])
        ->groupBy(DB::raw('concat(c.nom, " ", c.prenom)'))
        ->get();


        /*******************************************************  End Tableau Encaissement Credit  ******************************************/

        $TotalReglementPaye = DB::table('clients as c')
        ->join('reglements as r', 'c.id', '=', 'r.idclient')
        ->join('company as co', 'co.id', '=', 'r.idcompany')
        ->select(DB::raw('concat(c.nom, " ", c.prenom) as client'), DB::raw('SUM(r.total) as total'))
        ->whereNotNull('r.datepaiement')
        ->where(DB::raw('Date(r.datepaiement)'), '!=', DB::raw('Date(r.created_at)'))
        ->where('co.status', 'Active')
        ->whereBetween(DB::raw('DATE(r.datepaiement)'), [$DateStart, $DateEnd])
        ->sum('r.total');


        $SoldeCaisse  = DB::table('soldecaisse as s')
        ->join('company as c','c.id','=','s.idcompany')
        ->where('c.status','Active')
        ->whereBetween(DB::raw('DATE(s.created_at)'),[$DateStart,$DateEnd])
        ->sum('s.total');

        $Paiement_Employee = DB::table('personnels as p')
        ->join('reglementspersonnels as r', 'p.id', '=', 'r.idpersonnel')
        ->join('company as c', 'p.idcompany', '=', 'c.id')
        ->select(DB::raw('concat(p.nom, " ", p.prenom) as employe'), 'r.total')
        ->where('c.status', 'Active')
        ->whereBetween(DB::raw('DATE(r.created_at)'),[$DateStart,$DateEnd])
        ->get();



        $reste =  ( $TotalByModePaiement->sum('totalpaye') + $TotalReglementPaye +  $SoldeCaisse ) - ($Charge->sum('total') + $Versement->sum('total') + $Paiement_Employee->sum('total'));

        $TotalPaiement = DB::select("select sum(p.total) as total from paiements p ,company c where c.id = p.idcompany and c.status = 'Active' and date(p.created_at) between  ? and ? and p.idmode = ? ;",
        [$DateStart,$DateEnd,$IdEspece]);
        $SoldeDepart   = DB::select('select sum(s.total) as solde from soldecaisse s ,company c where s.idcompany = c.id and c.status = "Active" and date(s.created_at) between ? and ?',[$DateStart,$DateEnd]);
        $Charge   = DB::select('select sum(c.total)as charge from charge c, company co where c.idcompany = co.id and co.status = "Active"  and date(c.created_at) between ? and ? ',[$DateStart,$DateEnd]);
        $Versement = DB::select('select sum(v.total) as versement from versement v , company c where v.idcompany = c.id and c.status = "Active" and date(v.created_at) between ? and ? ',[$DateStart,$DateEnd]);
        $Reste  =($TotalPaiement[0]->total + $SoldeDepart[0]->solde)  - ($Charge[0]->charge + $Versement[0]->versement);
    // Load view and render HTML
    $html = view('Etat.EtatTEST', compact(
        'CompanyIsActive',
        'DataByClient',
        'TotalByClient',
        'LastRowByClient',
        'TotalCreditByClient',
        'GrandTotal',
        'GrandTotalCredit',
        'DateStart',
        'DateEnd',
        'Charge',
        'Versement',
        'TotalByModePaiement',
        'TotalPayeByClient',
        'TotalReglementPaye',
        'SoldeCaisse',
        'reste',
        'Tableau_enccaissement_Credit',
        'Paiement_Employee',
        'Reste'



    ))->render();

    // Load HTML to dompdf
    $pdf->loadHtml($html);

    // (Optional) Setup default paper size and orientation
    $pdf->setPaper('A4', 'portrait');

    // Render PDF (first pass)
    $pdf->render();

    // Stream PDF to browser (inline view), 'D' to force download
    return $pdf->stream('Report.pdf', ['Attachment' => 1]);






    }
}
