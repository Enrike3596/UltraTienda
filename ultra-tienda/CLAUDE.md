<laravel-boost-guidelines>
=== reglas fundamentales ===

# Directrices de Laravel Boost

## Contexto fundamental

Esta es una aplicación Laravel que se ejecuta en PHP 8.4. Utiliza siempre las API que correspondan a la versión principal instalada de cada paquete; no asumas una versión concreta.

Antes de utilizar la API de un paquete, confirma la versión instalada:
- Paquetes PHP: ejecuta `composer show --direct` para listar las dependencias directas con sus versiones, o `composer show <vendor/package>` para un paquete específico.
- Paquetes JS: consulta `package.json` para ver las versiones instaladas.

## Activación de habilidades

Este proyecto cuenta con habilidades específicas del dominio disponibles en `**/skills/**`. DEBES activar la habilidad correspondiente siempre que trabajes en ese dominio; no esperes a encontrarte bloqueado.

## Convenciones

- Debes seguir todas las convenciones de código existentes en esta aplicación. Al crear o editar un archivo, consulta los archivos adyacentes para verificar la estructura, el enfoque y la nomenclatura correctos.
- Utiliza nombres descriptivos para variables y métodos. Por ejemplo, `isRegisteredForDiscounts` en lugar de `discount()`.
- Comprueba si existen componentes reutilizables antes de escribir uno nuevo.

## Scripts de verificación

- No crees scripts de verificación ni realices pruebas manuales si ya existen pruebas automatizadas que cubren esa funcionalidad y confirman su correcto funcionamiento. Las pruebas unitarias y de características (*feature tests*) son prioritarias.

## Estructura y arquitectura de la aplicación

- Mantén la estructura de directorios existente; no crees nuevas carpetas raíz sin autorización.
- No modifiques las dependencias de la aplicación sin autorización.

## Empaquetado del frontend

- Si un cambio en el frontend no se refleja en la interfaz de usuario o recibes el error "Unable to locate file in Vite manifest", ejecuta `pnpm run build` o pide al usuario que ejecute `pnpm run dev` o `composer run dev`.

## Archivos de documentación

- Solo debes crear archivos de documentación si el usuario lo solicita explícitamente.

=== reglas de boost ===

# Laravel Boost

## Herramientas

- Laravel Boost es un servidor MCP con herramientas diseñadas específicamente para esta aplicación. Prioriza el uso de las herramientas de Boost frente a alternativas manuales, como comandos de shell o la lectura directa de archivos. - Utiliza `database-query` para ejecutar consultas de solo lectura en la base de datos en lugar de escribir SQL sin procesar en Tinker.
- Utiliza `database-schema` para inspeccionar la estructura de las tablas antes de escribir migraciones o modelos.
- Utiliza `get-absolute-url` para resolver el esquema, dominio y puerto correctos de las URL del proyecto. Úsalo siempre antes de compartir una URL con el usuario.
- Utiliza `browser-logs` para leer los registros, errores y excepciones del navegador. Solo los registros recientes son útiles; ignora las entradas antiguas.

## Búsqueda en la documentación (IMPORTANTE)

- Utiliza `search-docs` antes de realizar cambios que dependan de API, comportamiento, configuración o sintaxis específica de versión del ecosistema Laravel. Omite este paso en ediciones que solo impliquen texto y otros cambios donde la documentación del paquete sea irrelevante. Reutiliza los resultados que ya estén en el contexto en lugar de realizar una nueva búsqueda.
- Pasa un array `packages` para acotar los resultados cuando sepas qué paquetes son relevantes.
- Utiliza varias consultas amplias basadas en temas: `['rate limiting', 'routing rate limiting', 'routing']`. Espera obtener primero los resultados más relevantes.
- No incluyas nombres de paquetes en las consultas, ya que la información del paquete ya se comparte. Usa `test resource table`, no `filament 4 test resource table`.

### Sintaxis de búsqueda

1. Usa palabras para una lógica AND con lematización automática: `rate limit` coincide con "rate" Y "limit".
2. Usa `"frases entre comillas"` para coincidencias de posición exacta: `"infinite scroll"` requiere palabras adyacentes en ese orden.
3. Combina palabras y frases para consultas mixtas: `middleware "rate limit"`.
4. Usa múltiples consultas para una lógica OR: `queries=["authentication", "middleware"]`.

## Reglas del proyecto

- Este proyecto contiene reglas confirmadas (commit) y agrupadas por área en `.ai/rules` cuando dicho directorio existe, incluidas las directrices del framework acotadas por ruta en `.ai/rules/boost`. Antes de entrar en modo de planificación o de crear/editar cualquier archivo, DEBES primero: abrir `@.ai/rules/index.md` (que asigna patrones de archivo a archivos de reglas), leer todos los archivos de reglas cuyos patrones cubran las rutas en cuestión y ejecutar `grep -rin 'keyword' .ai/rules` para detectar aquello que una simple coincidencia de ruta podría pasar por alto. No escribas código hasta haber leído y estar siguiendo todas las reglas aplicables. Si `.ai/rules` no existe, continúa sin él.

## Artisan

- Ejecuta comandos de Artisan directamente desde la línea de comandos (p. ej., `php artisan route:list`). Usa `php artisan list` para ver los comandos disponibles y `php artisan [command] --help` para consultar los parámetros.
- Inspecciona las rutas con `php artisan route:list`. Filtra usando: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Lee valores de configuración usando notación de puntos: `php artisan config:show app.name`, `php artisan config:show database.default`. O bien, lee los archivos de configuración directamente desde el directorio `config/`.

## Tinker

- Ejecuta código PHP en el contexto de la aplicación para depuración y pruebas. No crees modelos sin la aprobación del usuario; en su lugar, prefiere realizar pruebas utilizando *factories*. Prioriza el uso de comandos de Artisan existentes frente a código personalizado en Tinker.
- Utiliza siempre comillas simples para evitar la expansión del *shell*: `php artisan tinker --execute 'Your::code();'`
- Comillas dobles para PHP
