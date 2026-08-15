# ALOJA360 — Prompt Maestro para Codex y Lógica de Negocio

## 1. Contexto del proyecto

**Aloja360** es una aplicación web desarrollada en Laravel para la administración integral de alojamientos turísticos.

El sistema debe poder gestionar inicialmente cabañas, pero su arquitectura debe permitir administrar también:

- Glampings
- Apartamentos turísticos
- Casas vacacionales
- Villas
- Habitaciones
- Fincas
- Otros tipos de alojamiento

La plataforma debe centralizar:

- Alojamientos
- Disponibilidad
- Tarifas
- Clientes y huéspedes
- Cotizaciones
- Reservas
- Pagos
- Check-in
- Check-out
- Limpieza
- Mantenimiento
- Inventario
- Servicios adicionales
- Ingresos
- Gastos
- Notificaciones
- Reportes
- Usuarios
- Roles y permisos
- Auditoría

La aplicación debe diseñarse desde el inicio para soportar múltiples negocios mediante `business_id`, aunque la primera instalación pueda utilizar un solo negocio.

---

# 2. Instrucción principal para Codex

Antes de modificar cualquier archivo del proyecto:

1. Analiza completamente la estructura Laravel existente.
2. Identifica:
   - Versión de Laravel
   - Versión de PHP
   - Base de datos utilizada
   - Sistema de autenticación
   - Paquetes instalados
   - Migraciones existentes
   - Modelos existentes
   - Middleware
   - Rutas
   - Controladores
   - Servicios
   - Policies
   - Jobs
   - Eventos y listeners
   - Frontend utilizado
3. No reemplaces código existente sin justificarlo.
4. No elimines migraciones existentes.
5. No ejecutes `migrate:fresh`.
6. No ejecutes comandos destructivos sin autorización explícita.
7. Mantén compatibilidad con la arquitectura actual.
8. Usa convenciones oficiales de Laravel.
9. Usa nombres de clases, tablas, modelos, rutas internas y código en inglés.
10. La interfaz de usuario estará en español.
11. Antes de implementar una modificación importante, explica:
    - Qué archivos serán modificados
    - Qué archivos serán creados
    - Qué dependencias existen
    - Qué reglas de negocio se afectan
12. Después de cada implementación:
    - Ejecuta pruebas relacionadas
    - Ejecuta validaciones pertinentes
    - Revisa errores
    - Resume los cambios realizados

---

# 3. Principios de arquitectura

El proyecto debe seguir una arquitectura organizada y mantenible.

No colocar toda la lógica dentro de los controladores.

Preferir:

```text
Controller
    ↓
Form Request
    ↓
Service / Action
    ↓
Model
    ↓
Database
```

Cuando corresponda utilizar:

- Form Requests
- Services
- Actions
- Policies
- Events
- Listeners
- Jobs
- Notifications
- PHP Enums
- DTOs
- Observers

Los controladores deben ser delgados.

---

# 4. Estructura conceptual

```text
BUSINESS
│
├── USERS
│
├── ACCOMMODATIONS
│   ├── AMENITIES
│   ├── IMAGES
│   ├── RATES
│   ├── BLOCKED PERIODS
│   ├── INVENTORY
│   ├── CLEANING
│   └── MAINTENANCE
│
├── GUESTS
│
├── QUOTES
│
├── RESERVATIONS
│   ├── GUESTS
│   ├── SERVICES
│   ├── PAYMENTS
│   ├── STATUS HISTORY
│   └── STAY
│
├── EXPENSES
│
├── OUTBOUND MESSAGES
│
└── AUDIT LOGS
```

---

# 5. Entidades principales

## Business

Representa el negocio, propietario o empresa operadora.

Relaciones:

```text
Business
├── hasMany Accommodations
├── hasMany Guests
├── hasMany Reservations
├── hasMany Services
├── hasMany Expenses
├── hasMany Payments
└── belongsToMany Users
```

Campos principales:

```text
id
name
legal_name
tax_id
email
phone
whatsapp
address
city
country
timezone
currency
status
timestamps
softDeletes cuando corresponda
```

Valor inicial recomendado:

```text
timezone = America/Bogota
currency = COP
```

---

# 6. Usuarios, roles y permisos

Un usuario puede pertenecer a uno o varios negocios.

Tabla pivot:

```text
business_user
```

Roles iniciales:

```text
owner
administrator
receptionist
accountant
cleaner
maintenance
```

## Owner

Acceso total al negocio.

