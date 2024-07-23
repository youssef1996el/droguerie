<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Company;
use App\Models\Paiements;
use App\Models\Client;
use App\Models\Order;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $NameUser              = Auth::user()->name;
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        $SoldeCaisseToday      = DB::table('soldecaisse as s')
        ->join('company as c','c.id','=','s.idcompany')
        ->where('c.status','Active')
        ->whereDate('s.created_at', Carbon::today())
        ->value('s.total');
        // extract how much total today

        $totalToday = DB::table('paiements as p')
                    ->join('company as c','c.id','=','p.idcompany')
                    ->where('c.status','=','Active')
                    ->whereDate('p.created_at', Carbon::today())
                    ->sum('total');
        $Charge   = DB::table('charge as c')
                    ->join('company as co','c.id','=','c.idcompany')
                    ->where('co.status','=','Active')
                    ->whereDate('c.created_at', Carbon::today())
                    ->sum('total');
        $totalToday = ($totalToday + $SoldeCaisseToday)  - ($Charge);

        $SoldeCaisseAll      = DB::table('soldecaisse as s')
        ->join('company as c','c.id','=','s.idcompany')
        ->where('c.status','Active')
        ->whereDate('s.created_at', Carbon::today())
        ->value('s.total');
        // extract total start application
        $AllTotal   = DB::table('paiements as p')
                    ->join('company as c','c.id','=','p.idcompany')
                    ->where('c.status','=','Active')
                    ->sum('total');
        $AllTotal   = ($AllTotal + $SoldeCaisseAll )- ($Charge);

        // extract count client
        $CountClient = DB::table('clients as c')
                    ->join('company as co','co.id','=','c.idcompany')
                    ->where('co.status','=','Active')
                    ->count();

        $currentMonth   = Carbon::now()->startOfMonth();
        $previousMonth  = Carbon::now()->subMonth()->startOfMonth();
        // Count clients added in the current month
        $countCurrentMonth = DB::table('clients as c')
                        ->join('company as co', 'co.id', '=', 'c.idcompany')
                        ->whereDate('c.created_at', '>=', $currentMonth)
                        ->count();
        // Count clients added in the previous month
        $countPreviousMonth = DB::table('clients as c')
                            ->join('company as co', 'co.id', '=', 'c.idcompany')
                            ->whereDate('c.created_at', '>=', $previousMonth)
                            ->whereDate('c.created_at', '<', $currentMonth)
                            ->count();
        // Calculate percentage increase
        if ($countPreviousMonth > 0) {
            $percentageIncrease = (($countCurrentMonth - $countPreviousMonth) / $countPreviousMonth) * 100;
        } else {
            // Handle division by zero or if no clients were added in the previous month
            $percentageIncrease = 100; // Assuming a 100% increase if there were no clients in the previous month
        }

        // Round the percentage increase to two decimal places
        $percentageIncrease = round($percentageIncrease,2);

        // total reglement personnel
        $AlltotalReglementPersonnel = DB::table('personnels as p')
        ->join('company as c','c.id','=','p.idcompany')
        ->join('reglementspersonnels as r','r.idpersonnel','=','p.id')
        ->where('c.status','=','Active')
        ->sum('r.total');
        // total reglement personnel eveyday
        $totalReglementPersonnelEveryDay = DB::table('personnels as p')
        ->join('company as c','c.id','=','p.idcompany')
        ->join('reglementspersonnels as r','r.idpersonnel','=','p.id')
        ->where('c.status','=','Active')
        ->whereDate('r.created_at',Carbon::today())
        ->sum('r.total');


        $CalcuLNumberBon = Order::whereNull('idfacture')
                         ->whereDate('created_at', Carbon::today())
                         ->count();

        $CalcuLNumberFacture = Order::whereNotNull('idfacture')
                            ->whereDate('created_at', Carbon::today())
                            ->count();

        $TotalOrderStartApp = Order::count();

        // Determine the range of years to query
        $currentYear = Carbon::now()->year;
        $threeYearsAgo = $currentYear - 2; // Calculate three years ago

        // Determine the minimum and maximum years with data in paiements, charge, or reglementspersonnels
        $minYear = DB::table('paiements')
        ->join('company', 'paiements.idcompany', '=', 'company.id')
        ->where('company.status', 'Active')
        ->min(DB::raw('YEAR(paiements.created_at)'));

        $maxYear = DB::table('paiements')
        ->join('company', 'paiements.idcompany', '=', 'company.id')
        ->where('company.status', 'Active')
        ->max(DB::raw('YEAR(paiements.created_at)'));
        $startYear = max($currentYear - 2, $minYear); // Start from three years ago or the minimum year with data

        // Initialize an array to store the result data
        $dataTotalChart = [];

        // Loop through each year from $startYear to $currentYear

        for ($year = $startYear; $year <= $currentYear; $year++) {
            // Query for paiements, charge, and reglementspersonnels for each year
            $monthlyData = DB::table(DB::raw('(SELECT MAKEDATE(' . $year . ', 1) + INTERVAL (m.month - 1) MONTH AS m
                                    FROM (SELECT 1 AS month UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                                        UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                                        UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) AS m) AS months'))
                ->leftJoin(DB::raw('(SELECT
                                        YEAR(paiements.created_at) AS year,
                                        MONTH(paiements.created_at) AS month,
                                        SUM(paiements.total) AS total_sum
                                    FROM
                                        paiements
                                    JOIN
                                        company ON paiements.idcompany = company.id
                                    WHERE YEAR(paiements.created_at) = ' . $year . ' AND company.status = "Active"
                                    GROUP BY
                                        YEAR(paiements.created_at), MONTH(paiements.created_at)) AS paiements'), function($join) {
                    $join->on(DB::raw('YEAR(months.m)'), '=', 'paiements.year')
                        ->on(DB::raw('MONTH(months.m)'), '=', 'paiements.month');
                })
                ->leftJoin(DB::raw('(SELECT
                                        YEAR(charge.created_at) AS year,
                                        MONTH(charge.created_at) AS month,
                                        SUM(charge.total) AS total_sum
                                    FROM
                                        charge
                                    JOIN
                                        company ON charge.idcompany = company.id
                                    WHERE YEAR(charge.created_at) = ' . $year . ' AND company.status = "Active"
                                    GROUP BY
                                        YEAR(charge.created_at), MONTH(charge.created_at)) AS charges'), function($join) {
                    $join->on(DB::raw('YEAR(months.m)'), '=', 'charges.year')
                        ->on(DB::raw('MONTH(months.m)'), '=', 'charges.month');
                })
                ->leftJoin(DB::raw('(SELECT
                                        YEAR(rp.created_at) AS year,
                                        MONTH(rp.created_at) AS month,
                                        SUM(rp.total) AS total_sum
                                    FROM
                                        reglementspersonnels rp
                                    JOIN
                                        personnels p ON rp.idpersonnel = p.id
                                    JOIN
                                        company c ON p.idcompany = c.id
                                    WHERE YEAR(rp.created_at) = ' . $year . ' AND c.status = "Active"
                                    GROUP BY
                                        YEAR(rp.created_at), MONTH(rp.created_at)) AS reglementspersonnels'), function($join) {
                    $join->on(DB::raw('YEAR(months.m)'), '=', 'reglementspersonnels.year')
                        ->on(DB::raw('MONTH(months.m)'), '=', 'reglementspersonnels.month');
                })
                ->select(DB::raw("SUBSTRING(DATE_FORMAT(months.m, '%M'), 1, 3) AS month_name"),
                        DB::raw("YEAR(months.m) AS year"),
                        DB::raw("IFNULL(paiements.total_sum, 0)
                                - IFNULL(charges.total_sum, 0)
                                - IFNULL(reglementspersonnels.total_sum, 0) AS net_total"),
                        DB::raw("MONTH(months.m) AS month_number"))
                ->orderBy(DB::raw("YEAR(months.m)"))
                ->orderBy(DB::raw("MONTH(months.m)"))
                ->get();

            foreach ($monthlyData as $monthData) {
                $dataTotalChart[] = [
                    'month_name' => $monthData->month_name,
                    'year' => $monthData->year,
                    'net_total' => $monthData->net_total,
                    'month_number' => $monthData->month_number,
                ];
            }
        }

        $groupedByYear = collect($dataTotalChart)->groupBy('year')->toArray();
        $orders = Order::select(
            'orders.id',
            DB::raw('orders.total  AS totalvente'),
            DB::raw('SUM(reglements.total) AS totalpaye'),
            DB::raw('(orders.total   - SUM(reglements.total)) AS reste'),
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
        ->where('reglements.status','=',null)
        ->groupBy('orders.id')
        ->orderBy('orders.id', 'desc')
        ->get();


       // Get today's date
    // Get today's date
    $today = Carbon::today();

    // Fetch the creation dates of clients from the clients table
    $clientCounts = DB::table('clients')
        ->join('company as c', 'c.id', '=', 'clients.idcompany')
        ->where('c.status', 'Active')
        ->select(
            DB::raw('WEEK(clients.created_at) as week_number'),
            DB::raw('YEAR(clients.created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('week_number', 'year')
        ->get();

    // Initialize an empty array to hold the weekly counts
    $weeklyCounts = [];

    // Loop through each client count
    foreach ($clientCounts as $clientCount) {
        // Calculate the start date of the week based on year and week number
        $startDate = Carbon::now()->setISODate($clientCount->year, $clientCount->week_number)->startOfWeek();

        // Calculate the end date of the week
        $endDate = Carbon::now()->setISODate($clientCount->year, $clientCount->week_number)->endOfWeek();

        // Format the dates as strings
        $startDateString = $startDate->toDateString();
        $endDateString = $endDate->toDateString();

        // Push the weekly count to the array


        $weeklyCounts[] = $clientCount->count ;

    }


        // Return the weekly counts as a JSON response

        return view('Dashboard.index')
            ->with('NameUser'                           ,$NameUser)
            ->with('CompanyIsActive'                    ,$CompanyIsActive)
            ->with('totalToday'                         ,$totalToday)
            ->with('AllTotal'                           ,$AllTotal)
            ->with('CountClient'                        ,$CountClient)
            ->with('percentClient'                      ,$percentageIncrease)
            ->with('AlltotalReglementPersonnel'         ,$AlltotalReglementPersonnel)
            ->with('totalReglementPersonnelEveryDay'    ,$totalReglementPersonnelEveryDay)
            ->with('CalcuLNumberBon'                    ,$CalcuLNumberBon)
            ->with('CalcuLNumberFacture'                ,$CalcuLNumberFacture)
            ->with('TotalOrderStartApp'                 ,$TotalOrderStartApp)
            ->with('orders'                             ,$orders)
            ->with('weeklyCounts'                       ,$weeklyCounts)
            ->with('groupedByYear'                      ,$groupedByYear);
    }

    public function cleanup()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Step 1: Truncate tables without foreign key constraints issues
        DB::table('paiements')->truncate();
        DB::table('modepaiement')->delete(); // Using delete instead of truncate due to foreign key constraints
        DB::statement('ALTER TABLE modepaiement AUTO_INCREMENT = 1');
        DB::table('reglements')->truncate();
        DB::table('lineorder')->truncate();
        DB::table('orders')->truncate();
        DB::table('factures')->truncate();
        DB::table('stock')->truncate();
        DB::table('bonentres')->truncate();
        DB::table('clients')->truncate();
        DB::table('personnels')->truncate();
        DB::table('reglementspersonnels')->truncate();
        DB::table('categorys')->truncate();
        DB::table('setting')->truncate();
        DB::table('company')->truncate();
        /* DB::table('infos')->truncate(); */
        DB::table('charge')->truncate();
        DB::table('tva')->truncate();
        DB::table('products')->truncate();


        // Step 2: Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');


    }
}
