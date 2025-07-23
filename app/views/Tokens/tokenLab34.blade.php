{{-- resources/views/Tokens/tokenLab34.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Print Token</title>

    <!-- JsBarcode CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .token-container {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            border: 2px solid #000;
            border-radius: 10px;
            text-align: center;
        }

        .barcode-area {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .token-details {
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="token-container">
        <h2>Patient Token</h2>

        <div class="token-details">
            <p><strong>Sample No:</strong> {{ $sno }}</p>
            {{-- <p><strong>Date:</strong> {{ $date }}</p> --}}
        </div>

       
        <div class="barcode-area">
            <svg id="barcode"></svg>
        </div>
    </div>

    <script>
      
        var sampleNo = "{{ $sno }}";

        
        var barcodeData = sampleNo;

       
        JsBarcode("#barcode", barcodeData, {
            format: "CODE128",
            lineColor: "#000",
            width: 3,
            height: 100,
            displayValue: true,
            fontSize: 16,
            margin: 10
        });

      
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 1000);
        };
    </script>

</body>
</html>
