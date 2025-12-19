<!DOCTYPE html>
<html>
<head>
    <title>Games Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Games Report</h1>
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Recommended</th>
                <th>Visits</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($games as $game)
                <tr>
                    <td>{{ $game->id }}</td>
                    <td>{{ $game->name }}</td>
                    <td>{{ $game->description }}</td>
                    <td>{{ $game->category }}</td>
                    <td>{{ $game->recommended ? 'Yes' : 'No' }}</td>
                    <td>{{ $game->visits }}</td>
                    <td>{{ $game->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
