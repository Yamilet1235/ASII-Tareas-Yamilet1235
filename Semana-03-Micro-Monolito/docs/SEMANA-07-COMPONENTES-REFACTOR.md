# Semana 7 — Diseño de componentes y refactorización

**Estudiante:** María Yamilet Lindo Pablo  
**Carné:** 1890-23-14827  
**Módulo:** Catálogo de medicamentos  
**Asignatura:** Análisis de Sistemas II  
**Rama:** `feature/semana-7-componentes-refactor`

## 1. Objetivo

Diseñar y documentar los componentes que participan en el flujo de alta, búsqueda y control de vigencia de medicamentos, y realizar un refactor pequeño que reduzca el acoplamiento de la presentación web con la estructura de la petición HTTP.

## 2. Alcance

El alcance se mantiene dentro de Catálogo de medicamentos:

- Registrar medicamentos ficticios.
- Buscar por nombre comercial o genérico.
- Consultar un medicamento por identificador mediante la API.
- Activar o desactivar un medicamento.
- Conservar la unicidad del nombre del catálogo.
- Mantener la interfaz web y la API REST de la Semana 5.

No se agregaron recursos clínicos, frameworks, paquetes, rutas REST ni servicios externos.

## 3. Arquitectura conservada

Se conservan las cuatro capas existentes:

| Capa | Responsabilidad |
|---|---|
| Presentation | Recibir HTTP, coordinar la interacción y producir HTML o JSON. |
| Application | Ejecutar los casos de uso y traducir fallos de persistencia a errores de aplicación. |
| Domain | Validar el medicamento, representar su estado y definir el contrato del repositorio. |
| Persistence | Implementar el repositorio con PDO/SQLite o memoria y ejecutar las operaciones de datos. |

La dirección principal continúa siendo `Presentation -> Application -> Domain`. Persistence implementa `MedicationRepository`, definido en Domain. No existe acceso directo desde los controladores a SQLite.

## 4. Componentes frontend

| Componente | Función actual |
|---|---|
| `public/index.php` | Punto de entrada web y raíz de composición. Construye dependencias, crea `MedicationWebRequest` y entrega el resultado a la vista. |
| `src/Presentation/views/catalog.php` | Renderiza alta, búsqueda, resultados, estado de vigencia y mensajes. |
| `public/assets/app.js` | Mejora progresivamente búsquedas, altas y cambios de estado con `fetch`, manteniendo formularios funcionales. |
| `public/assets/styles.css` | Define presentación visual adaptable para escritorio y móvil. |
| `MedicationWebRequest` | Extrae y normaliza los datos escalares de la petición web. |
| `MedicationController` | Coordina las acciones web con los casos de uso y prepara los datos de la vista. |
| `MedicationApiController` | Adapta los casos de uso al contrato JSON de la API REST. |
| `public/api.php` | Resuelve rutas REST, decodifica JSON y emite la respuesta HTTP. |
| `public/router.php` | Envía `/api/*` a la API, archivos estáticos al servidor y las demás solicitudes a la entrada web. |

## 5. Componentes backend

### Application

| Componente | Responsabilidad |
|---|---|
| `RegisterMedication` | Construir el medicamento mediante Domain, comprobar unicidad y solicitar su persistencia. |
| `SearchMedications` | Normalizar el término externo con `trim` y solicitar la búsqueda al repositorio. |
| `GetMedication` | Consultar un medicamento por identificador. |
| `ToggleMedicationStatus` | Alternar `ACTIVE`/`INACTIVE` para el flujo web. |
| `ChangeMedicationStatus` | Aplicar el estado explícito recibido por la API. |

### Domain

| Componente | Responsabilidad |
|---|---|
| `Medication` | Entidad con campos obligatorios, estado, vigencia y condición seleccionable. |
| `MedicationStatus` | Enumera `ACTIVE` e `INACTIVE`, valida entradas y proporciona la etiqueta visual. |
| `MedicationNameNormalizer` | Normaliza el nombre para comparaciones sin diferencia de mayúsculas. |
| `MedicationRepository` | Define las operaciones de alta, consulta, búsqueda y cambio de estado. |

### Persistence

| Componente | Responsabilidad |
|---|---|
| `PdoMedicationRepository` | Ejecutar SQL parametrizado, hidratar entidades y proteger la unicidad en SQLite. |
| `InMemoryMedicationRepository` | Implementar el mismo contrato en memoria para pruebas. |
| `DatabaseConnection` | Crear la conexión PDO configurada para SQLite. |

## 6. Responsabilidades por componente

El flujo web queda distribuido de esta forma:

1. `public/index.php` recibe las variables globales y construye los objetos.
2. `MedicationWebRequest` convierte la petición HTTP en valores simples y seguros para Presentation.
3. `MedicationController` selecciona la acción web, invoca Application y prepara mensajes y datos de vista.
4. Los casos de uso aplican el flujo de aplicación mediante `MedicationRepository`.
5. Domain valida datos, estado y vigencia.
6. Persistence resuelve las operaciones de almacenamiento.
7. `catalog.php` presenta el resultado; JavaScript agrega interacción asíncrona sin sustituir el flujo HTML.

La API sigue un adaptador independiente: `public/api.php -> MedicationApiController -> Application`.

## 7. Punto de mayor acoplamiento identificado

El punto real de mayor acoplamiento estaba en `MedicationController::handle()`. El método recibía tres arrays HTTP, definía una función local para convertir valores, conocía claves de `$_SERVER`, `$_GET` y `$_POST`, armaba los valores del formulario, seleccionaba acciones, invocaba casos de uso y preparaba la respuesta.

Las reglas de negocio no estaban en el controlador: ya se encontraban correctamente en Application y Domain. Por ello no fue necesario mover unicidad, validación, vigencia ni SQL.

## 8. Diseño ANTES del refactor

```text
HTTP / formularios
       |
       v
MedicationController(array $server, array $query, array $form)
       |-- interpreta REQUEST_METHOD
       |-- lee action, q, id y campos del formulario
       |-- convierte valores HTTP a string/int
       |-- asigna el estado visual por defecto
       |-- decide la acción
       |-- invoca casos de uso
       `-- prepara mensajes y datos para catalog.php
```

El controlador estaba acoplado a la forma exacta de los arrays de entrada web y repetía acceso a las mismas claves.

## 9. Refactor realizado

Se integró el archivo existente `MedicationWebRequest.php` como objeto de entrada de Presentation. Encapsula:

- Método HTTP, normalizado y con `GET` por defecto.
- Acción web.
- Término `q`, sin espacios externos.
- Identificador convertido a entero.
- Campos `name`, `generic_name`, `presentation`, `concentration` y `status`.
- Estado `ACTIVE` por defecto cuando el formulario no lo envía.
- Protección ante valores HTTP no escalares.

`public/index.php` construye el objeto con `$_SERVER`, `$_GET` y `$_POST`. `MedicationController::handle()` recibe ahora `MedicationWebRequest`, no arrays HTTP.

## 10. Diseño DESPUÉS del refactor

```text
HTTP / formularios
       |
       v
public/index.php
       |
       v
MedicationWebRequest
       |
       v
MedicationController
       |
       v
Application Use Cases
       |
       v
Domain + MedicationRepository
       |
       v
