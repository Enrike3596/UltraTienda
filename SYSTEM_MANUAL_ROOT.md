# Manual de Arquitectura del Sistema UltraTienda

## 1. Introducción

Este manual describe la arquitectura, patrones y estructura del sistema **UltraTienda**, una aplicación de comercio electrónico construida con Laravel 13.x. El objetivo es proporcionar una guía completa para desarrolladores, equipos de mantenimiento y partes interesadas sobre cómo está diseñado el sistema, cómo interactúan sus componentes y cómo extenderlo o mantenerlo en el futuro. La fuente canónica de este documento es este archivo en la raíz del repositorio.

---

## 2. Arquitectura del Sistema

UltraTienda sigue la arquitectura **MVC (Model-View-Controller)** clásica de Laravel, que es un patrón de diseño que separa las responsabilidades en tres componentes principales:

- **Model**: Representa la lógica de negocio y la interacción con la base de datos. Cada modelo corresponde a una tabla de base de datos y maneja relaciones, validaciones y consultas.
- **View**: Capa de presentación que genera la respuesta HTML. En Laravel, las vistas son archivos Blade con extensión `.blade.php` que contienen la estructura HTML y la lógica de plantilla.
- **Controller**: Actúa como intermediario entre Model y View. Recibe las solicitudes HTTP, procesa la lógica mediante los modelos y devuelve la vista adecuada o una respuesta JSON.

### Patrones Implementados

1. **Resourceful Routing**: Laravel proporciona rutas de recursos RESTful que mapean operaciones CRUD (Create, Read, Update, Delete) a controladores de forma convencional.

2. **Eloquent ORM**: Los modelos encapsulan el acceso a datos y declaran las relaciones entre entidades mediante `belongsTo` y `hasMany`.

3. **Controladores orientados a recursos**: Los controladores coordinan validación, persistencia y respuestas HTML o JSON para cada recurso.

4. **Middleware Pattern**: El sistema usa middleware para filtrar peticiones entrantes, proteger sesiones y aplicar rate limiting a autenticación.

5. **Sanctum para API**: Las rutas API protegidas usan `auth:sanctum`, mientras que las rutas web usan sesiones y el middleware `auth`.

---

## 3. Interacciones entre Tablas (Relaciones E-R)

El sistema utiliza una base de datos relacional. La configuración de ejemplo usa SQLite para desarrollo local; PostgreSQL 15+ es una alternativa soportada para otros entornos. Las tablas principales y sus relaciones son:

### Diagrama de Relaciones Entidad-Relación (E-R)

```
       +----------------+      +------------------+
       |     users      |      |     roles        |
       +----------------+      +------------------+
       | id | name | ... |      | id | name         |
       |role_id                  |    | users (FK)       |
       +----------------+      +------------------+
             |                        |
             | 1:N                  1:N
             |                        |
       +----------------+      +------------------+
       |    orders      |      |  categories      |
       +----------------+      +------------------+
       |id|user_id|...|      |id|name|description|
       |status                |    |orden|is_active    |
       +----------------+      +------------------|
             |                        |
       1:N       |                        |1:N
             |                        |
     +-----------------+          +------------------+
     | order_details   |          |  products        |
     +-----------------+          +------------------+
     |id|order_id|...|          |id|category_id|...|
     |product_id               |    |name|price|disponible|
     |quantity               |    |    |     |is_active|
     +-----------------+          +------------------+
             |
     1:N        |
             |
     +-----------------+
     |order_payments   |
     +-----------------+
     |id|order_id|...|
     |amount|payment_status|
     +-----------------+
```

### Descripción de Relaciones Principales

| Relación | De Tabla | A Tabla | Tipo | Descripción |
|----------|----------|---------|------|-------------|
| `users.role_id` → `roles.id` | users | roles | BelongsTo | Un usuario pertenece a un rol |
| `roles.users` | roles | users | HasMany | Un rol tiene muchos usuarios |
| `categories.products` | categories | products | HasMany | Una categoría tiene muchos productos |
| `products.category_id` | products | categories | BelongsTo | Un producto pertenece a una categoría |
| `orders.user_id` | orders | users | BelongsTo | Una orden pertenece a un usuario |
| `order_details.order_id` | order_details | orders | BelongsTo | Un detalle de orden pertenece a una orden |
| `order_details.product_id` | order_details | products | BelongsTo | Un detalle de orden pertenece a un producto |
| `order_payments.order_id` | order_payments | orders | BelongsTo | Un pago pertenece a una orden |

