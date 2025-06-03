<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación de ventas!</h5>
                    <h4 class="text-center mb-2 text-primary">MANIPULACION DE VENTAS</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormVentas">
                        <input type="hidden" id="venta_id" name="venta_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="cliente_id" class="form-label">SELECCIONE EL CLIENTE</label>
                                <select class="form-control" id="cliente_id" name="cliente_id">
                                    <option value="">-- Seleccione un cliente --</option>
                                    <?php foreach($clientes as $cliente): ?>
                                        <option value="<?= $cliente['cliente_id'] ?>">
                                            <?= $cliente['cliente_nombres'] ?> <?= $cliente['cliente_apellidos'] ?> - NIT: <?= $cliente['cliente_nit'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <h6 class="text-primary mb-3">PRODUCTOS DISPONIBLES</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th width="5%">Seleccionar</th>
                                                <th width="30%">Producto</th>
                                                <th width="15%">Precio</th>
                                                <th width="10%">Stock</th>
                                                <th width="15%">Cantidad</th>
                                                <th width="15%">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ProductosBody">
                                            <?php foreach($productos as $producto): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input" 
                                                               data-producto-id="<?= $producto['producto_id'] ?>" 
                                                               onchange="ToggleProducto(this)">
                                                    </td>
                                                    <td><?= $producto['producto_nombre'] ?></td>
                                                    <td class="text-end">Q <?= number_format($producto['producto_precio'], 2) ?></td>
                                                    <td class="text-center"><?= $producto['producto_cantidad'] ?></td>
                                                    <td>
                                                        <input type="number" class="form-control cantidad-input" 
                                                               data-producto-id="<?= $producto['producto_id'] ?>"
                                                               data-precio="<?= $producto['producto_precio'] ?>"
                                                               data-stock="<?= $producto['producto_cantidad'] ?>"
                                                               min="1" max="<?= $producto['producto_cantidad'] ?>" 
                                                               value="1" disabled
                                                               onchange="ActualizarCantidad(this)">
                                                    </td>
                                                    <td class="text-end subtotal-cell" data-producto-id="<?= $producto['producto_id'] ?>">
                                                        Q 0.00
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6 text-center">
                                <h5>TOTAL A PAGAR: <span class="text-success" id="TotalVenta">Q 0.00</span></h5>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    Guardar
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">VENTAS REGISTRADAS EN LA BASE DE DATOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableVentas">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<script src="<?= asset('build/js/ventas/index.js') ?>"></script>