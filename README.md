# UltraTienda - Sistema de E-commerce

> La documentación técnica y de arquitectura se mantiene en el [manual del sistema](./SYSTEM_MANUAL_ROOT.md).

## Descripción general

**UltraTienda** es un sistema completo de comercio electrónico construido con Laravel, diseñado para gestionar catálogos de productos, órdenes de compra, pagos y control de usuarios con roles diferenciados. Proporciona una interfaz administrativa completa optimizada para la venta en línea.

## 🚀 Características principales

- **Gestión de catálogos**: CRUD completo de productos y categorías con validación de stock y estado activo
- **Carro de compras y órdenes**: Ciclo completo de órdenes desde creación hasta entrega con seguimiento de pagos
- **Sistema de pagos**: Múltiples métodos de pago con registro de montos, estatus y fechas
- **Autenticación y roles**: Sistema robusto con roles (admin, client) y permisos diferenciados
- **Panel de administración**: Interfaz completa para gestión de todos los recursos del sistema
- **Catálogo filtrado**: Productos disponibles y activos, categorías ordenables

## 🛠️ Tecnologías

| Tecnología | Versión |
|------------|---------|
| Laravel | 13.x |
| PHP | 8.3+ |
| SQLite | Desarrollo local |
| PostgreSQL | 15+ (alternativa para producción) |
| Tailwind CSS | v4.0.0 |
| DaisyUI | v5.7.47 |
| Vite | Build tool |

## 📋 Requisitos previos

- PHP >= 8.3
- Composer
- Node.js & npm (o pnpm)
- SQLite (incluido para desarrollo) o PostgreSQL

## 🔧 Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/Enrike3596/UltraTienda.git
cd UltraTienda\ultra-tienda

# 2. Instalar dependencias de PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env (SQLite es el valor predeterminado)
# DB_CONNECTION=sqlite
# Para PostgreSQL:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=UltraTienda
# DB_USERNAME=postgres
# DB_PASSWORD=

# 5. Ejecutar migraciones
php artisan migrate --force

# 6. Instalar dependencias frontend
npm install

# 7. Compilar assets
npm run build

# 8. Iniciar servidor
php artisan serve
```

El sistema estará disponible en `http://localhost:8000`

## 👤 Credenciales por defecto

- **Email**: `test@example.com`
- **Password**: (configurar según sea necesario)

## 📡 Rutas principales

### Rutas web públicas (guest)
- `GET /login` - Formulario de inicio de sesión
- `POST /login` - Autenticación
- `GET /recuperar` - Recuperación de contraseña

### Rutas autenticadas (auth middleware)
- `GET /` - Panel de control
- `GET /users` - Gestión de usuarios
- `GET /products` - Catálogo de productos
- `GET /categories` - Gestión de categorías
- `GET /orders` - Órdenes de compra
- `GET /roles` - Roles y permisos

### API

- `POST /api/login` - Autenticación y token de Sanctum
- `POST /api/logout` y `GET /api/me` - Sesión API autenticada
- Recursos protegidos: `/api/roles`, `/api/categories`, `/api/products`, `/api/users`, `/api/orders`, `/api/order-details`, `/api/order-payments`

## 🗄️ Estructura de base de datos

| Tabla | Campos principales | Relaciones |
|-------|-------------------|------------|
| **users** | id, name, email, password, role_id | FK → roles; hasMany orders |
| **categories** | id, name (unique), description, orden, is_active | hasMany products |
| **products** | id, category_id (FK), name, description, price, image, disponible, is_active | FK → categories; belongsTo category |
| **roles** | id, name (unique) | hasMany users |
| **orders** | id, user_id (FK), customer_name, customer_email, customer_address, subtotal, shipping_cost, total, status, payment_method, payment_status | FK → users; hasMany order_details, hasMany payments |
| **order_details** | id, order_id (FK), product_id (FK), quantity, price | FK → orders & products |
| **order_payments** | id, order_id (FK), amount, payment_method, payment_status, payment_date | FK → orders |

Relaciones clave:
- User → Role (BelongsTo)
- User → Orders (HasMany)
- Category → Products (HasMany)
- Order → OrderDetails (HasMany)
- Order → Payments (HasMany)

## 🛠️ Scripts disponibles

| Script | Descripción |
|--------|-------------|
| `php artisan serve` | Servidor de desarrollo (puerto 8000) |
| `npm run build` | Compilar assets de producción |
| `npm run dev` | Modo desarrollo Vite |
| `php artisan migrate` | Ejecutar migraciones |
| `php artisan config:show app.name` | Ver configuración |

## 🤝 Contribución

1. Fork el repositorio
2. Crea tu rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la [Licencia MIT](https://opensource.org/licenses/MIT). Véase el archivo `LICENSE` para más detalles.

## 📧 Contacto

Para soporte o consultas, por favor abrir un issue en el repositorio o contactar al equipo de desarrollo.