## Administrator

Gestiona:

- Alojamientos
- Reservas
- Clientes
- Pagos
- Operación
- Reportes

## Receptionist

Gestiona principalmente:

- Disponibilidad
- Cotizaciones
- Reservas
- Clientes
- Check-in
- Check-out
- Pagos autorizados

## Accountant

Gestiona:

- Pagos
- Ingresos
- Gastos
- Reportes financieros

## Cleaner

Solo debe consultar y modificar tareas de limpieza que le correspondan.

## Maintenance

Solo debe consultar y modificar mantenimiento que le corresponda.

Aplicar autorización mediante Policies y permisos.

Nunca confiar únicamente en ocultar botones en la interfaz.

---

# 7. Alojamientos

Entidad:

```text
Accommodation
```

No usar `Cabin` como entidad principal porque la aplicación debe crecer hacia otros tipos de alojamiento.

Tipos posibles:

```text
cabin
glamping
apartment
house
villa
room
farm
other
```

Estados:

```text
available
reserved
occupied
pending_cleaning
cleaning
maintenance
blocked
```

## Regla fundamental

El estado operativo de un alojamiento y su disponibilidad por fechas son conceptos relacionados pero diferentes.

No se debe asumir que:

```text
status = available
```

significa que está disponible para cualquier fecha.

La disponibilidad real debe calcularse considerando:

- Reservas activas
- Fechas
- Bloqueos
- Mantenimiento
- Reglas operativas

---

# 8. Disponibilidad

Crear posteriormente:

```text
App\Services\AvailabilityService
```

Este servicio será la fuente central para consultar disponibilidad.

Nunca duplicar la lógica de disponibilidad en:

- Controladores
- Livewire components
- API controllers
- Jobs
- Commands

Todos deben consumir el mismo servicio.

## Regla de solapamiento

Una nueva reserva se cruza con una reserva existente cuando:

```text
new_check_in < existing_check_out
AND
new_check_out > existing_check_in
```

Ejemplo:

Reserva existente:

```text
10 agosto → 15 agosto
```

Nueva reserva:

```text
14 agosto → 17 agosto
```

Resultado:

```text
NO DISPONIBLE
```

Nueva reserva:

```text
15 agosto → 18 agosto
```

Resultado:

```text
DISPONIBLE
```

si el check-out se considera liberación del alojamiento ese mismo día y no existe una regla adicional de separación entre reservas.

## Reservas que bloquean disponibilidad

Inicialmente:

```text
pending
confirmed
checked_in
```

Debe evaluarse si una reserva `pending` bloquea indefinidamente o solo durante un período de retención.

## Reservas que NO bloquean

```text
cancelled
checked_out
no_show
```

Un estado `quotation` tampoco debe bloquear disponibilidad.

---

# 9. Bloqueos de alojamiento

Entidad:

```text
BlockedPeriod
```

Tipos:

```text
owner_use
maintenance
administrative
other
```

Una reserva no se puede confirmar cuando sus fechas se superponen con un bloqueo activo.

El `AvailabilityService` debe consultar:

```text
reservations
+
blocked_periods
```

---

# 10. Tarifas

Entidad:

```text
RatePeriod
```

Las tarifas pueden variar por:

- Alojamiento
- Día de semana
- Fin de semana
- Festivo
- Temporada
- Rango de fechas
- Mínimo de noches
- Promoción

Valores monetarios:

```text
decimal(14,2)
```

Nunca utilizar:

```text
float
double
```

para dinero.

## Motor de precios

Crear posteriormente:

```text
App\Services\PricingService
```

Responsabilidad:

```text
check_in
check_out
accommodation
guests
rate rules
services
discounts
fees
taxes
```

Resultado:

```text
nightly_subtotal
services_total
discount_total
tax_total
cleaning_fee
security_deposit
total_amount
```

El cálculo debe ejecutarse noche por noche si las tarifas pueden cambiar dentro de una misma reserva.

---

# 11. Clientes y huéspedes

Entidad:

```text
Guest
```

Datos:

```text
first_name
last_name
document_type
document_number
email
phone
whatsapp
birth_date
country
city
address
notes
marketing_consent
```

## Regla de duplicidad

Antes de crear un huésped se debe intentar detectar existencia por:

1. `business_id + document_number`
2. correo electrónico
3. teléfono

No fusionar automáticamente registros sin autorización cuando exista ambigüedad.

---

# 12. Cotizaciones

Entidad:

