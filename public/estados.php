<?php
require '../src/dbConfig.php';


$stmt = $conn->query("SELECT id, nom_cliente, estado FROM reservas");
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

//agrupar las reservas por estado
$reservasPorEstado = [];
foreach ($reservas as $reserva) {
    $estado = $reserva['estado'];
    if (!isset($reservasPorEstado[$estado])) {
        $reservasPorEstado[$estado] = [];
    }
    $reservasPorEstado[$estado][] = $reserva;
}

//crear la tabla HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas Agrupadas por Estado</title>
    <link rel="stylesheet" href="style.css">
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
<div class="tContainer">
<table class="tEstados">
    <thead>
    <tr class = "tEstados2">
        <th>ENTRANTE</th>
        <th>INMINENTE</th>
        <th>PENDIENTE</th>
    </tr>
    </thead>
    <tbody>
    <?php
    //calcular cantidad de reservas en los estados para crear filas
    $maxLength = max(count($reservasPorEstado[0] ?? []), count($reservasPorEstado[1] ?? []), count($reservasPorEstado[2] ?? []));

    for ($i = 0; $i < $maxLength; $i++) {
        echo '<tr>';
        //Mostrar reservas para el estado 0
        echo '<td class ="tEstados3">';
        if (isset($reservasPorEstado[0][$i])) {
            echo "ID: " . $reservasPorEstado[0][$i]['id'] . "<br>";
            echo " " . $reservasPorEstado[0][$i]['nom_cliente'];
        }
        echo '</td class ="tEstados3">';

        //estado 1
        echo '<td class ="tEstados3">';
        if (isset($reservasPorEstado[1][$i])) {
            echo "ID: " . $reservasPorEstado[1][$i]['id'] . "<br>";
            echo " " . $reservasPorEstado[1][$i]['nom_cliente'];
        }
        echo '</td class ="tEstados3">';

        //estado 2
        echo '<td class ="tEstados3">';
        if (isset($reservasPorEstado[2][$i])) {
            echo "ID: " . $reservasPorEstado[2][$i]['id'] . "<br>";
            echo " " . $reservasPorEstado[2][$i]['nom_cliente'];
        }
        echo '</td class ="tEstados3">';

        echo '</tr>';
    }
    ?>
    </tbody>
</table>
</div>

<script>
    //Actualiza la pagina
    setInterval(function(){
        location.reload();
    }, 15000);
</script>

</body>
</html>