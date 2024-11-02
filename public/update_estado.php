<?php
require '../src/dbConfig.php';  // Asegúrate de que la conexión se establece aquí

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['estado'])) {
    $id = intval($_POST['id']);
    $estado = intval($_POST['estado']);

    // Actualizar el estado en la base de datos
    $stmt = $conn->prepare("UPDATE reservas SET estado = ? WHERE id = ?");
    if ($stmt->execute([$estado, $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>