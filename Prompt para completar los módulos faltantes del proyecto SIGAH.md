Actúa como un desarrollador senior experto en Laravel, PHP, MySQL, arquitectura MVC, Blade, Bootstrap/Tailwind según corresponda, JavaScript, Eloquent ORM, seguridad, autenticación, autorización, pruebas y refactorización de aplicaciones empresariales.

Estoy trabajando en un proyecto Laravel llamado **SIGAH**.

El proyecto originalmente tenía arquitectura multitenant, pero ya fue o está siendo convertido a una arquitectura:

**SINGLE-TENANT**

Es decir:

```text
Usuario autenticado
        ↓
Sistema SIGAH
        ↓
Módulos
        ↓
Única base de datos
```

No debe volver a introducirse ninguna arquitectura basada en:

```text
tenant
tenant_id
multitenancy
tenancy
bases de datos por tenant
subdominios por tenant
contexto de tenant
```

Tu tarea ahora es **revisar nuevamente TODO el proyecto, identificar los módulos faltantes o incompletos y completar su implementación funcional**.

# OBJETIVO

Quiero que SIGAH quede como una aplicación completa, coherente y funcional.

Debes:

1. Auditar el proyecto.
2. Identificar módulos existentes.
3. Identificar módulos incompletos.
4. Identificar funcionalidades faltantes.
5. Identificar páginas sin funcionalidad.
6. Identificar botones que no hacen nada.
7. Identificar rutas faltantes.
8. Identificar controladores incompletos.
9. Identificar modelos incompletos.
10. Identificar migraciones faltantes.
11. Identificar relaciones Eloquent faltantes.
12. Identificar formularios incompletos.
13. Identificar validaciones faltantes.
14. Identificar vistas faltantes.
15. Identificar permisos faltantes.
16. Identificar reportes o estadísticas incompletas.
17. Implementar todo lo necesario.
18. Probar el sistema completo.

No debes limitarte a decir qué falta.

Debes **realizar las modificaciones necesarias en el código**.

---

# FASE 1 — AUDITORÍA COMPLETA

Antes de modificar código revisa como mínimo:

```text
composer.json
package.json
.env.example

app/
app/Models/
app/Http/
app/Http/Controllers/
app/Http/Requests/
app/Policies/
app/Services/
app/Providers/

bootstrap/

config/

database/
database/migrations/
database/seeders/
database/factories/

resources/
resources/views/
resources/js/
resources/css/

routes/
routes/web.php
routes/api.php

public/

tests/
```

También revisa cualquier otra carpeta relevante encontrada.

Ejecuta:

```bash
git status
```

y respeta cualquier modificación existente del usuario.

NO utilices:

```bash
git reset --hard
```

ni descartes cambios existentes.

---

# FASE 2 — IDENTIFICAR LA ARQUITECTURA ACTUAL

Determina:

- versión de Laravel;
- versión de PHP;
- motor de base de datos;
- sistema de autenticación;
- sistema de roles y permisos;
- frontend utilizado;
- librerías principales;
- sistema de plantillas;
- estructura de navegación;
- organización de controladores;
- organización de servicios;
- organización de modelos;
- convenciones de código utilizadas.

Respeta la arquitectura existente.

NO actualices Laravel ni PHP salvo que sea absolutamente necesario.

---

# FASE 3 — INVENTARIO DE MÓDULOS

Construye internamente un inventario completo.

Para cada módulo identifica:

```text
Módulo
Estado
Modelo
Migración
Controlador
Rutas
Vistas
Formularios
Validaciones
Relaciones
Permisos
Servicios
Reportes
Pruebas
```

Clasifica cada módulo como:

```text
COMPLETO
PARCIAL
INCOMPLETO
NO IMPLEMENTADO
CÓDIGO MUERTO
```

Utiliza como fuentes para descubrir los módulos:

- menú lateral;
- dashboard;
- rutas;
- modelos;
- migraciones;
- controladores;
- vistas;
- enlaces;
- tarjetas;
- documentación existente;
- seeders;
- nombres de tablas;
- permisos;
- funcionalidades ya iniciadas.