```text
Quote
```

Estados:

```text
draft
sent
accepted
rejected
expired
converted
```

Una cotización:

- No bloquea disponibilidad.
- Puede tener fecha de expiración.
- Debe guardar una fotografía de los valores calculados.
- Puede convertirse en reserva.
- Una cotización convertida debe quedar relacionada con la reserva resultante.

## Regla crítica

Antes de convertir una cotización en reserva:

1. Volver a verificar disponibilidad.
2. Volver a validar reglas.
3. Confirmar tarifa o respetar el valor cotizado según política definida.
4. Crear la reserva dentro de una transacción.

---

# 13. Reservas

Entidad:

```text
Reservation
```

Estados iniciales:

```text
pending
confirmed
checked_in
checked_out
cancelled
no_show
```

No utilizar ENUM nativo de base de datos.

Crear PHP Enum posteriormente:

```text
ReservationStatus
```

## Código de reserva

Toda reserva debe tener código humano único.

Ejemplo:

```text
RES-2026-000001
```

El código no reemplaza el `id`.

## Reserva pendiente

Una reserva `pending` puede representar:

- Reserva creada esperando anticipo.
- Reserva retenida temporalmente.
- Solicitud pendiente de confirmación.

Configurar posteriormente tiempo máximo de retención.

Ejemplo:

```text
reservation_hold_minutes = 30
```

o según configuración del negocio.

---

# 14. Ciclo de una reserva

Flujo principal:

```text
Quote
  ↓
Pending Reservation
  ↓
Payment / Deposit
  ↓
Confirmed
  ↓
Checked In
  ↓
Checked Out
  ↓
Pending Cleaning
  ↓
Available
```

Flujos alternativos:

```text
Pending
  ↓
Cancelled
```

```text
Confirmed
  ↓
Cancelled
```

```text
Confirmed
  ↓
No Show
```

No permitir transiciones arbitrarias.

Crear posteriormente:

```text
ReservationService
```

o acciones específicas:

```text
CreateReservationAction
ConfirmReservationAction
CancelReservationAction
CheckInReservationAction
CheckOutReservationAction
```

---

# 15. Historial de estados

Toda modificación de estado de una reserva debe registrar:

```text
reservation_status_histories
```

Campos:

```text
reservation_id
previous_status
new_status
changed_by
notes
created_at
```

Nunca depender únicamente de `updated_at` para reconstruir el historial.

---

# 16. Pagos

Entidad:

```text
Payment
```

Tipos:

```text
payment
refund
deposit
deposit_return
```

Estados:

```text
pending
confirmed
rejected
cancelled
```

Métodos:

```text
cash
bank_transfer
credit_card
debit_card
nequi
daviplata
other
```

## Saldo

No almacenar un campo `balance` como fuente primaria si puede calcularse.

Calcular:

```text
reservation.total_amount
-
confirmed_payments
+
confirmed_refunds
=
outstanding_balance
```

La implementación exacta debe tener en cuenta el signo y tipo de cada movimiento financiero.

## Regla

Solo pagos `confirmed` afectan el saldo real.

---

# 17. Anticipo y confirmación

El sistema debe soportar políticas configurables.

Ejemplos:

```text
50% de anticipo
100% del valor
valor fijo
confirmación manual
```

No codificar un porcentaje único directamente en controladores.

Preparar configuración del negocio.

Una reserva puede pasar de:

```text
pending
```

a:

```text
confirmed
```

cuando:

- Cumpla el anticipo requerido, o
- Un usuario autorizado confirme manualmente.

---

# 18. Servicios adicionales

Entidad:

```text
Service
```

Ejemplos:

- Decoración
- Desayuno
- Cena
- Transporte
- Tour
- Leña
- Mascota
- Lavandería

Pivot:

```text
reservation_services
```

Debe almacenar:

```text
quantity
unit_price
total
```

Nunca depender del precio actual en `services.price` para mostrar reservas históricas.

Si hoy:

```text
Desayuno = 25.000
```

y mañana:

```text
Desayuno = 30.000
```

una reserva anterior debe conservar:

```text
25.000
```

---

# 19. Check-in

Una reserva solo puede hacer check-in cuando:

```text
status = confirmed
```

o mediante excepción autorizada.

Antes del check-in validar:

- Fecha correcta
- Identidad del huésped
- Número de huéspedes
- Saldo requerido según política
- Depósito de seguridad si aplica
- Alojamiento en condición operativa

Al realizar check-in:

