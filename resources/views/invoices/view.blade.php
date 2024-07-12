<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        /* Define your styles for the invoice here */
        /* Example styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .invoice-header {
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-items {
            margin-bottom: 20px;
        }
        .invoice-footer {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>Invoice #{{ $invoice->id }}</h1>
        <p>Date: {{ $invoice->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="invoice-details">
        <p>Customer: El hamra youssef</p>
        <p>Email: elhamrayoussef@gmail.com</p>
        <!-- Add more details as needed -->
    </div>

    <div class="invoice-items">
        <h3>Invoice Items:</h3>
        <ul>
            {{-- @foreach ($invoice->items as $item)
                <li>{{ $item->description }} - ${{ $item->amount }}</li>
            @endforeach --}}
        </ul>
    </div>

    <div class="invoice-footer">
        <p>Total: $1000.00</p>
    </div>
</body>
</html>
