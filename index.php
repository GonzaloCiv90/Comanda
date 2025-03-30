<?php
session_start();
session_destroy();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicializar variable carritoJS
$carritoJS = isset($_SESSION['carrito']) ? json_encode($_SESSION['carrito']) : '[]';

// Verificar que las sesiones funcionan
if (!isset($_SESSION)) {
    die('Error: Las sesiones no están funcionando correctamente.');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Delicioso</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#" onclick="confirmarSalida(event)">
                <img src="logo.png" alt="Logo" width="100">
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
                            <li><a class="dropdown-item" href="menu.php?categoria=pizzas">Pizzas</a></li>
                            <li><a class="dropdown-item" href="menu.php?categoria=lomos">Lomos</a></li>
                            <li><a class="dropdown-item" href="menu.php?categoria=hamburguesas">Hamburguesas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="carrito.php" class="btn btn-warning position-relative me-3">
                        <i class="bi bi-cart"></i> Carrito
                        <span id="carrito-contador" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
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
        </div>
    </nav>

    <!-- Carrusel con Títulos -->
    <header class="mb-5">
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" style="background-image: url('img1.jpg');">
                    <div class="carousel-caption d-none d-md-block">
                        <h1 class="display-4">Bienvenidos a Nuestro Restaurante</h1>
                        <h2 class="h3">Los mejores sabores en un solo lugar</h2>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('img2.jpg');"></div>
                <div class="carousel-item" style="background-image: url('img3.jpg');"></div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </header>

    <!-- Sección Principal -->
    <main class="container py-5">
        <section class="text-center mb-5">
            <h2 class="mb-4">Nuestras Especialidades</h2>
            <p class="lead">Disfruta de nuestros platos preparados con los mejores ingredientes y recetas tradicionales.</p>
        </section>

        <!-- Categorías -->
        <section id="categorias" class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card category-card h-100 text-center p-4">
                    <i class="bi bi-egg-fried category-icon mb-3"></i>
                    <h3>Lomos</h3>
                    <p>Deliciosos lomos con variedad de ingredientes y salsas especiales.</p>
                    <a href="menu.php?categoria=lomos" class="btn btn-warning">Ver Menú</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card category-card h-100 text-center p-4">
                    <i class="bi bi-basket category-icon mb-3"></i>
                    <h3>Hamburguesas</h3>
                    <p>Jugosas hamburguesas con carne 100% de res y opciones vegetarianas.</p>
                    <a href="menu.php?categoria=hamburguesas" class="btn btn-warning">Ver Menú</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card category-card h-100 text-center p-4">
                    <i class="bi bi-pie-chart category-icon mb-3"></i>
                    <h3>Pizzas</h3>
                    <p>Pizzas artesanales con masa fresca y ingredientes de primera calidad.</p>
                    <a href="menu.php?categoria=pizzas" class="btn btn-warning">Ver Menú</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Pie de Página -->
    <footer class="py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Contacto</h5>
                    <p><i class="bi bi-geo-alt-fill"></i> Dirección del local</p>
                    <p><i class="bi bi-telephone-fill"></i> +54 123 456789</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Horarios</h5>
                    <p>Lunes a Viernes: 11:00 - 23:00</p>
                    <p>Sábados y Domingos: 11:00 - 00:00</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Síguenos</h5>
                    <div class="fs-3">
                        <i class="bi bi-facebook me-2"></i>
                        <i class="bi bi-instagram me-2"></i>
                        <i class="bi bi-twitter"></i>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <p class="text-center mb-0">© 2024 Restaurante Delicioso. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sincronizar carrito al iniciar
        if ('<?php echo $carritoJS; ?>') {
            localStorage.setItem('carrito', '<?php echo $carritoJS; ?>');
        }

        function actualizarContadorCarrito() {
            try {
                const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
                const contador = document.getElementById('carrito-contador');
                if (contador) {
                    const totalItems = carrito.reduce((total, item) => total + (parseInt(item.cantidad) || 1), 0);
                    contador.textContent = totalItems;
                }
            } catch (error) {
                console.error('Error al actualizar contador:', error);
            }
        }

        // Inicializar contador al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            actualizarContadorCarrito();
        });

        // Actualizar contador cuando cambie el localStorage
        window.addEventListener('storage', (e) => {
            if (e.key === 'carrito') {
                actualizarContadorCarrito();
            }
        });
    </script>
</body>

</html>