### Flujos de Datos Comunes

1. **Crear una orden**: User → Order (vía user_id) → OrderDetails (vía product_id) → OrderPayments (vía order_id)
2. **Catálogo de productos**: Category → Products (HasMany relationship)
3. **Gestión de usuarios**: Role → Users (HasMany relationship)

---

## 4. Modelos y Controladores

### 4.1 Estructura de Modelos (`app/Models/`)

Cada modelo extiende `Illuminate\Database\Eloquent\Model` y representa una tabla de la base de datos. Los modelos utilizan Eloquent ORM para interactuar con la base de datos de forma orientada a objetos.

#### Modelo User

```php
// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    // Relación: Usuario pertenece a un Rol
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Relación: Usuario tiene muchas Órdenes
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
```

#### Modelo Role

```php
// app/Models/Role.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name'];

    // Relación: Rol tiene muchos Usuarios
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
```

#### Modelo Product

```php
// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'image', 'disponible', 'is_active',
    ];

    // Relación: Producto pertenece a una Categoría
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

#### Modelo Category

```php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'orden', 'is_active'];

    // Relación: Categoría tiene muchos Productos
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
```

#### Modelo Order, OrderDetail, OrderPayment

Estos modelos gestionan el ciclo de vida de una transacción de venta:

- **Order**: Representa una orden de compra completa. Tiene relaciones con User (quién creó la orden), OrderDetails (productos en la orden) y OrderPayments (pagos asociados).
- **OrderDetail**: Línea de detalle de una orden. Contiene la relación Many-to-One con ambos Order y Product. Cuando se elimina una orden, sus detalles se eliminan en cascada (`cascadeOnDelete`); un producto relacionado no se elimina si tiene detalles (`restrictOnDelete`).
- **OrderPayment**: Registro de pagos asociados a una orden. También utiliza eliminación en cascada al eliminar la orden.

### 4.2 Estructura de Controladores (`app/Http/Controllers/`)

Los controladores siguen la convención de Laravel y manejan las solicitudes HTTP. Cada controlador tiene métodos para las operaciones CRUD estándar.

#### Controlador UserController

| Método | Descripción | Ruta |
|--------|-------------|------|
| `index` | Listar todos los usuarios | `GET /users` |
| `create` | Formulario para crear usuario | `GET /users/create` |
| `store` | Guardar nuevo usuario | `POST /users` |
| `show` | Mostrar un usuario específico | `GET /users/{user}` |
| `edit` | Formulario de edición | `GET /users/{user}/edit` |
| `update` | Actualizar usuario | `PUT/PATCH /users/{user}` |
| `destroy` | Eliminar usuario | `DELETE /users/{user}` |

#### Controlador ProductController

| Método | Descripción | Ruta |
|--------|-------------|------|
| `index` | Listar productos con filtros | `GET /products` |
| `create` | Formulario para nuevo producto | `GET /products/create` |
| `store` | Guardar nuevo producto | `POST /products` |
| `show` | Detalle de un producto | `GET /products/{product}` |
| `edit` | Formulario de edición | `GET /products/{product}/edit` |
| `update` | Actualizar producto | `PUT/PATCH /products/{product}` |
| `destroy` | Eliminar producto | `DELETE /products/{product}` |

#### Controlador OrderController

Maneja el ciclo completo de una orden:

1. **Creación**: Captura datos del cliente (nombre, email, teléfono, dirección) + productos del carrito
2. **Show**: Ver estado de la orden, historial de pagos
3. **Update**: Cambiar estado (pending → paid → shipped → delivered)
4. **Destroy**: Cancelar orden

#### Controlador CategoryController

CRUD para gestión de categorías con campos adicionales:
- `orden`: Para ordenamiento personalizado
- `is_active`: Para activar/desactivar categorías

#### Controlador AuthController

Maneja autenticación y recuperación de contraseña:
- `login` / `login.attempt`: Autenticación con throttle (rate limiting)
- `recoveryForm` / `sendRecoveryCode`: Recuperación vía email
- `resetForm` / `resetPassword`: Restablecimiento de contraseña

#### Controladores API

La carpeta `app/Http/Controllers/Api/` contiene controladores JSON equivalentes para autenticación, usuarios, roles, categorías, productos, órdenes, detalles y pagos. Las rutas se registran en `routes/api.php`:

- `POST /api/login`: obtiene un token de Sanctum.
- `POST /api/logout` y `GET /api/me`: requieren `auth:sanctum`.
- `/api/roles`, `/api/categories`, `/api/products`, `/api/users`, `/api/orders`, `/api/order-details` y `/api/order-payments`: recursos protegidos con `apiResource`.

### 4.3 Acoplamiento Model-Controller

El nivel de acoplamiento en este sistema es **bajo desacoplamiento (low coupling)**:

- Los controladores dependen de las interfaces de los modelos (métodos definidos en las clases)
- Los modelos no tienen conocimiento de los controladores (dependencia unidireccional)
- Se utilizan relaciones Eloquent (`hasMany`, `belongsTo`) en lugar de consultas SQL directas
- Los controladores coordinan validación, modelos y respuestas; los modelos encapsulan persistencia y relaciones

**Ejemplo de flujo**:

```
Solicitud HTTP
        ↓
