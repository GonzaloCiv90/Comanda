<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener el carrito actual o inicializarlo
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Inicializar la variable $total
$total = 0;

// Verificar si hay un carrito en localStorage solo si el actual está vacío
if (empty($carrito)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const carritoLocal = localStorage.getItem('carrito');
            if (carritoLocal) {
                const carritoData = JSON.parse(carritoLocal);
                if (Array.isArray(carritoData) && carritoData.length > 0) {
                    fetch('actualizar_carrito.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: carritoLocal
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && carritoData.length > 0) {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Agregar script de inicialización al inicio del head -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carritoLocal = localStorage.getItem('carrito');
            if (carritoLocal) {
                const carrito = JSON.parse(carritoLocal);
                if (Array.isArray(carrito) && carrito.length > 0) {
                    // Solo sincronizar si hay productos
                    fetch('actualizar_carrito.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: carritoLocal
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                console.error('Error al sincronizar carrito');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }
        });
    </script>
</head>

<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#" onclick="confirmarSalida(event)">
                <img src="logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="confirmarSalida(event)">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Menú
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="menu.php?categoria=pizzas" data-categoria="pizzas">Pizzas</a></li>
                            <li><a class="dropdown-item" href="menu.php?categoria=lomos" data-categoria="lomos">Lomos</a></li>
                            <li><a class="dropdown-item" href="menu.php?categoria=hamburguesas" data-categoria="hamburguesas">Hamburguesas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                </ul>
                <!-- Enlace/Botón para ver el carrito -->
                <a href="carrito.php" class="btn btn-warning position-relative me-3">
                    <i class="bi bi-cart"></i> Carrito
                    <span id="carrito-contador" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>
                    </span>
                </a>
                <form class="d-flex">
                    <div class="input-group">
                        <input class="form-control" type="search" placeholder="Buscar..." aria-label="Buscar">
                        <button class="btn btn-outline-dark" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </nav>


    <!-- Contenido del Carrito -->
    <div class="container py-4">
        <h1 class="text-center mb-4">Tu Carrito</h1>

        <?php if (empty($carrito)): ?>
            <div class="text-center mt-5">
                <i class="bi bi-cart-x" style="font-size: 4rem; color: #6c757d;"></i>
                <p class="lead mt-3">Tu carrito está vacío</p>
                <a href="index.php" class="btn btn-primary">Ver Menú</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <?php foreach ($carrito as $producto):
                        $subtotal = $producto['precio'] * $producto['cantidad'];
                        $total += $subtotal;
                    ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <img src="<?php echo $producto['imagen']; ?>"
                                        class="img-fluid rounded-start producto-img-carro"
                                        alt="<?php echo $producto['nombre']; ?>"
                                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%25\' height=\'100%25\' viewBox=\'0 0 300 200\' preserveAspectRatio=\'none\'%3E%3Crect width=\'100%25\' height=\'100%25\' fill=\'%23f8f9fa\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'14px\' fill=\'%23adb5bd\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="eliminarProducto(<?php echo $producto['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row align-items-center mt-3">
                                            <div class="col-sm-4">
                                                <div class="input-group cantidad-control">
                                                    <button class="btn btn-outline-secondary"
                                                        onclick="actualizarCantidad(<?php echo $producto['id']; ?>, -1)">-</button>
                                                    <input type="text" class="form-control text-center"
                                                        value="<?php echo $producto['cantidad']; ?>" readonly>
                                                    <button class="btn btn-outline-secondary"
                                                        onclick="actualizarCantidad(<?php echo $producto['id']; ?>, 1)">+</button>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="card-text mb-0">Precio: $<?php echo number_format($producto['precio'], 2); ?></p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="card-text mb-0">Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Resumen del Pedido</h5>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Envío:</span>
                                <span>Gratis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>$<?php echo number_format($total, 2); ?></strong>
                            </div>
                            <button class="btn btn-success w-100" onclick="enviarPedidoWhatsApp()">
                                <i class="bi bi-whatsapp me-2"></i>Finalizar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Función para actualizar cantidad
        function actualizarCantidad(id, cambio) {
            let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
            const producto = carrito.find(item => parseInt(item.id) === id);

            if (producto) {
                producto.cantidad = Math.max(1, producto.cantidad + cambio);
                localStorage.setItem('carrito', JSON.stringify(carrito));

                // Actualizar en el servidor
                fetch('actualizar_carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(carrito)
                    })
                    .then(() => location.reload());
            }
        }

        // Reemplazar la función eliminarProducto existente
        function eliminarProducto(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'El producto será eliminado del carrito',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash"></i> Eliminar',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
                    carrito = carrito.filter(item => parseInt(item.id) !== id);
                    localStorage.setItem('carrito', JSON.stringify(carrito));

                    fetch('actualizar_carrito.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(carrito)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: '¡Eliminado!',
                                    text: 'El producto ha sido eliminado del carrito',
                                    icon: 'success',
                                    confirmButtonColor: '#198754',
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo eliminar el producto',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        // Función para enviar pedido por WhatsApp
        function enviarPedidoWhatsApp() {
            const carrito = <?php echo json_encode($carrito); ?>;
            if (carrito.length === 0) {
                alert('No hay productos en el carrito.');
                return;
            }

            let mensaje = '¡Hola! Quiero hacer el siguiente pedido:\n\n';
            let total = 0;

            carrito.forEach(producto => {
                const subtotal = producto.precio * producto.cantidad;
                mensaje += `▪ ${producto.nombre}\n`;
                mensaje += `  Cantidad: ${producto.cantidad}\n`;
                mensaje += `  Precio unitario: $${producto.precio}\n`;
                mensaje += `  Subtotal: $${subtotal}\n\n`;
                total += subtotal;
            });

            mensaje += `\n💰 Total: $${total.toFixed(2)}`;
            mensaje = encodeURIComponent(mensaje);

            window.open(`https://wa.me/543516453580?text=${mensaje}`, '_blank');
        }

        function actualizarContadorCarrito() {
            try {
                const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
                const contador = document.getElementById('carrito-contador');
                if (contador) {
                    const totalItems = carrito.reduce((total, item) => total + (parseInt(item.cantidad) || 0), 0);
                    contador.textContent = totalItems;
                }
            } catch (error) {
                console.error('Error al actualizar contador:', error);
                document.getElementById('carrito-contador').textContent = '0';
            }
        }

        // Actualizar contador cuando cambie el carrito
        window.addEventListener('storage', (e) => {
            if (e.key === 'carrito') {
                actualizarContadorCarrito();
            }
        });

        // Actualizar contador al cargar la página
        document.addEventListener('DOMContentLoaded', actualizarContadorCarrito);

        function confirmarSalida(event) {
            event.preventDefault();

            const carrito = JSON.parse(localStorage.getItem('carrito') || '[]');

            if (carrito.length > 0) {
                Swal.fire({
                    title: '¡Atención!',
                    text: 'Tienes productos en tu carrito. Si sales ahora, perderás tu pedido actual.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-house"></i> Ir a inicio',
                    cancelButtonText: '<i class="bi bi-cart"></i> Seguir pidiendo'
                }).then((result) => {
                    if (result.isConfirmed) {
                        irAInicio();
                    }
                });
            } else {
                window.location.href = 'index.php';
            }
        }

        function irAInicio() {
            // Limpiar carrito
            localStorage.removeItem('carrito');

            // Sincronizar con el servidor
            fetch('actualizar_carrito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify([])
                })
                .then(() => {
                    // Redirigir a inicio
                    window.location.href = 'index.php';
                })
                .catch(error => {
                    console.error('Error al limpiar carrito:', error);
                    // Redirigir de todos modos
                    window.location.href = 'index.php';
                });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>