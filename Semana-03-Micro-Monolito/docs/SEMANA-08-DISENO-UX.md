# Semana 8 — Diseño de experiencia de usuario

- **Estudiante:** María Yamilet Lindo Pablo
- **Carné:** 1890-23-14827
- **Módulo:** Catálogo de medicamentos
- **Asignatura:** Análisis de Sistemas II
- **Rama:** `feature/semana-8-diseno-ux`

## 1. Objetivo

Diseñar la experiencia de usuario para el alta, la búsqueda y el control de vigencia de medicamentos, de forma que cada rol comprenda qué puede hacer, qué información necesita proporcionar y cómo recuperarse de una validación o un fallo operativo.

La propuesta conserva el alcance del Micro-HIS académico. Es una evidencia de diseño de baja fidelidad y no sustituye la interfaz funcional existente.

## 2. Alcance

La Semana 8 cubre:

- Entrada al Catálogo de medicamentos.
- Consulta de nombre comercial, nombre genérico, presentación, concentración y vigencia.
- Búsqueda parcial por nombre comercial o genérico.
- Alta por el rol autorizado.
- Validaciones de campos, estado y unicidad.
- Confirmación antes de activar o desactivar.
- Retroalimentación de carga, vacío, sin resultados, éxito y error recuperable.
- Ayuda contextual y reglas de interacción.
- Protección de datos mediante contenido exclusivo de catálogo y ejemplos ficticios.

Quedan fuera del alcance la edición, eliminación, inventario, lotes, fechas de vencimiento, precios, recetas, pacientes, diagnósticos, datos clínicos identificables y cualquier reconstrucción del backend.

## 3. Roles autorizados

La documentación de la Semana 1 identifica tres actores humanos para este módulo:

- **Administrador:** administra el catálogo; puede ver, buscar, registrar y activar o desactivar medicamentos.
- **Médico:** consulta y busca medicamentos activos, y visualiza presentación y concentración. La selección para una receta fue descrita históricamente, pero no forma parte de esta evidencia UX.
- **Enfermera:** consulta medicamentos activos y visualiza presentación y concentración.

La Semana 2 confirma que el registro y el cambio de vigencia son operaciones administrativas. Por ello no se inventan permisos adicionales para Médico o Enfermera.

La aplicación local actual no implementa autenticación ni autorización. Esta propuesta documenta la experiencia por rol definida en el análisis previo; no afirma que la restricción ya esté aplicada en el código.

## 4. Matriz rol -> acción

| Acción | Administrador | Médico | Enfermera |
|---|---|---|---|
| Ver catálogo | Sí | Sí | Sí |
| Buscar por nombre comercial o genérico | Sí | Sí | Sí |
| Consultar presentación y concentración | Sí | Sí | Sí |
| Registrar medicamento | Sí | No | No |
| Activar o desactivar | Sí | No | No |
| Ver controles administrativos | Sí | No | No |

Para Médico y Enfermera, los controles de registro y cambio de vigencia no se presentan como deshabilitados: se omiten porque no son acciones disponibles para esos roles.

## 5. Principios UX aplicados

- **Visibilidad del estado:** la interfaz comunica carga, cantidad de resultados, vigencia y resultado de cada operación.
- **Prevención de errores:** los campos obligatorios, las ayudas y la confirmación de vigencia aparecen antes de completar una acción sensible.
- **Recuperación:** un error conserva los datos válidos y explica qué debe corregirse.
- **Consistencia:** se emplean los términos Medicamento, Presentación, Concentración, `ACTIVE/ACTIVO` e `INACTIVE/INACTIVO` ya usados en el módulo.
- **Control del usuario:** la búsqueda puede limpiarse y el cambio de vigencia puede cancelarse sin modificar datos.
- **Reconocimiento sobre recuerdo:** las ayudas breves explican cada dato junto a su campo.
- **Permisos visibles:** la interfaz solo ofrece acciones que el rol puede ejecutar.
- **Información mínima:** las pantallas muestran datos de catálogo, no datos personales ni clínicos.

## 6. User flow por rol

### Administrador

```text
Inicio
  -> Abrir Catálogo de medicamentos
  -> Mostrar "Cargando medicamentos..."
  -> ¿Existen medicamentos?
     -> No: mostrar estado vacío y "Registrar primer medicamento"
     -> Sí: mostrar catálogo, búsqueda, registrar y control de vigencia
  -> Elegir una tarea
     -> Buscar: ingresar término -> mostrar coincidencias o sin resultados
     -> Registrar: abrir formulario -> validar -> comprobar duplicado -> éxito o corrección
     -> Cambiar vigencia: elegir acción -> confirmar o cancelar -> mostrar resultado
```