Persistence -> SQLite
```

Después del cambio, `MedicationWebRequest` conoce la estructura de entrada web. `MedicationController` conserva las decisiones propias de presentación y la coordinación, pero ya no implementa lectura o conversión de arrays HTTP. No se introdujo SQL ni lógica de dominio en Presentation.

## 11. Contratos de entrada/salida

### A. Registrar medicamento

**Web:** `POST /`

| Entrada web | Tipo | Regla de interfaz |
|---|---|---|
| `action` | string | Debe ser `register`. |
| `name` | string | Obligatorio. |
| `generic_name` | string | Obligatorio; nombre `snake_case` del formulario. |
| `presentation` | string | Obligatorio. |
| `concentration` | string | Obligatorio. |
| `status` | string | `ACTIVE` o `INACTIVE`; `MedicationWebRequest` usa `ACTIVE` si falta o está vacío. |

Salida web: el caso de uso devuelve un `Medication` con `id`; el controlador guarda un mensaje de éxito y responde mediante redirección `303` al catálogo. Si hay error, vuelve a renderizar el formulario con mensajes y valores ingresados.

**API:** `POST /api/v1/medications`

La API usa `genericName` en JSON, no `generic_name`. En éxito responde `201` con `data.id`, `data.name`, `data.genericName`, `data.presentation`, `data.concentration` y `data.status`.

Domain elimina espacios externos de los campos y `MedicationStatus` normaliza el estado a mayúsculas. Para unicidad, Persistence compara el nombre mediante `MedicationNameNormalizer` y SQLite conserva una restricción única.

Errores verificables:

| Condición | Web | API |
|---|---|---|
| Campo obligatorio o estado inválido | Mensaje de validación en la vista. | `422 VALIDATION_ERROR`. |
| Nombre duplicado | Mensaje de duplicado en la vista. | `409 DUPLICATE_MEDICATION`. |
| Fallo de persistencia | Mensaje de aplicación en la vista. | `500 INTERNAL_ERROR`. |

### B. Buscar medicamentos

**Web:** `GET /?q={término}`. `MedicationWebRequest` recorta espacios de `q`.  
**API:** `GET /api/v1/medications?search={término}`. `public/api.php` lee `search`.

Entrada: término opcional; vacío devuelve el catálogo.  
Salida web: lista de objetos `Medication` entregada a `catalog.php`.  
Salida API: `200` con `data`, una colección de medicamentos serializados.  
Error de persistencia: mensaje web o `500 INTERNAL_ERROR` en API.

### C. Consultar medicamento por ID

**API:** `GET /api/v1/medications/{id}`.

Entrada: identificador entero positivo representado en la ruta.  
Salida: `200` con el medicamento en `data`.  
Inexistente: `404 MEDICATION_NOT_FOUND`.  
Fallo de persistencia: `500 INTERNAL_ERROR`.

Este detalle individual no tiene una ruta web equivalente; el flujo web usa el `id` únicamente para alternar estado.

### D. Cambiar vigencia

**Web:** `POST /` con `action=toggle` e `id`. `ToggleMedicationStatus` calcula el estado opuesto. La salida es una redirección `303` y un mensaje con la nueva etiqueta.

**API:** `PATCH /api/v1/medications/{id}/status` con JSON:

```json
{
  "status": "INACTIVE"
}
```

Entrada API: `id` y estado explícito `ACTIVE` o `INACTIVE`.  
Salida API: `200` con `data.id`, `data.status`, `data.current` y `data.selectable`.  
Estado inválido: `422 VALIDATION_ERROR`.  
Medicamento inexistente: `404 MEDICATION_NOT_FOUND`.  
Fallo de persistencia: `500 INTERNAL_ERROR`.

## 12. Justificación arquitectónica

La extracción corresponde a una responsabilidad de frontera: interpretar la forma de una petición web. Mantenerla en Presentation evita contaminar Application con conceptos como `REQUEST_METHOD`, `q`, `generic_name` o arrays globales.

El cambio reduce el conocimiento que necesita `MedicationController`, facilita probar la interpretación HTTP sin construir repositorios ni casos de uso y conserva las reglas donde ya estaban. No se creó una abstracción adicional para API porque su contrato JSON y enrutamiento son distintos y el alcance solicitado se satisface con una extracción pequeña.

## 13. Impacto del refactor

Impacto concreto:

- `MedicationController::handle()` recibe un objeto en lugar de tres arrays.
- La conversión defensiva de valores HTTP tiene un único lugar.
- El estado inicial `ACTIVE` se entrega de forma consistente al registro web y a la vista.
- La interfaz web, la API REST y sus rutas permanecen disponibles.
- Application, Domain, Repository y SQL no fueron modificados.

El refactor no elimina todas las responsabilidades de Presentation: el controlador todavía selecciona acciones, genera mensajes y prepara la vista, porque son responsabilidades de coordinación y presentación del flujo actual.

## 14. Verificación

La evidencia se obtiene con:

```powershell
php Semana-03-Micro-Monolito/tests/run.php
Get-ChildItem Semana-03-Micro-Monolito -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
git diff --check
git status --short
```

Las pruebas nuevas ejercitan `MedicationWebRequest` sin depender de SQLite:

- Normalización de método y acción, y conversión de identificador.
- Normalización del término de búsqueda.
- Extracción de los campos del formulario y estado `ACTIVE` por defecto.

## 15. Resultado de pruebas

Resultado final de `tests/run.php`: **9 aprobadas, 0 fallidas**.

Las seis pruebas existentes continúan aprobando y se agregaron tres pruebas de Presentation para demostrar el componente extraído.

## 16. Limitaciones

- No se incorporó un framework ni un contenedor de dependencias.
- La API conserva su adaptación HTTP en `public/api.php`; no fue parte del refactor concreto.
- El flujo web alterna el estado, mientras la API recibe el estado objetivo explícito; ambos contratos se mantienen por compatibilidad.
- El diagrama fuente se entrega en PlantUML. No se generó PNG local porque no hay ejecutable ni archivo JAR de PlantUML disponible en el worktree.

## 17. Conclusión

El diseño mantiene la arquitectura por capas y documenta los componentes reales del flujo. La extracción de `MedicationWebRequest` reduce el acoplamiento de `MedicationController` con HTTP sin trasladar reglas de medicamento, vigencia, búsqueda, unicidad o persistencia fuera de sus capas actuales. El comportamiento previo queda respaldado por las seis pruebas existentes y el refactor por tres pruebas adicionales.
