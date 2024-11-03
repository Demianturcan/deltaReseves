<?php
require '../src/dbConfig.php';
require '../vendor/autoload.php';
global $conn;

use src\ExcelConfig;




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $fileName = $_FILES['file']['tmp_name'];
    $data = ExcelConfig::leerReservas($fileName);

    foreach ($data as $index => $row) {
        if ($index == 0) {
            // Skip the first row (header)
            continue;
        }
        array_pop($row);

        // Check if the ID already exists
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE id = ?");
        $stmtCheck->execute([$row[0]]);
        $exists = $stmtCheck->fetchColumn();

        if ($exists > 0) {
            // Skip this row if the ID already exists
            continue;
        }
        echo '<pre>'; // Formato más legible
        print_r($datos);
        echo '</pre>';
        // Convert the date from "dd-mm-yyyy" format to "YYYY-MM-DD"
        if (!empty($row[3])) { // Assuming the date is in the 4th column (index 3)
            $dateObject = DateTime::createFromFormat('d-m-Y', $row[3]);
            if ($dateObject !== false) {
                $row[3] = $dateObject->format('Y-m-d'); // Change to "YYYY-MM-DD"
            } else {
                // Handle the error if date conversion fails
                // For example, you could set it to NULL or log an error
                $row[3] = null; // or continue, or some error handling
            }
        }


        $stmt = $conn->prepare("INSERT INTO reservas (id, nom_cliente, num_pers, data, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$row[0], $row[1], $row[2], $row[3], $row[4]]); // Asegúrate de que sean las columnas correctas
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
<div class ="dataContainer">
<table class="tData">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Clientes</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Actualizar</th>
    </tr>
    </thead>
    <tbody>



    <?php
    $stmt = $conn->query("SELECT * FROM reservas");
    while ($row = $stmt->fetch()) {
        echo "<tr >
                <td>{$row['id']}</td>
                <td>{$row['nom_cliente']}</td>
                <td>{$row['num_pers']}</td>
                <td>{$row['data']}</td>
                <td >{$row['estado']}</td>
                <td>
                    <select  id='opciones' onchange='refreshPage2()' class='estado-select' data-id='{$row['id']}'>
                        <option id value='0' " . ($row['estado'] == 0 ? 'selected' : '') . ">0</option>
                        <option id value='1' " . ($row['estado'] == 1 ? 'selected' : '') . ">1</option>
                        <option id value='2' " . ($row['estado'] == 2 ? 'selected' : '') . ">2</option>
                    </select>
                    
                </td>
                
              </tr>";
    }
    ?>

    <script>
        function refreshPage2() {
            // Redirect to page2.php to trigger a refresh
            //window.location.href = 'estados.php';
        }
    </script>
    </tbody>
</table>
</div>

<script>
    $(document).ready(function() {
        $('.estado-select').change(function() {
            var newState = $(this).val();
            var reservaId = $(this).data('id');

            $.ajax({
                url: 'update_estado.php', // Archivo que manejará la actualización
                type: 'POST',
                data: {
                    id: reservaId,
                    estado: newState
                },
                success: function(response) {
                    // Actualizar la celda del estado en la tabla
                    var $row = $('select[data-id="' + reservaId + '"]').closest('tr');
                    $row.find('.estado').text(newState);
                },
                error: function(xhr, status, error) {
                    alert('Error al actualizar el estado: ' + error);
                }
            });
        });
    });
</script>
</body>
</html>