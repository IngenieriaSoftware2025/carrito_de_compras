import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

// Elementos del DOM
const FormVentas = document.getElementById('FormVentas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectCliente = document.getElementById('cliente_id');
const TotalVenta = document.getElementById('TotalVenta');

// Variables globales
let carrito = [];

// Toggle producto (seleccionar/deseleccionar)
window.ToggleProducto = (checkbox) => {
    const productoId = parseInt(checkbox.dataset.productoId);
    const cantidadInput = document.querySelector(`input.cantidad-input[data-producto-id="${productoId}"]`);
    const row = checkbox.closest('tr');
    const nombreProducto = row.cells[1].textContent;
    const precio = parseFloat(cantidadInput.dataset.precio);
    
    if (checkbox.checked) {
        cantidadInput.disabled = false;
        AgregarAlCarrito(productoId, nombreProducto, precio);
    } else {
        cantidadInput.disabled = true;
        cantidadInput.value = 1;
        QuitarDelCarrito(productoId);
    }
};

// Actualizar cantidad del producto
window.ActualizarCantidad = (input) => {
    const productoId = parseInt(input.dataset.productoId);
    const cantidad = parseInt(input.value);
    const stock = parseInt(input.dataset.stock);
    const precio = parseFloat(input.dataset.precio);
    
    // Validar stock
    if (cantidad > stock) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock insuficiente',
            text: `Solo hay ${stock} unidades disponibles`
        });
        input.value = stock;
        return;
    }
    
    if (cantidad < 1) {
        input.value = 1;
        return;
    }
    
    // Actualizar en el carrito
    const index = carrito.findIndex(item => item.producto_id === productoId);
    if (index !== -1) {
        carrito[index].cantidad = cantidad;
        carrito[index].subtotal = cantidad * precio;
        ActualizarTotales();
        ActualizarSubtotalTabla(productoId);
    }
};

// Agregar producto al carrito
const AgregarAlCarrito = (productoId, nombreProducto, precio) => {
    const cantidad = 1;
    const subtotal = cantidad * precio;
    
    const itemCarrito = {
        producto_id: productoId,
        producto_nombre: nombreProducto,
        cantidad: cantidad,
        precio_unitario: precio,
        subtotal: subtotal
    };
    
    carrito.push(itemCarrito);
    ActualizarTotales();
    ActualizarSubtotalTabla(productoId);
};

// Quitar producto del carrito
const QuitarDelCarrito = (productoId) => {
    carrito = carrito.filter(item => item.producto_id !== productoId);
    ActualizarTotales();
    ActualizarSubtotalTabla(productoId);
};

// Actualizar subtotal en la tabla de productos
const ActualizarSubtotalTabla = (productoId) => {
    const subtotalCell = document.querySelector(`.subtotal-cell[data-producto-id="${productoId}"]`);
    const item = carrito.find(i => i.producto_id === productoId);
    
    if (subtotalCell) {
        if (item) {
            subtotalCell.textContent = `Q ${item.subtotal.toFixed(2)}`;
            subtotalCell.classList.add('text-success', 'fw-bold');
        } else {
            subtotalCell.textContent = 'Q 0.00';
            subtotalCell.classList.remove('text-success', 'fw-bold');
        }
    }
};

// Actualizar totales
const ActualizarTotales = () => {
    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);
    TotalVenta.textContent = `Q ${total.toFixed(2)}`;
};

// Guardar venta
const GuardarVenta = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;
    
    // Validaciones
    if (!SelectCliente.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Cliente requerido',
            text: 'Debe seleccionar un cliente'
        });
        BtnGuardar.disabled = false;
        return;
    }
    
    if (carrito.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Productos requeridos',
            text: 'Debe seleccionar al menos un producto'
        });
        BtnGuardar.disabled = false;
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('cliente_id', SelectCliente.value);
        formData.append('productos', JSON.stringify(carrito));
        
        const url = '/carrito_de_compras/ventas/guardarAPI';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            await Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: datos.mensaje
            });
            
            LimpiarTodo();
            BuscarVentas();
        } else {
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje
            });
        }
    } catch (error) {
        console.error('Error al guardar venta:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al guardar la venta'
        });
    }
    
    BtnGuardar.disabled = false;
};

