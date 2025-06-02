<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de clientes!</h5>
                    <h4 class="text-center mb-2 text-primary">MANIPULACION DE CLIENTES</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormClientes">
                        <input type="hidden" id="cliente_id" name="cliente_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="cliente_nombres" class="form-label">INGRESE EL NOMBRE DEL CLIENTE</label>
                                <input type="text" class="form-control" id="cliente_nombres" name="cliente_nombres" placeholder="Juan">
                            </div>
                            <div class="col-lg-6">
                                <label for="cliente_apellidos" class="form-label">INGRESE LOS APELLIDOS</label>
                                <input type="text" class="form-control" id="cliente_apellidos" name="cliente_apellidos" placeholder="Torres">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="cliente_nit" class="form-label">INGRESE EL NIT</label>
                                <input type="number" class="form-control" id="cliente_nit" name="cliente_nit" placeholder="Sin guiones">
                            </div>
                            <div class="col-lg-6">
                                <label for="cliente_telefono" class="form-label">INGRESE EL TELEFONO</label>
                                <input type="number" class="form-control" id="cliente_telefono" name="cliente_telefono" placeholder="Ingrese aca el numero de telefono sin el +502">
                            </div>
                        </div>



                        <div class="row mb-3 justify-content-center mb-3">
                            <div class="col-lg-6">
                                <label for="cliente_correo" class="form-label">INGRESE EL CORREO ELECTRONICO</label>
                                <input type="email" class="form-control" id="cliente_correo" name="cliente_correo" placeholder="Ingrese aca el correo ejemplo@ejemplo.com">
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
                <h3 class="text-center">CLIENTES REGISTRADOS EN LA BASE DE DATOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableClientes">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<script src="<?= asset('build/js/clientes/index.js') ?>"></script>