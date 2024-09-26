<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editable Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .editable {
            border: none;
            background-color: transparent;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .editable:focus {
            outline: none;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="text" class="editable" value="Header 1" disabled></th>
                    <th><input type="text" class="editable" value="Header 2"></th>
                    <th><input type="text" class="editable" value="Header 3"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="editable" value="Row Header 1" disabled></td>
                    <td><input type="text" class="editable" value="Cell 1"></td>
                    <td><input type="text" class="editable" value="Cell 2"></td>
                </tr>
                <tr>
                    <td><input type="text" class="editable" value="Row Header 2" disabled></td>
                    <td><input type="text" class="editable" value="Cell 3"></td>
                    <td><input type="text" class="editable" value="Cell 4"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