Ruta (routes/web.php)
        ↓
Controlador (ej. OrderController@store)
        ↓
Método del Modelo (ej. $order->save(), $order->details()->create())
        ↓
Base de datos configurada
        ↓
Respuesta / Vistas
```

---

## 5. Conexión Backend-Frontend

### 5.1 Arquitectura Backend

El backend de UltraTienda está construido completamente con Laravel y cumple las siguientes funciones:

- **Enrutamiento de rutas**: `routes/web.php` define todas las rutas públicas y protegidas
- **Controladores**: Procesan la lógica, validan entradas, interactúan con modelos
- **Autenticación y Autorización**: middleware `auth` protege rutas sensibles
- **API RESTful**: `routes/api.php` expone recursos JSON protegidos con Laravel Sanctum
- **Gestor de vistas**: Motor Blade renderiza plantillas HTML

### 5.2 Arquitectura Frontend

El frontend utiliza tecnologías modernas de interfaz:

- **Tailwind CSS v4.0.0**: Framework de CSS utility-first para estilos responsivos
- **DaisyUI v5.7.47**: Componentes UI pre-diseñados para Tailwind
- **Material Tailwind HTML**: Estructura de componentes visuales

#### Estructura de Vistas Blade (`resources/views/`)

Las vistas son archivos `.blade.php` que contienen HTML con directivas de Blade:

```
resources/views/
├── layout/           # Plantillas maestras (app.blade.php, sidebar, topbar)
├── auth/             # Vistas de login y recuperación
├── Users/            # CRUD de usuarios
├── Categories/       # CRUD de categorías
├── Products/         # CRUD de productos
├── Orders/           # CRUD de órdenes
├── Order-Details/    # Detalles de órdenes
├── Order-payments/   # Pagos de órdenes
└── components/       # Componentes reutilizables (icon, badge, avatar, money, page-header)
```

#### Flujo de Datos Backend-Frontend

```
Solicitud del Usuario
        ↓
Ruta HTTP (GET /products)
        ↓
Middleware auth (verifica sesión)
        ↓
Controlador (ProductController@index)
        ↓
Método Eloquent (Product::with('category')->get())
        ↓
Datos JSON/Collection
        ↓
Vista Blade (resources/views/Products/index.blade.php)
        ↓
Motor Blade renderiza HTML
        ↓
Tailwind CSS aplica los estilos
        ↓
