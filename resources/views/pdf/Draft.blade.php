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
                "Segoe UI",
                Roboto,
                Helvetica,
                Arial,
                sans-serif,
                "Apple Color Emoji",
                "Segoe UI Emoji";
        }
    </style>
</head>

<body>
    <table role="presentation" style="border:0; color:black;  font-size: medium" cellspacing='0' width="100%">
        <tbody>
            <tr >
                <td colspan="6">
                    <h3>{{$agency_name}}</h3>
                </td>
            </tr>
            <tr>
                <td width="10%"><strong>Hours</strong></td>
                <td width="10%">{{$hours}}</td>
                <td width="20%"><strong>Final Cost</strong></td>
                <td width="20%">{{$price}}</td>
                <td width="20%"><strong>Starting Date</strong></td>
                <td width="20%">{{$starting_date}}</td>
            </tr>
            <tr>
                <td  colspan="6">
                    <h3> {{$title}}</h3 >
                    <p>{{$description}}</p>
                </td>
            </tr>
            <tr>
                <td  colspan="6">
                    <h4>Milestones </h4>
                </td>
            </tr>
           {!! $milestones !!}
        </tbody>
    </table>
</body>