---

# FASE 4 — DETECTAR MÓDULOS FALTANTES

No inventes módulos arbitrariamente.

Determina qué módulos deberían existir basándote en:

1. arquitectura actual;
2. navegación;
3. entidades de la base de datos;
4. modelos;
5. relaciones;
6. permisos;
7. vistas;
8. botones;
9. endpoints;
10. comentarios TODO;
11. funcionalidades iniciadas pero no terminadas.

Busca también:

```text
TODO
FIXME
PENDING
pendiente
implementar
proximamente
próximamente
href="#"
route pendiente
return null
throw new Exception
```

y cualquier placeholder similar.

---

# FASE 5 — COMPLETAR CRUD

Todo módulo administrativo que corresponda debe tener, cuando sea funcionalmente apropiado:

```text
Listado
Crear
Ver
Editar
Actualizar
Eliminar
Buscar
Filtrar
Paginar
Validar
Mostrar mensajes de éxito/error
```

Implementa usando las convenciones actuales del proyecto.

Cuando sea adecuado utiliza:

```php
index()
create()
store()
show()
edit()
update()
destroy()
```

No implementes funciones CRUD innecesarias si el módulo es únicamente de consulta.

---

# FASE 6 — MODELOS

Revisa todos los modelos Eloquent.

Cada modelo debe tener correctamente definidos:

```php
$fillable
$casts
```

y cuando aplique:

```php
$hidden
$appends
```

Configura todas las relaciones necesarias:

```php
belongsTo()
hasOne()
hasMany()
belongsToMany()
morphTo()
morphMany()
```

según la estructura real de la base de datos.

Evita consultas N+1 utilizando eager loading cuando corresponda:

```php
with()
load()
```

---

# FASE 7 — MIGRACIONES

Comprueba que la base de datos soporte correctamente todos los módulos.

Revisa:

- foreign keys;
- índices;
- campos nullable;
- valores default;
- campos unique;
- restricciones;
- tipos de datos;
- timestamps;
- soft deletes si el proyecto los utiliza.

Si una tabla existente necesita cambios:

NO modifiques migraciones antiguas ya ejecutadas salvo que el proyecto se encuentre claramente en una etapa inicial donde esto sea seguro.

Preferiblemente crea nuevas migraciones.

Ejemplo:

```bash
php artisan make:migration add_estado_to_solicitudes_table
```

---

# FASE 8 — FORM REQUESTS

No llenes los controladores de validaciones extensas.

Cuando corresponda crea:

```text
StoreXRequest
UpdateXRequest
```

Ejemplo:

```bash
php artisan make:request StoreUsuarioRequest
php artisan make:request UpdateUsuarioRequest
```

Implementa:

```php
public function rules(): array
```

y mensajes personalizados cuando sea conveniente.

---

# FASE 9 — CONTROLADORES

Los controladores deben:

- ser claros;
- evitar duplicación;
- utilizar Eloquent correctamente;
- utilizar Form Requests;
- manejar errores;
- utilizar transacciones cuando existan operaciones múltiples;
- comprobar permisos;
- retornar vistas o respuestas adecuadas.

Cuando una operación afecte varias tablas utiliza:

```php
DB::transaction(function () {
    //
});
```

---

# FASE 10 — SERVICIOS

Si existe lógica de negocio compleja, no la acumules dentro de los controladores.

Utiliza la arquitectura existente del proyecto.

Cuando sea apropiado crea servicios como:

```text
app/Services/
```

Ejemplo:

```text
UsuarioService
ReporteService
InventarioService
SolicitudService
```

pero solo si realmente simplifica el código.

No sobrearquitectures el proyecto.

---

# FASE 11 — INTERFAZ

Revisa todos los elementos visuales.

Identifica:

- enlaces rotos;
- botones sin acción;
- formularios incompletos;
- páginas vacías;
- modales incompletos;
- tablas incompletas;
- botones que apuntan a `#`;
- rutas inexistentes;
- menús sin módulo asociado.

Completa cada funcionalidad encontrada.

Conserva el diseño visual actual.

NO rediseñes la aplicación completa.

---

