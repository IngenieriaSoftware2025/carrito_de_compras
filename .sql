CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    cliente_nombres VARCHAR(255),
    cliente_apellidos VARCHAR(255),
    cliente_nit INT,
    cliente_telefono INT,
    cliente_correo VARCHAR(100),
    cleinte_estado CHAR(1),
    cliente_situacion SMALLINT DEFAULT 1
)

CREATE TABLE productos (
    producto_id SERIAL PRIMARY KEY,
    producto_nombre VARCHAR(255),
    producto_descripcion VARCHAR(255),
    producto_precio INT,
    producto_cantidad INT,
    producto_situacion SMALLINT DEFAULT 1
)