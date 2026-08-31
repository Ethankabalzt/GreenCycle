# GreenCycle
 
Proyecto desarrollado para el curso **TM4100**.
 
GreenCycle es una aplicación web gamificada cliente-servidor en la que cada
persona usuaria administra un **vivero digital**. Cada árbol posee un tipo,
un nivel, salud, progreso y estado. El servidor aplica las reglas de
crecimiento y deterioro según el tiempo transcurrido: un árbol con salud 0
muere y ya no puede recibir cuidados; un árbol que alcanza el nivel máximo
puede cosecharse para obtener monedas e invertirlas en ítems de la tienda.
 
Este README documenta el estado del proyecto durante el **Sprint 1**:
autenticación de usuarios, modelo de datos, API inicial de árboles e
interfaz base.
 
---
 
## Tabla de contenidos
 
- [Propósito](#propósito)
- [Tecnologías](#tecnologías)
- [Requisitos locales](#requisitos-locales)
- [Instalación local](#instalación-local)
- [Modelo de datos](#modelo-de-datos)
- [Funcionalidades del Sprint 1](#funcionalidades-del-sprint-1)
- [API inicial](#api-inicial)
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
 
**Frontend:** HTML5 semántico, CSS3 (Flexbox/Grid), JavaScript (ES6+),
Fetch API.
 
**Backend:** PHP 8.x, Laravel 13, Laravel Sanctum, Blade, Eloquent ORM.
 
**Infraestructura y herramientas:** Vite, PostgreSQL (Neon), PHPUnit,
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
 
```powershell
git clone https://github.com/Ethankabalzt/GreenCycle.git
Set-Location GreenCycle
```
 
### 2. Configurar Laravel Herd
 
```powershell
herd init
herd isolate
```
 
### 3. Seleccionar la versión de Node
 
```powershell
nvm use
```
 
### 4. Instalar dependencias
 
```powershell
composer install
npm install
```
 
### 5. Crear el archivo de configuración
 
```powershell
Copy-Item .env.example .env
php artisan key:generate
```
### 6. Configurar la base de datos
 
Editar el archivo `.env` con la información de conexión correspondiente:
 
```env
DB_CONNECTION=pgsql
DB_URL="URL_DE_NEON_DEVELOPMENT"
DB_SSLMODE=require
```
 
### 7. Limpiar la configuración
 
```powershell
php artisan optimize:clear
```
 
### 8. Ejecutar migraciones y seeders
 
```powershell
php artisan migrate:fresh --seed
```
 
### 9. Iniciar Vite
 
```powershell
npm run dev
```
 
### 10. Ejecutar la aplicación
 
```powershell
php artisan serve
```
 
Abrir en el navegador:
 
```
http://greencycle.test/
```
 
---
 
## Modelo de datos
 
> 📌 Pendiente: agregar aquí el **diagrama entidad-relación** (imagen o
> enlace) exigido por el criterio **S1.2** de la rúbrica.
 
| Entidad | Campos clave | Notas |
|---|---|---|
| `users` | id, name, email, password | Autenticación con Sanctum |
| `trees` | id, user_id, seed_type_id, level, health, progress, status, planted_at, harvested_at | `status`: ACTIVE, MATURE, DEAD, HARVESTED |
| `seed_types` | id, name, care_needed_per_level, harvest_reward | Base para "semilla especial" del Sprint 3 |
| `items` *(futuro)* | id, name, cost, effect_type, duration | Catálogo de tienda (Sprint 3) |
| `inventories` *(futuro)* | id, user_id, item_id, quantity | Ítems por usuario (Sprint 3) |
 
Reglas de estado inicial de un árbol: nivel `0`, salud `100`, progreso `0`,
estado `ACTIVE`.
 
---
 
## Funcionalidades del Sprint 1
 
**Alcance actual:** en este sprint la aplicación permite registrar e
iniciar sesión, plantar un árbol, consultar el listado propio de árboles
y ver el detalle de cada uno. Se implementó el modelo de datos inicial,
las relaciones y los seeders. Las reglas temporales (cuidado, cooldown,
deterioro), la economía y el inventario se desarrollarán en sprints
posteriores.
 
- [ ] Registro de usuarios.
- [ ] Inicio y cierre de sesión mediante Laravel Sanctum.
- [ ] Rutas y endpoints privados protegidos.
- [ ] Modelo de datos inicial (usuarios, árboles, tipos de semilla).
- [ ] Migraciones y seeders reproducibles.
- [ ] Autorización por propiedad (cada usuario solo ve/modifica sus árboles).
- [ ] Creación de árboles (`POST /api/trees`).
- [ ] Consulta del listado de árboles propios.
- [ ] Consulta del detalle de un árbol propio.
- [ ] Dashboard inicial (registro/login, plantar y visualizar árboles).
- [ ] Integración Frontend-Backend mediante Fetch API, con estados de carga,
      éxito, error y vacío, sin recargas completas de página.
---
 
## API inicial
 
Todas las rutas bajo `/api/*` requieren autenticación (Sanctum), salvo que
se indique lo contrario. El cliente **nunca** define nivel, salud, estado,
fechas u otros valores internos: esos los calcula y devuelve el servidor.
 
| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| POST | `/register` | Registrar usuario | No |
| POST | `/login` | Iniciar sesión | No |
| POST | `/logout` | Cerrar sesión | Sí |
| GET | `/api/trees` | Listar árboles del usuario autenticado | Sí |
| GET | `/api/trees/{tree}` | Consultar detalle de un árbol propio | Sí |
| POST | `/api/trees` | Plantar un árbol (indicando tipo de semilla) | Sí |
 

**Documentación / colección del API:** *(agregar aquí el enlace a la
colección de Postman/Insomnia o al archivo `.http`/OpenAPI del
repositorio)*.
 
---
 
## Comprobaciones del proyecto
 
Ejecutar las pruebas automatizadas:
 
```powershell
php artisan test
```
 
Comprobar el formato del código:
 
```powershell
.\vendor\bin\pint --test
```
 
Corregir automáticamente el formato:
 
```powershell
.\vendor\bin\pint
```
 
Generar los recursos de producción:
 
```powershell
npm run build
```
 
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
 
- El archivo `.env`.
- Credenciales de la base de datos.
- `APP_KEY`.
- Tokens de acceso.
- Contraseñas.
- Claves API.
- Información privada.
Antes de realizar un commit ejecute:
 
```powershell
git status
```
 
---
 
## Flujo de trabajo
 
1. Actualizar la rama `main`.
2. Crear una nueva rama.
3. Desarrollar la funcionalidad.
4. Ejecutar las pruebas (`php artisan test`).
5. Verificar el formato del código (`pint --test`).
6. Crear el commit.
7. Publicar la rama.
8. Crear un Pull Request.
9. Esperar la validación automática (GitHub Actions).
10. Fusionar los cambios.
---
 
## Credenciales demo
 
| Rol | Email | Contraseña |
|---|---|---|
| Usuario demo | `demo@greencycle.test` | *demo1234.* |
 
---
 
## Equipo y atribuciones
 
**Equipo**
 
- Ethan Cabalceta
- Jaret Paniagua


**Uso de IA y recursos externos**
 
*(Declarar aquí, según lo exigido por el curso, cualquier herramienta de
IA u otro recurso externo utilizado durante el desarrollo del sprint,
indicando en qué parte se usó.)*
 
---
 
## Estado del proyecto
 
🚧 **Sprint 1 en desarrollo.**
 
