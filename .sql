
-- Tabla principal de clientes
CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    cliente_nombres VARCHAR(255),
    cliente_apellidos VARCHAR(255),
    cliente_nit INT,
    cliente_telefono INT,
    cliente_correo VARCHAR(100),
    cliente_situacion SMALLINT DEFAULT 1
)

-- Tabla principal de productos
CREATE TABLE productos (
    producto_id SERIAL PRIMARY KEY,
    producto_nombre VARCHAR(255),
    producto_descripcion VARCHAR(255),
    producto_precio DECIMAL (10,2),
    producto_cantidad INT,
    producto_situacion SMALLINT DEFAULT 1
)


-- Tabla principal de ventas
CREATE TABLE ventas (
    venta_id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL,
    venta_fecha DATETIME YEAR TO SECOND NOT NULL,
    venta_total DECIMAL(10,2),
    venta_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id)
);

-- Detalle de cada venta
CREATE TABLE venta_detalle (
    detalle_id SERIAL PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    subtotal DECIMAL(10,2),
    FOREIGN KEY (venta_id )REFERENCES ventas(venta_id),
    FOREIGN KEY (producto_id)REFERENCES productos(producto_id)
);
