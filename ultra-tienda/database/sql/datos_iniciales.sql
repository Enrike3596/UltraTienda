-- =====================================================================
-- Ultra Tienda · Datos iniciales para PostgreSQL
-- Base de datos: UltraTienda (DB_CONNECTION=pgsql)
-- Uso: ejecutar DESPUÉS de `php artisan migrate` sobre la BD vacía.
--      php artisan tinker --execute 'DB::unprepared(file_get_contents(''database/sql/datos_iniciales.sql''));'
--
-- Contenido: 5 registros por módulo con claves foráneas válidas y
--            totales coherentes (total = subtotal + domicilio).
--
-- Credenciales de acceso:
--   admin@ultratienda.com / Admin1234   (rol: admin)
--   carlos.vendedor@ultratienda.com / Admin1234
--   mariana.bodega@ultratienda.com / Admin1234
--   laura.cliente@ultratienda.com / Admin1234
--   invitado@ultratienda.com / Admin1234
-- =====================================================================

BEGIN;

-- ---------------------------------------------------------------------
-- 1) roles (5)
-- ---------------------------------------------------------------------
INSERT INTO roles (id, name, created_at, updated_at) VALUES
    (1, 'admin',     '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
    (2, 'vendedor',  '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
    (3, 'bodeguero', '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
    (4, 'cliente',   '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
    (5, 'invitado',  '2026-10-01 08:00:00', '2026-10-01 08:00:00')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------
-- 2) users (5) · contraseña de todos: Admin1234 (bcrypt)
-- ---------------------------------------------------------------------
INSERT INTO users (id, name, email, email_verified_at, password, remember_token, role_id, created_at, updated_at) VALUES
    (1, 'Administrador General', 'admin@ultratienda.com',        '2026-10-01 08:00:00', '$2y$12$mx1lx7GL9EknKGj0YncFE.RMAZupBRyZX18DUey9OBNj.OYJzzyvm', 'rK2mP9qXs4T', 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
    (2, 'Carlos Vendedor',       'carlos.vendedor@ultratienda.com', '2026-10-01 08:05:00', '$2y$12$mx1lx7GL9EknKGj0YncFE.RMAZupBRyZX18DUey9OBNj.OYJzzyvm', 'wN5tR8vYb1U', 2, '2026-10-01 08:05:00', '2026-10-01 08:05:00'),
    (3, 'Mariana Bodega',        'mariana.bodega@ultratienda.com',  '2026-10-01 08:10:00', '$2y$12$mx1lx7GL9EknKGj0YncFE.RMAZupBRyZX18DUey9OBNj.OYJzzyvm', 'xQ7cL3jZd6I', 3, '2026-10-01 08:10:00', '2026-10-01 08:10:00'),
    (4, 'Laura Cliente',         'laura.cliente@ultratienda.com',   '2026-10-01 08:15:00', '$2y$12$mx1lx7GL9EknKGj0YncFE.RMAZupBRyZX18DUey9OBNj.OYJzzyvm', 'yF1sV6aHn9O', 4, '2026-10-01 08:15:00', '2026-10-01 08:15:00'),
    (5, 'Invitado Demo',         'invitado@ultratienda.com',        '2026-10-01 08:20:00', '$2y$12$mx1lx7GL9EknKGj0YncFE.RMAZupBRyZX18DUey9OBNj.OYJzzyvm', 'zG4dB2kMe5A', 5, '2026-10-01 08:20:00', '2026-10-01 08:20:00')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------
-- 3) categories (5)
-- ---------------------------------------------------------------------
INSERT INTO categories (id, name, description, orden, is_active, created_at, updated_at) VALUES
    (1, 'Electrónica',          'Audio, cómputo y dispositivos inteligentes.',        1, TRUE, '2026-10-01 09:00:00', '2026-10-01 09:00:00'),
    (2, 'Hogar y Cocina',       'Electrodomésticos y artículos para el hogar.',       2, TRUE, '2026-10-01 09:00:00', '2026-10-01 09:00:00'),
    (3, 'Deportes',             'Artículos para entrenamiento y vida activa.',        3, TRUE, '2026-10-01 09:00:00', '2026-10-01 09:00:00'),
    (4, 'Ropa y Accesorios',    'Prendas y accesorios de uso diario.',                4, TRUE, '2026-10-01 09:00:00', '2026-10-01 09:00:00'),
    (5, 'Oficina y Papelería',  'Mobiliario y útiles para el trabajo.',               5, TRUE, '2026-10-01 09:00:00', '2026-10-01 09:00:00')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------
-- 4) products (5) · uno por categoría
-- ---------------------------------------------------------------------
INSERT INTO products (id, category_id, name, description, price, image, disponible, is_active, created_at, updated_at) VALUES
    (1, 1, 'Auriculares Bluetooth X200',    'Auriculares inalámbricos con cancelación de ruido y 30 h de batería.',        149900.00, NULL, TRUE, TRUE, '2026-10-01 10:00:00', '2026-10-01 10:00:00'),
    (2, 2, 'Licuadora Pro 1.5 L',           'Licuadora de vaso con motor de 1000 W, 5 velocidades y jarra resistente.',  259900.00, NULL, TRUE, TRUE, '2026-10-01 10:05:00', '2026-10-01 10:05:00'),
    (3, 3, 'Balón de Fútbol Profesional',   'Balón talla 5 con costura reforzada y agarre superior para césped sintético.', 89900.00, NULL, TRUE, TRUE, '2026-10-01 10:10:00', '2026-10-01 10:10:00'),
    (4, 4, 'Mochila Urbana 25 L',           'Mochila impermeable con compartimento acolchado para portátil de 15 pulgadas.', 119900.00, NULL, TRUE, TRUE, '2026-10-01 10:15:00', '2026-10-01 10:15:00'),
    (5, 5, 'Silla Ergonómica Executive',    'Silla con soporte lumbar regulable, reposabrazos 4D y base de aluminio.',    549900.00, NULL, TRUE, TRUE, '2026-10-01 10:20:00', '2026-10-01 10:20:00')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------
-- 5) orders (5) · total = subtotal + domicilio
-- ---------------------------------------------------------------------
INSERT INTO orders (id, user_id, customer_name, customer_email, customer_phone, customer_address, subtotal, shipping_cost, total, status, payment_method, payment_status, created_at, updated_at) VALUES
    (1, 4, 'Laura Pérez',   'laura.cliente@ultratienda.com',   '3001112222', 'Calle 10 # 25-30, Apto 301',    149900.00, 12000.00, 161900.00, 'pending',  NULL,          'pending', '2026-10-02 10:15:00', '2026-10-02 10:15:00'),
    (2, 4, 'Laura Pérez',   'laura.cliente@ultratienda.com',   '3001112222', 'Calle 10 # 25-30, Apto 301',    259900.00, 12000.00, 271900.00, 'paid',     'efectivo',    'paid',     '2026-10-03 09:30:00', '2026-10-03 09:30:00'),
    (3, 2, 'Carlos Vendedor', 'carlos.vendedor@ultratienda.com', '3002223333', 'Carrera 15 # 42-18, Oficina 204', 179800.00, 12000.00, 191800.00, 'shipped',  'transferencia', 'paid',   '2026-10-05 14:45:00', '2026-10-05 14:45:00'),
    (4, 5, 'Invitado Demo', 'invitado@ultratienda.com',        '3004445555', 'Avenida Siempre Viva 742',       549900.00, 15000.00, 564900.00, 'delivered', 'tarjeta',     'paid',     '2026-10-06 11:00:00', '2026-10-06 11:00:00'),
    (5, 4, 'Laura Pérez',   'laura.cliente@ultratienda.com',   '3001112222', 'Calle 10 # 25-30, Apto 301',    119900.00, 12000.00, 131900.00, 'pending',  NULL,          'pending', '2026-10-08 16:20:00', '2026-10-08 16:20:00')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------
-- 6) order_details (5) · uno por pedido, referencia los 5 productos
--    subtotal del pedido = SUM(cantidad * price)
-- ---------------------------------------------------------------------
INSERT INTO order_details (id, order_id, product_id, quantity, price, created_at, updated_at) VALUES
    (1, 1, 1, 1, 149900.00, '2026-10-02 10:15:00', '2026-10-02 10:15:00'),
    (2, 2, 2, 1, 259900.00, '2026-10-03 09:30:00', '2026-10-03 09:30:00'),
    (3, 3, 3, 2,  89900.00, '2026-10-05 14:45:00', '2026-10-05 14:45:00'),
    (4, 4, 5, 1, 549900.00, '2026-10-06 11:00:00', '2026-10-06 11:00:00'),
    (5, 5, 4, 1, 119900.00, '2026-10-08 16:20:00', '2026-10-08 16:20:00')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------
