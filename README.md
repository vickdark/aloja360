# 🚀 Laravel 12 Base Boilerplate - Starter Kit Profesional

Este proyecto es una **base sólida y escalable** diseñada para servir como punto de partida para cualquier aplicación web. El objetivo principal es eliminar el trabajo repetitivo de configurar sistemas de autenticación, roles y permisos, permitiendo pasar de la idea al producto funcional en tiempo récord.

Lo que antes tomaba días de configuración manual, aquí se ha centralizado y optimizado para estar listo en menos de 12 horas, proporcionando una estructura profesional desde el primer minuto.

---

## 💎 Características Principales

*   **Gestión de Usuarios Completa:** Registro, perfil y control de acceso.
*   **Sistema de Roles y Permisos:** Control granular de qué puede hacer cada usuario en la plataforma.
*   **UI/UX Premium:** Sidebar colapsable, diseño responsivo y componentes modernos ya integrados.
*   **Arquitectura Escalable:** Preparado para crecer hacia cualquier tipo de producto (SaaS, ERP, CRM, etc.).

---

## 🏗️ Arquitectura MVC (Modelo-Vista-Controlador)

El proyecto utiliza el patrón de diseño **MVC**, que separa la lógica de negocio de la interfaz de usuario:

1.  **Modelo (Model):** Ubicados en `app/Models/`. Representan los datos y las reglas de la base de datos (ej. Usuario, Rol, Venta). Usamos **Eloquent ORM** para interactuar con la DB de forma sencilla.
2.  **Vista (View):** Ubicadas en [resources/views](file:///c:/Users/victo/Herd/ventas/resources/views). Usamos el motor de plantillas **Blade**. Es lo que el usuario final ve en su navegador.
3.  **Controlador (Controller):** Ubicados en `app/Http/Controllers/`. Son el "cerebro" que recibe las peticiones del usuario, consulta al Modelo y decide qué Vista mostrar.

---

## 🛠️ Stack Tecnológico (Tecnologías Frontend)

Para ofrecer una experiencia de usuario moderna y fluida, utilizamos las siguientes tecnologías:

*   **Core:** [Bootstrap 5.3](https://getbootstrap.com/) para el diseño responsivo y [jQuery 4.0](https://jquery.com/).
*   **Interactividad:** [Alpine.js](https://alpinejs.dev/) para componentes dinámicos ligeros.
*   **Iconografía:** [FontAwesome 7.1](https://fontawesome.com/) para todos los iconos del sistema.
*   **Gráficos:** [Chart.js](https://www.chartjs.org/) para reportes y estadísticas visuales.
*   **Tablas de Datos:** [Grid.js](https://gridjs.io/) para tablas avanzadas con búsqueda y filtrado.
*   **Selectores:** [Tom-Select](https://tom-select.js.org/) para menús desplegables inteligentes.
*   **Alertas:** [SweetAlert2](https://sweetalert2.github.io/) para notificaciones y diálogos estéticos.
*   **Reportes:** [jsPDF](https://parall.ax/products/jspdf) y [SheetJS (XLSX)](https://sheetjs.com/) para exportación de documentos.
*   **Build Tool:** [Vite](https://vitejs.dev/) para la compilación y optimización de assets en tiempo real.

---

## 📁 Estructura del Proyecto (Puntos Clave)

*   **[routes/](file:///c:/Users/victo/Herd/ventas/routes)**: Define las URLs del sistema ([web.php](file:///c:/Users/victo/Herd/ventas/routes/web.php) y [auth.php](file:///c:/Users/victo/Herd/ventas/routes/auth.php)).
*   **[resources/](file:///c:/Users/victo/Herd/ventas/resources)**: Contiene el código fuente del frontend (Vistas Blade, CSS y JavaScript).
*   **[public/](file:///c:/Users/victo/Herd/ventas/public)**: Única carpeta accesible desde la web. Contiene el punto de entrada `index.php` y los archivos compilados por Vite.
*   **[bootstrap/](file:///c:/Users/victo/Herd/ventas/bootstrap)**: Se encarga del arranque inicial del motor de la aplicación.
*   **[app/](file:///c:/Users/victo/Herd/ventas/app)**: Contiene la lógica central (Controladores, Modelos, Middlewares).

---

## ⚙️ Instalación y Personalización

Si vas a usar este boilerplate para un nuevo proyecto, sigue estos pasos para renombrarlo correctamente:

### 1. Clonar e Instalar
1.  **Clonar el repositorio** en una nueva carpeta.
2.  **Instalar dependencias PHP:** `composer install`
3.  **Instalar dependencias JS:** `npm install`

### 2. Configuración de Identidad (Importante)
Para que el proyecto tenga el nombre de tu nuevo producto, debes cambiarlo en estos archivos:

*   **Archivo `.env`**: 
    *   `APP_NAME`: Cambia "Aloja360" por el nombre de tu proyecto.
    *   `APP_URL`: Actualiza la URL (ej. `http://mi-nuevo-proyecto.test`).
    *   `DB_DATABASE`: Define el nombre de tu nueva base de datos.
*   **Archivo `composer.json`**:
    *   Cambia la propiedad `"name": "laravel/aloja360"` por el nombre de tu organización/proyecto.

### 3. Puesta en Marcha
1.  **Generar clave de aplicación:** `php artisan key:generate`
2.  **Migrar base de datos:** `php artisan migrate --seed`
3.  **Compilar assets:** `npm run dev` o `npm run build`
