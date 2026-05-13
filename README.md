# AsistenciaEscolar

Sistema web para gestionar la asistencia y permisos del personal de **América Latina Online Academy**. Permite a los empleados registrar su entrada y salida diaria, solicitar permisos, y al administrador aprobar solicitudes, visualizar reportes y generar el reporte de vacaciones en Excel.

---

## Características

- **Empleados**
  - Registro de entrada y salida (Check-In / Check-Out)
  - Solicitud de permisos con fechas y descripción
- **Administrador**
  - Gestión de solicitudes (aprobar, rechazar, poner en pendiente)
  - Reporte de asistencias vs. permisos por empleado (gráfico de barras)
  - Generación de reporte de vacaciones descargable en Excel

---

## Tecnologías

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 7+ con MySQLi |
| Base de datos | MySQL |
| Frontend | HTML5, CSS3, JavaScript |
| UI | Bootstrap 5.0.2 |
| Gráficos | AmCharts 5 |
| Excel | PhpSpreadsheet (Composer) |
| Ajax | jQuery 3.6.0 |

---

## Estructura del proyecto

```
AsistenciaEscolar/
├── controladores/
│   ├── config.php            # Credenciales de BD (excluido de git)
│   ├── conexion.php          # Conexión MySQLi
│   ├── consultaVacaciones.php
│   ├── footer.php
│   ├── generarExcel.php
│   ├── header.php
│   ├── obtenerDatosGrafica.php
│   └── obtenerPermisos.php
├── CSS/
│   ├── accionesAdmin.css
│   ├── accionesUsuarios.css
│   ├── form.css
│   ├── indexStyle.css
│   └── permiso.css
├── JS/
│   ├── cargarGraficoAsistencia.js
│   ├── cargarTabla.js
│   ├── generarExcel.js
│   └── opcionesAdmin.js
├── Json/vendor/              # Dependencias Composer (excluido de git)
├── archivos/                 # Archivos Excel generados (excluido de git)
├── index.php                 # Login
├── solicitudPermiso.php
├── vistaAdmin.php
└── vistaUsuario.php
```

---

## Instalación

### Requisitos

- PHP 7.4+
- MySQL 5.7+ (o MariaDB equivalente)
- Composer
- Servidor web (Apache / Nginx)

### Pasos

1. **Clonar el repositorio**

   ```bash
   git clone https://github.com/G-Gamboa/AsistenciaEscolar.git
   cd AsistenciaEscolar
   ```

2. **Instalar dependencias PHP**

   ```bash
   cd Json
   composer install
   ```

3. **Crear la base de datos**

   Importa el esquema SQL en MySQL y asegúrate de que la base de datos se llame `AsistenciaPersonal`. Las tablas necesarias son:

   - `Usuarios` — credenciales de acceso
   - `Empleado` — datos del personal
   - `RegistroAsistencia` — entradas y salidas
   - `Permiso` — solicitudes de permiso
   - `TiposPermiso` — catálogo de tipos de permiso
   - `EstadosPermiso` — catálogo de estados (1 Pendiente, 2 Aprobado, 3 Rechazado)

4. **Configurar las credenciales**

   Copia el archivo de ejemplo y edítalo con tus datos:

   ```bash
   cp controladores/config.example.php controladores/config.php
   ```

   ```php
   // controladores/config.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   define('DB_NAME', 'AsistenciaPersonal');
   ```

   > `config.php` está en `.gitignore` y nunca debe subirse al repositorio.

5. **Crear el directorio de archivos generados**

   ```bash
   mkdir archivos
   chmod 755 archivos
   ```

6. **Hash de contraseñas**

   Las contraseñas en la tabla `Usuarios` deben estar almacenadas con `password_hash()` de PHP (bcrypt). Para migrar contraseñas existentes en texto plano:

   ```php
   $hash = password_hash('contraseña_actual', PASSWORD_DEFAULT);
   // UPDATE Usuarios SET contrasena = '$hash' WHERE nombre_usuario = '...';
   ```

---

## Flujo de uso

```
Login (index.php)
├── Empleado regular  →  vistaUsuario.php
│                           ├── Check-In / Check-Out
│                           └── solicitudPermiso.php
└── Administrador     →  vistaAdmin.php
                            ├── Gestionar permisos
                            ├── Reporte de asistencias (gráfico)
                            └── Generar vacaciones (Excel)
```

---

## Seguridad

- Las credenciales de BD se separan en `config.php`, excluido de git mediante `.gitignore`.
- Las contraseñas se validan con `password_verify()` (bcrypt).
- Todas las consultas usan *prepared statements* con MySQLi.
- Las salidas HTML están escapadas con `htmlspecialchars()`.
- Los datos de respuestas JSON se escapan con la función `escapeHtml()` en el frontend antes de insertarse en el DOM.

---

## Licencia

Uso interno / educativo — América Latina Online Academy.