# FASE 12 — TABLAS

Los listados administrativos deben incluir, cuando tenga sentido:

- búsqueda;
- paginación;
- ordenamiento;
- filtros;
- estado;
- acciones.

Ejemplo conceptual:

```text
Buscar: [____________]

Estado: [Todos ▼]

------------------------------------------------
Código | Nombre | Estado | Fecha | Acciones
------------------------------------------------
```

Acciones comunes:

```text
Ver
Editar
Eliminar
Activar
Desactivar
```

según corresponda.

---

# FASE 13 — CONFIRMACIONES

Las acciones destructivas deben solicitar confirmación.

Por ejemplo:

```text
¿Está seguro de eliminar este registro?
```

Utiliza el sistema visual ya existente.

No introduzcas librerías nuevas innecesariamente.

---

# FASE 14 — MENSAJES

Las operaciones CRUD deben mostrar mensajes.

Ejemplo:

```text
Registro creado correctamente.
Registro actualizado correctamente.
Registro eliminado correctamente.
```

y errores cuando corresponda.

---

# FASE 15 — ESTADOS

Si existen módulos que manejan procesos, revisa si necesitan estados.

Ejemplo:

```text
Pendiente
En proceso
Aprobado
Rechazado
Finalizado
Cancelado
```

No inventes estados sin analizar previamente la lógica encontrada.

Cuando un modelo ya contiene un campo:

```text
estado
status
situacion
```

determina su uso correcto.

---

# FASE 16 — ROLES Y PERMISOS

Revisa si el proyecto utiliza:

```text
spatie/laravel-permission
```

u otro sistema.

Mantén el sistema existente.

Todo módulo sensible debe tener controles de autorización.

Ejemplo conceptual:

```php
$this->authorize('update', $registro);
```

o middleware:

```php
permission:usuarios.editar
```

según la arquitectura actual.

No confíes exclusivamente en ocultar botones en la interfaz.

La autorización también debe existir del lado servidor.

---

# FASE 17 — USUARIOS

Revisa completamente el módulo de usuarios.

Debe permitir, cuando corresponda:

```text
Listar usuarios
Crear usuarios
Editar usuarios
Ver usuarios
Activar/desactivar usuarios
Asignar roles
Asignar permisos
Cambiar contraseña
Restablecer contraseña
Gestionar perfil
```

Nunca mostrar contraseñas.

Utiliza:

```php
Hash::make()
```

para almacenar contraseñas.

---

# FASE 18 — DASHBOARD

Analiza el dashboard actual.

Las tarjetas y estadísticas deben provenir de datos reales.

Eliminar valores hardcodeados como:

```text
150 usuarios
20 solicitudes
85 %
```

si no representan datos reales.

Convertirlos en consultas dinámicas.

Por ejemplo:

```php
$usuarios = User::count();
```

Cuando corresponda.

El dashboard debe mostrar indicadores útiles basados en los módulos existentes.

---

# FASE 19 — REPORTES

Si el sistema ya contempla reportes, completa las funcionalidades faltantes.

Los reportes deben poder filtrar, cuando aplique, por:

```text
Fecha inicial
Fecha final
Estado
Categoría
Responsable
Usuario
```

No inventes reportes fuera del alcance funcional del proyecto.

---

# FASE 20 — BÚSQUEDA GLOBAL

Si ya existe una interfaz de búsqueda global incompleta, termínala.

No la agregues si no existe ninguna señal arquitectónica de que sea necesaria.

---

# FASE 21 — ARCHIVOS

Si existen módulos que manejan archivos:

- validar extensión;
- validar tamaño;
- generar nombres seguros;
- utilizar Storage de Laravel;
- evitar sobrescribir archivos accidentalmente;
- controlar permisos de acceso.

Utiliza:

```php
Storage
```

según la configuración existente.

Nunca confíes en el nombre original enviado por el navegador.

---

# FASE 22 — SEGURIDAD

Comprueba:

- CSRF;
- autenticación;
- autorización;
- mass assignment;
- validación;
- SQL injection;
- XSS;
- subida de archivos;
- acceso directo a URLs protegidas;
- exposición de datos sensibles.

