# GreenCycle
 
Proyecto desarrollado para el curso *TM4100*.
 
GreenCycle es una aplicación web gamificada cliente-servidor en la que cada
persona usuaria administra un *vivero digital*. Cada árbol posee un tipo,
un nivel, salud, progreso y estado. El servidor aplica las reglas de
crecimiento y deterioro según el tiempo transcurrido: un árbol con salud 0
muere y ya no puede recibir cuidados; un árbol que alcanza el nivel máximo
puede cosecharse para obtener monedas e invertirlas en ítems de la tienda.
 
Este README documenta el estado del proyecto durante el *Sprint 1*:
autenticación de usuarios, modelo de datos, API inicial de árboles e
interfaz base.
 
---
 
## Tabla de contenidos
 
- [Propósito](#propósito)
- [Tecnologías](#tecnologías)
- [Requisitos locales](#requisitos-locales)
- [Instalación local](#instalación-local)
- [Modelo de datos](#modelo-de-datos)
- [DER (diagrama entidad-relación)](#der-diagrama-entidad-relación)
- [Funcionalidades del Sprint 1](#funcionalidades-del-sprint-1)
- [API inicial](#api-inicial)
- [Autenticación](#autenticación)
- [Autorización](#autorización)
- [Comprobaciones del proyecto](#comprobaciones-del-proyecto)
- [Ambientes](#ambientes)
- [Seguridad](#seguridad)
- [Flujo de trabajo](#flujo-de-trabajo)
- [Equipo](#equipo)
- [Estado del proyecto](#estado-del-proyecto)
---
 
## Propósito
 
Desarrollar una aplicación web cliente-servidor que permita autenticar
usuarios, gestionar árboles digitales y —en sprints futuros— aplicar reglas
temporales de salud, crecimiento, cosecha e inventario, mediante una API
REST en Laravel y una interfaz construida con HTML, CSS y JavaScript.
 
Objetivos específicos cubiertos en este sprint:
 
- Registrar e iniciar sesión de usuarios.
- Modelar los datos base (usuarios y árboles) de forma que puedan
  evolucionar hacia catálogo, inventario y efectos sin rediseñar la base.
- Implementar una API REST autenticada que autorice únicamente acciones
  sobre recursos propios.
- Construir una interfaz base que consuma el API mediante Fetch.
- Aplicar el patrón MVC utilizando Laravel, con migraciones, seeders y
  Eloquent ORM.
---
 
## Tecnologías
 
*Frontend:* HTML5 semántico, CSS3 (Flexbox/Grid), JavaScript (ES6+),
Fetch API.
 
*Backend:* PHP 8.x, Laravel 13, Laravel Sanctum (tokens de acceso personal,
sin JWT), Blade, Eloquent ORM.
 
*Infraestructura y herramientas:* Vite, PostgreSQL (Neon), PHPUnit,
Laravel Pint, Git, GitHub, GitHub Actions, Render.
 
---
 
## Requisitos locales
 
- Windows 10 o superior
- Laravel Herd
- PHP 8.x
- Composer
- Node.js
- npm
- Git
- Visual Studio Code
- Cuenta de Neon
- Cuenta de Render
---
 
## Instalación local
 
### 1. Clonar el repositorio
 
powershell
git clone https://github.com/Ethankabalzt/GreenCycle.git
Set-Location GreenCycle

 
### 2. Configurar Laravel Herd
 
powershell
herd init
herd isolate

 
### 3. Seleccionar la versión de Node
 
powershell
nvm use

 
### 4. Instalar dependencias
 
powershell
composer install
npm install

 
### 5. Crear el archivo de configuración
 
powershell
Copy-Item .env.example .env
php artisan key:generate

### 6. Configurar la base de datos
 
Editar el archivo .env con la información de conexión correspondiente:
 
env
DB_CONNECTION=pgsql
DB_URL="URL_DE_NEON_DEVELOPMENT"
DB_SSLMODE=require

 
### 7. Limpiar la configuración
 
powershell
php artisan optimize:clear

 
### 8. Ejecutar migraciones y seeders
 
powershell
php artisan migrate:fresh --seed

 
### 9. Iniciar Vite
 
powershell
npm run dev

 
### 10. Ejecutar la aplicación
 
powershell
php artisan serve

 
Abrir en el navegador:
 

http://greencycle.test/

 
---
 
## Modelo de datos
 
| Entidad | Campos clave | Notas |
|---|---|---|
| users | id, name, email, password, coins | Autenticación con Sanctum. |
| seed_types | id, name, cares_by_level, harvest_coins | Catálogo de semillas. |
| trees | id, user_id, seed_type_id, level, health, progress, status, planted_at, last_cared_at, next_care_at, last_decay_at, next_decay_at, harvested_at | status: ACTIVE, MATURE, DEAD, HARVESTED |
| cares | id, tree_id, user_id, action, progress_gained, performed_at | Historial de cuidados. |
| shop_items | id, name, effect_type, cost, effect_duration_min | Catálogo de la tienda. |
| inventory_items | id, user_id, shop_item_id, quantity | UNIQUE (user_id, shop_item_id) y quantity >= 0. |
| purchases | id, user_id, shop_item_id, quantity, coins_spent, purchased_at | Historial de compras. |
| active_effects | id, tree_id, shop_item_id, effect_type, activated_at, expires_at, active_flag | UNIQUE (tree_id, effect_type, active_flag): un efecto vigente por tipo y árbol. |
| personal_access_tokens | id, tokenable_id, name, token, abilities, expires_at | Tokens opacos de Laravel Sanctum. |

Reglas de estado inicial de un árbol: nivel 0, salud 100, progreso 0,
estado ACTIVE.

`last_cared_at` y `last_decay_at` son marcas distintas: la primera responde
"¿cuándo se cuidó el árbol?" y la segunda "¿hasta qué momento ya se aplicó el
deterioro?". El deterioro programado avanza `last_decay_at` hasta el instante
del intervalo realmente procesado, así que ejecutar el proceso dos veces con la
misma hora simulada produce un solo deterioro.

---

## DER (diagrama entidad-relación)

El diagrama completo, con entidades, cardinalidades, llaves foráneas y
restricciones, está en [`docs/der.md`](docs/der.md) (Mermaid, se renderiza
directamente en GitHub) y en [`docs/der.dbml`](docs/der.dbml) para
dbdiagram.io. Ambos se mantienen en sincronía con las migraciones.

---
 
## Funcionalidades del Sprint 1
 
*Alcance actual:* en este sprint la aplicación permite registrar e
iniciar sesión, plantar un árbol, consultar el listado propio de árboles
y ver el detalle de cada uno. Se implementó el modelo de datos completo
del dominio, las relaciones, los seeders y el deterioro programado. La
economía, la tienda y el inventario se desarrollarán en sprints posteriores.

- [x] Registro de usuarios.
- [x] Inicio y cierre de sesión mediante Laravel Sanctum.
- [x] Rutas y endpoints privados protegidos.
- [x] Modelo de datos inicial (usuarios, árboles, tipos de semilla, cuidados,
      tienda, inventario, compras y efectos).
- [x] Migraciones y seeders reproducibles.
- [x] Autorización por propiedad (cada usuario solo ve/modifica sus árboles).
- [x] Creación de árboles (POST /api/trees).
- [x] Consulta del listado de árboles propios.
- [x] Consulta del detalle de un árbol propio.
- [x] Dashboard inicial (registro/login, plantar y visualizar árboles).
- [x] Integración Frontend-Backend mediante Fetch API, con estados de carga,
      éxito, error y vacío, sin recargas completas de página.
- [x] Checkpoint de deterioro (`last_decay_at`) y comando programado
      `trees:apply-decay` idempotente.
- [x] Restricciones de inventario y de efectos vigentes en la base de datos.
- [x] DER del modelo de datos ([`docs/der.md`](docs/der.md)).
---

## Tareas programadas

El deterioration de los árboles y la liberación de efectos vencidos se ejecutan
con el scheduler de Laravel, registrado en `bootstrap/app.php`:

| Comando | Frecuencia | Qué hace |
|---|---|---|
| `php artisan trees:apply-decay` | cada hora | Aplica el deterioro pendiente y avanza el checkpoint `last_decay_at`. |
| `php artisan effects:release-expired` | cada hora | Libera los efectos que ya cumplieron su duración. |

Para probarlas manualmente: `php artisan trees:apply-decay`. En producción se
dispara con `php artisan schedule:run` una vez por minuto.

---

## API inicial

Todas las rutas bajo /api/* requieren autenticación (Sanctum), salvo que
se indique lo contrario. El cliente *nunca* define nivel, salud, estado,
fechas u otros valores internos: esos los calcula y devuelve el servidor.

| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| POST | /api/auth/register | Registrar usuario | No |
| POST | /api/auth/login | Iniciar sesión | No |
| POST | /api/auth/logout | Cerrar sesión | Sí |
| GET | /api/user | Usuario autenticado | Sí |
| GET | /api/trees | Listar árboles del usuario autenticado | Sí |
| GET | /api/trees/{tree} | Consultar detalle de un árbol propio | Sí |
| POST | /api/trees | Plantar un árbol (indicando tipo de semilla) | Sí |


*Documentación / colección del API:* *(agregar aquí el enlace a la
colección de Postman/Insomnia o al archivo .http/OpenAPI del
repositorio)*.

---

## Autenticación

La estrategia elegida es **Laravel Sanctum con tokens de acceso personal
(personal access tokens)**, que son cadenas opacas almacenadas con su hash en
la tabla `personal_access_tokens`. **No se usa JWT**: el token no contiene
payload ni claims, no se decodifica ni se expira por sí solo, y el servidor no
lo interpreta, solo lo busca.

Flujo completo:

1. El cliente envía `POST /api/auth/register` o `POST /api/auth/login` con sus
   credenciales.
2. El servidor crea el token y devuelve el valor en texto plano:

   ```php
   $token = $user->createToken('greencycle')->plainTextToken;

   return response()->json(['token' => $token]);
   ```

3. El cliente lo guarda y lo envía en cada petición:

   ```
   Authorization: Bearer <token>
   ```

4. El servidor valida el token con el middleware `auth:sanctum` de las rutas
   protegidas en `routes/api.php`.
5. `POST /api/auth/logout` elimina el token actual de la base de datos.

Para cerrar sesión en todos los dispositivos se llama a
`$user->tokens()->delete()`.

---
 
## Comprobaciones del proyecto
 
Ejecutar las pruebas automatizadas:
 
powershell
php artisan test

 
Comprobar el formato del código:
 
powershell
.\vendor\bin\pint --test

 
Corregir automáticamente el formato:
 
powershell
.\vendor\bin\pint

 
Generar los recursos de producción:
 
powershell
npm run build

 
---
 
## Ambientes
 
| Ambiente | Aplicación | Base de datos |
|---|---|---|
| Desarrollo | Laravel Herd | Neon (development) |
| Pruebas | PHPUnit | SQLite en memoria |
| Producción | Render | Neon (production) |
 
---
 
## Seguridad
 
Nunca deben publicarse en el repositorio:
 
- El archivo .env.
- Credenciales de la base de datos.
- APP_KEY.
- Tokens de acceso.
- Contraseñas.
- Claves API.
- Información privada.
Antes de realizar un commit ejecute:
 
powershell
git status

 
---
 
## Flujo de trabajo
 
1. Actualizar la rama main.
2. Crear una nueva rama.
3. Desarrollar la funcionalidad.
4. Ejecutar las pruebas (php artisan test).
5. Verificar el formato del código (pint --test).
6. Crear el commit.
7. Publicar la rama.
8. Crear un Pull Request.
9. Esperar la validación automática (GitHub Actions).
10. Fusionar los cambios.
---
 
## Credenciales demo
 
| Rol | Email | Contraseña |
|---|---|---|
| Usuario demo | demo@greencycle.test | demo1234. |
 
---
 
## Equipo y atribuciones
 
*Equipo*
 
- Ethan Cabalceta
- Jaret Paniagua


*Uso de IA y recursos externos*
 
Se usa para la revisión y corrección del código además de ofrecer orientación para 
llegar a la solución del problema
 
---
 
## Estado del proyecto

🚧 *Sprint 1 en desarrollo.*

Correcciones aplicadas tras la revisión del Sprint 1:

- Autenticación documentada como Laravel Sanctum con tokens de acceso
  personal, sin JWT ni claims.
- `last_decay_at` y `next_decay_at` como checkpoint del deterioro, con el
  comando programado `trees:apply-decay` idempotente.
- DER del modelo de datos en [`docs/der.md`](docs/der.md).
- Restricciones `UNIQUE` y `CHECK` en `inventory_items` y `active_effects`
  para impedir inventarios duplicados, cantidades negativas y efectos
  duplicados vigentes.