### Médico

```text
Inicio
  -> Abrir Catálogo de medicamentos
  -> Mostrar "Cargando medicamentos..."
  -> Consultar medicamentos disponibles
  -> Ingresar nombre comercial o genérico
  -> ¿Hay coincidencias?
     -> No: informar sin resultados -> limpiar búsqueda o volver al listado
     -> Sí: mostrar nombre, presentación, concentración y vigencia
  -> No mostrar registro ni controles para cambiar vigencia
```

### Enfermera

```text
Inicio
  -> Abrir Catálogo de medicamentos
  -> Mostrar "Cargando medicamentos..."
  -> Consultar medicamentos disponibles
  -> Buscar por nombre comercial o genérico cuando sea necesario
  -> ¿Hay coincidencias?
     -> No: informar sin resultados -> limpiar búsqueda o volver al listado
     -> Sí: mostrar nombre, presentación, concentración y vigencia
  -> No mostrar registro ni controles para cambiar vigencia
```

El flujo editable completo está en [`semana-08-user-flow.puml`](semana-08-user-flow.puml).

## 7. Flujo de alta

**Rol:** Administrador.

```text
Catálogo
  -> Seleccionar "Registrar medicamento"
  -> Mostrar formulario
  -> Completar nombre, nombre genérico, presentación, concentración y estado
  -> Quitar espacios externos antes de validar
  -> ¿Hay campos vacíos o estado inválido?
     -> Sí: mostrar errores junto a los campos, conservar datos y enfocar el primer error
     -> No: comprobar unicidad del nombre comercial
  -> ¿El nombre ya existe?
     -> Sí: mostrar "Ya existe un medicamento con ese nombre."
             conservar datos y enfocar Nombre
     -> No: registrar
  -> Mostrar "Medicamento registrado correctamente."
  -> Incorporar el medicamento al catálogo
```

El nombre duplicado se compara según la regla vigente del Micro-HIS: nombre comercial normalizado, sin distinguir mayúsculas y con espacios externos recortados. Presentación y concentración no forman parte de esa clave de unicidad.

## 8. Flujo de búsqueda

**Roles:** Administrador, Médico y Enfermera.

```text
Catálogo
  -> Enfocar búsqueda
  -> Ingresar nombre comercial o genérico
  -> Mostrar "Buscando medicamentos..."
  -> ¿La consulta pudo completarse?
     -> No: mostrar error recuperable y permitir reintentar
     -> Sí: ¿Hay coincidencias?
        -> No: mostrar "No se encontraron medicamentos para '{término}'."
                ofrecer "Limpiar búsqueda" y "Volver al listado"
        -> Sí: mostrar coincidencias y cantidad de resultados
```

Una búsqueda vacía muestra el catálogo completo. El término se recorta en sus extremos. Los wireframes no incorporan filtros ni paginación porque no forman parte del alcance ejecutable actual.

## 9. Flujo de control de vigencia

**Rol:** Administrador.

```text
Catálogo
  -> Seleccionar Activar o Desactivar
  -> Mostrar confirmación con nombre y efecto de la acción
  -> ¿Confirmar?
     -> No: cerrar confirmación y conservar el estado
     -> Sí: solicitar el cambio
  -> ¿La operación se completó?
     -> No: mantener el estado anterior y ofrecer reintentar
     -> Sí: actualizar estado y mostrar confirmación visible
```

Un medicamento `ACTIVE` se presenta como **ACTIVO - Vigente y seleccionable**. Un medicamento `INACTIVE` se presenta como **INACTIVO - No vigente ni seleccionable**. Cambiar vigencia nunca elimina el registro.

## 10. Estados de interfaz

