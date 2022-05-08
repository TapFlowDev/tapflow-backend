@component('mail::message')

    <body style="font-family: Helvetica,Arial,sans-serif;">
        <p style="color: black;">
            A transaction was made to your wallet,
            <br>
            <br>
        </p>
        <table role="presentation" style="border:'0'; color:black;  font-size: medium" cellspacing="0" width="100%">
            <tr>
                <td>Type:</td>
                <td>
                    @if ($details['transactionType'] == 1)
                        Deposit
                    @elseif ($details['transactionType'] == 2)
                        Withdrawal
                    @endif
                </td>
            </tr>
            <tr>
                <td>Amount:</td>
                <td>{{ $details['amount'] }}</td>
            </tr>
            <tr>
                <td>Currnet Amount:</td>
                <td>{{ $details['currentAmount'] }}</td>
            </tr>
        </table>
    </body>
@endcomponent
