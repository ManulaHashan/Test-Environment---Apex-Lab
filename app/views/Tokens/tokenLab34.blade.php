{{-- resources/views/Tokens/tokenLab34.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Print Token</title>

    <!-- QRCode.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

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

        #qrcode {
            margin-top: 15px;
           align-items: center;
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
            <p><strong>Date:</strong> {{ $date }}</p>
        </div>

        <!-- QR Code will be rendered here -->
        <div id="qrcode"></div>
    </div>

    <script>
        // Combine sample no and date
        var qrData = "{{ $sno }}|{{ $date }}";

        // Generate QR code
        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Auto-print on load
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 1000);
        };
    </script>

</body>
</html>