```text
reservation.status = checked_in
```

y:

```text
accommodation.status = occupied
```

Registrar:

```text
actual_check_in_at
checked_in_by
```

---

# 20. Check-out

Una reserva normalmente solo puede hacer check-out si:

```text
status = checked_in
```

Al realizar check-out:

```text
reservation.status = checked_out
```

y:

```text
accommodation.status = pending_cleaning
```

Además:

1. Registrar hora real de salida.
2. Revisar cargos pendientes.
3. Registrar daños si existen.
4. Registrar consumos adicionales.
5. Crear automáticamente tarea de limpieza.
6. Opcionalmente crear revisión de inventario.

Todo el proceso debe ejecutarse dentro de una transacción cuando corresponda.

---

# 21. Limpieza

Entidad:

```text
CleaningTask
```

Estados:

```text
pending
assigned
in_progress
completed
cancelled
```

Flujo:

```text
Check-out
    ↓
CleaningTask pending
    ↓
assigned
    ↓
in_progress
    ↓
completed
```

Cuando se complete exitosamente:

```text
accommodation.status = available
```

excepto si existe:

- Mantenimiento
- Bloqueo
- Otra condición operativa

Por lo tanto, no cambiar automáticamente a `available` sin verificar primero otras restricciones.

---

# 22. Mantenimiento

Entidad:

```text
MaintenanceRequest
```

Prioridades:

```text
low
medium
high
critical
```

Estados:

```text
reported
scheduled
in_progress
completed
cancelled
```

Si un mantenimiento impide utilizar el alojamiento:

```text
accommodation.status = maintenance
```

y crear o asociar un:

```text
BlockedPeriod
```

Cuando se complete mantenimiento, verificar:

- Limpieza
- Bloqueos
- Estado de otra orden

antes de pasar a:

```text
available
```

---

# 23. Inventario

Entidad:

```text
InventoryItem
```

Ejemplos:

```text
Toallas
Almohadas
Vasos
Platos
Control remoto
Televisor
Cafetera
```

Cada alojamiento posee su inventario esperado.

Las revisiones pueden ocurrir:

```text
check_in
check_out
cleaning
```

Entidad:

```text
InventoryCheck
```

Detalle:

```text
InventoryCheckItem
```

Registrar:

```text
expected_quantity
actual_quantity
missing_quantity
damaged_quantity
charge_amount
```

Los daños o faltantes pueden generar cargos adicionales a la reserva.

---

# 24. Gastos

Entidad:

```text
Expense
```

Categorías:

```text
utilities
maintenance
payroll
cleaning
supplies
advertising
transport
taxes
commissions
other
```

Un gasto puede pertenecer:

- Al negocio completo
- A un alojamiento específico

No mezclar gastos con pagos de huéspedes.

---

# 25. Ingresos

Los ingresos operativos deben derivarse principalmente de movimientos financieros confirmados.

No asumir que:

```text
reservation.total_amount
```

es ingreso recibido.

Ejemplo:

```text
Reserva = 2.000.000
Pagado = 500.000
```

Ingresos cobrados:

```text
500.000
```

Ventas contratadas:

```text
2.000.000
```

Ambas métricas deben diferenciarse.

---

# 26. Dashboard

El dashboard no debe implementar consultas financieras independientes si ya existen servicios de reportes.

Crear posteriormente:

```text
DashboardService
```

Indicadores principales:

- Alojamientos disponibles
- Alojamientos ocupados
- Check-ins de hoy
- Check-outs de hoy
- Reservas próximas
- Reservas pendientes
- Pagos pendientes
- Ingresos del período
- Gastos del período
- Utilidad
- Tasa de ocupación
- Limpiezas pendientes
- Mantenimientos pendientes

---

# 27. Tasa de ocupación

La ocupación debe calcularse sobre noches disponibles.

Ejemplo conceptual:

```text
occupied_nights
/
available_inventory_nights
* 100
```

No calcular simplemente:

```text
reservations / accommodations
```

---

# 28. Notificaciones

Entidad de historial:

```text
OutboundMessage
```

Canales:

```text
whatsapp
email
sms
```

Estados:

```text
pending
scheduled
sent
failed
cancelled
```

Eventos futuros:

```text
ReservationCreated
ReservationConfirmed
PaymentConfirmed
CheckInReminder
CheckOutReminder
CleaningAssigned
MaintenanceAssigned
```

Usar:

- Events
- Listeners
- Queues
- Jobs
- Notifications