Respuesta al navegador
```

### 5.3 Compilación de Assets

- **Vite** es el build tool que procesa archivos `.js` y `.css`
- `resources/js/` contiene el JavaScript de la aplicación
- `resources/css/` o directivas `@tailwind` en Blade
- `npm run build` genera los archivos optimizados en `public/build/`
- `npm run dev` para desarrollo con hot-reload

---

## 6. Estructura de Carpetas del Sistema

### 6.1 Estructura Raíz (`ultra-tienda/`)

```
ultra-tienda/
├── .env              # Configuración de entorno
├── .env.example      # Ejemplo de configuración
├── artisan           # Punto de entrada de Artisan CLI
├── composer.json     # Dependencias PHP
├── composer.lock     # Bloqueo de versiones
├── package.json      # Dependencias Node.js
├── vite.config.js    # Configuración de Vite
├── AGENTS.md         # Configuración de agentes
├── database/
│   ├── migrations/   # Archivos de migración SQL
│   └── seeders/      # Datos de prueba/seed
├── app/
│   ├── Models/       # Modelos Eloquent
│   └── Http/
│       ├── Controllers/ # Controladores
│       └── ...
├── resources/
│   ├── views/        # Vistas Blade
│   ├── js/           # JavaScript de cliente
│   └── css/          # Estilos
├── routes/
│   ├── web.php       # Definición de rutas web
│   └── api.php       # Definición de rutas API
├── config/           # Configuración Laravel
├── storage/          # Archivos generados, logs, uploads
└── public/           # Punto de entrada web
    ├── index.php     # Bootstrap Laravel
    └── build/        # Assets compilados (Vite)
