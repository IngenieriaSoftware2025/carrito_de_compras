<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Clase No. 1</title>
</head>

<body>
    <nav id="navbar-example2" class="navbar bg-dark px-3 mb-3">
        <a class="navbar-brand text-white" href="/carrito_de_compras/">INICIO</a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link" href="/carrito_de_compras/clientes"> <i class="bi bi-person-add"></i> Clientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/carrito_de_compras/productos"> <i class="bi bi-inboxes"></i> Productos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/carrito_de_compras/ventas"><i class="bi bi-cart-check-fill"></i> Ventas</a>
            </li>
        </ul>
    </nav>
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">

        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid ">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>

</html>