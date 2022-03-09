@component('mail::message')
    <body style="font-family: Helvetica,Arial,sans-serif;">
        <h1 style="color: black; font-weight: bold;">Hey Abed,</h1>
        <p style="color: black;">
            There is new demo request,
            <br>
        </p>
        <p style="color: black; font-weight: bold;">
            Here is his Information:
        </p>
        <table role="presentation" style="border:'0'; color:black;  font-size: medium" cellspacing="0" width="100%">
            <tr>
                <td>Name:</td>
                <td>{{ $details['name'] }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $details['email'] }}</td>
            </tr>
            @foreach ($details['questions'] as $question)
                <tr>
                    <td>{{ $question['label'] }}</td>
                    <td>{{ $question['answer'] }}</td>
                </tr>
            @endforeach
        </table>
        <br>
        <p style="color: black;">
            Good luck,
        </p>
    </body>
@endcomponent