```

### 6.2 Directorios Clave y Propósito

| Directorio | Propósito | Contenido típico |
|------------|-----------|------------------|
| `app/Models/` | Lógica de negocio y mapeo OBJETO-RELACIONAL | User, Product, Order, Category, Role, etc. |
| `app/Http/Controllers/` | Manejadores de solicitudes HTTP | UserController, ProductController, OrderController, etc. |
| `database/migrations/` | Evolución estructural de BD | Archivos PHP que crean/alteran tablas |
| `database/seeders/` | Datos iniciales de prueba | Factory calls, datos de demostración |
| `resources/views/` | Capa de presentación | Archivos `.blade.php` para cada recurso |
| `resources/js/` | Lógica del lado del cliente | JavaScript de la interfaz |
| `routes/web.php` | Definición de enrutamiento web | Rutas agrupadas por `guest` y `auth` |
| `routes/api.php` | Definición de API | Rutas JSON agrupadas por `auth:sanctum` |
| `config/` | Configuración de la aplicación | archivos PHP: auth.php, database.php, mail.php, etc. |
| `storage/` | Almacenamiento de archivos | Logs, caches, archivos subidos (imágenes) |
| `public/` | Entrada pública | `index.php` que bootstrapea Laravel, assets compilados |
| `storage/app/public` | Archivos públicos subidos | Imágenes de productos, documentos |

### 6.3 Flujo de Solicitud Completo

```
1. Usuario accede a http://localhost:8000/products
2. Servidor recibe petición en public/index.php
3. Laravel bootstrap se ejecuta (bootstrappers)
4. Router analiza la URL y carga routes/web.php
5. Middleware `web` se aplica (sesión, cookies)
6. Si ruta requiere auth, middleware `auth` verifica usuario
7. Controlador correspondiente se ejecuta
8. Modelo interactúa con la base de datos configurada vía Eloquent
9. Resultados retornan al controlador
10. Vista Blade se renderiza con datos
11. Tailwind CSS estiliza componentes
12. HTTP response enviado al navegador
```

---

## 7. Patrones de Diseño y Buenas Prácticas

### 7.1 Convenciones de Nomenclatura

- **Modelos**: Nombre singular en StudlyCase (User, Product, Category)
- **Tablas**: Nombre plural en snake_case (users, products, categories)
- **Migraciones**: Fecha + descripción descriptiva (2026_10_12_005003_create_products_table.php)
- **Controladores**: Nombre plural en StudlyCase seguido de Controller (UserController, ProductController)
- **Vistas**: Nombre descriptivo en minúsculas con guiones bajos o camelCase (index.blade.php, form.blade.php)
- **Rutas**: nombres descriptivos en minúsculas con guiones (users, products, order-details)

### 7.2 Principios SOLID (aplicados parcialmente)

| Principio | Aplicación en UltraTienda |
|-----------|--------------------------|
| **Single Responsibility** | Cada controlador maneja un recurso específico |
| **Open/Closed** | Se pueden agregar nuevas rutas sin modificar las existentes |
| **Liskov Substitution** | Los modelos pueden sustituirse por sus interfaces |
| **Interface Segregation** | Controladores usan solo los métodos que necesitan |
| **Dependency Inversion** | Controladores dependen de abstracciones de Eloquent |

### 7.3 Validación de Datos

- Validación en controladores usando reglas de validación de Laravel
- Conversión de tipos y formatos mediante el método `casts()` de los modelos
- Validación en las vistas mediante errores `$errors`

### 7.4 Manejo de Errores

- Excepciones de modelo (ModelNotFoundException)
- Páginas de error 404 personalizadas
- Logs de error en `storage/logs/laravel.log`

---

## 8. Guía para Desarrolladores

### 8.1 Cómo Añadir un Nuevo Recurso

1. **Crear migración**: `php artisan make:migration create_nombre_tabla`
2. **Crear modelo**: `php artisan make:model NombreModelo`
3. **Definir relaciones**: Agregar métodos `belongsTo`, `hasMany` en los modelos
4. **Crear controlador**: `php artisan make:controller NombreController --resource`
5. **Definir rutas**: `Route::resource('nombre', NombreController::class)`
6. **Crear vistas**: `resources/views/nombre/`
7. **Añadir assets**: Si es necesario, actualizar Tailwind components

### 8.2 Puntos de Extensión

- **Nuevas rutas**: Agregar en `routes/web.php` dentro de grupos de middleware
- **Nuevos campos en modelos**: Agregar en `$fillable` y migraciones
- **Nuevas vistas**: Copiar estructura de vista existente y personalizar
- **Nuevos componentes Tailwind**: Agregar en `resources/views/components/`

### 8.3 Comandos Artisan Útiles

| Comando | Descripción |
|---------|-------------|
| `php artisan make:model ModelName` | Crear modelo |
| `php artisan make:controller ControllerName --resource` | Crear controlador con métodos CRUD |
| `php artisan make:migration create_table_name` | Crear migración |
| `php artisan migrate` | Ejecutar migraciones pendientes |
| `php artisan migrate:refresh --seed` | Revertir y volver a ejecutar + seed |
| `php artisan tinker` | Interactuar con la aplicación PHP |
| `php artisan serve` | Servidor de desarrollo |
| `npm run build` | Compilar assets de producción |

---

## 9. Consideraciones de Despliegue

### 9.1 Entorno de Producción

- `APP_DEBUG=false` en `.env`
- `LOG_LEVEL=error` o `warning`
- Configurar caché: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
- Queue workers: `php artisan queue:work`
- Horizon para monitoreo de colas (opcional)

### 9.2 Base de Datos

- SQLite es la configuración predeterminada de `.env.example`
- PostgreSQL 15+ es una alternativa recomendada para producción
- Backups periódicos de la base de datos configurada
- Migraciones versionadas en control de fuentes

### 9.3 Frontend

- `npm run build` antes de desplegar
- CDN de Tailwind o build propio
- Verificar que `public/build/` contenga los assets compilados

### 9.4 Seguridad

- Mantener PHP y Laravel actualizados
- Usar HTTPS en producción
- Rate limiting en rutas de auth
- Validación siempre en servidor (nunca confiar en cliente)

---

## 10. Conclusión

El sistema **UltraTienda** implementa una arquitectura Laravel MVC robusta y escalable, con una separación clara de responsabilidades entre modelos, controladores y vistas. Las relaciones entre tablas siguen un patrón lógico de comercio electrónico (usuarios → roles, categorías → productos, órdenes → detalles → pagos), lo que facilita tanto el mantenimiento como la extensión del sistema.

El uso de Blade templates con Tailwind CSS/DaisyUI permite una interfaz de usuario moderna y responsiva, mientras que Eloquent ORM abstrae la complejidad de las consultas SQL. Este manual sirve como base para el onboarding de nuevos desarrolladores, mantenimiento continuo y futura expansión de funcionalidades.

---
*Documento actualizado el: 18 de octubre de 2026*
*Versión: 1.1*
*Sistema: UltraTienda - Laravel 13.x - SQLite (desarrollo) / PostgreSQL (alternativa)*