| Estado | Mensaje o representación | Recuperación o siguiente acción |
|---|---|---|
| Carga inicial | `Cargando medicamentos...` | Esperar; conservar visible el contexto del catálogo. |
| Búsqueda en curso | `Buscando medicamentos...` | Evitar envíos repetidos mientras termina la operación. |
| Catálogo vacío | `No hay medicamentos registrados.` | Administrador: `Registrar primer medicamento`. Médico/Enfermera: sin acción administrativa. |
| Sin resultados | `No se encontraron medicamentos para "Nuberal".` | `Limpiar búsqueda` o `Volver al listado`. |
| Éxito de alta | `Medicamento registrado correctamente.` | Mostrar el nuevo registro en el catálogo. |
| Éxito de vigencia | `Medicamento desactivado correctamente.` | Mostrar inmediatamente `INACTIVO` y su efecto. |
| Error de campo | `Este campo es obligatorio.` | Conservar valores, asociar el mensaje al campo y enfocar el primer error. |
| Duplicado | `Ya existe un medicamento con ese nombre.` | Corregir Nombre sin perder los demás datos. |
| Estado inválido | `Seleccione un estado válido.` | Elegir `ACTIVE` o `INACTIVE`. |
| Fallo operativo | `No fue posible completar la operación. Intente nuevamente.` | Mantener la información y ofrecer reintento. |
| Confirmación | `¿Desactivar este medicamento?` | `Cancelar` no cambia datos; `Desactivar` aplica la acción. |

Los estados utilizan texto visible, no únicamente color. El estado de carga también puede exponerse en la misma región de estado de la operación.

## 11. Validaciones

| Dato o regla | Validación UX | Mensaje junto al dato |
|---|---|---|
| Nombre comercial | Obligatorio; recortar espacios externos. | `Este campo es obligatorio.` |
| Nombre genérico | Obligatorio; recortar espacios externos. | `Este campo es obligatorio.` |
| Presentación | Obligatoria; recortar espacios externos. | `Este campo es obligatorio.` |
| Concentración | Obligatoria; recortar espacios externos. | `Este campo es obligatorio.` |
| Estado | Solo `ACTIVE` o `INACTIVE`. | `Seleccione un estado válido.` |
| Unicidad | Comparar el nombre comercial normalizado antes del alta. | `Ya existe un medicamento con ese nombre.` |
| Vigencia | Solicitar confirmación antes de activar o desactivar. | Pregunta específica con el nombre y el efecto. |

Reglas visuales de validación:

- Mostrar el mensaje inmediatamente debajo del campo relacionado.
- Añadir una indicación textual de error; no depender solo del borde o color.
- Conservar los valores correctos cuando otro campo falle.
- Llevar el foco al primer campo inválido después del intento de guardar.
- Mantener el nombre duplicado escrito para que el usuario pueda corregirlo.
- No explicar formatos clínicos ni validar conocimiento médico. La ayuda de concentración solo presenta el formato de ejemplo `500 mg`.

## 12. Mensajes

### Confirmaciones

- `Medicamento registrado correctamente.`
- `Medicamento activado correctamente.`
- `Medicamento desactivado correctamente.`

### Validación y recuperación

- `Este campo es obligatorio.`
- `Ya existe un medicamento con ese nombre.`
- `Seleccione un estado válido.`
- `No fue posible completar la operación. Intente nuevamente.`
- `No fue posible cargar el catálogo. Intente nuevamente.`

### Vacío y búsqueda

- `Cargando medicamentos...`
- `No hay medicamentos registrados.`
- `No se encontraron medicamentos para "Nuberal".`
- `Mostrando 2 coincidencias.`

### Confirmación sensible

- Título: `¿Desactivar este medicamento?`
- Efecto: `Alivion permanecerá en el catálogo, pero no estará vigente ni disponible para nuevos procesos clínicos.`
- Acciones: `Cancelar` y `Desactivar`.

No se presentan códigos como `Error 500` ni frases vagas cuando es posible indicar una acción de recuperación.

## 13. Ayuda contextual

| Campo | Ayuda |
|---|---|
| Nombre comercial | Nombre con el que se identifica el medicamento en el catálogo. |
| Nombre genérico | Principio o denominación genérica del medicamento. |
| Presentación | Forma de presentación usada por el catálogo. Ejemplo: tabletas. |
| Concentración | Ejemplo de formato: 500 mg. |
| Vigencia | Los medicamentos inactivos permanecen en el catálogo, pero no deben seleccionarse en nuevos procesos clínicos. |

Las ayudas son breves, se ubican cerca del dato y no contienen recomendaciones médicas.

## 14. Protección de datos

Estas pantallas trabajan exclusivamente con información de catálogo:

- Nombre comercial.
- Nombre genérico.
- Presentación.
- Concentración.
- Vigencia.