cuando sea apropiado.

No enviar mensajes externos directamente desde controladores.

---

# 29. Automatizaciones iniciales

## Reserva confirmada

```text
ReservationConfirmed
        ↓
Enviar confirmación
        ↓
Programar recordatorio
```

## Próximo check-in

Ejemplo:

```text
24 horas antes
```

enviar:

- Dirección
- Ubicación
- Hora de entrada
- Recomendaciones
- Saldo pendiente
- Normas

## Check-out

```text
CheckOutCompleted
       ↓
CreateCleaningTask
```

---

# 30. Auditoría

Entidad:

```text
AuditLog
```

Registrar cambios importantes sobre:

- Reservas
- Pagos
- Gastos
- Tarifas
- Usuarios
- Roles
- Reembolsos
- Cancelaciones

Datos:

```text
user_id
business_id
action
model_type
model_id
old_values
new_values
ip_address
created_at
```

Especial atención a cambios financieros.

---

# 31. Transacciones

Utilizar transacciones de base de datos para operaciones compuestas.

Ejemplos:

## Convertir cotización

```text
BEGIN

Validar disponibilidad
Crear reserva
Crear huéspedes relacionados
Crear servicios
Guardar valores económicos
Actualizar cotización

COMMIT
```

Si falla:

```text
ROLLBACK
```

## Check-out

```text
BEGIN

Cerrar estadía
Cambiar estado reserva
Cambiar estado alojamiento
Crear tarea de limpieza
Registrar inventario inicial si aplica

COMMIT
```

---

# 32. Concurrencia

La aplicación debe prepararse para que dos usuarios intenten reservar simultáneamente el mismo alojamiento.

No confiar exclusivamente en:

```text
"Lo comprobé antes de mostrar el formulario"
```

Al confirmar una reserva:

1. Iniciar transacción.
2. Volver a verificar disponibilidad.
3. Bloquear los registros necesarios cuando corresponda.
4. Crear/confirmar la reserva.
5. Commit.

Investigar y aplicar la estrategia adecuada según el motor de base de datos.

---

# 33. Soft Deletes

Utilizar `softDeletes()` donde tenga sentido.

Especialmente considerar:

```text
accommodations
guests
reservations
payments
expenses
maintenance_requests
```

Sin embargo:

No utilizar eliminación como mecanismo para cancelar una reserva.

Una reserva cancelada debe conservarse:

```text
status = cancelled
```

---

# 34. Índices

Agregar índices en campos consultados frecuentemente.

Especialmente:

```text
business_id
accommodation_id
primary_guest_id
reservation_id
status
code
check_in_date
check_out_date
document_number
payment_date
scheduled_at
```

Índices compuestos recomendados según consultas:

```text
business_id + status
accommodation_id + check_in_date + check_out_date
business_id + document_number
reservation_id + status
```

Codex debe revisar el motor de base de datos antes de diseñar índices definitivos.

---

# 35. Fechas y zona horaria

Zona horaria inicial:

```text
America/Bogota
```

No asumir UTC al mostrar información al usuario.

La estrategia de almacenamiento debe seguir buenas prácticas Laravel y documentarse.

Diferenciar:

```text
date
datetime
timestamp
```

No almacenar horas como texto.

---

# 36. Seguridad

Aplicar:

- Validación mediante Form Requests
- Policies
- CSRF
- Autorización por negocio
- Protección contra mass assignment
- Validación de archivos
- Límites de tamaño
- Tipos MIME permitidos
- Sanitización donde corresponda

## Regla multiempresa

Un usuario nunca debe poder consultar registros de otro `business_id` mediante manipulación de URL.

Ejemplo:

```text
/reservations/100
```

debe verificar que la reserva pertenece al negocio activo del usuario.

---

# 37. Archivos y comprobantes

Archivos posibles:

- Fotografías de alojamientos
- Comprobantes de pago
- Facturas de gastos
- Evidencias de daños
- Fotografías de mantenimiento
- Inventarios

No almacenar binarios directamente en tablas salvo justificación.

Guardar:

```text
path
disk
mime_type
size
```

cuando corresponda.

---

# 38. Base de datos inicial

Entidades requeridas:

```text
Business
User
BusinessUser

Accommodation
Amenity
AccommodationAmenity
AccommodationImage
RatePeriod
BlockedPeriod

Guest

Quote

Reservation
ReservationGuest
ReservationService
ReservationStatusHistory

Payment

Service

Stay

CleaningTask

MaintenanceRequest

InventoryItem
InventoryCheck
InventoryCheckItem

ExpenseCategory
Expense

OutboundMessage

AuditLog
```