Utiliza las protecciones nativas de Laravel.

No construyas SQL concatenando directamente valores del usuario.

---

# FASE 23 — SINGLE-TENANT

MUY IMPORTANTE:

El proyecto ya no debe depender de multitenancy.

Durante esta implementación no introduzcas:

```text
tenant_id
Tenant
tenant()
tenancy
multitenancy
bases por organización
conexiones dinámicas
subdominios tenant
```

Si encuentras referencias sobrantes de la arquitectura anterior, elimínalas o refactorízalas cuando sea seguro.

---

# FASE 24 — NAVEGACIÓN

Compara:

```text
Menú
        ↓
Rutas
        ↓
Controladores
        ↓
Vistas
        ↓
Base de datos
```

Todo elemento visible del menú debe tener una funcionalidad real.

Si existe:

```text
Usuarios
Inventario
Solicitudes
Reportes
Configuración
```

cada opción debe apuntar a una ruta válida y a un módulo funcional.

No deben quedar elementos decorativos que parezcan funcionalidades disponibles pero no funcionen.

---

# FASE 25 — RUTAS

Ejecuta:

```bash
php artisan route:list
```

Comprueba:

- rutas duplicadas;
- rutas inexistentes;
- nombres incorrectos;
- controladores faltantes;
- middleware incorrectos.

Mantén nombres consistentes.

Ejemplo:

```php
Route::resource('usuarios', UsuarioController::class);
```

cuando corresponda.

---

# FASE 26 — SEEDERS

Revisa los seeders.

Deben existir datos mínimos cuando sean necesarios para iniciar la aplicación.

Ejemplos:

```text
roles
permisos
usuario administrador
catálogos básicos
estados
tipos
```

No destruyas información existente.

---

# FASE 27 — DATOS HARDCODEADOS

Busca información codificada directamente en vistas o controladores que debería venir de base de datos.

Ejemplos:

```php
$estados = ['Activo', 'Inactivo'];
```

No es obligatorio mover todos los valores a base de datos.

Determina cuál información corresponde a:

```text
enum
configuración
catálogo
tabla
```

según su naturaleza.

---

# FASE 28 — CONFIGURACIÓN

Si existe un módulo de configuración, completa únicamente las funcionalidades que estén justificadas por la aplicación.

Podría incluir, si ya está contemplado:

```text
Nombre del sistema
Datos institucionales
Logo
Correo
Teléfono
Dirección
Parámetros generales
```

Al ser single-tenant, estas configuraciones son globales.

NO crear una tabla `tenants`.

Si se necesita información institucional utiliza un concepto como:

```text
settings
configuracion
institucion
```

según la estructura actual.

---

# FASE 29 — FECHAS

Utiliza Carbon cuando corresponda.

Respeta:

- timezone del proyecto;
- formatos configurados;
- tipos datetime;
- campos nullable.

No realices conversiones manuales innecesarias.

---

# FASE 30 — PAGINACIÓN

Evita:

```php
Model::all();
```

para listados administrativos grandes.

Utiliza preferiblemente:

```php
Model::query()
    ->latest()
    ->paginate(15);
```

cuando corresponda.

---

# FASE 31 — FILTROS

Implementa filtros utilizando Query Builder de forma clara.

Ejemplo:

```php
$query = Modelo::query();

$query->when($request->search, function ($query, $search) {
    $query->where('nombre', 'like', "%{$search}%");
});

$registros = $query->paginate(15);
```

Preserva filtros al paginar usando:

```php
->withQueryString()
```

cuando corresponda.

---

# FASE 32 — SOFT DELETE

Si el proyecto ya utiliza:

```php
SoftDeletes
```

respétalo.

No introduzcas soft deletes indiscriminadamente.

---

# FASE 33 — AUDITORÍA DE FUNCIONALIDADES ROTAS

Busca expresamente:

```text
href="#"
onclick vacío
formularios sin action
rutas inexistentes
controladores vacíos
métodos TODO
return view inexistente
variables inexistentes
componentes Blade inexistentes
JavaScript con selectores inexistentes
fetch hacia rutas inexistentes
axios hacia endpoints inexistentes
```

