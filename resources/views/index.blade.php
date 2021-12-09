<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Logs</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Lista de Logs</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Perfil</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Accion</th>
                        <th scope="col">Fecha</th>
                      </tr>
                    </thead>
                    <tbody>

                        @foreach ($logs as $log)
                        <tr>
                            <th scope="row">{{ $log->Perfil }}</th>
                            <td>{{ $log->Usuario }}</td>
                            <td>{{ $log->Accion }}</td>
                            <td>{{ $log->fecha }}</td>
                          </tr>
                        @endforeach

                    </tbody>
                  </table>
            </div>
        </div>
    </div>
</body>
</html>