---

# 39. Orden recomendado de migraciones

```text
01 businesses
02 users
03 business_user

04 accommodations
05 amenities
06 accommodation_amenity
07 accommodation_images
08 rate_periods
09 blocked_periods

10 guests
11 services

12 quotes

13 reservations
14 reservation_guests
15 reservation_services
16 reservation_status_histories

17 payments

18 stays

19 cleaning_tasks
20 maintenance_requests

21 inventory_items
22 inventory_checks
23 inventory_check_items

24 expense_categories
25 expenses

26 outbound_messages

27 audit_logs
```

Adaptar el orden si las migraciones existentes lo requieren.

---

# 40. Reglas monetarias

Todos los campos monetarios:

```php
decimal(14, 2)
```

Nunca:

```php
float
```

Usar casts adecuados.

Evitar cálculos monetarios imprecisos.

Valores:

```text
subtotal
discount
tax
fee
deposit
total
payment
refund
expense
```

deben tratarse consistentemente.

---

# 41. Códigos únicos

Utilizar códigos legibles para entidades relevantes.

Ejemplos:

```text
Accommodation:
CAB-001

Quote:
QUO-2026-000001

Reservation:
RES-2026-000001

Payment:
PAY-2026-000001
```

Los códigos deben ser únicos dentro del alcance definido.

No utilizar:

```text
COUNT(*) + 1
```

como generador sin protección de concurrencia.

---

# 42. Lógica de cancelación

Cancelar una reserva debe:

1. Validar permiso.
2. Registrar motivo.
3. Registrar usuario.
4. Cambiar estado a `cancelled`.
5. Guardar `cancelled_at`.
6. Liberar disponibilidad.
7. Evaluar pagos existentes.
8. Evaluar política de reembolso.
9. Registrar historial.
10. Generar notificaciones si corresponde.

Nunca eliminar la reserva.

---

# 43. No-show

Si un huésped no llega:

```text
confirmed
    ↓
no_show
```

Registrar:

```text
timestamp
reason
user
```

Los pagos permanecen registrados.

La política financiera decidirá devolución o penalización.

---

# 44. Depósito de seguridad

El depósito debe diferenciarse del ingreso de hospedaje.

Ejemplo:

```text
Hospedaje: 1.500.000
Depósito:    300.000
```

El depósito puede:

```text
recibirse
retenerse parcialmente
devolverse
```

No tratar automáticamente el depósito como ingreso definitivo.

---

# 45. Historial financiero

No sobrescribir pagos anteriores.

Si se registra incorrectamente:

Preferir:

- Cancelar
- Revertir
- Crear movimiento compensatorio

según diseño contable.

La auditoría debe preservar el historial.

---

# 46. Integraciones futuras

Preparar la arquitectura para futuras integraciones, sin implementarlas todavía:

- WhatsApp Business API
- Email
- SMS
- Pasarelas de pago
- Booking
- Airbnb
- Google Calendar
- Facturación electrónica
- DIAN
- Reportes contables
- Aplicación móvil
- API pública

Evitar acoplar lógica de negocio directamente a proveedores externos.

Ejemplo:

```text
MessagingService
```

podría usar:

```text
WhatsAppProvider
EmailProvider
SmsProvider
```

---

# 47. Reportes futuros

Preparar datos para:

- Ocupación
- ADR
- RevPAR
- Ingresos
- Gastos
- Utilidad
- Reservas por canal
- Cancelaciones
- No-show
- Clientes recurrentes
- Servicios más vendidos
- Alojamientos más rentables
- Mantenimiento por alojamiento

No implementar todos inicialmente.

---

# 48. MVP

Primera versión funcional:

```text
Authentication
Business
Users / Roles

Accommodations
Guests

Availability
Rates

Quotes
Reservations

Payments

Check-in
Check-out

Cleaning

Expenses

Dashboard básico
```

Después:

```text
Maintenance
Inventory
Notifications
Advanced reports
WhatsApp
Integrations
```

---

# 49. Estrategia de desarrollo entre dos personas

No trabajar directamente sobre `main`.

Ramas:

```text
main
└── develop
    ├── feature/accommodations
    ├── feature/guests
    ├── feature/reservations
    ├── feature/availability
    ├── feature/payments
    ├── feature/checkin-checkout
    ├── feature/cleaning
    ├── feature/accounting
    └── feature/reports
```

