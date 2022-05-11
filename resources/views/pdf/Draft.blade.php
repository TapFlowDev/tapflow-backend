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
                <td colspan="20">
                    <h3>{{$agency_name}}</h3>
                </td>
            </tr>
            <tr>
                <td><strong>Hours</strong></td>
                <td >{{$hours}}</td>
                <td ><strong>Final Cost</strong></td>
                <td>{{$price}}</td>
                <td ><strong>Starting Date</strong></td>
                <td>{{$starting_date}}</td>
            </tr>
            <tr>
                <td  colspan="20">
                    <h4> {{$title}}</h4>
                    <p>{{$description}}</p>
                </td>
            </tr>
            
            <tr ><td  colspan="20">{!! $milestones !!}</td></tr>
        </tbody>
    </table>
</body>