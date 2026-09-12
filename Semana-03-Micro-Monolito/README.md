# Micro-HIS Catálogo de Medicamentos

Micro-monolito educativo en PHP vanilla para registrar, buscar y controlar la vigencia de medicamentos ficticios. El módulo oficial y su alcance son **Catálogo de medicamentos**.

**Estudiante:** María Yamilet Lindo Pablo  
**GitHub:** [Yamilet1235](https://github.com/Yamilet1235)

> Este proyecto no contiene datos clínicos reales ni información identificable.

## Requisitos

- PHP 8.2 o superior.
- Extensiones PHP `pdo` y `pdo_sqlite` habilitadas.
- Un navegador web.
- No se requiere Composer ni un framework.

Puede comprobar el entorno con:

```bash
php --version
php -m
```

## Estructura

```text
Semana-03-Micro-Monolito/
|-- config/                 Configuración externa de SQLite
|-- database/               Esquema, semilla y base local ignorada
|-- docs/                   Informe, guía y diagramas PlantUML
|-- public/                 Punto de entrada y estilos web
|-- scripts/                Inicialización de la base de datos
|-- src/
|   |-- Application/        Casos de uso y errores de aplicación
|   |-- Domain/             Entidad, estados, reglas y puerto
|   |-- Persistence/        Conexión y repositorio PDO
|   `-- Presentation/       Controlador y vista
|-- tests/                  Pruebas automáticas sin framework
|-- DECLARACION_IA.md
`-- README.md
```

## Configuración

La ruta de SQLite está fuera del código fuente de la aplicación. En una copia nueva del repositorio, cree la configuración local a partir del ejemplo.

PowerShell:

```powershell
Copy-Item config/config.example.php config/config.php
```

Linux o macOS:

```bash
cp config/config.example.php config/config.php
```

El archivo `config/config.php` y las bases `*.sqlite`/`*.db` están ignorados por Git. Para cambiar la ubicación de la base, edite únicamente la clave `database.path` de la configuración local.

## Inicializar SQLite

Desde la raíz de esta carpeta ejecute:

```bash
php scripts/init_database.php
```

El script crea la tabla `medications` y agrega tres medicamentos completamente ficticios. Es seguro ejecutarlo otra vez: la tabla se crea con `IF NOT EXISTS` y la semilla usa `INSERT OR IGNORE`.

Salida esperada inicial:

```text
Base de datos inicializada correctamente.
Medicamentos disponibles: 3
```

## Ejecutar la aplicación

```bash
php -S localhost:8000 -t public
```

Abra [http://localhost:8000](http://localhost:8000). Para detener el servidor presione `Ctrl+C` en la terminal.

La interfaz permite:

- Registrar nombre, nombre genérico, presentación, concentración y estado.
- Buscar por nombre comercial o genérico.
- Ver el catálogo completo.
- Activar o desactivar un medicamento.
- Distinguir si está vigente y es seleccionable.
- Recibir mensajes comprensibles de éxito y error.

## Ejecutar las pruebas

```bash
php tests/run.php
```

El ejecutor informa cada prueba aprobada o fallida y devuelve código `1` cuando hay fallos. Cubre registro válido, campos vacíos, duplicados sin distinguir mayúsculas, vigencia, búsquedas, transformación de errores y una integración con PDO SQLite real en memoria.

## Arquitectura

El sistema es un único despliegue con límites internos claros:

- **Presentation:** recibe HTTP, valida la acción solicitada y genera la vista HTML.
- **Application:** coordina los casos de uso de alta, búsqueda y cambio de estado.
- **Domain:** contiene `Medication`, `MedicationStatus`, las reglas principales y el puerto `MedicationRepository`.
- **Persistence:** implementa el puerto con PDO y SQLite; traduce errores técnicos a excepciones entendibles.

Las dependencias apuntan hacia el dominio. En particular, Application y Persistence dependen de la interfaz definida en Domain; el dominio no conoce PDO ni la web.

## Persistencia y seguridad

- Todas las consultas, inserciones, actualizaciones, el esquema y la semilla se ejecutan mediante `PDO::prepare()` y `execute()`.
- La columna interna `normalized_name` y su restricción `UNIQUE` evitan duplicados sin distinguir mayúsculas, incluidas las letras acentuadas habituales en español.
- La restricción `CHECK` limita el estado a `ACTIVE` o `INACTIVE`.
- La vista escapa toda salida variable con `htmlspecialchars`.
- Los datos de ejemplo son ficticios y no representan prescripciones ni pacientes.

## Datos ficticios incluidos

- Alivion.
- Respiralux.
- Dermacalm X.

Los nombres genéricos también se identifican explícitamente como compuestos ficticios.

## Errores comunes

### Falta `config/config.php`

Copie `config/config.example.php` como se explica en Configuración. No quite ese archivo de `.gitignore`.

### `could not find driver`

Habilite `pdo_sqlite` en el `php.ini` usado por la terminal y reinicie el servidor. Confirme con `php -m`.

### `no such table: medications`

Ejecute `php scripts/init_database.php` antes de abrir la aplicación.

### No se puede abrir la base de datos

Revise que la ruta de `database.path` exista, que apunte dentro del proyecto y que PHP tenga permiso de escritura en `database/`.

### El puerto 8000 está ocupado

Use otro puerto, por ejemplo:

```bash
php -S localhost:8080 -t public
```

### Los estilos no cargan

Inicie el servidor desde la raíz con `-t public`. No abra `public/index.php` directamente como archivo local.
