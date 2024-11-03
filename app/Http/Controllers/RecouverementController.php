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
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Crypt;

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
                                        /* $Reglements = Reglements::selectRaw('reglements.*, SUM(total) as total')
                                        ->where('idorder', $item['idorder'])
                                        ->where('idmode', $IdCredit->id)
                                        ->first(); */


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
                    DB::raw('date(p.created_at) as date_paye'),DB::raw('date(r.created_at) as date_credit'),'co.title','p.id as idpaiement')
            ->where('co.status', 'Active')
            ->whereRaw('r.datepaiement != date(r.created_at)')
            ->groupBy('p.id')
            ->get();

            /* return DataTables::of($Recouvement)->addIndexColumn()->make(true); */

            return DataTables::of($Recouvement)->addIndexColumn()->addColumn('action', function ($row)
            {
                $encryptedId = Crypt::encrypt($row->idpaiement);

                $btn = '<div class="action-btn d-flex">';

                
                /* if (auth()->user()->can('clients-supprimer')) { */
                    $btn .= '<a href="#" class="text-light trashP_Paiement ms-2" value="' . $row->idpaiement . '">
                                <i class="ti ti-trash fs-5 border rounded-2 bg-danger p-1" title="Supprimer le paiement"></i>
                            </a>';

                /* } */
                $btn .='</div>';


                return $btn;
            })->rawColumns(['action'])->make(true);
        }

        return view('Recouverement.suivi')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }

    public function Listcredit()
    {
        $ListCredit = DB::table('clients as c')
        ->join('company as co', 'c.idcompany', '=', 'co.id')
        ->join('orders as o', 'c.id', '=', 'o.idclient')
        ->join('reglements as r', 'o.id', '=', 'r.idorder')
        ->join('modepaiement as m', 'r.idmode', '=', 'm.id')
        ->select(DB::raw('CONCAT(c.nom, " ", c.prenom) as client'), DB::raw('SUM(r.total) as total'), 'o.created_at', 'm.name')
        ->where('m.name', 'crédit')
        ->where('co.status', 'Active')
        ->groupBy('client', 'o.created_at', 'm.name')
        ->orderBy('client')
        ->get();

        // Calculate grand total
        $grandTotal = $ListCredit->sum('total');
        $imagePath = public_path('images/R.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $pdf = app('dompdf.wrapper');
        $context = stream_context_create([
            'ssl'  => [
                'verify_peer'  => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE,
            ]
        ]);
        $html = view('Recouverement.Listcredit', [
            'ListCredit'        => $ListCredit,
            'imageData'     => $imageData,
            'grandTotal' => $grandTotal,
        ])->toArabicHTML();
    
        // تحميل HTML إلى PDF
        $pdf = Pdf::loadHTML($html)->output();
    
        // تحديد رؤوس الاستجابة
        $headers = [
            "Content-type" => "application/pdf",
        ];
        
        return response()->streamDownload(
            fn() => print($pdf),
            "List_de_crédit.pdf",
            $headers
        );
    }

    public function DeletePaiement(Request $request)
    {
        // extraxct id regelement
        $IdRegelement = Paiements::where('id',$request->idPaiement)->value('idreglement');
        
        // check if idregelement is double
        $check_Double = Paiements::where('idreglement',$IdRegelement)->count();
        
        
        if($check_Double > 1)
        {
            // Delete Paiement
            $Delete_Paiement = Paiements::where('id',$request->idPaiement)->delete();
            return response()->json([
                'status' => 200,
                'message' => 'supprimé avec succès',
            ]);
        }
        else
        {
            
            // extract id mode credit by company and extract credit this order
            $IdCredit = DB::table('modepaiement as m')
            ->join('company as c','c.id','=','m.idcompany')
            ->where('c.status','=','Active')
            ->where('m.name','=','crédit')
            ->value('m.id');
            // extract idorder from reglement
            $ExtractIdorderFromReglement = DB::table('reglements as r')
            ->join('orders as o','o.id','=','r.idorder')
            ->where('r.id',$IdRegelement)
            ->select('r.idorder')
            ->first();
            // check if has regelement credit
            $check_Regelement_credit = DB::table('reglements as r')
            ->where('r.idorder','=',$ExtractIdorderFromReglement->idorder)
            ->where('r.idmode','=',$IdCredit)
            ->get();
            
            if($check_Regelement_credit->isEmpty())
            {
                // get information reglement
                $getInformationReglement = Reglements::where('id',$IdRegelement)->first();
                
                
                // update Reglement to credit
                $UpdateReglement = Reglements::where('id',$IdRegelement)->update([
                    'idmode'  => $IdCredit,
                    'updated_at' =>Carbon::now(),
                ]);
                //delete Paiement
                $Delete_Paiement = Paiements::where('id',$request->idPaiement)->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'supprimé avec succès',
                ]);
            }
            else
            {
                // extract total paiement 
                $Total_Paiement = Paiements::where('id',$request->idPaiement)->value('total');
                
                $Total_Paiement = floatval($Total_Paiement);
               
                //delete paiement
                $Delete_Paiement = Paiements::where('id',$request->idPaiement)->delete();
                
               // extract total reglement content for credit*
               $Total_CreditFromReglement=DB::table('reglements as r')
               ->where('r.idorder','=',$ExtractIdorderFromReglement->idorder)
               ->where('r.idmode','=',$IdCredit)
               ->first();
               
                // delete Reglement 
                $Delete_Reglement = Reglements::where('id',$IdRegelement)->delete();


                
               
                $UpdateReglement = Reglements::where('id',$Total_CreditFromReglement->id)->update([
                    'total'  => $Total_Paiement + $Total_CreditFromReglement->total,

                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'supprimé avec succès',
                ]);


            }
            
        }
    }

    public function ListPaiement(Request $request)
    {
        if($request->ajax())
        {
            $results = DB::table('clients as c')
            ->join('reglements as r', 'c.id', '=', 'r.idclient')
            ->join('paiements as p', 'r.id', '=', 'p.idreglement')
            ->join('modepaiement as m', 'p.idmode', '=', 'm.id')
            ->join('company as co', 'p.idcompany', '=', 'co.id')
            ->select(
                DB::raw("CONCAT(c.nom, ' ', c.prenom) as clients"),
                'p.total',
                'p.created_at',
                DB::raw("IF(r.datepaiement IS NULL, 'Vente sample', 'paiement crédit') as status"),
                'p.id',
                'm.name'
            )
            ->where('co.status', 'Active')
            ->orderBy('p.id', 'desc')
            ->get();
            return DataTables::of($results)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('Recouverement.ListPaiement')
        ->with('CompanyIsActive'         ,$CompanyIsActive);
    }
}
