
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('css/dashboard/styles.css')}}">
    <style>
        * {
            font-family: DejaVu Sans !important;
        }
        @page {
            size: a4;
            margin: 0;
            padding: 0;
        }
        .invoice-container table {
            direction: ltr;
            width: 100%;
            text-align: right;
            border: 1px solid;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
        }
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
        .TitleTable
        {

            color: #000;
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
            margin-bottom: 20px;
            padding: 20px;
            margin-top: 10px;

        }
        @media print {
    .page-break {
        page-break-before: always;
    }

    .client-section {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .TitleClient {
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 10px;
    }

    #tableDetail {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    #tableDetail th, #tableDetail td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    #tableDetail th {
        background-color: #f2f2f2;
    }

    .d-flex {
        display: flex !important;
    }

    .justify-content-end {
        justify-content: flex-end !important;
    }

    .align-items-end {
        align-items: flex-end !important;
    }
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

        @php
    $clientCounter = 0;
@endphp

@foreach ($DataByClient as $client => $values)
    @if ($clientCounter % 2 == 0 && $clientCounter != 0)
        <div class="page-break"></div>
    @endif

    <div class="client-section" style="page-break-inside: avoid; break-inside: avoid;">
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
                    <th>Totaux HT</th>
                    <th style="text-align: right">{{ number_format($TotalByClient[$client] ?? '0.00', 2, ".", "") }} DH</th>
                </tr>
                <tr>
                    <th>Total Payé</th>
                    <th style="text-align: right">{{ number_format($TotalPayeByClient[$client] ?? '0.00', 2, ".", "") }} DH</th> 
                </tr>
                <tr>
                    <th>Total Credit</th>
                    <th style="text-align: right">{{ number_format($TotalCreditByClient[$client] ?? '0.00', 2, ".", "") }} DH</th>
                </tr>
            </table>
        </div>
    </div>

    <hr>

    @php
        $clientCounter++;
    @endphp
@endforeach



<style>
    .page-break {
        page-break-before: always;
    }
</style>


<div class="page-break"></div>



          <div style="display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 40px;margin-top:10px">
            <div style="float: left; width: 50%; border: 1px solid black">
                <u class="TitleTable">Tableau encaissement</u>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    @php
                        $TotalEncaissementPaye = 0;
                    @endphp
                    @foreach ($TotalByModePaiement as $item)
                        @php
                            $TotalEncaissementPaye += $item->totalpaye;
                        @endphp
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px;">{{ $item->name }}</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($item->totalpaye, 2, ".", "") }} DH</th>
                        </tr>
                    @endforeach
                    <tr style="background-color: rgb(195, 255, 190);">
                        <th style="border: 1px solid #ccc; padding: 8px;">TOTAL</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($TotalEncaissementPaye, 2, ".", "") }} DH</th>
                    </tr>
                </table>
            </div>
            <div style="margin-left: 55%; width: 45%; border: 1px solid black">
                <u class="TitleTable">Tableau charge</u>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    @php
                        $TotalCharge = 0;
                    @endphp
                    @foreach ($Charge as $item)
                        @php
                            $TotalCharge += $item->total;
                        @endphp
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px;">{{ $item->name }}</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($item->total, 2, ".", "") }} DH</th>
                        </tr>
                    @endforeach
                    <tr style="background-color: rgb(255, 190, 190);">
                        <th style="border: 1px solid #ccc; padding: 8px;">TOTAL</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($TotalCharge, 2, ".", "") }} DH</th>
                    </tr>
                </table>
            </div>
        </div>
        <br><br><br><br><br><br>
        <div style="display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 40px;">
            <div style="float: left; width: 50%; border: 1px solid black">
                <u class="TitleTable">Tableau ENCAISSEMENT CRÉDIT</u>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;" >
                    @php
                        $TotalEncaissement_Credit = 0;
                    @endphp
                    @foreach ($Tableau_enccaissement_Credit as $item)
                        @php
                            $TotalEncaissement_Credit += $item->total;
                        @endphp
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $item->name}} </th>
                            <th style="border: 1px solid #ccc; padding: 8px;">{{ $item->client }}</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($item->total, 2, ".", "") }} DH</th>
                            
                        </tr>
                    @endforeach
                    <tr style="background-color: rgb(195, 255, 190);">
                        <th colspan="2" style="border: 1px solid #ccc; padding: 8px;">TOTAL</th>
                        <th  style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($TotalEncaissement_Credit, 2, ".", "") }} DH</th>
                    </tr>
                </table>
            </div>
            <br><br><br><br><br><br><br><br><br><br><br><br>
            <div style="margin-left: 55%; width: 45%; border: 1px solid black">
                <u class="TitleTable">Tableau versement</u>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    @php
                        $TotalVersement = 0;
                    @endphp
                    @foreach ($Versement as $item) 
                        @php
                            $TotalVersement += $item->total;
                        @endphp
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px;">{{ $item->comptable }}</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($item->total, 2, ".", "") }} DH</th>
                        </tr>
                    @endforeach
                    <tr style="background-color: rgb(255, 190, 190);">
                        <th style="border: 1px solid #ccc; padding: 8px;">TOTAL</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($TotalVersement, 2, ".", "") }} DH</th>
                    </tr>
                </table>
            </div>
        </div>
        <br><br><br><br><br><br>
        <div style="display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 40px;">
            <div style="float: left; width: 50%; border: 1px solid black">
                <u class="TitleTable">Tableau solde de la caisse</u>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <tr style="background-color: rgb(195, 255, 190);">
                        <th style="border: 1px solid #ccc; padding: 8px;">Solde</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($SoldeCaisse, 2, ".", "") }} DH</th>
                    </tr>
                </table>
            </div>
            @if (count($Paiement_Employee) > 0)
                <div style="margin-left: 55%; width: 45%; border: 1px solid black">
                    <u class="TitleTable">Tableau Encaissement personnel</u>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        @php
                            $TotalEncaissementPersonnel = 0;
                        @endphp
                        @foreach ($Paiement_Employee as $item)
                            @php
                                $TotalEncaissementPersonnel += $item->total;
                            @endphp
                            <tr>
                                <th style="border: 1px solid #ccc; padding: 8px;">{{$item->employe}}</th>
                                <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($item->total, 2, ".", "") }} DH</th>
                            </tr>
                        @endforeach
                        <tr style="background-color: rgb(255, 190, 190);">
                            <th style="border: 1px solid #ccc; padding: 8px;">TOTAL</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($TotalEncaissementPersonnel, 2, ".", "") }} DH</th>
                        </tr>
                    </table>
                </div>
            @else
                <div style="margin-left: 55%; width: 45%; border: 1px solid black">
                    <u class="TitleTable">Tableau reste</u>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <tr style="background-color: rgb(195, 255, 190);">
                            <th style="border: 1px solid #ccc; padding: 8px;">Reste</th>
                            {{-- <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($reste, 2, ".", "") }} DH</th> --}}
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($Reste, 2, ".", "") }} DH</th>
                        </tr>
                    </table>
                </div>
            @endif
        </div>
        <br><br><br><br><br><br><br><br><br><br><br><br>
            <div style="margin-left: 55%; width: 45%; border: 1px solid black">
                <u class="TitleTable">Tableau Revenus</u>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    @php
                        $TotalRevenus = 0;
                    @endphp
                    @foreach ($Renevus as $item)
                        @php
                            $TotalRevenus += $item->total;
                        @endphp
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px;">{{ $item->friend }}</th>
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($item->total, 2, ".", "") }} DH</th>
                        </tr>
                    @endforeach
                    <tr style="background-color: rgb(195, 255, 190);">
                        <th style="border: 1px solid #ccc; padding: 8px;">TOTAL</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($TotalRevenus, 2, ".", "") }} DH</th>
                    </tr>
                </table>
            </div>
        </div>

        @if (count($Paiement_Employee) > 0)
            <div style="display: flex; justify-content: space-between; align-items: stretch;">
                <div style="float: left; width: 50%; border: 1px solid black">
                    <u class="TitleTable">Tableau reste</u>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <tr style="background-color: rgb(195, 255, 190);">
                            <th style="border: 1px solid #ccc; padding: 8px;">Reste</th>
                            {{-- <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($reste, 2, ".", "") }} DH</th> --}}
                            <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ number_format($Reste, 2, ".", "") }} DH</th>
                        </tr>
                    </table>
                </div>
            </div>
        @endif




    </div>
</body>
</html>

