# Centro de Incidencias (PHP + MySQL, arquitectura MVC)

Base de conocimiento de incidencias con solución paso a paso (texto + imágenes WebP).

## Instalación
1. Copia la carpeta `incidencias/` en tu servidor (XAMPP, Laragon o hosting con **PHP 8.1+**).
2. Edita los datos de MySQL en `config/config.php`.
3. Abre `http://localhost/incidencias/` — las tablas se crean solas.
4. Ingresa con **admin / admin123** y cambia la clave en *Usuarios*.

Requisitos PHP: `pdo_mysql`, `mbstring`, `gd` con soporte WebP (y `exif`, opcional).
La carpeta `uploads/` debe tener permiso de escritura.

## Estructura
```
index.php            Front controller (único punto de entrada)
config/config.php    Conexión, uploads, paginación
app/
  Core/              Núcleo: App, Router, Controller, View, Model, Database, Auth, Session, Csrf
  Controllers/       Reciben la petición y devuelven una vista (delgados)
  Models/            Acceso a datos (consultas preparadas PDO)
  Services/          Reglas de negocio: IncidenciaService, UsuarioService, AjusteService, ImageService, SchemaInstaller
  Views/             Plantillas PHP (layout, incidencias, usuarios, historial, ajustes…)
  routes.php         Tabla de rutas: método, controlador, acción y nivel de acceso
  helpers.php        Funciones auxiliares de las vistas
assets/css, assets/js  Estilos y scripts (ui, live, form-incidencia, usuarios, ajustes)
uploads/             Imágenes subidas (WebP), logo y favicon
```

## Roles
- **Visitante**: consulta incidencias.
- **Técnico**: consulta, crea y edita/desactiva solo las suyas.
- **Administrador**: edita todas; gestiona catálogos, usuarios, historial y configuración.

## Agregar una pantalla nueva
1. Crea el modelo en `app/Models` (si necesita datos) y el controlador en `app/Controllers`.
2. Registra la ruta en `app/routes.php`.
3. Crea la vista en `app/Views/<modulo>/`.
