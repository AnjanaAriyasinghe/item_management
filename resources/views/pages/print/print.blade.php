<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheque Layout Test</title>
    <style>
        @page {
            margin: 0;
            /* Remove all page margins */
        }

        body {
            margin: 0;
            /* Remove margins from the body */
            padding: 0;
            /* Remove any padding */
        }


        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .cheque {
            width: 95%;
            /* 7 inches in cm */
            height: 8.89cm;
            /* 3.5 inches in cm */
            padding: 0;
            margin-left: 23px;
            /* Remove margins */
            position: relative;
            transform: rotate(90deg);
            transform-origin: left top;
        }

        .date {
            position: absolute;
            top: 0.5cm;
            right: 1cm;
            font-size: 20px;
            letter-spacing: 12px;
        }

        .payee {
            position: absolute;
            top: 2cm;
            left: 2.3cm;
            font-size: 13px;
            font-weight: bold;
        }

        .amount-words {
            position: absolute;
            top: 3cm;
            left: 2.3cm;
            width: 16cm;
            font-size: 13px;
            font-weight: bold;
        }

        .amount-figures {
            position: absolute;
            top: 3.7cm;
            right: 1cm;
            font-size: 15px;
            font-weight: bold;
        }

        .layout_payee {
            position: absolute;
            top: 5.5cm;
            /* Adjust the vertical position as needed */
            left: 6.4cm;
            /* Adjust the horizontal position as needed */
            text-align: left;
            /* Aligns content to the left */
            font-size: 10px;
            font-weight: bold;
            width: auto;
            /* Allows the text to determine the width */
        }

        .layout_payee::before,
        .layout_payee::after {
            content: '';
            display: block;
            width: 100%;
            /* Matches the text width */
            height: 1px;
            /* Thickness of the line */
            background-color: black;
            /* Line color */
            margin: 0;
            /* Removes extra space around the lines */
        }

        .layout_payee::before {
            margin-bottom: 2px;
            /* Space between the top line and the text */
        }

        .layout_payee::after {
            margin-top: 2px;
            /* Space between the bottom line and the text */
        }


        .layout_payee_validity {
            position: absolute;
            top: 4.8cm;
            /* Adjust the vertical position as needed */
            left: 6.4cm;

            /* top: 5.5cm;
            left: 6.2cm; */
            /* Adjust the horizontal position as needed */
            text-align: left;
            /* Aligns content to the left */
            font-size: 10px;
            font-weight: bold;
            width: auto;
            /* Allows the text to determine the width */
        }

        .layout_payee_validity::before,
        .layout_payee_validity::after {
            content: '';
            display: block;
            width: 100%;
            /* Matches the text width */
            height: 1px;
            /* Thickness of the line */
            background-color: black;
            /* Line color */
            margin: 0;
            /* Removes extra space around the lines */
        }

        .layout_payee_validity::before {
            margin-bottom: 2px;
            /* Space between the top line and the text */
        }

        .layout_payee_validity::after {
            margin-top: 2%;
            /* Space between the bottom line and the text */
        }

        .signature-section {
            position: absolute;
            bottom: 60px;
            right: 30px;
            font-size: 12px;
            text-align: center;
            margin-top: 5px;
            /* Added top margin */
        }

        .signature {
            margin-bottom: 0px;
            /* Reduced bottom margin */
            line-height: 1.2;
            /* Reduced line spacing for signatures */
        }

        .signature div {
            display: inline-block;
            /* Ensures signatures align properly */
            margin: 0 10px;
            /* Adjust horizontal spacing */
        }


        .layout_company_logo {
            position: absolute;
            top: 5.4cm;
            right: 3.1cm;
            height: auto;
            width: 15%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        #layout_cheque_date {
            letter-spacing: 15px;
            left: 12px;

            /* Add space between characters */
        }


    </style>
</head>
<body>
    <div class="cheque" >
        <!-- Date -->
        <div class="date"><span id="layout_cheque_date">{{ $date }}</span></div>

        <!-- Payee Name -->
        <div class="payee"><span id="layout_vendor_name">{{ $payee_name }}</span></div>

        <!-- Amount in Words -->
        {{-- <div class="amount-label">Rupees:</div> --}}
        <div class="amount-words">**<span id="layout_amount_on_words">{{ $amountWords }}</span> Only**</div>

        <!-- Amount in Figures -->
        {{-- <div class="amount-box">Rs.</div> --}}
        <div class="amount-figures">**<span id="layout_payment_amount">{{ $amount }}</span>**</div>
        <div class="layout_company_logo">
            <img src="{{ public_path("storage/$logo")}}" alt="Company Logo" width="50%" />
        </div>
        <div class="layout_payee"><span id="layout_payee">{{ $payment_condition }}</span></div>
        @if($validity_period)
        <div class="layout_payee_validity">
            <span id="layout_payee_validity">{{ $validity_period }}</span>
        </div>
        @endif
        <div class="signature-section">
            @if($signature == 1)
            <div class="signature" id="1">
                <div>___________________<br>Director</div>
            </div>
            @endif

            @if($signature == 2)
            <div class="signature" id="2">
                <div>___________________<br>Director</div>
                <div>___________________<br>Director</div>
            </div>
            @endif

            @if($signature == 3)
            <div class="signature" id="3">
                <div>___________________<br>Director</div>
                <div>___________________<br>Director</div>
                <div>___________________<br>Director</div>
            </div>
            @endif

            @if($signature == 4)
            <div class="signature" id="4">
                <div>___________________<br>Director</div>
                <div>___________________<br>Authorized Signatory</div>
            </div>
            @endif

            @if($signature == 5)
            <div class="signature" id="5">
                <div>___________________<br>Director</div>
                <div>___________________<br>Director</div>
                <div>___________________<br>Authorized Signatory</div>
            </div>
            @endif
        </div>

    </div>
</body>
</html>
