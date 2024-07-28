<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Order;
use App\Models\Charge;
use App\Models\ModePaiement;
use Carbon\Carbon;
use DB;
class EtatController extends Controller
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
}
