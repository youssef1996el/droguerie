<!DOCTYPE html>
<html lang="en">
<head>
    <title>Liste Crédit</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* General Reset */
        * {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 20px;
        }

        /* Page Setup */
        @page {
            size: A4;
            margin: 20px;
        }

        .invoice-container {
            width: 100%;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #fff;
            position: relative;
        }

        .header-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-title h3 {
            font-size: 18px;
            text-transform: uppercase;
            color: #333;
            letter-spacing: 1px;
        }

        .content-section {
            margin-bottom: 30px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Subtotal and Total Styling */
        .subtotal-table td,
        .total-table td {
            text-align: right;
            font-weight: bold;
            background-color: #f9f9f9;
            padding: 10px;
        }

        .total-label {
            text-align: center;
        }

        .invoice-footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: right;
            font-size: 10px;
            color: #888;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: #ccc;
            opacity: 0.2;
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        <!-- Watermark -->
        <div class="watermark">PAID</div>

        <!-- Header Title -->
        <div class="header-title">
            <h3>Liste de Crédit</h3>
        </div>

        <!-- Main Table -->
        @php
            $currentClient = '';
            $subTotal = 0;
        @endphp

        @foreach ($ListCredit as $item)
            <!-- Check for new client -->
            @if ($currentClient !== $item->client)
                @if ($currentClient !== '')
                    <!-- Close previous client table -->
                    </tbody>
                    </table>

                    <!-- Subtotal -->
                    <table class="subtotal-table">
                        <tbody>
                            <tr>
                                <td colspan="1" class="total-label">Sous-total</td>
                                <td colspan="3">{{ $subTotal }} DH</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                <!-- Reset for new client -->
                @php
                    $currentClient = $item->client;
                    $subTotal = 0;
                @endphp

                <!-- New Client Header -->
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>La date de vente</th>
                            <th>Mode de paiement</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
            @endif

            <!-- Display client rows -->
            <tr>
                <td>{{ $item->client }}</td>
                
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->total }} DH</td>
            </tr>

            @php
                $subTotal += $item->total;
            @endphp
        @endforeach

        <!-- Close last client table -->
        </tbody>
        </table>

        <!-- Final Subtotal -->
        <table class="subtotal-table">
            <tbody>
                <tr>
                    <td colspan="3" class="total-label">Sous-total</td>
                    <td>{{ $subTotal }} DH</td>
                </tr>
            </tbody>
        </table>

        <!-- Grand Total -->
        <table class="total-table">
            <tbody>
                <tr>
                    <td colspan="3" class="total-label">Total Général</td>
                    <td>{{ $grandTotal }} DH</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="invoice-footer">
            Page 1 of 1
        </div>
    </div>

</body>
</html>