No muestran ni solicitan pacientes, diagnósticos, recetas reales, datos personales o información clínica identificable. Los nombres `Alivion`, `Nuberal`, `Calmofin` y `Dermasol` usados en los wireframes son ficticios y solo sirven como evidencia visual académica.

La interfaz incluye la nota: `Datos ficticios de catálogo. No contiene información de pacientes.`

## 15. Descripción de wireframes

La evidencia visual se encuentra en [`semana-08-wireframes.html`](semana-08-wireframes.html). La hoja [`semana-08-wireframes.css`](semana-08-wireframes.css) solo da formato de baja fidelidad a esa documentación.

### Wireframe 1 - Listado principal y búsqueda

- **Objetivo:** consultar el catálogo, buscar y reconocer la vigencia.
- **Rol:** Administrador; se anota la variante de solo consulta para Médico y Enfermera.
- **Estado:** carga inicial y listado con resultados.
- **Acción principal:** buscar; para Administrador también registrar o iniciar un cambio de vigencia.
- **Validaciones:** término opcional con espacios externos recortados.
- **Mensaje:** `Cargando medicamentos...` y cantidad de resultados.
- **Regla de interacción:** Médico y Enfermera no ven acciones administrativas. `ACTIVE/INACTIVE` siempre tiene texto.
- **Recuperación:** limpiar la búsqueda o reintentar si no se carga el catálogo.

### Wireframe 2 - Alta de medicamento

- **Objetivo:** registrar un medicamento ficticio con todos los datos requeridos.
- **Rol:** Administrador.
- **Estado:** edición.
- **Acción principal:** guardar medicamento.
- **Validaciones:** obligatorios, `trim`, estado válido y unicidad del nombre comercial.
- **Mensaje:** ayudas contextuales antes de guardar.
- **Regla de interacción:** Cancelar vuelve al catálogo; Guardar inicia validación y muestra estado de proceso.
- **Recuperación:** conservar los datos válidos y dirigir el foco al primer error.

### Wireframe 3 - Validaciones y error recuperable

- **Objetivo:** explicar cómo corregir campos obligatorios y un nombre duplicado.
- **Rol:** Administrador.
- **Estado:** formulario con errores.
- **Acción principal:** corregir y volver a guardar.
- **Validaciones:** nombre duplicado, nombre genérico obligatorio y presentación obligatoria.
- **Mensaje:** mensajes específicos bajo cada dato y resumen `Revise los campos indicados.`
- **Regla de interacción:** no limpiar concentración ni estado válidos; no depender solo del color.
- **Recuperación:** foco en Nombre, edición disponible y nuevo intento sin volver a llenar el formulario.

### Wireframe 4 - Confirmación de cambio de vigencia

- **Objetivo:** prevenir un cambio accidental y explicar su efecto.
- **Rol:** Administrador.
- **Estado:** confirmación antes de desactivar.
- **Acción principal:** desactivar medicamento.
- **Validaciones:** medicamento identificado y acción de destino explícita.
- **Mensaje:** `¿Desactivar este medicamento?` con el efecto sobre la selección futura.
- **Regla de interacción:** Cancelar conserva el estado; la acción sensible usa una etiqueta específica.
- **Recuperación:** ante fallo se cierra el estado de proceso, se conserva `ACTIVO` y se permite reintentar.

### Wireframe 5 - Catálogo vacío y búsqueda sin resultados

- **Objetivo:** diferenciar que no existen registros de que una búsqueda no tenga coincidencias.
- **Rol:** todos los roles; la acción de primer registro solo aparece al Administrador.
- **Estado:** vacío y sin resultados.
- **Acción principal:** registrar el primer medicamento o limpiar la búsqueda, según el caso.
- **Validaciones:** ninguna entrada obligatoria; se conserva visible el término buscado.
- **Mensaje:** `No hay medicamentos registrados.` y `No se encontraron medicamentos para "Nuberal".`
- **Regla de interacción:** no confundir ambos estados ni ofrecer acciones sin permiso.
- **Recuperación:** limpiar búsqueda, volver al listado o registrar si el rol está autorizado.

### Wireframe 6 - Alta exitosa

