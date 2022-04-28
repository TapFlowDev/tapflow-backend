<!DOCTYPE html>
<html>

<head>
    <style>
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
    </style>
    <title>Transfare Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="margin-left: 80px;margin-right: 80px;">
    <div>

        <img style="height: 50px; width: 184px" src="https://operations.tapflow.dev/images/tapflow.jpeg">
    </div>
    <div>
        <h2> Deposit Details</h2>
    </div>
    <div>
        <br>
    </div>
    <table width="100%">
        <tbody>
            <tr height="40px">
                <td style="font-weight: 900;">Refrence Number</td>
                <td style="color:#8F8DA5; align='right';">{{ $ref_number }}</td>
            </tr>
            <tr height="40px">
                <td style="font-weight: 900;">Amount</td>
                <td style="color:#8F8DA5; align='right';">${{ $amount }}</td>
            </tr>

            <tr height="40px">
                <td style="font-weight: 900;">Date</td>
                <td style="color:#8F8DA5; align='right';">{{ $date }}</td>
            </tr>
            <tr>
                <td>
                    <h2> Transfer Details</h2>
                    <h3 style="color:#0B3AE1;font-weight: 600;"> Domestic</h3>
                    <p style="font-weight: 600;">Use these details to send both domestic wires and ACH transfers to
                        Tapflow,inc's Mercury account.</p>
                </td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody width="100%">
            <tr>
                <td style="color:#0B3AE1;font-weight: 500;">Beneficiary</td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;" width="200px"> Beneficiary Name </td>
                <td> Tapfloe.inc </td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;"> Account Number </td>
                <td> 9801821958 </td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;"> Type Of Account </td>
                <td> Checking </td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;">Beneficiary Address </td>
                <td>651 North Broad Street, Suite 206
                    Middletown, DE 19709 </td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody width="100%">
            <tr>
                <td style="color:#0B3AE1;font-weight: 500;">Receiving Bank Details</td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;" width="200px"> ABA Routing Number </td>
                <td> 084106768</td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;"> Bank Name </td>
                <td>
                    <h4>Evolve Bank & Trust</h4>
                    <p style="color:#8F8DA5;font-size: x-small; margin-top:-15px ;">
                        Mercury uses Evolve Bank & Trust as a banking partner.
                    </p>
                </td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;"> Bank Address </td>
                <td>6070 Poplar Ave, Suite 200
                    Memphis, TN 38119</td>
            </tr>

        </tbody>
    </table>
    <br>
    <table>
        <tbody width="100%">
            <tr>
                <td style="color:#0B3AE1;font-weight: 500;" width="200px" valign="top">
                    Tapflow,inc.<p> International Wire Details</p>
                </td>
                <td style="background-color:#d2d8d9; border-radius: 10px; padding: 10px;">
                    <strong>Important.</strong> To successfully send a wire:
                    <ul>
                        <li> use Evolve Bank & Trust details in the beneficiary (recipient) field</li>
                        <li> use Tapflow,inc's details in the reference field</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <p style="font-size:x-small;">
                        If you are filling out a wire form, please reference the section labels with MT103 field numbers
                        in grey.
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table>
        <tbody width="100%">
            <tr>
                <td style="color:#0B3AE1;font-weight: 500;">Receiving Bank Details</td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;" width="200px"> 57D Account with institution </td>
                <td style="color:#8F8DA5;" width="200px">SWift/BIC Code </td>
                <td style="font-weight: 500;" width="200px">
                    FRNAUS44XXX
                    <p style="font-size:x-small;color:#8F8DA5;">
                        Remove the trailing XXX if you are asked for an eight-digit code.
                    </p>
                </td>
            </tr>
            <tr height="40px">
                <td></td>
                <td style="color:#8F8DA5;"> Bank Name </td>
                <td>
                    <h4>First National Bankers Bank</h4>
                </td>
            </tr>
            <tr height="40px">
                <td></td>
                <td style="color:#8F8DA5;"> Bank Address </td>
                <td> 7813 Office Park Blvd
                    Baton Rouge, LA, 70809
                    USA
                </td>
            </tr>
            <tr>
                <td style="color:#0B3AE1;font-weight: 500;">Beneficiary</td>
            </tr>
            <tr height="40px">
                <td style="color:#8F8DA5;" width="200px"> 59 Beneficiary customer name & address </td>
                <td style="color:#8F8DA5;" width="200px">IBAN/Account Number </td>
                <td style="font-weight: 500;" width="200px">
                    084106768
                </td>
            </tr>
            <tr height="40px">
                <td></td>
                <td style="color:#8F8DA5;"> Beneficiary Name </td>
                <td>
                    Evolve Bank & Trust
                </td>
            </tr>
            <tr height="40px">
                <td></td>
                <td style="color:#8F8DA5;"> Beneficiary Address  </td>
                <td> 6070 Poplar Ave, Suite 200
                    Memphis, TN 38119
                    USA </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table>
        <tbody>
            <tr>
                <td style="color:#0B3AE1;font-weight: 500;" width="200px">Reference field <p
                        style="font-size:x-small;color:#8F8DA5;">
                        70 Remittance information
                    </p>
                </td>
                <td>
                    Account 19816816846 for Tapflow, inc. at Evolve Bank &Trust.
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
