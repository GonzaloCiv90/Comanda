<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar el método de la petición
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener y validar los datos del carrito
    $json = file_get_contents('php://input');
    if (!$json) {
        throw new Exception('No se recibieron datos');
    }

    $carrito = json_decode($json, true);

    // Verificar si hubo error al decodificar JSON
    if ($carrito === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    // Validar estructura del carrito
    if (!is_array($carrito)) {
        throw new Exception('Formato de carrito inválido');
    }

    // Validar cada producto del carrito
    foreach ($carrito as $producto) {
        if (
            !isset($producto['id']) ||
            !isset($producto['nombre']) ||
            !isset($producto['precio']) ||
            !isset($producto['cantidad'])
        ) {
            throw new Exception('Producto con formato inválido');
        }

        // Validar tipos de datos
        if (
            !is_numeric($producto['id']) ||
            !is_string($producto['nombre']) ||
            !is_numeric($producto['precio']) ||
            !is_numeric($producto['cantidad'])
        ) {
            throw new Exception('Tipos de datos inválidos en producto');
        }

        // Validar valores
        if ($producto['precio'] <= 0 || $producto['cantidad'] <= 0) {
            throw new Exception('Valores inválidos en producto');
        }
    }

    // Actualizar el carrito en la sesión
    $_SESSION['carrito'] = $carrito;

    // Calcular totales
    $total_items = array_reduce($carrito, function ($sum, $item) {
        return $sum + $item['cantidad'];
    }, 0);

    $total_precio = array_reduce($carrito, function ($sum, $item) {
        return $sum + ($item['precio'] * $item['cantidad']);
    }, 0);

    // Responder con éxito y datos actualizados
    echo json_encode([
        'success' => true,
        'message' => 'Carrito actualizado correctamente',
        'data' => [
            'items' => $total_items,
            'total' => $total_precio,
            'carrito' => $carrito
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('Error en actualizar_carrito.php: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
