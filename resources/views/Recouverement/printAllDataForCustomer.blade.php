<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Orders</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6f8;
        }

        .order-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .order-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .total-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .total-buttons .btn {
            min-width: 160px;
        }


        @media print 
        {

            body {
                background: #fff !important;
            }

            .no-print {
                display: none !important;
            }

            .order-card {
                box-shadow: none !important;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }

            .table {
                width: 100% !important;
            }

            .btn {
                border: none !important;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>

<body>

<div class="container py-4">

    <h2 class="mb-4">Ventes</h2>
    <button type="button" class="btn btn-dark mb-3" onclick="window.print()">
        🖨️ Imprimer
    </button>

    @foreach($Data as $orderId => $products)

        @php
            $orderTotal = 0;

            $totalPaye = 0;
            $totalCredit = 0;
        @endphp


        <div class="order-card">

            <div class="order-title">
                Vente N° {{ $orderId }}
            </div>


            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th>Qte</th>
                            <th>Prix</th>
                            <th>Accessoire</th>
                            <th>Total</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($products as $index => $product)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | QUANTITY
                                |--------------------------------------------------------------------------
                                */

                                $quantity = (float) $product->quantity;


                                /*
                                |--------------------------------------------------------------------------
                                | PRICE
                                |--------------------------------------------------------------------------
                                */

                                $price = (float) $product->price;


                                /*
                                |--------------------------------------------------------------------------
                                | ACCESSOIRE
                                |--------------------------------------------------------------------------
                                */

                                $accessoire = (float) $product->accessoire;


                                /*
                                |--------------------------------------------------------------------------
                                | TOTAL LINE
                                |--------------------------------------------------------------------------
                                |
                                | Exemple:
                                |
                                | quantity = 3
                                | price = 860
                                | accessoire = -30
                                |
                                | (860 × 3) + (-30)
                                | = 2550
                                |
                                */

                                $lineTotal = ($price * $quantity) + $accessoire;


                                /*
                                |--------------------------------------------------------------------------
                                | ADD TO ORDER TOTAL
                                |--------------------------------------------------------------------------
                                */

                                $orderTotal += $lineTotal;

                            @endphp


                            <tr>

                                <td>
                                    {{ $index + 1 }}
                                </td>


                                <td>
                                    {{ $product->name }}
                                </td>


                                <td>
                                    {{ round($quantity) }} {{ $product->type }}
                                </td>


                                <td>
                                    {{ number_format($price, 2, '.', ' ') }}
                                </td>


                                <td>

                                    @if($accessoire < 0)

                                        <span class="text-danger">
                                            {{ number_format($accessoire, 2, '.', ' ') }}
                                        </span>

                                    @elseif($accessoire > 0)

                                        <span class="text-success">
                                            +{{ number_format($accessoire, 2, '.', ' ') }}
                                        </span>

                                    @else

                                        0.00

                                    @endif

                                </td>


                                <td class="fw-bold">

                                    {{ number_format($lineTotal, 2, '.', ' ') }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    <tfoot>

                        <tr>

                            <th colspan="5" class="text-end">
                                Total vente
                            </th>

                            <th>
                                {{ number_format($orderTotal, 2, '.', ' ') }} DH
                            </th>

                        </tr>

                    </tfoot>

                </table>

            </div>


            @php

                /*
                |--------------------------------------------------------------------------
                | PAYMENTS
                |--------------------------------------------------------------------------
                */

                if (isset($Reglements[$orderId])) {

                    foreach ($Reglements[$orderId] as $reglement) {

                        if (strtolower(trim($reglement->name)) == 'crédit') {

                            $totalCredit += (float) $reglement->total;

                        } else {

                            $totalPaye += (float) $reglement->total;

                        }

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | RESTE
                |--------------------------------------------------------------------------
                */

                $reste = $orderTotal - $totalPaye - $totalCredit;

                if ($reste < 0) {
                    $reste = 0;
                }

            @endphp


            <div class="total-buttons">


                <button type="button" class="btn btn-primary">

                    <strong>Total Vente</strong><br>

                    {{ number_format($orderTotal, 2, '.', ' ') }} DH

                </button>


                <button type="button" class="btn btn-success">

                    <strong>Total Payé</strong><br>

                    {{ number_format($totalPaye, 2, '.', ' ') }} DH

                </button>


                <button type="button" class="btn btn-warning">

                    <strong>Total Crédit</strong><br>

                    {{ number_format($totalCredit, 2, '.', ' ') }} DH

                </button>


                <button type="button" class="btn btn-danger">

                    <strong>Reste</strong><br>

                    {{ number_format($reste, 2, '.', ' ') }} DH

                </button>

            </div>

        </div>

    @endforeach

</div>

</body>
</html>