- **Objetivo:** confirmar el alta y ubicar visualmente el nuevo registro.
- **Rol:** Administrador.
- **Estado:** éxito.
- **Acción principal:** continuar consultando el registro incorporado.
- **Validaciones:** superadas antes de mostrar el éxito.
- **Mensaje:** `Medicamento registrado correctamente.`
- **Regla de interacción:** la confirmación permanece visible y el registro aparece en el catálogo con vigencia textual.
- **Recuperación:** si la actualización visual del listado falla, ofrecer recargar el catálogo sin reenviar el alta.

## 16. Reglas de interacción

- El usuario no pierde datos válidos cuando ocurre un error recuperable.
- El sistema recorta espacios externos antes de validar y guardar.
- Al cambiar vigencia se solicita confirmación con una acción específica.
- Cancelar una confirmación no produce cambios.
- Después de una operación exitosa se muestra retroalimentación visible.
- El buscador se puede limpiar fácilmente y una búsqueda vacía vuelve al listado.
- Las acciones no permitidas por rol no se presentan como disponibles.
- Los errores explican qué dato debe corregirse y cómo continuar.
- El diseño no depende únicamente de colores para carga, error, éxito o vigencia.
- Después de validar, el foco se dirige al primer error.
- `ACTIVE/INACTIVE` y `ACTIVO/INACTIVO` tienen texto visible junto a su significado.
- Durante una operación se informa el progreso y se evita el envío repetido.
- Un fallo al cambiar vigencia conserva el estado anterior hasta recibir confirmación de éxito.
- Los medicamentos inactivos siguen visibles en la consulta administrativa y no se eliminan.
- Médico y Enfermera disponen de consulta y búsqueda, sin controles de alta o vigencia.

## 17. Trazabilidad con semanas anteriores

| Evidencia previa | Regla recuperada en Semana 8 |
|---|---|
| `Semana-01-UML/ASII-14-SEMANA-1.md`, actores y CU-MED-02 | Administrador, Médico y Enfermera consultan y buscan. |
| `Semana-01-UML/ASII-14-SEMANA-1.md`, CU-MED-03 | Administrador realiza el alta y recibe validación/confirmación. |
| `Semana-01-UML/ASII-14-SEMANA-1.md`, CU-MED-06 | Administrador confirma activar/desactivar; cancelar no modifica. |
| `Semana-02-SOLID/ASII-14-SEMANA-2-SOLID.md`, RF-06 y RF-07 | Solo permisos administrativos registran o cambian vigencia. |
| `docs/INFORME.md`, funcionalidades y dominio | Campos, búsqueda, `ACTIVE/INACTIVE`, obligatorios, unicidad y vigencia. |
| `docs/SEMANA-07-COMPONENTES-REFACTOR.md`, contratos | `trim`, estado por defecto, alta, búsqueda y mensajes de error existentes. |
| `src/Presentation/views/catalog.php` | Etiquetas actuales, buscador, listado, presentación, concentración y texto de vigencia. |

La documentación inicial planteó unicidad dentro de un hospital. El Micro-HIS ejecutable actual no contiene `tenant_id` y aplica unicidad global al nombre comercial normalizado. Los wireframes siguen el comportamiento ejecutable actual y registran el aislamiento por hospital como una limitación, no como una función disponible.

## 18. Limitaciones

- Los wireframes son estáticos, de baja fidelidad y no reemplazan `catalog.php`.
- El Micro-HIS actual es de un solo usuario y no implementa autenticación ni autorización; la separación por rol es una especificación UX respaldada por Semanas 1 y 2.
- La confirmación de vigencia está diseñada, pero todavía no existe en la interfaz funcional actual.
- Los errores por campo son una propuesta visual; actualmente la vista presenta mensajes globales.
- La unicidad actual es global por nombre comercial normalizado, no por hospital.
- Presentación y concentración son textos obligatorios; no existe un catálogo de presentaciones ni validación médica del formato.
- No se incorporan edición, eliminación, categoría, paginación, filtro por estado ni multitenancy.
- No se realiza auditoría WCAG completa, backlog de Semana 9, diseño móvil detallado de Semana 10 ni prototipo navegable final de Semana 11.

## 19. Conclusión

La propuesta define una experiencia coherente para que Administrador consulte, busque, registre y controle vigencia, mientras Médico y Enfermera consultan y buscan sin recibir controles administrativos. Los seis wireframes hacen visibles carga, vacío, sin resultados, validación, confirmación y éxito, con recuperación clara y protección de datos. El diseño mantiene la arquitectura y funcionalidad de semanas anteriores sin modificar el backend ni ampliar el alcance del Catálogo de medicamentos.