Corrige todo lo relacionado directamente con los módulos del sistema.

---

# FASE 34 — ERRORES

Revisa logs si están disponibles:

```text
storage/logs/laravel.log
```

Identifica errores causados por:

- clases inexistentes;
- rutas;
- SQL;
- columnas inexistentes;
- vistas;
- relaciones;
- permisos;
- referencias antiguas a tenancy.

Corrige los errores relacionados con el proyecto.

---

# FASE 35 — PRUEBAS MANUALES Y AUTOMATIZADAS

Después de cada grupo importante de cambios realiza validaciones.

Ejecuta:

```bash
php artisan optimize:clear
```

```bash
php artisan route:list
```

```bash
php artisan migrate:status
```

```bash
php artisan test
```

Si el proyecto utiliza frontend compilado:

```bash
npm run build
```

o el comando existente correspondiente.

---

# FASE 36 — CREAR TESTS FALTANTES

Agrega pruebas para los módulos importantes cuando sea razonable.

Prioriza:

```text
Autenticación
Usuarios
Roles/permisos
CRUD principales
Validaciones
Autorización
Rutas principales
```

No necesitas alcanzar 100 % de cobertura.

La prioridad es comprobar los flujos críticos.

---

# FASE 37 — NO ROMPER FUNCIONALIDADES EXISTENTES

Antes de cambiar un módulo funcional:

analiza si realmente necesita cambios.

No reescribas código solo por preferencia personal.

Prioridades:

```text
1. Corregir
2. Completar
3. Integrar
4. Optimizar
5. Refactorizar solo cuando sea necesario
```

---

# FASE 38 — RESPETAR EL DISEÑO

Mantén:

- layout;
- navbar;
- sidebar;
- tipografía;
- componentes;
- colores;
- iconos;
- estilo visual.

Cuando crees nuevas vistas reutiliza los componentes existentes.

No mezcles Bootstrap con Tailwind si el proyecto utiliza únicamente uno de ellos.

---

# FASE 39 — FLUJO DE CADA MÓDULO

Para cada módulo incompleto comprueba:

```text
MIGRACIÓN
   ↓
MODELO
   ↓
RELACIONES
   ↓
REQUEST
   ↓
CONTROLADOR / SERVICE
   ↓
RUTAS
   ↓
VISTAS
   ↓
PERMISOS
   ↓
NAVEGACIÓN
   ↓
PRUEBAS
```

No consideres un módulo terminado solamente porque existe su interfaz.

---

# FASE 40 — PRIORIZACIÓN

Completa los módulos en este orden:

```text
1. Errores estructurales
2. Autenticación
3. Usuarios
4. Roles y permisos
5. Catálogos/dependencias
6. Módulos principales de negocio
7. Módulos secundarios
8. Dashboard
9. Reportes
10. Configuración
11. Mejoras UX
12. Tests finales
```

Adapta este orden dependiendo de las dependencias reales encontradas.

---

# CRITERIOS PARA CONSIDERAR UN MÓDULO COMPLETO

Un módulo solo se considera:

```text
COMPLETO
```

si, cuando corresponda, cuenta con:

```text
✔ tabla
✔ modelo
✔ relaciones
✔ controlador
✔ requests
✔ rutas
✔ vistas
✔ crear
✔ editar
✔ eliminar
✔ consultar
✔ buscar
✔ filtros
✔ paginación
✔ validación
✔ autorización
✔ mensajes
✔ navegación
✔ manejo de errores
✔ pruebas básicas
```

---

# FASE FINAL — REVISIÓN COMPLETA

Cuando termines, vuelve a realizar una auditoría global.

Busca nuevamente:

```text
TODO
FIXME
href="#"
tenant
tenant_id
tenancy
multitenancy
```

También busca:

```text
404
500
Route not defined
View not found
Class not found
Unknown column
Undefined variable
```

cuando sea posible.

---

# VALIDACIÓN FINAL

Ejecuta:

```bash
composer dump-autoload
```

```bash
php artisan optimize:clear
```

