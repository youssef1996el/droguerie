
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('css/dashboard/styles.css')}}">
    <style>

        .footer-content {
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-top: 1px solid #f2f2f2;
            padding-top: 80%;
        }
        .dateEdite {
            float: right;
        }
        .signature {
            width: 28%;
            padding-bottom: 10%;
            border: 1px solid;
            display: inline;
        }
        .invoice-container {
            height: 1060px;
            position: relative;
            border: 1px solid;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #ffffff; /* Set the background color */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add the shadow effect */
        }
        .invoice-title {
            text-transform: uppercase;
            text-align: center;
        }
        .invoice-footer {
            text-transform: uppercase;
            white-space: nowrap;
            margin-top: 5px;
            bottom: 12;
            position: absolute;
        }
        .client-info {
            margin-top: 20px;
            border: 1px solid;
        }
        .client-info label {
            font-weight: bold;
        }
        .facture-date {
            text-align: right;
            margin-top: 20px;
        }
        #tableDetail {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            font-size: 12px;
        }
        #tableTitle {
            border-collapse: collapse;
            border: none;
        }
        #tableDetail th,
        #tableDetail td {
            border: 1px solid;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        #tableDetail th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
            white-space: nowrap;
        }
        span {
            border: none;
            font-size: 12px;
            white-space: nowrap;
        }
        label {
            font-size: 12px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 48%;
            transform: translate(-50%, -50%) rotate(-45deg); /* Rotate the watermark */
            font-size: 200px;
            opacity: 0.1;
            pointer-events: none;
            text-transform: uppercase;
        }
        @page {
            size: A4;
            margin: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100% !important;
            margin: 20px auto;
            box-sizing: border-box;
        }
        .left {
            width: 50%;
            text-align: center;
            padding: 10px;
            box-sizing: border-box;
        }
        .right {
            width: 50%;
            text-align: center;
            padding: 10px;
            box-sizing: border-box;
        }
        .titleLeft {
            border: 1px solid rgb(150, 196, 255);
            border-radius: 10px;
            width: 100% !important;
        }
        .DivContentInformationClient {
            border: 1px solid rgb(150, 196, 255);
            border-radius: 10px;
            width: 95%;
        }
        .TitleClient
        {
            text-transform: uppercase !important;
            text-align: left !important;
            padding: .5rem !important;
            margin-top: 1rem !important;
            line-height: normal !important;
            font-size: calc(1.3rem + .6vw);
           /*  border: 1px solid black;
            border-radius: 10px */

        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
            padding-left: 15px;
        }
        .border {
            border: 1px solid #dee2e6;
        }
        .rounded-2 {
            border-radius: 0.25rem; /* 4px */
        }
        .bg-light {
            background-color: #f8f9fa;
        }
        .text-dark {
            color: #343a40;
        }
        .text-center
        {
            text-align: center;
        }
        .p-2 {
            padding: 0.5rem; /* 8px */
        }
        .text-uppercase
        {
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }

        /* Bordered table */
        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        /* Striped table */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Table header styling */
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="invoice-container">

        <div class="container" style="margin: auto">
            <table style="width: 100%; text-align: center;">
                <tr>
                    <th>
                        <div class="left titleLeft">
                            @php
                                use Carbon\Carbon;

                                // Ensure dates are Carbon instances
                                $dateStart = Carbon::parse($DateStart);
                                $dateEnd = Carbon::parse($DateEnd);
                            @endphp
                            @if ($dateStart->eq($dateEnd))
                                <h3 style="text-transform: uppercase; text-align: center;">Etat : {{ $dateStart->format('d/m/Y') }}</h3>
                            @else
                                <h3 style="text-transform: uppercase; text-align: center;">Etat : {{ $dateStart->format('d/m/Y') }} - {{$dateEnd->format('d/m/Y')}}</h3>
                            @endif
                        </div>
                    </th>
                </tr>
            </table>
        </div>
        {{-- @foreach ($DataByClient as $client => $values)
            <h3 class="TitleClient" >{{ $client }}</h3>
            <table class="" id="tableDetail">
                <thead>
                    <tr>

                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalItems = count($values);
                    @endphp
                    @foreach ($values as $index => $item)
                        @if ($index < $totalItems - 1) <!-- Exclude the last row -->
                            <tr>

                                <td>{{ $item->name ?? 'N/A' }}</td>
                                <td>{{ $item->QteConvert ?? 'N/A' }}</td>
                                <td style="text-align: end">{{ $item->price ?? 'N/A' }} DH</td>
                                <td style="text-align: end">{{ $item->total ?? 'N/A' }} DH</td>
                            </tr>

                        @endif
                    @endforeach
                    <tr>

                        <td>{{ $LastRowByClient[$client]->name ?? 'N/A' }}</td>
                        <td>{{ $LastRowByClient[$client]->QteConvert ?? 'N/A' }}</td>
                        <td style="text-align: end">{{ $LastRowByClient[$client]->price ?? 'N/A' }} DH</td>
                        <td style="text-align: end">{{ $LastRowByClient[$client]->total ?? 'N/A' }} DH</td>
                    </tr>
                </tbody>

            </table>
            <div class="d-flex justify-content-end align-items-end" style="display: flex;justify-content: flex-end;align-items: flex-end;">
                <table class="" id="tableDetail" style="width: 50%">
                    <tr>
                        <th colspan="3">Totaux HT</th>

                        <th style="text-align: end">{{ number_format($TotalByClient[$client] ?? '0.00',2,".","") }} DH</th>
                    </tr>
                    <tr>
                        <th colspan="3">Total Credit</th>
                        <th style="text-align: end">{{ number_format($TotalCreditByClient[$client] ?? '0.00',2,".","") }} DH</th>
                    </tr>
                </table>
            </div>

            <hr>
        @endforeach --}}
        @php
            $clientCounter = 0;
        @endphp

        @foreach ($DataByClient as $client => $values)
            @if ($clientCounter % 2 == 0 && $clientCounter != 0)
                <div class="page-break"></div>
            @endif

            <u class="TitleClient">Client : {{ $client }}</u>
            <table class="" id="tableDetail">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Remise</th>
                        <th>Prix</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalItems = count($values);
                    @endphp
                    @foreach ($values as $index => $item)
                        @if ($index < $totalItems - 1) <!-- Exclude the last row -->
                            <tr>
                                <td>{{ $item->name ?? 'N/A' }}</td>
                                <td>{{ $item->QteConvert ?? 'N/A' }}</td>

                                <td>{{ $item->remise ?? 'N/A' }}</td>
                                <td style="text-align: right">{{ $item->price_new ?? 'N/A' }} DH</td>
                                <td style="text-align: right">{{ $item->totalnew ?? 'N/A' }} DH</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td>{{ $LastRowByClient[$client]->name ?? 'N/A' }}</td>
                        <td>{{ $LastRowByClient[$client]->QteConvert ?? 'N/A' }}</td>
                        <td>{{ $LastRowByClient[$client]->remise ?? 'N/A' }}</td>
                        <td style="text-align: right">{{ $LastRowByClient[$client]->price_new ?? 'N/A' }} DH</td>
                        <td style="text-align: right">{{ $LastRowByClient[$client]->totalnew ?? 'N/A' }} DH</td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end align-items-end" style="display: flex;justify-content: flex-end;align-items: flex-end;">
                <table class="" id="tableDetail" style="width: 50%">
                    <tr>
                        <th >Totaux HT</th>
                        <th style="text-align: right">{{ number_format($TotalByClient[$client] ?? '0.00', 2, ".", "") }} DH</th>
                    </tr>
                    <tr>
                        <th >Total Payé</th>
                        <th style="text-align: right">{{ number_format($TotalPayeByClient[$client] ?? '0.00', 2, ".", "") }} DH</th>
                    </tr>
                    <tr>
                        <th >Total Credit</th>
                        <th style="text-align: right">{{ number_format($TotalCreditByClient[$client] ?? '0.00', 2, ".", "") }} DH</th>
                    </tr>
                </table>
            </div>

            <hr>

            @php
                $clientCounter++;
            @endphp
        @endforeach

        <div class="d-flex justify-content-end align-items-end">
            <table class="" id="tableDetail" style="width: 50%">
                <tr>
                    <th colspan="3">Total général des ventes</th>
                    <th style="text-align: right">{{ number_format($GrandTotal, 2, ".", "") }} DH</th>
                </tr>
                <tr>
                    <th colspan="3">Total général des crédits</th>
                    <th style="text-align: right">{{ number_format($GrandTotalCredit, 2, ".", "") }} DH</th>
                </tr>

            </table>
        </div>
        {{-- <div class="row">
            <div class="col-6">
                <h3 class="border rounded-2 bg-light text-dark text-center p-2 text-uppercase">Tableau encaissement</h3>
                <table  class="table table-striped table-bordered">
                    @foreach ($TotalByModePaiement as $item)
                    <tr>
                       <th colspan="3">{{$item->name}}</th>
                       <th style="text-align: right">{{ number_format($item->totalpaye, 2, ".", "") }} DH</th>
                    </tr>
                   @endforeach
                </table>
            </div>
            <div class="col-6">
                <h3 class="border rounded-2 bg-light text-dark text-center p-2 text-uppercase">Tableau encaissement crédit</h3>
                <table  class="table table-striped table-bordered">
                    @foreach ($Tableau_enccaissement as $item)
                        <tr>
                            <th colspan="3">{{$item->client}}</th>
                            <th style="text-align: right">{{ number_format($item->total, 2, ".", "") }} DH</th>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div> --}}
        <div>

            <table id="tableDetail" style="width: 50%">
                 @foreach ($TotalByModePaiement as $item)
                 <tr>
                    <th colspan="3">{{$item->name}}</th>
                    <th style="text-align: right">{{ number_format($item->totalpaye, 2, ".", "") }} DH</th>
                 </tr>
                @endforeach
                <tr>
                    <th colspan="3" style="text-transform: uppercase">Total Reglement</th>
                    <th style="text-align: right">{{ number_format($TotalReglementPaye, 2, ".", "") }} DH</th>
                </tr>
                <tr>
                    <th colspan="3" style="text-transform: uppercase">Solde de départ la caisse </th>
                    <th style="text-align: right">{{ number_format($SoldeCaisse, 2, ".", "") }} DH</th>
                </tr>
                <tr>
                    <th colspan="3" style="text-transform: uppercase">Charge</th>
                    <th style="text-align: right">{{ number_format($Charge, 2, ".", "") }} DH</th>
                </tr>
                @foreach ($Versement as $item)
                <tr>
                    <th colspan="3" style="text-transform: uppercase">Versement / {{$item->comptable}}</th>
                    <th style="text-align: right">{{ number_format($item->total, 2, ".", "") }} DH</th>
                </tr>
                @endforeach
                @if (count($Paiement_Employee) > 0)
                    @foreach ($Paiement_Employee as $item)
                    <tr>
                        <th colspan="3">{{$item->employe}}</th>
                        <th style="text-align: right">{{ number_format($item->total, 2, ".", "") }} DH</th>
                     </tr>
                    @endforeach
                @endif
                <tr>
                    <th colspan="3">Reste</th>
                    <th style="text-align: right">{{ number_format($reste, 2, ".", "") }} DH</th>
                </tr>

            </table>
        </div>

    </div>
</body>
</html>

