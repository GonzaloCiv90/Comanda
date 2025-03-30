<?php
session_start();
session_destroy();
//session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mejora en la inicialización del carritoJS
try {
    $carritoJS = isset($_SESSION['carrito']) ? json_encode($_SESSION['carrito'], JSON_HEX_APOS | JSON_HEX_QUOT) : '[]';
} catch (Exception $e) {
    error_log('Error al codificar carrito: ' . $e->getMessage());
    $carritoJS = '[]';
}

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
    <title>Menú del Restaurante</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            <li><a class="dropdown-item" href="#" data-categoria="pizzas">Pizzas</a></li>
                            <li><a class="dropdown-item" href="#" data-categoria="lomos">Lomos</a></li>
                            <li><a class="dropdown-item" href="#" data-categoria="hamburguesas">Hamburguesas</a></li>
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

    <!-- Header de la Categoría -->
    <header class="bg-light py-5 mb-4">
        <div class="container">
            <div class="text-center">
                <h1 class="display-4" id="categoria-titulo">Nuestro Menú</h1>
                <p class="lead" id="categoria-descripcion">Selecciona entre nuestras deliciosas opciones</p>
            </div>
        </div>
    </header>

    <!-- Listado de Productos -->
    <main class="container">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="productos-container">
            <!-- Los productos se cargarán dinámicamente aquí -->
        </div>
    </main>

    <!-- Pie de Página -->
    <footer class="py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">© 2023 Restaurante Delicioso. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sincronizar carrito al iniciar
        if ('<?php echo $carritoJS; ?>') {
            localStorage.setItem('carrito', '<?php echo $carritoJS; ?>');
        }

        // Datos de productos
        const productos = {
            pizzas: [{
                    id: 1,
                    nombre: 'Pizza Margarita',
                    precio: 5000,
                    imagen: 'images/pizza-margarita.jpg',
                    descripcion: 'Pizza con salsa de tomate, mozzarella y albahaca'
                },
                {
                    id: 2,
                    nombre: 'Pizza Napolitana',
                    precio: 5000,
                    imagen: 'images/pizza-napolitana.jpg',
                    descripcion: 'Pizza con salsa de tomate, mozzarella, anchoas y aceitunas'
                }
            ],
            hamburguesas: [{
                    id: 3,
                    nombre: 'Hamburguesa Clásica',
                    precio: 900,
                    imagen: 'images/hamburguesa-clasica.jpg',
                    descripcion: 'Hamburguesa con lechuga, tomate, cebolla y queso'
                },
                {
                    id: 4,
                    nombre: 'Hamburguesa Especial',
                    precio: 1100,
                    imagen: 'images/hamburguesa-especial.jpg',
                    descripcion: 'Hamburguesa con bacon, huevo, queso y salsa especial'
                }
            ],
            lomos: [{
                    id: 5,
                    nombre: 'Lomo Completo',
                    precio: 1400,
                    imagen: 'images/lomo-completo.jpg',
                    descripcion: 'Lomo con lechuga, tomate, huevo, jamón y queso'
                },
                {
                    id: 6,
                    nombre: 'Lomo Especial',
                    precio: 1600,
                    imagen: 'images/lomo-especial.jpg',
                    descripcion: 'Lomo con bacon, huevo, queso, champiñones y salsa especial'
                }
            ]
        };

        // Reemplaza la función cargarProductos actual con esta versión mejorada:
        function cargarProductos(categoria) {
            const contenedor = document.getElementById('productos-container');
            const productosList = productos[categoria] || [];

            // Limpiamos el contenedor
            contenedor.innerHTML = '';

            // Creamos una fila para contener las tarjetas
            const row = document.createElement('div');
            row.className = 'row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4';

            productosList.forEach(producto => {
                const col = document.createElement('div');
                col.className = 'col mb-4';
                col.innerHTML = `
                    <div class="card h-100 product-card shadow-sm">
                        <div class="position-relative">
                            <img src="${producto.imagen}" 
                                 class="card-img-top product-img" 
                                 alt="${producto.nombre}"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%25\' height=\'100%25\' viewBox=\'0 0 300 200\' preserveAspectRatio=\'none\'%3E%3Crect width=\'100%25\' height=\'100%25\' fill=\'%23f8f9fa\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'14px\' fill=\'%23adb5bd\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'">
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3">
                                $${producto.precio}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${producto.nombre}</h5>
                            <p class="card-text flex-grow-1">${producto.descripcion}</p>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary agregar-al-carrito"
                                        data-id="${producto.id}"
                                        data-nombre="${producto.nombre}"
                                        data-precio="${producto.precio}"
                                        data-imagen="${producto.imagen}">
                                    <i class="bi bi-cart-plus"></i> Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                row.appendChild(col);
            });

            contenedor.appendChild(row);

            // Actualizar título y descripción con animación
            const titulo = document.getElementById('categoria-titulo');
            const descripcion = document.getElementById('categoria-descripcion');

            titulo.style.opacity = '0';
            descripcion.style.opacity = '0';

            setTimeout(() => {
                switch (categoria) {
                    case 'pizzas':
                        titulo.textContent = 'Nuestras Pizzas';
                        descripcion.textContent = 'Las mejores pizzas artesanales';
                        break;
                    case 'hamburguesas':
                        titulo.textContent = 'Hamburguesas';
                        descripcion.textContent = 'Deliciosas hamburguesas caseras';
                        break;
                    case 'lomos':
                        titulo.textContent = 'Lomos';
                        descripcion.textContent = 'Los más completos lomos';
                        break;
                    default:
                        titulo.textContent = 'Nuestro Menú';
                        descripcion.textContent = 'Selecciona entre nuestras deliciosas opciones';
                }

                titulo.style.opacity = '1';
                descripcion.style.opacity = '1';
            }, 300);

            // Agregar eventos a los botones
            document.querySelectorAll('.agregar-al-carrito').forEach(boton => {
                boton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const producto = {
                        id: parseInt(this.dataset.id),
                        nombre: this.dataset.nombre,
                        precio: parseFloat(this.dataset.precio),
                        imagen: this.dataset.imagen
                    };
                    agregarAlCarrito(producto);

                    // Efecto visual al agregar
                    this.classList.add('btn-success');
                    this.innerHTML = '<i class="bi bi-check2"></i> Agregado';
                    setTimeout(() => {
                        this.classList.remove('btn-success');
                        this.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al carrito';
                    }, 1500);
                });
            });
        }

        // Función para mostrar mensajes
        function mostrarMensaje(mensaje, tipo = 'success') {
            const alertaExistente = document.querySelector('.alert');
            if (alertaExistente) {
                alertaExistente.remove();
            }

            const alerta = document.createElement('div');
            alerta.className = `alert alert-${tipo} fixed-top w-50 mx-auto mt-3 text-center`;
            alerta.textContent = mensaje;
            document.body.appendChild(alerta);
            setTimeout(() => alerta.remove(), 3000);
        }

        // Función para actualizar el contador del carrito
        function actualizarContadorCarrito() {
            try {
                const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
                const contador = document.getElementById('carrito-contador');
                if (contador) {
                    const totalItems = carrito.reduce((total, item) => total + parseInt(item.cantidad), 0);
                    contador.textContent = totalItems;
                }
            } catch (error) {
                console.error('Error al actualizar contador:', error);
                mostrarMensaje('Error al actualizar el carrito', 'danger');
            }
        }

        // Actualiza la función agregarAlCarrito
        function agregarAlCarrito(producto) {
            try {
                let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

                const productoExistente = carrito.find(item => parseInt(item.id) === producto.id);
                if (productoExistente) {
                    productoExistente.cantidad += 1;
                } else {
                    carrito.push({
                        ...producto,
                        cantidad: 1
                    });
                }

                localStorage.setItem('carrito', JSON.stringify(carrito));

                // Sincronizar con el servidor
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
                            mostrarMensaje(`${producto.nombre} agregado al carrito`);
                            actualizarContadorCarrito();
                        } else {
                            throw new Error(data.error || 'Error al actualizar el carrito');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarMensaje('Error al agregar el producto', 'danger');
                    });

            } catch (error) {
                console.error('Error al agregar al carrito:', error);
                mostrarMensaje('Error al agregar el producto', 'danger');
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            // Cargar productos iniciales
            const urlParams = new URLSearchParams(window.location.search);
            const categoriaInicial = urlParams.get('categoria') || 'pizzas';
            cargarProductos(categoriaInicial);

            // Eventos para los enlaces del menú
            document.querySelectorAll('.dropdown-item').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const categoria = e.target.dataset.categoria;
                    cargarProductos(categoria);
                    history.pushState(null, '', `?categoria=${categoria}`);
                });
            });

            // Actualizar contador inicial
            actualizarContadorCarrito();
        });

        // Reemplaza la función confirmarSalida actual
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
</body>

</html>