Cada módulo debe tener un responsable principal.

Antes de modificar arquitectura compartida:

- Migraciones centrales
- Reservation
- Accommodation
- Guest
- Payment
- Business

coordinar el cambio.

---

# 50. Regla para Codex al trabajar en ramas

Antes de programar una funcionalidad:

```text
1. Leer este archivo.
2. Leer AGENTS.md.
3. Revisar git status.
4. Identificar rama activa.
5. Revisar código relacionado.
6. No modificar módulos no relacionados sin necesidad.
7. Informar cualquier conflicto arquitectónico.
```

---

# 51. Archivo AGENTS.md recomendado

Crear en la raíz:

```text
AGENTS.md
```

Contenido sugerido:

```text
# Aloja360 Agent Instructions

Before making changes:

1. Read docs/ALOJA360_BUSINESS_LOGIC.md.
2. Read docs/database-schema.md if it exists.
3. Inspect the existing implementation before creating new abstractions.
4. Do not duplicate business logic.
5. Do not create alternative entities for existing concepts.

Canonical entities:

- Business
- Accommodation
- Guest
- Quote
- Reservation
- Payment
- Service
- Stay
- CleaningTask
- MaintenanceRequest
- InventoryItem
- Expense

Do not introduce competing names such as:

- Cabin instead of Accommodation
- Customer instead of Guest
- Booking instead of Reservation

unless explicitly approved.

Database changes must update:

docs/database-schema.md

Business-rule changes must update:

docs/ALOJA360_BUSINESS_LOGIC.md

Do not run destructive database commands.

Never execute:

php artisan migrate:fresh
php artisan db:wipe

without explicit authorization.

Use Laravel conventions.

Controllers must remain thin.

Centralize business logic in Services or Actions.

Run relevant tests after changes.
```

---

# 52. Prompt operativo para Codex

Utiliza el siguiente prompt cuando Codex vaya a comenzar el desarrollo:

```text
You are working on Aloja360, a Laravel-based property management system.

Before making any changes:

1. Read AGENTS.md.
2. Read docs/ALOJA360_BUSINESS_LOGIC.md.
3. Read docs/database-schema.md if it exists.
4. Inspect the current Laravel project.
5. Inspect existing migrations, models, routes, controllers, services, tests and installed packages.
6. Check git status and current branch.

Do not implement anything until you understand the current architecture.

The canonical business entities are:

Business
User
Accommodation
Amenity
RatePeriod
BlockedPeriod
Guest
Quote
Reservation
Payment
Service
Stay
CleaningTask
MaintenanceRequest
InventoryItem
InventoryCheck
Expense
OutboundMessage
AuditLog

Important architectural rules:

- The system is multi-business using business_id.
- Use English names in code and database.
- The UI will be Spanish.
- Do not use database ENUM types.
- Use PHP Enums for statuses when appropriate.
- Monetary values must use decimal(14,2), never float.
- Controllers must remain thin.
- Business logic must live in Services or Actions.
- Authorization must use Policies where appropriate.
- Database operations involving multiple writes must use transactions.
- Never duplicate availability or pricing logic.

Availability must ultimately be centralized in:

App\Services\AvailabilityService

A reservation overlaps another reservation when:

new_check_in < existing_check_out
AND
new_check_out > existing_check_in

Availability must consider both:

reservations
blocked_periods

Reservation statuses:

pending
confirmed
checked_in
checked_out
cancelled
no_show

Accommodation statuses:

available
reserved
occupied
pending_cleaning
cleaning
maintenance
blocked

Payment statuses:

pending
confirmed
rejected
cancelled

Cleaning statuses:

pending
assigned
in_progress
completed
cancelled

Maintenance statuses:

reported
scheduled
in_progress
completed
cancelled

Important business flow:

Quote
→ Pending Reservation
→ Payment / Deposit
→ Confirmed
→ Check-in
→ Stay
→ Check-out
→ Pending Cleaning
→ Cleaning Completed
→ Available

A quotation does not block availability.

Before converting a quote to a reservation, availability must be checked again.

Check-in should normally only be allowed for confirmed reservations.

Check-out should normally only be allowed for checked-in reservations.

Check-out must create a cleaning task and change the accommodation to pending_cleaning.

Completing cleaning must only change an accommodation to available if no maintenance, block or other operational restriction exists.

Confirmed payments affect outstanding balance.

Do not use a stored balance as the primary financial truth if it can be calculated from reservation total and confirmed financial transactions.

Do not delete cancelled reservations.

Do not overwrite historical prices for services or reservations.

reservation_services must store unit_price and total.

Any important reservation status change must be stored in reservation_status_histories.

Any important financial modification must be auditable.

Do not execute:

php artisan migrate:fresh
php artisan db:wipe

or any destructive command without explicit authorization.

TASK:

First analyze the existing project and report:

1. Laravel version.
2. PHP version.
3. Database engine.
4. Authentication system.
5. Installed relevant packages.
6. Existing migrations.
7. Existing models.
8. Existing architecture.
9. Conflicts with the Aloja360 architecture.
10. Files you propose creating or modifying.

Then propose the implementation plan.

Only after the analysis, proceed with the requested module.

After implementation:

- Run relevant tests.
- Run lint/static checks available in the project.
- Report files changed.
- Report database changes.
- Report business rules implemented.
- Report pending work.
```

