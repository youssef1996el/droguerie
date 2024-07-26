
<div class="invoice-container">

    @php
        function formatPhoneNumber($phoneNumber) {
            return implode('.', str_split($phoneNumber, 2));
        }

        // Assuming $Info->phone and $Info->fix contain the phone numbers
        $formattedPhone = formatPhoneNumber($Info->phone);
        $formattedFix = formatPhoneNumber($Info->fix);
    @endphp
    <div class="container">
        <table style="width: 100%">
            <tr>
                <th>
                    <div class="left titleLeft">
                        <h3 style="text-transform: uppercase;">{{$Info->title}}</h3>
                        <h5>TEL: {{$formattedPhone}} / {{$formattedFix}}</h5>
                    </div>
                </th>
                <th>
                    <div class="right titleRight">
                        <p>@if ($typeOrder) Facture @else Bon @endif</p>
                        <p>@if ($typeOrder) N° {{$formattedId}} @else N° {{$formattedId}} @endif </p>


                    </div>
                </th>
            </tr>
        </table>


    </div>

    <div>
        <div class="container DivContentInformationClient" >

            <table style="width: 100%">
                <tr>
                    <th class="left;" style="white-space: nowrap;text-transform: uppercase;text-align: left">
                        CLIENT : {{$Client->nom}} {{$Client->prenom}}
                    </th>
                    <th  class="right" style="float: right; text-align: right">
                        Date : {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
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
                    $SumTotalHT += $item->total + $item->accessoire;
                    $SumTotalAccessoire = $item->accessoire;



                @endphp
                <tr>
                    <td style="text-align: center">{{$item->name}}</td>
                    <td style="text-align: center">{{$item->qte}}</td>
                    <td style="text-align: center">{{number_format($item->price + $item->accessoire,2,","," ")}} DH</td>
                    <td style="text-align: right">{{number_format($item->total + $item->accessoire ,2,","," ")}} DH</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="table-responsive">
        <table class="custom-table" id="tableDetail" style="width: 50%;float:right;">
            <tr>
                <td class="text-end"><strong>Total HT:</strong></td>
                <td style="text-align: right">{{number_format($SumTotalHT ,2,","," ")}} DH</td>
            </tr>
            @if($Credit)
                <td class="text-end"><strong>Crédit Restant:</strong></td>
                <td style="text-align: right">{{number_format($Credit ,2,","," ")}} DH</td>
            @endif
            @if ($typeOrder)
                @php
                    $taxRate                    = floatval(rtrim($Tva->name, '%')) / 100;
                    $taxAmount                  = ($SumTotalHT) * $taxRate;
                    $totalIncludingTax          = ($SumTotalHT) * (1 + $taxRate);
                @endphp
                <tr>
                    <td class="text-end"><strong>TVA {{ $Tva->name }}:</strong></td>
                    <td style="text-align: right">{{number_format($taxAmount,2,","," ")}} DH</td>
                </tr>
                <tr>
                    <td class="text-end"><strong>Total TTC:</strong></td>
                    <td style="text-align: right">{{number_format($totalIncludingTax,2,","," ")}} DH</td>
                </tr>
            @endif
        </table>
    </div>
    <footer>


        <div class="invoice-footer">
            <span class="text-uppercase" style="padding:8px;">
                <p style="font-size: 14px;">ICE : {{$Info->ice}} / CNSS : {{$Info->cnss}}/ RC : {{$Info->rc}}/ IF : {{$Info->if}}/ adresse : {{$Info->address}}</p>
            </span>
        </div>
    </footer>




    <div class="watermark">@if ($typeOrder) Facture @else Bon @endif</div>
</div>
<style>

    .footer-content {
        width: 100%;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-top: 1px solid #f2f2f2;
        padding-top: 80%;
    }
    .dateEdite
    {
        float: right;
    }
    .signature {
        width: 28%;
        padding-bottom: 10%;
        border: 1px solid;
        display: inline;

    }
</style>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    #tableInformation
    {
        border-collapse: collapse;
        border: none !important;
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

    #tableDetail{
        width: 100%;
        border-collapse: collapse;
        margin-top: 100px;
        font-size:12px;
    }
    #tableTitle
    {
        border-collapse: collapse;
        border: none;
    }

    #tableDetail th,
    #tableDetail td {
        border: 1px solid;
        padding: 8px;
        text-align: left;

        font-size:10px;
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
    label
    {
        font-size: 12px;
    }

    /* Watermark styles */
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

        .titleLeft
        {
            border: 1px solid rgb(150, 196, 255);
            border-radius: 10px
        }
       /*  .titleRight
        {
            border: 1px solid rgb(150, 196, 255);
            border-radius: 10px;
            width: 80%;

        } */
        .DivContentInformationClient
        {
            border: 1px solid rgb(150, 196, 255);
            border-radius: 10px;
            width: 95%;
        }
</style>

