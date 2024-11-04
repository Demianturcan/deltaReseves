<?php
require '../src/dbConfig.php';
require '../vendor/autoload.php';
global $conn;

use src\ExcelConfig;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $fileName = $_FILES['file']['tmp_name'];
    $data = ExcelConfig::leerReservas($fileName);

    foreach ($data as $index => $row) {
        //saltar la primera fila
        if ($index == 0) {
            continue;
        }

        //comprobar si el id ya existe
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE id = ?");
        $stmtCheck->execute([$row[0]]);
        $exists = $stmtCheck->fetchColumn();

        if ($exists > 0) {
            continue;
        }

        //convertir la fecha
        if (!empty($row[3])) {
            $dateObject = DateTime::createFromFormat('d/m/Y', $row[3]);
            if ($dateObject !== false) {
                $row[3] = $dateObject->format('Y-m-d');
            } else {
                $row[3] = null;
            }
        }

        //manejar la hora
        $hour = null;
        if (!empty($row[5])) {
            $datetimeObject = DateTime::createFromFormat('Y-m-d H:i:s', $row[5]);
            if ($datetimeObject !== false) {
                $hour = $datetimeObject->format('H:i:s'); //solo la hora
            } else {
                $hour = null;
            }
        }

        //insertar la reserva en la base de datos
        $stmt = $conn->prepare("INSERT INTO reservas (id, nom_cliente, num_pers, data, estado, hora) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$row[0], $row[1], $row[2], $row[3], $row[4], $hour]);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <nav>
        <ul class="nav">
            <li><a href="index.php">Datos</a></li>
            <li><a href="estados.php">Estados</a></li>
        </ul>
    </nav>
</div>
<div class="archivo">
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <input type="submit" value="Cargar">
    </form>
</div>
<div class="dataContainer">
    <table class="tData">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Clientes</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $stmt = $conn->query("SELECT * FROM reservas");
        while ($row = $stmt->fetch()) {
            echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['nom_cliente']}</td>
        <td>{$row['num_pers']}</td>
        <td>{$row['data']}</td>
        <td>{$row['hora']}</td> 
        <td>
            <select id='opciones' class='estado-select' data-id='{$row['id']}'>
                <option value='0' " . ($row['estado'] == 0 ? 'selected' : '') . ">0</option>
                <option value='1' " . ($row['estado'] == 1 ? 'selected' : '') . ">1</option>
                <option value='2' " . ($row['estado'] == 2 ? 'selected' : '') . ">2</option>
            </select>
            
        </td>
        <td>
           
            <button class='delete-btn' data-id='{$row['id']}'>Eliminar</button> 
        </td>
      </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.estado-select').change(function() {
            var newState = $(this).val();
            var reservaId = $(this).data('id');

            $.ajax({
                url: 'update_estado.php',
                type: 'POST',
                data: {
                    id: reservaId,
                    estado: newState
                },
                success: function(response) {
                    //actualizar la celda del estado en la tabla
                    var $row = $('select[data-id="' + reservaId + '"]').closest('tr');
                    $row.find('.estado').text(newState);
                },
                error: function(xhr, status, error) {
                    alert('Error al actualizar el estado: ' + error);
                }
            });
        });

        $('.delete-btn').click(function() {
            var reservaId = $(this).data('id');
            if(confirm('¿Estás seguro de que deseas eliminar esta fila?')) {
                $.ajax({
                    url: 'delete_reserva.php',
                    method: 'POST',
                    data: { id: reservaId },
                    success: function(response) {
                        //eliminar la fila de la tabla
                        $('button[data-id="' + reservaId + '"]').closest('tr').remove();
                    },
                    error: function(xhr, status, error) {
                        alert('Error al eliminar la reserva: ' + error);
                    }
                });
            }
        });
    });
</script>
</body>
</html>
