# UltraTienda - Sistema de E-commerce

## Descripción general

**UltraTienda** es un sistema completo de comercio electrónico construido con Laravel, diseñado para gestionar catálogos de productos, órdenes de compra, pagos y control de usuarios con roles diferenciados. El sistema proporciona una interfaz administrativa completa y una experiencia de usuario optimizada para la venta en línea.

## 🚀 Características principales

- **Gestión de catálogos**: CRUD completo de productos y categorías con validación de stock y estado activo
- **Carro de compras y órdenes**: Ciclo completo de órdenes desde creación hasta entrega con seguimiento de pagos
- **Sistema de pagos**: Múltiples métodos de pago con registro de montos, estatus y fechas
- **Autenticación y roles**: Sistema de autenticación robusto con roles (admin, client) y permisos diferenciados
- **Panel de administración**: Interfaz completa para gestión de todos los recursos del sistema
- **Catálogo filtrado**: Productos disponibles y activos, categorías ordenables

## 📸 Capturas de pantalla

*(Agregar capturas aquí)*

## 🛠️ Tecnologías

| Tecnología | Versión |
|------------|---------|
| Laravel | 13.x |
| PHP | 8.3+ |
| PostgreSQL | 15+ |
| Tailwind CSS | v4.0.0 |
| DaisyUI | v5.7.47 |
| Vite | Build tool |

## 📋 Requisitos previos

- PHP >= 8.3
- Composer
- Node.js & npm (o pnpm)
- PostgreSQL

## 🔧 Instalación

```bash
# 1. Clonar el repositorio
git clone <repository-url>
cd ultra-tienda

# 2. Instalar dependencias de PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=UltraTienda
# DB_USERNAME=postgres
# DB_PASSWORD=3596

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
- **Password**: (no requiere contraseña - configurar según sea necesario)

## 📡 Rutas principales

### Rutas públicas (guest)
- `GET /login` - Formulario de inicio de sesión
- `POST /login` - Autenticación
- `GET /recuperar` - Recuperación de contraseña
- Rutas de recursos: `/products`, `/categories`, `/orders` (parciales)

### Rutas autenticadas (auth middleware)
- `GET /` - Panel de control
- `GET /users` - Gestión de usuarios
- `GET /products` - Catálogo de productos
- `GET /categories` - Gestión de categorías
- `GET /orders` - Órdenes de compra
- `GET /roles` - Roles y permisos

## 🗄️ Estructura de base de datos

Tabla principal:
- **users** - Usuarios con rol_id (FK → roles)
- **categories** - Categorías (name UNIQUE, orden, is_active)
- **products** - Productos (category_id FK, price, disponible, is_active)
- **orders** - Órdenes (user_id FK, status, payment_method, payment_status)
- **order_details** - Detalles de orden (product_id FK, quantity, price)
- **order_payments** - Pagos de orden (amount, payment_method, payment_status)
- **roles** - Roles del sistema (name UNIQUE)

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