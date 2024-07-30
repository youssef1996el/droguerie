
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
            width: 100%;
            margin: 20px;
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
        }
        .DivContentInformationClient {
            border: 1px solid rgb(150, 196, 255);
            border-radius: 10px;
            width: 95%;
        }
        .TitleClient
        {
            text-transform: uppercase !important;
            text-align: center !important;
            padding: .5rem !important;
            margin-top: 1rem !important;
            line-height: normal !important;
            font-size: calc(1.3rem + .6vw);
            border: 1px solid black;
            border-radius: 10px

        }
    </style>
</head>
<body>
    <div class="invoice-container">

        <div class="container">
            <table style="width: 100%">
                <tr>
                    <th>
                        <div class="left titleLeft">
                            <h3 style="text-transform: uppercase;">Etat</h3>
                        </div>
                    </th>
                </tr>
            </table>
        </div>
        @foreach ($DataByClient as $client => $values)
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
            <div class="d-flex justify-content-end align-items-end" style="display: flex;
  justify-content: flex-end;
  align-items: flex-end;">
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
        @endforeach

        <div class="d-flex justify-content-end align-items-end">
            <table class="" id="tableDetail" style="width: 50%">
                <tr>
                    <th colspan="3">Grand Total HT</th>
                    <th>{{ number_format($GrandTotal, 2, ".", "") }}</th>
                </tr>
                <tr>
                    <th colspan="3">Grand Total Credit</th>
                    <th>{{ number_format($GrandTotalCredit, 2, ".", "") }}</th>
                </tr>
            </table>
        </div>
        {{-- <div class="watermark">ETAT</div> --}}
    </div>
</body>
</html>

