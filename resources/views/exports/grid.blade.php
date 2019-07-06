<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Grid Data</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                <th>{{$header['content']}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($body as $row)
                <tr>
                @foreach($row['columns'] as $column)
                @if(isset($column['align']) && $column['align'] == 'text-right')
                    <td style="text-align:right">{{$column['content']}}</td>
                @else
                    <td>{{$column['content']}}</td>
                @endif
                @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>