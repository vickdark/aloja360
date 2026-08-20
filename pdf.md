# Plan de Implementación: Facturas en PDF de Cotizaciones y Reservas con Impresión y Envío por Correo

Este plan detalla la implementación completa para la generación, visualización/impresión y envío por correo electrónico de facturas y cotizaciones en formato PDF profesional para **Aloja360**.

---

## 1. Campos Adicionales en la Configuración de la Empresa
En [configuracion/index.blade.php](file:///c:/Users/victo/Herd/aloja360/resources/views/configuracion/index.blade.php) y [ConfiguracionController.php](file:///c:/Users/victo/Herd/aloja360/app/Http/Controllers/ConfiguracionController.php):
- **Campos a incorporar para las facturas/cotizaciones:**
  - `empresa_ciudad`: Ciudad / Municipio y Departamento.
  - `empresa_regimen`: Régimen tributario o resolución de facturación (opcional).
  - `empresa_banco_info`: Información y cuentas bancarias para pagos/transferencias (opcional, muy útil en cotizaciones/reservas).
  - `empresa_pie_pagina`: Frase o texto opcional de pie de página para documentos (ej: *"¡Gracias por preferirnos! Para garantizar su reserva recuerde realizar el anticipo estipulado."*).
- **Actualización de controlador:** Actualizar `ConfiguracionController@update` para usar `Configuracion::updateOrCreate` garantizando el guardado dinámico de nuevos campos.

---

## 2. Plantillas de Factura / Documento PDF (Estilo Profesional y Limpio)
Diseñaremos plantillas Blade optimizadas para renderizado en DomPDF:
- [resources/views/pdf/quote.blade.php](file:///c:/Users/victo/Herd/aloja360/resources/views/pdf/quote.blade.php):
  - Cabecera con logo de la empresa y datos fiscales/contacto (`setting('empresa_nombre')`, `setting('empresa_id_fiscal')`, `setting('empresa_telefono')`, etc.).
  - Información del cliente/huésped (Nombre, documento, teléfono, email).
  - Datos de la cotización: Código, fecha de emisión, fecha de vencimiento.
  - Modalidad: Pasadía (horarios y fecha) o Hospedaje (fechas check-in/out, noches).
  - Desglose de pax (adultos, niños) y forma de cobro (por persona o por alojamiento).
  - Tabla de liquidación financiera detallada (Alojamiento/Pasadía, servicios, limpieza, depósito, descuentos, impuestos y total).
  - Sección de cuentas bancarias y notas al cliente.
  - Frase de pie de página personalizada (`setting('empresa_pie_pagina')`).
- [resources/views/pdf/reservation.blade.php](file:///c:/Users/victo/Herd/aloja360/resources/views/pdf/reservation.blade.php):
  - Estructura de Factura / Comprobante de Reserva.
  - Estado actual de la reserva y código identificador.
  - Fechas, horarios de entrada y salida o pasadía.
  - Tabla detallada de conceptos facturados.
  - Historial de pagos confirmados recibidos, saldo pagado y saldo pendiente.
  - Notas y pie de página de la empresa.

---

## 3. Servicio de Generación de PDF y Controladores
- **Rutas PDF:**
  - `GET /quotes/{quote}/pdf` → `QuoteController@pdf` (descargar/visualizar en navegador para imprimir).
  - `GET /reservations/{reservation}/pdf` → `ReservationController@pdf` (descargar/visualizar en navegador para imprimir).
- Generación mediante `Barryvdh\DomPDF\Facade\Pdf::loadView(...)` en formato carta/A4 con márgenes y estilos CSS optimizados.

---

## 4. Sistema de Envío por Correo Electrónico con PDF Adjunto
- **Mailables:**
  - `App\Mail\QuoteInvoiceMail`: Envía la cotización por correo electrónico adjuntando el PDF generado en tiempo de ejecución.
  - `App\Mail\ReservationInvoiceMail`: Envía la factura/comprobante de reserva por correo electrónico con el PDF adjunto.
- **Rutas de Envío:**
  - `POST /quotes/{quote}/send-email` → `QuoteController@sendEmail`
  - `POST /reservations/{reservation}/send-email` → `ReservationController@sendEmail`
- **Modal Interactivo en la Vista ([quotes/show.blade.php](file:///c:/Users/victo/Herd/aloja360/resources/views/quotes/show.blade.php) y [reservations/show.blade.php](file:///c:/Users/victo/Herd/aloja360/resources/views/reservations/show.blade.php)):**
  - Botón *"Enviar por Correo"* que abre modal de confirmación.
  - Permite seleccionar enviar al **correo registrado del huésped** o **ingresar un correo alternativo/adicional válido**.
  - Permite incluir un mensaje o nota personalizada para el cliente en el cuerpo del correo.
  - Notificaciones flash de éxito o error al enviar.

---

## 5. Botones y Acciones en las Vistas de Cotización y Reserva
- Botón **"Imprimir / PDF"** (abre en nueva pestaña para guardar o imprimir directamente).
- Botón **"Enviar por Correo"** (despliega el modal de envío).

---

## Plan de Verificación
1. Guardar los nuevos datos en el panel de configuración de la empresa (logo, ID fiscal, dirección, teléfono, cuentas bancarias, pie de página).
2. Generar el PDF de una cotización (tanto para hospedaje como para pasadía) y verificar formato visual y cálculos.
3. Generar el PDF de una reserva y verificar estado, pagos registrados, saldo y desglose.
4. Probar el modal de envío por correo tanto al correo registrado del huésped como a un correo alternativo.
5. Ejecutar la suite de pruebas unitarias (`php artisan test`) para certificar que todo el flujo permanezca 100% verde.
