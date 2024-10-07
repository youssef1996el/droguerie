
<!DOCTYPE html>
<html >
<head>
    <title>Arabic Invoice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            font-family: DejaVu Sans !important;
        }
       
        @page {
            size: a4;
            margin: 0;
            padding: 0;
        }
        .invoice-box table {
            direction: ltr;
            width: 100%;
            text-align: right;
            border: 1px solid;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
        }
        .row, .column {
            display: block;
            page-break-before: avoid;
            page-break-after: avoid;
        }
    </style>
    <style>
        .invoice-container {
            height: 1060px;
            position: relative;
            border: 1px solid;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #ffffff; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
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
        .container {
            display: flex;
            width: 98%;
            margin: 20px;
            box-sizing: border-box;
        }
        #tableDetail {
            width: 100%;
            border-collapse: collapse;
            margin-top: 100px;
            font-size: 12px;
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
        .invoice-footer {
            text-transform: uppercase;
            white-space: nowrap;
            margin-top: 5px;
            bottom: 12;
            position: absolute;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 48%;
            transform: translate(-50%, -50%) rotate(-45deg); 
            font-size: 200px;
            opacity: 0.1;
            pointer-events: none;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        @php
            function formatPhoneNumber($phoneNumber) 
            {
                return implode('.', str_split($phoneNumber, 2));
            }

            $formattedPhone = formatPhoneNumber($Info->phone);
            $formattedFix = formatPhoneNumber($Info->fix);
        @endphp
        <div class="container">
            <table style="width: 100%">
                <tr>
                    <th>
                        <div class="left titleLeft">
                            <h3 style="text-transform: uppercase;">{{ $Info->title }}</h3>
                            <h5>TEL: {{ $formattedPhone }} / {{ $formattedFix }}</h5>
                        </div>
                    </th>
                    <th>
                        <div class="right titleRight">
                            <img src="data:image/png;base64,{{ $imageData }}" alt="" style="width: 150px; height: 150px;">
                        </div>
    
                    </th>
                </tr>
            </table>
        </div>
        <div>
            <div class="container DivContentInformationClient">
                <div class="" style="margin-left: 15px;">
                    <p style="padding-right: 5px;">{{ $typeOrder ? 'Facture :' : 'Bon :' }} N° {{ $formattedId }}</p>

                </div>
                <table style="width: 100%">
                    <tr>
                        <th class="right" style="float: right; text-align: left;">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }} التاريخ :
                        </th>
                        <th class="left" style="white-space: nowrap; text-transform: uppercase; text-align:right;">
                            الزبون :{{ $Client->nom }} {{ $Client->prenom }} 
                        </th>
                    </tr>
                </table>
            </div>
        </div>
        <table id="tableDetail">
            <thead>
                <tr>
                    <td style="text-align: center"><strong>Description</strong></td>
                    <td style="text-align: center"><strong>Quantité</strong></td>
                    <td style="text-align: center"><strong>P.U HT</strong></td>
                    <td style="text-align: center"><strong>Total HT</strong></td>
                </tr>
            </thead>
            <tbody>
                @php
                    $SumTotalHT = 0;
                    $SumTotalAccessoire = 0;
                @endphp

                @foreach ($DataLine as $item)
                    @php
                        // Sum up the total and accessoire
                        $SumTotalHT += $item->total + $item->accessoire;
                        $SumTotalAccessoire += $item->accessoire;

                        // Calculate the difference between qteKG * priceKG and totalnew
                        $difference = $item->QteConvertWithOutConcat * $item->convert;
                        $difference *=$item->PriceStock;
                        
                        // If the calculated difference doesn't match totalnew, add accessoire to difference
                       /*  if ($difference != $item->totalnew) {
                            $difference = $item->accessoire / $item->qteKG;
                            $difference += $item->PriceStock;
                            $item->PriceStock = $difference;
                        } */
                    @endphp

                    <tr>
                        <td style="text-align: center">{{ $item->name }}</td>
                        <td style="text-align: center">{{ $item->QteConvertWithOutConcat * $item->convert }}</td>
                        <td style="text-align: right">{{ number_format($item->PriceStock, 2, ",", " ") }}</td>
                        <td style="text-align: right">{{ number_format($item->totalnew, 2, ",", " ") }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <div class="table-responsive">
            <table class="custom-table" id="tableDetail" style="width: 50%; float: right;">
                <tr>
                    <td class="text-end"><strong>Total HT:</strong></td>
                    <td style="text-align: right">{{ number_format($SumTotalHT, 2, ",", " ") }} DH</td>
                </tr>
                
                
                    @php
                        $taxRate = floatval(rtrim($Tva->name, '%')) / 100;
                        $taxAmount = $SumTotalHT * $taxRate;
                        $totalIncludingTax = $SumTotalHT * (1 + $taxRate);
                    @endphp
                    <tr>
                        <td class="text-end"><strong>TVA {{ $Tva->name }}:</strong></td>
                        <td style="text-align: right">{{ number_format($taxAmount, 2, ",", " ") }} DH</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>Total TTC:</strong></td>
                        <td style="text-align: right">{{ number_format($totalIncludingTax, 2, ",", " ") }} DH</td>
                    </tr>
                
            </table>

            
            
        </div>
        
       
        <footer>
            <div class="invoice-footer">
                <span class="text-uppercase" style="padding: 8px;display: flex;justify-content: center;align-content: center">
                    <p style="font-size: 12px;">ICE: {{ $Info->ice }} / CNSS: {{ $Info->cnss }} / RC: {{ $Info->rc }} / IF: {{ $Info->if }} / adresse: {{ $Info->address }}</p>
                </span>
            </div>
        </footer>
        <div class="watermark">Facture</div>
    </div>
    
    
    

</body>

</html>