---

# 53. Primera tarea recomendada para Codex

Después de agregar este documento al proyecto, la primera tarea debe ser:

```text
Read AGENTS.md and docs/ALOJA360_BUSINESS_LOGIC.md.

Analyze the current Laravel project.

Do not create controllers or UI yet.

Create or complete the core database architecture for:

Business
Accommodation
Amenity
RatePeriod
BlockedPeriod
Guest
Service
Quote
Reservation
ReservationGuest
ReservationService
ReservationStatusHistory
Payment
Stay
CleaningTask
MaintenanceRequest
InventoryItem
InventoryCheck
InventoryCheckItem
ExpenseCategory
Expense
OutboundMessage
AuditLog

Create:

- migrations
- models
- Eloquent relationships
- appropriate casts
- PHP Enums for stable statuses
- factories where useful
- seeders for base development data
- database tests

Also create:

docs/database-schema.md

The document must include:

- tables
- columns
- foreign keys
- indexes
- relationships
- important constraints
- Mermaid ER diagram

Do not create UI.

Do not create final controllers yet.

Do not implement external integrations.

Do not run destructive database commands.

Before modifying files, show the proposed architecture and identify any conflict with existing code.
```

---

# 54. Reglas que NO deben romperse

Estas reglas son obligatorias durante todo el proyecto.

## Regla 1

Nunca permitir doble reserva del mismo alojamiento en fechas solapadas.

## Regla 2

Nunca confiar únicamente en validación de frontend.

## Regla 3

Nunca utilizar `float` para dinero.

## Regla 4

Nunca eliminar una reserva para representar cancelación.

## Regla 5

Nunca modificar precios históricos al cambiar una tarifa actual.

## Regla 6

Nunca permitir acceso a registros de otro negocio.

## Regla 7

Nunca duplicar la lógica de disponibilidad.

## Regla 8

Nunca duplicar la lógica de precios.

## Regla 9

Toda operación crítica con múltiples escrituras debe ser transaccional.

## Regla 10

Los cambios importantes deben ser auditables.

## Regla 11

Un check-out debe desencadenar el proceso operativo de limpieza.

## Regla 12

Una limpieza completada no implica automáticamente disponibilidad si existe mantenimiento o bloqueo.

## Regla 13

Los pagos y los valores contratados son conceptos diferentes.

## Regla 14

Los depósitos de seguridad no deben mezclarse automáticamente con ingresos.

## Regla 15

Codex debe leer la documentación de arquitectura antes de modificar módulos críticos.

---

# 55. Objetivo final

Aloja360 debe evolucionar hacia un PMS/ERP ligero para pequeños y medianos alojamientos.

Debe permitir que un propietario conozca en cualquier momento:

```text
¿Qué alojamientos tengo?

¿Cuáles están disponibles?

¿Quién llega hoy?

¿Quién sale hoy?

¿Cuánto me deben?

¿Cuánto he cobrado?

¿Cuánto he gastado?

¿Cuál alojamiento produce más?

¿Qué alojamiento necesita limpieza?

¿Qué mantenimiento está pendiente?

¿Qué reservas vienen próximamente?

¿Cuál es mi nivel de ocupación?

¿Cuál es mi utilidad?
```

La arquitectura debe priorizar:

```text
Integridad de datos
Consistencia
Seguridad
Escalabilidad
Trazabilidad
Mantenibilidad
Facilidad de uso
```

sobre soluciones rápidas que comprometan la arquitectura futura.