// Modificar venta
const ModificarVenta = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;
    
    if (!SelectCliente.value || carrito.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Debe seleccionar cliente y al menos un producto'
        });
        BtnModificar.disabled = false;
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('venta_id', document.getElementById('venta_id').value);
        formData.append('cliente_id', SelectCliente.value);
        formData.append('productos', JSON.stringify(carrito));
        
        const url = '/carrito_de_compras/ventas/modificarAPI';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            await Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: datos.mensaje
            });
            
            LimpiarTodo();
            BuscarVentas();
        } else {
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje
            });
        }
    } catch (error) {
        console.error('Error al modificar venta:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al modificar la venta'
        });
    }
    
    BtnModificar.disabled = false;
};

// Buscar ventas para la tabla
const BuscarVentas = async () => {
    try {
        const url = '/carrito_de_compras/ventas/buscarAPI';
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            datatable.clear().draw();
            datatable.rows.add(datos.data).draw();
        }
    } catch (error) {
        console.error('Error al buscar ventas:', error);
    }
};

// Limpiar todo el formulario
const LimpiarTodo = () => {
    FormVentas.reset();
    carrito = [];
    TotalVenta.textContent = 'Q 0.00';
    
    // Limpiar checkboxes y inputs de cantidad
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.disabled = true;
        input.value = 1;
    });
    
    document.querySelectorAll('.subtotal-cell').forEach(cell => {
        cell.textContent = 'Q 0.00';
        cell.classList.remove('text-success', 'fw-bold');
    });
    
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    document.getElementById('venta_id').value = '';
};

// Llenar formulario para modificar
const LlenarFormulario = async (event) => {
    const ventaId = event.currentTarget.dataset.id;
    
    try {
        // Obtener detalle de la venta
        const url = `/carrito_de_compras/ventas/detalleAPI?venta_id=${ventaId}`;
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            document.getElementById('venta_id').value = ventaId;
            
            // Llenar carrito con los productos de la venta
            carrito = [];
            datos.data.forEach(detalle => {
                carrito.push({
                    producto_id: detalle.producto_id,
                    producto_nombre: detalle.producto_nombre,
                    cantidad: detalle.cantidad,
                    precio_unitario: parseFloat(detalle.precio_unitario),
                    subtotal: parseFloat(detalle.subtotal)
                });
                
                // Marcar checkbox y establecer cantidad
                const checkbox = document.querySelector(`input[data-producto-id="${detalle.producto_id}"]`);
                const cantidadInput = document.querySelector(`input.cantidad-input[data-producto-id="${detalle.producto_id}"]`);
                
                if (checkbox && cantidadInput) {
                    checkbox.checked = true;
                    cantidadInput.disabled = false;
                    cantidadInput.value = detalle.cantidad;
                }
                
                ActualizarSubtotalTabla(detalle.producto_id);
            });
            
            ActualizarTotales();
            
            BtnGuardar.classList.add('d-none');
            BtnModificar.classList.remove('d-none');
            
            window.scrollTo({ top: 0 });
        }
    } catch (error) {
        console.error('Error al cargar venta:', error);
    }
};

// DataTable para ventas
const datatable = new DataTable('#TableVentas', {
    dom: `
        <"row mt-3 justify-content-between" 
            <"col" l> 
            <"col" B> 
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between" 
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'venta_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Cliente', 
            data: 'cliente_nombres',
            render: (data, type, row) => `${row.cliente_nombres} ${row.cliente_apellidos}`
        },
        { title: 'NIT', data: 'cliente_nit' },
        { 
            title: 'Fecha', 
            data: 'venta_fecha',
            render: (data) => new Date(data).toLocaleDateString()
        },
        { 
            title: 'Total', 
            data: 'venta_total',
            render: (data) => `Q ${parseFloat(data).toFixed(2)}`
        },
        {
            title: 'Acciones',
            data: 'venta_id',
            searchable: false,
            orderable: false,
            render: (data, type, row) => {
                return `
                    <div class='d-flex justify-content-center'>
                        <button class='btn btn-warning modificar mx-1' 
                                data-id="${data}">
                            <i class='bi bi-pencil-square me-1'></i> Modificar
                        </button>
                    </div>
                `;
            }
        }
    ]
});

// Event Listeners
FormVentas.addEventListener('submit', GuardarVenta);
BtnModificar.addEventListener('click', ModificarVenta);
BtnLimpiar.addEventListener('click', LimpiarTodo);

// Event listeners para DataTable
datatable.on('click', '.modificar', LlenarFormulario);

// Inicializar aplicación
BuscarVentas();