```bash
php artisan migrate:status
```

```bash
php artisan route:list
```

```bash
php artisan test
```

y:

```bash
npm run build
```

si el proyecto utiliza Vite/NPM.

Si existe Laravel Pint:

```bash
./vendor/bin/pint
```

aplícalo únicamente a los archivos modificados si es posible, evitando cambios masivos no relacionados.

---

# PROHIBICIONES

NO:

- recrees todo el proyecto;
- cambies framework;
- cambies Laravel innecesariamente;
- cambies la versión de PHP;
- implementes nuevamente multitenancy;
- agregues `tenant_id`;
- elimines información existente;
- borres migraciones históricas sin necesidad;
- elimines módulos funcionales;
- cambies el diseño completo;
- agregues dependencias sin necesidad;
- reemplaces código funcional únicamente por preferencia personal;
- uses `git reset --hard`;
- elimines cambios del usuario.

---

# FORMA DE TRABAJO

No te limites a dar recomendaciones.

Debes trabajar directamente sobre el repositorio.

Realiza:

```text
INSPECCIONAR
↓
DETECTAR
↓
CLASIFICAR
↓
CORREGIR
↓
COMPLETAR
↓
INTEGRAR
↓
PROBAR
↓
VOLVER A REVISAR
```

No me pidas confirmación entre cada módulo.

Continúa de forma autónoma hasta completar todo lo que sea razonablemente posible.

Solo detente ante una decisión de negocio que sea verdaderamente imposible inferir del código y cuya elección pueda causar pérdida de datos o alterar de forma importante el funcionamiento esperado.

---

# INFORME FINAL OBLIGATORIO

Cuando termines entrégame un informe dividido así:

## 1. Estado inicial

Lista:

```text
Módulos completos
Módulos parciales
Módulos faltantes
Problemas encontrados
```

## 2. Módulos completados

Para cada módulo indica:

```text
Nombre:
Estado anterior:
Estado final:
Funcionalidades implementadas:
```

## 3. Archivos creados

Lista cada archivo nuevo.

## 4. Archivos modificados

Lista cada archivo modificado.

## 5. Migraciones

Indica todas las migraciones nuevas.

## 6. Base de datos

Describe:

- tablas modificadas;
- campos agregados;
- relaciones;
- índices;
- restricciones.

## 7. Rutas

Indica las rutas nuevas o corregidas.

## 8. Seguridad

Indica:

- validaciones;
- autorización;
- permisos;
- protecciones implementadas.

## 9. Interfaz

Indica:

- vistas nuevas;
- formularios;
- tablas;
- filtros;
- botones corregidos;
- navegación corregida.

## 10. Pruebas

Muestra el resultado de:

```bash
php artisan route:list
php artisan migrate:status
php artisan test
npm run build
```

cuando correspondan.

## 11. Elementos pendientes

Si existe algo que no pudo completarse, indica exactamente:

```text
qué falta
por qué falta
archivo relacionado
qué decisión necesita
```

No utilices simplemente:

```text
queda pendiente
```

sin explicación.

## 12. Estado final

Entrega una tabla similar a:

```text
MÓDULO                  ESTADO
--------------------------------------
Autenticación           COMPLETO
Usuarios                COMPLETO
Roles                   COMPLETO
Permisos                COMPLETO
Módulo X                COMPLETO
Módulo Y                COMPLETO
Dashboard               COMPLETO
Reportes                 COMPLETO
Configuración            COMPLETO
```

Utiliza los nombres REALES de los módulos encontrados en SIGAH.

Finalmente confirma:

```text
ARQUITECTURA: SINGLE-TENANT

AUDITORÍA COMPLETADA: SÍ

MÓDULOS REVISADOS: [cantidad]

MÓDULOS COMPLETADOS: [cantidad]

ERRORES CRÍTICOS PENDIENTES: [cantidad]

TESTS: [resultado]
```

Comienza ahora revisando todo el repositorio.

No supongas qué módulos existen.

Descúbrelos a partir del código real, la base de datos, rutas, menú, vistas y controladores existentes, y después completa las funcionalidades faltantes de SIGAH.