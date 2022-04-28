<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td {
            padding: 0;
        }

        table tr td:last-child {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .large {
            font-size: 1.75em;
        }

        .total {
            font-weight: bold;
            color: #fb7578;
        }

        .logo-container {
            margin: 20px 0 70px 0;
        }

        .invoice-info-container {
            font-size: 0.875em;
        }

        .invoice-info-container td {
            padding: 4px 0;
        }

        .client-name {
            font-size: 1.5em;
            vertical-align: top;
        }

        .line-items-container {
            margin: 70px 0;
            font-size: 0.875em;
        }

        .line-items-container th {
            text-align: left;
            color: #999;
            border-bottom: 2px solid #ddd;
            padding: 10px 0 15px 0;
            font-size: 0.75em;
            text-transform: uppercase;
        }

        .line-items-container th:last-child {
            text-align: right;
        }

        .line-items-container td {
            padding: 15px 0;
        }

        .line-items-container tbody tr:first-child td {
            padding-top: 25px;
        }

        .line-items-container.has-bottom-border tbody tr:last-child td {
            padding-bottom: 25px;
            border-bottom: 2px solid #ddd;
        }

        .line-items-container.has-bottom-border {
            margin-bottom: 0;
        }

        .line-items-container th.heading-quantity {
            width: 50px;
        }

        .line-items-container th.heading-price {
            text-align: right;
            width: 100px;
        }

        .line-items-container th.heading-subtotal {
            width: 100px;
        }

        .payment-info {
            width: 38%;
            font-size: 0.75em;
            line-height: 1.5;
        }

        .footer {
            margin-top: 100px;
        }

        .footer-thanks {
            font-size: 1.125em;
        }

        .footer-thanks img {
            display: inline-block;
            position: relative;
            top: 1px;
            width: 16px;
            margin-right: 4px;
        }

        .footer-info {
            float: right;
            margin-top: 5px;
            font-size: 0.75em;
            color: #ccc;
        }

        .footer-info span {
            padding: 0 5px;
            color: black;
        }

        .footer-info span:last-child {
            padding-right: 0;
        }

        .page-container {
            display: none;
        }

        .web-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 50px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

 
        :root {
            -moz-tab-size: 4;
            tab-size: 4;
        }


        html {
            line-height: 1.15;
            /* 1 */
            -webkit-text-size-adjust: 100%;
            /* 2 */
        }


        body {
            margin: 0;
        }

       

        body {
            font-family:
                system-ui,
                -apple-system,
                'Segoe UI',
                Roboto,
                Helvetica,
                Arial,
                sans-serif,
                'Apple Color Emoji',
                'Segoe UI Emoji';
        }



        hr {
            height: 0;
            /* 1 */
            color: inherit;
            /* 2 */
        }

  

        abbr[title] {
            text-decoration: underline dotted;
        }

        b,
        strong {
            font-weight: bolder;
        }



        code,
        kbd,
        samp,
        pre {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Consolas,
                'Liberation Mono',
                Menlo,
                monospace;
           
            font-size: 1em;
           
        }

 
        small {
            font-size: 80%;
        }

      

        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline;
        }

        sub {
            bottom: -0.25em;
        }

        sup {
            top: -0.5em;
        }


        table {
            text-indent: 0;
            border-color: inherit;
        }


        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            font-size: 100%;
            line-height: 1.15;
            margin: 0;
        }

        button,
        select {
            text-transform: none;
        }

        button,
        [type='button'],
        [type='reset'],
        [type='submit'] {
            -webkit-appearance: button;
        }


        ::-moz-focus-inner {
            border-style: none;
            padding: 0;
        }

 

        :-moz-focusring {
            outline: 1px dotted ButtonText;
        }


        :-moz-ui-invalid {
            box-shadow: none;
        }


        legend {
            padding: 0;
        }

  

        progress {
            vertical-align: baseline;
        }

  
        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto;
        }


        [type='search'] {
            -webkit-appearance: textfield;
            outline-offset: -2px;
        }

   
        ::-webkit-search-decoration {
            -webkit-appearance: none;
        }


        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            font: inherit;
        }


        summary {
            display: list-item;
        }

    </style>
    {{-- <meta charset="utf-8"> --}}
    <title>Invoice Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <link href="{{ asset('css/web/modern-normalize.css') }}" rel="stylesheet">
  <link href="{{ asset('css/web/web-base.css') }}" rel="stylesheet">
  <link href="{{ asset('css/web/invoice.css') }}" rel="stylesheet"> --}}
</head>

<div class="web-container">

    <!-- <div class="page-container">
      Page
      <span class="page"></span>
      of
      <span class="pages"></span>
    </div> -->

     <div class="logo-container">
      <img
        style="height: 100px"
        src="\public\images\profile-pic.jpg"
      >
    </div> 

    <table class="invoice-info-container">
        <tr>
            <td rowspan="2" class="client-name">
                Client Name
            </td>
            <td>
                Anvil Co
            </td>
        </tr>
        <tr>
            <td>
                123 Main Street
            </td>
        </tr>
        <tr>
            <td>
                Invoice Date: <strong>May 24th, 2024</strong>
            </td>
            <td>
                San Francisco CA, 94103
            </td>
        </tr>
        <tr>
            <td>
                Invoice No: <strong>12345</strong>
            </td>
            <td>
                hello@useanvil.com
            </td>
        </tr>
    </table>


    <table class="line-items-container">
        <thead>
            <tr>
                <!-- <th class="heading-quantity">Qty</th> -->
                <th class="heading-description">Description</th>
                <th class="heading-price">Price</th>
                <!-- <th class="heading-subtotal">Subtotal</th> -->
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- <td>2</td> -->
                <td>Blue large widgets</td>
                <td class="right">$15.00</td>
                <!-- <td class="bold">$30.00</td> -->
            </tr>
            <tr>
                <!-- <td>4</td> -->
                <td>Green medium widgets</td>
                <td class="right">$10.00</td>
                <!-- <td class="bold">$40.00</td> -->
            </tr>
            <tr>
                <!-- <td>5</td> -->
                <td>Red small widgets with logo</td>
                <td class="right">$7.00</td>
                <!-- <td class="bold">$35.00</td> -->
            </tr>
        </tbody>
    </table>


    <table class="line-items-container has-bottom-border">
        <thead>
            <tr>
                <!-- <th>Payment Info</th> -->
                <th>Due By</th>
                <th>Total Due</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- <td class="payment-info">
            <div>
              Account No: <strong>123567744</strong>
            </div>
            <div>
              Routing No: <strong>120000547</strong>
            </div>
          </td> -->
                <td class="large">May 30th, 2024</td>
                <td class="large total">$105.00</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-info">
            <span>hello@useanvil.com</span> |
            <!-- <span>555 444 6666</span> | -->
            <span>useanvil.com</span>
        </div>
        <!-- <div class="footer-thanks">
        <img src="https://github.com/anvilco/html-pdf-invoice-template/raw/main/img/heart.png" alt="heart">
        <span>Thank you!</span>
      </div> -->
    </div>


</div>

<!-- <script type="text/javascript">
    load(document.querySelector('.web-container'), './invoice.html');
</script> -->

</body>

</html>