-- 7) order_payments (5) · uno por pedido
--    pagos completos (paid, con fecha) en pedidos resueltos;
--    abonos parciales (pending, sin fecha) en los pedidos pendientes
-- ---------------------------------------------------------------------
INSERT INTO order_payments (id, order_id, amount, payment_method, payment_status, payment_date, created_at, updated_at) VALUES
    (1, 1,  50000.00, 'transferencia', 'pending', NULL,                        '2026-10-02 10:20:00', '2026-10-02 10:20:00'),
    (2, 2, 271900.00, 'efectivo',      'paid',    '2026-10-03 09:45:00',       '2026-10-03 09:45:00', '2026-10-03 09:45:00'),
    (3, 3, 191800.00, 'transferencia', 'paid',    '2026-10-05 15:10:00',       '2026-10-05 15:10:00', '2026-10-05 15:10:00'),
    (4, 4, 564900.00, 'tarjeta',       'paid',    '2026-10-06 11:05:00',       '2026-10-06 11:05:00', '2026-10-06 11:05:00'),
    (5, 5,  30000.00, 'efectivo',      'pending', NULL,                        '2026-10-08 16:25:00', '2026-10-08 16:25:00')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------
-- 8) Avance de secuencias (bigserial) para que los próximos registros
--    creados desde la aplicación no colisionen con los IDs 1..5
-- ---------------------------------------------------------------------
SELECT setval(pg_get_serial_sequence('roles',         'id'), COALESCE((SELECT MAX(id) FROM roles),         1));
SELECT setval(pg_get_serial_sequence('users',         'id'), COALESCE((SELECT MAX(id) FROM users),         1));
SELECT setval(pg_get_serial_sequence('categories',    'id'), COALESCE((SELECT MAX(id) FROM categories),    1));
SELECT setval(pg_get_serial_sequence('products',      'id'), COALESCE((SELECT MAX(id) FROM products),      1));
SELECT setval(pg_get_serial_sequence('orders',        'id'), COALESCE((SELECT MAX(id) FROM orders),        1));
SELECT setval(pg_get_serial_sequence('order_details', 'id'), COALESCE((SELECT MAX(id) FROM order_details), 1));
SELECT setval(pg_get_serial_sequence('order_payments','id'), COALESCE((SELECT MAX(id) FROM order_payments),1));

COMMIT;
