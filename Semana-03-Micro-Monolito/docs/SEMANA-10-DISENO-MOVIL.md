# Semana 10 - Diseño para movilidad

## Datos del estudiante

- **Estudiante:** María Yamilet Lindo Pablo
- **Carné:** 1890-23-14827
- **Módulo:** ASII-14 - Catálogo de Medicamentos
- **Asignatura:** Análisis de Sistemas II
- **Sistema:** Micro-HIS Catálogo de Medicamentos
- **Rama:** `feature/semana-10-diseno-movil`
- **Evidencia visual:** [`semana-10-mobile.html`](semana-10-mobile.html)
- **Reglas detalladas:** [`semana-10-breakpoints.md`](semana-10-breakpoints.md)

## 1. Objetivo

Proponer la adaptación responsive del alta, búsqueda, consulta y control de vigencia del Catálogo de Medicamentos para dispositivos de **320 px a 430 px**, priorizando contenido, acciones y recuperación ante conexión limitada sin reconstruir la aplicación ni alterar su comportamiento funcional.

La entrega es una evidencia estática de diseño. No modifica `catalog.php`, `app.js`, el backend, la API ni la base de datos, y no constituye una aplicación móvil nativa.

## 2. Alcance

La propuesta cubre:

- Búsqueda parcial por nombre comercial o nombre genérico.
- Consulta de nombre comercial, nombre genérico, presentación, concentración y vigencia.
- Alta con Nombre comercial, Nombre genérico, Presentación, Concentración y Estado inicial.
- Confirmación previa a activar o desactivar un medicamento.
- Estados de carga, catálogo vacío, búsqueda sin resultados y fallo de conexión.
- Conservación de término, resultados previos y estado confirmado cuando una solicitud falla.
- Reglas de navegación, foco, legibilidad y tamaño de controles en móvil.

Se conservan las reglas reales del módulo:

- Todos los datos del alta son obligatorios.
- El estado permitido es `ACTIVE` o `INACTIVE`, presentado también como ACTIVO o INACTIVO.
- La unicidad se aplica al nombre comercial normalizado, sin distinguir mayúsculas y con espacios externos recortados.
- Presentación y concentración no forman parte de la clave de unicidad.
- Buscar admite nombre comercial o nombre genérico; una búsqueda vacía muestra el catálogo completo.
- Cambiar vigencia no elimina el medicamento.
- Los datos usados en la evidencia son ficticios y no contienen información de pacientes.

Quedan fuera edición, eliminación, inventario, lotes, precios, recetas, pacientes, autenticación, autorización implementada, nuevas reglas clínicas, cambios funcionales y actividades de Semana 11.

## 3. Análisis desktop a móvil

| Elemento actual | Riesgo entre 320 y 430 px | Decisión móvil |
|---|---|---|
| Alta y catálogo en dos paneles | Competencia visual y ancho insuficiente | Separar en vistas; una tarea principal por pantalla. |
| Tabla de cuatro columnas | Scroll horizontal y asociación débil entre dato y acción | Convertir cada fila en tarjeta vertical. |
| Nombre y genérico dentro de una celda | El dato principal puede perder jerarquía | Nombre comercial como título; genérico como dato secundario. |
| Presentación y concentración separadas | Más etiquetas y exploración visual | Agrupar como `Tabletas · 250 mg`, sin fusionar los valores del dominio. |
| Vigencia dentro de una columna | Puede quedar lejos del medicamento | Insignia textual junto al nombre comercial. |
| Acción Activar/Desactivar aislada | Nombre accesible ambiguo y riesgo de error | Acción visual breve con nombre accesible conceptual `Desactivar Alivion`; confirmación previa. |
| Formulario con una fila de dos campos | Controles estrechos y ayudas fragmentadas | Una sola columna, etiquetas y ayudas persistentes. |
| Mensajes globales | En móvil pueden quedar fuera del contexto | Mensaje junto a la tarea; foco al primer error al implementar. |
| Fallo de búsqueda | El contenido anterior puede confundirse con el resultado nuevo | Avisar que no se actualizó, conservar término y resultados, ofrecer Reintentar. |

No se propone comprimir la tabla ni obligar a desplazarla horizontalmente. La transformación a tarjetas conserva todos los datos relevantes y hace explícita la relación entre medicamento, estado y acción.

## 4. Jerarquía de información

El orden de lectura de cada tarjeta es:

1. **Nombre comercial**, como título de la tarjeta.
2. **Vigencia**, con texto `ACTIVO` o `INACTIVO` y no solo color.
3. **Presentación y concentración**, agrupadas en una línea y con salto seguro si el texto crece.
4. **Nombre genérico**, como información secundaria identificada.
5. **Acción disponible**, al final y vinculada al medicamento.

Ejemplo conceptual:

```text
Alivion                         ACTIVO
Tabletas · 250 mg
Nombre genérico: Compuesto Alfa

[Desactivar Alivion]
```

Esta secuencia favorece reconocimiento rápido: primero identifica el medicamento, después comprueba si está vigente y luego consulta su forma y concentración. La acción queda separada de los datos para evitar activaciones accidentales.

## 5. Navegación móvil

- **Catálogo** es la vista de entrada y concentra búsqueda, resultados y acceso autorizado al alta.
- **Registrar medicamento** abre una vista dedicada; `Volver al catálogo` y `Cancelar` regresan sin registrar.
- **Activar/Desactivar** abre una confirmación sobre el contexto del catálogo; no cambia el estado todavía.
- **Cancelar** o `Escape` cierran la confirmación sin mutación y deben devolver el foco a la acción que la abrió.
- **Confirmar Activación/Desactivación** inicia la solicitud; la tarjeta solo cambia después de una respuesta exitosa.
- **Reintentar** repite la última operación fallida sin borrar el término o el estado confirmado.
- **Limpiar búsqueda** vacía el término y vuelve al catálogo completo.
- Médico y Enfermera conservan consulta y búsqueda, pero la propuesta no les muestra alta ni acciones de vigencia, de acuerdo con el diseño previo. La aplicación actual aún no implementa autorización.

El orden de teclado propuesto sigue el orden visual y del documento: volver/encabezado, título, campos o búsqueda, contenido y acciones. No se usan índices de tabulación positivos.

## 6. Diseño de las pantallas

### Pantalla 1 - Catálogo móvil y búsqueda

**Propósito:** buscar, consultar y reconocer la vigencia sin tabla horizontal.

Contenido:

- Encabezado simple `Micro-HIS / Catálogo`.
- Acción `Registrar` cuando el rol sea Administrador.
- Etiqueta visible `Buscar por nombre comercial o genérico`.
- Campo de búsqueda, botón Buscar y opción Limpiar.
- Región de estado `Buscando medicamentos...` durante carga.
- Cantidad de resultados después de una respuesta válida.
- Una tarjeta por medicamento con la jerarquía definida.
- Estado alterno sin resultados con el término y acción `Limpiar búsqueda`.
- Estado de catálogo vacío distinto: `No hay medicamentos registrados.`

La evidencia muestra resultados y, debajo, muestras compactas de carga y cero coincidencias. Son variantes de estado de la misma pantalla, no estados simultáneos de una implementación.

### Pantalla 2 - Alta de medicamento

**Propósito:** completar una sola tarea en una columna legible a 320 px.

Contenido:

- Título `Registrar medicamento`.
- Instrucción previa: `Todos los campos son obligatorios.`
- Nombre comercial.
- Nombre genérico.
- Presentación.
- Concentración.
- Estado inicial con opciones ACTIVO e INACTIVO.
- Ayudas persistentes visibles incluso después de escribir.
- Acciones Guardar y Cancelar.
- Nota de unicidad del nombre comercial.

Si existe un error, el diseño previsto conserva los datos correctos, presenta el mensaje debajo del campo relacionado y lleva el foco al primer control inválido. Un duplicado se asocia con Nombre comercial y no borra los otros campos.

### Pantalla 3 - Confirmación de cambio de vigencia

**Propósito:** impedir un cambio accidental y explicar la consecuencia antes de enviar.

Contenido:

- Medicamento: `Alivion`.
- Estado actual: `ACTIVE · ACTIVO`.
- Estado destino: `INACTIVE · INACTIVO`.
- Efecto: permanece en el catálogo, pero deja de estar vigente y seleccionable.
- Acciones `Cancelar` y `Confirmar desactivación`.

Patrón de teclado que debe respetar una futura implementación:

- Al abrir, el foco entra al diálogo, preferentemente en Cancelar para evitar confirmar por accidente.
- `Tab` y `Shift+Tab` permanecen entre los controles del diálogo.
- `Escape` y Cancelar cierran sin cambiar el medicamento.
- El fondo queda inoperable mientras el diálogo está abierto.
- Al cerrar, el foco vuelve a `Desactivar Alivion`.

La evidencia HTML es estática y documenta este patrón; no afirma implementar el ciclo de foco sin JavaScript.

### Pantalla 4 - Recuperación por conexión limitada

**Propósito:** conservar el contexto cuando la búsqueda no puede actualizarse.

Contenido:

- Término `Alivion` conservado en el buscador.
- Mensaje `No se pudieron actualizar los resultados.`
- Explicación de que se mantienen visibles los resultados anteriores.
- Tarjeta anterior rotulada `Resultado anterior`.
- Acción principal `Reintentar`.
- Acciones secundarias `Volver` y `Limpiar búsqueda`.

Los resultados anteriores no se presentan como respuesta nueva: una nota explícita indica su procedencia. De esta manera se conserva contexto sin comunicar información desactualizada como si hubiera sido confirmada por la última solicitud.

## 7. Reglas responsive y breakpoints

| Rango | Regla principal |
|---|---|
| 320-359 px | Una columna compacta, separación lateral de 12 px y botones apilados a ancho completo cuando compiten por espacio. |
| 360-399 px | Una columna con 16 px laterales; acciones principales visibles y controles con más respiración. |
| 400-430 px | Una columna; acciones breves pueden compartir fila si conservan al menos 44 px de alto y texto íntegro. |
| Más de 430 px | Progresión de la página de evidencia, no una nueva interfaz móvil; las pantallas mantienen ancho máximo de 430 px. |

Reglas comunes:

- No existe scroll horizontal obligatorio para operar el catálogo.
- El contenido usa `overflow-wrap: anywhere` en nombres o valores excepcionalmente largos.
- Los controles interactivos tienen altura mínima de 44 px.
- La separación entre controles consecutivos es de al menos 8 px; entre grupos, 16 px.
- Las tarjetas ocupan el ancho disponible y no fijan una altura.
- Presentación y concentración permanecen juntas, pero pueden envolver a otra línea.
- ACTIVO e INACTIVO siempre incluyen texto; el color solo refuerza el estado.
- En 320-359 px, las acciones de formularios y confirmaciones se apilan.
- En 400-430 px, Cancelar y Confirmar pueden ubicarse en línea cuando ambas etiquetas caben completas.
- No se truncan mensajes, nombres ni etiquetas mediante puntos suspensivos.

El detalle completo está en [`semana-10-breakpoints.md`](semana-10-breakpoints.md).

## 8. Formularios móviles

- Una sola columna entre 320 y 430 px.
- Etiqueta visible y persistente encima de cada control.
- Indicador textual `(obligatorio)`; el asterisco no es la única explicación.
- Ayuda persistente debajo del control, no dependiente del placeholder.
- Ejemplos como `Tabletas` y `250 mg` permanecen visibles después de escribir.
- Estado inicial explica que INACTIVO permanece en catálogo, pero no está vigente ni seleccionable.
- Guardar es la acción primaria; Cancelar tiene menor énfasis visual, pero el mismo tamaño táctil mínimo.
- Errores específicos aparecen debajo del campo, se asocian programáticamente al implementarse y no dependen solo del color.
- Los valores correctos no se borran por un error en otro campo o por duplicidad.
- Durante Guardar se informa progreso y se evita el envío repetido.

## 9. Adaptación de tabla a tarjetas

Cada fila de escritorio se convierte conceptualmente en un `article` o elemento de lista. No se ocultan datos del dominio ni se crea un carrusel.

| Tabla de escritorio | Tarjeta móvil |
|---|---|
| Medicamento | Título con nombre comercial y línea secundaria de nombre genérico. |
| Presentación | Línea compacta `Presentación · Concentración`. |
| Vigencia | Insignia textual cerca del título y explicación breve. |
| Acción | Botón al final, con acción y medicamento en el nombre accesible. |

Una tarjeta INACTIVA puede variar borde y fondo, pero sigue mostrando `INACTIVO` y `No vigente ni seleccionable`. Así, la diferencia no depende únicamente del color.

## 10. Reducción de carga cognitiva

- Una acción principal por zona: Buscar, Guardar, Confirmar o Reintentar.
- Etiquetas directas y persistentes, sin códigos técnicos como mensaje principal.
- Ausencia de columnas horizontales y de contenido clínico fuera del catálogo.
- Presentación y concentración agrupadas para lectura rápida.
- Vigencia próxima al nombre, con texto y explicación.
- Confirmación breve con medicamento, origen, destino y efecto.
- Mensajes de error accionables que indican qué se conservó y qué hacer.
- Estados vacío, sin resultados y error de red con textos y acciones diferentes.

## 11. Confirmación de vigencia

La confirmación resuelve la prevención de error detectada en Semana 9:

1. La persona activa `Desactivar Alivion`.
2. El estado sigue mostrando ACTIVO.
3. El diálogo identifica Alivion, ACTIVO, INACTIVO y el efecto.
4. Cancelar o Escape cierra sin enviar cambios.
5. Confirmar desactivación inicia una única solicitud.
6. Solo una respuesta exitosa cambia la tarjeta a INACTIVO y anuncia el resultado.
7. Si falla, la tarjeta continúa ACTIVA y se ofrece Reintentar.

No se usa una etiqueta ambigua como `Aceptar`.

## 12. Recuperación ante errores y conexión limitada

Principios:

- Conservar el término de búsqueda y los datos válidos del formulario.
- Mantener resultados anteriores cuando sea posible, identificándolos como anteriores.
- No aplicar visualmente una mutación que el servidor no confirmó.
- Diferenciar validación, cero coincidencias, catálogo vacío y fallo de red.
- Proporcionar una acción concreta: Corregir, Limpiar, Volver o Reintentar.
- Anunciar el error en una región de estado al implementarse.

### Escenario móvil 1 - Búsqueda con conexión limitada

**Situación:** la persona busca `Alivion`; la solicitud falla mientras había resultados anteriores visibles.

**Decisión de contenido:** se conserva `Alivion` en el campo. Se mantienen las tarjetas anteriores y se rotulan como `Resultados anteriores`; no se actualiza el contador como si la búsqueda hubiera terminado correctamente.

**Interacción:** el foco puede pasar al mensaje y a `Reintentar`; también están disponibles `Volver` y `Limpiar búsqueda`.

**Recuperación:** Reintentar repite la consulta con `Alivion`. Limpiar vuelve al catálogo completo cuando pueda cargarse.

**Error o conexión limitada:** mensaje `No se pudieron actualizar los resultados.` y explicación `Conservamos tu búsqueda y los resultados anteriores.`

**Justificación:** conservar término y contenido evita que la persona vuelva a escribir o pierda su referencia. Rotular el contenido anterior evita confundirlo con el resultado de la solicitud fallida.

### Escenario móvil 2 - Cambio de vigencia con conexión limitada

**Situación:** el Administrador abre la confirmación para desactivar Alivion y confirma; la solicitud falla.

**Decisión de contenido:** Alivion sigue mostrando `ACTIVE · ACTIVO`. Se informa el estado destino solicitado, pero no se representa como aplicado.

**Interacción:** el mensaje ofrece `Reintentar` y `Volver al catálogo`. Reintentar solicita nuevamente la desactivación de Alivion; no alterna localmente el valor.

**Recuperación:** se conserva el estado anterior confirmado por el servidor. Al cancelar o volver, la tarjeta permanece ACTIVA. Solo una respuesta exitosa cambia el estado y comunica éxito.

**Error o conexión limitada:** mensaje `No se pudo actualizar la vigencia.` y aclaración `Alivion continúa ACTIVO.`

**Justificación:** una interfaz nunca debe aparentar una modificación que el servidor no confirmó. Mantener ACTIVO evita decisiones posteriores basadas en un estado falso.

## 13. Accesibilidad móvil

- Texto base legible y contraste alto en información, ayudas y estados.
- Etiqueta de búsqueda visible; el placeholder no contiene la única instrucción.
- Controles de al menos 44 px de alto y separación suficiente.
- Indicador de foco visible con contorno y separación respecto al control.
- Orden de foco equivalente al orden visual.
- Etiquetas y ayudas persistentes para todos los campos.
- Errores junto al campo, con texto; no solo borde o color.
- ACTIVO e INACTIVO visibles como texto.
- Acción visual `Desactivar` con nombre accesible específico `Desactivar Alivion`.
- Confirmación con título y descripción asociados; patrón previsto de modalidad, Escape, ciclo y retorno de foco.
- Mensajes comprensibles y accionables, sin depender de códigos HTTP.
- Respeto conceptual de reducción de movimiento; la evidencia no requiere animaciones para comprender estados.

Estas decisiones son una propuesta académica y no declaran certificación WCAG completa.

## 14. Relación con hallazgos de Semana 9

| Hallazgo | Respuesta propuesta en Semana 10 | Evidencia |
|---|---|---|
| UX-01 | Confirmación con medicamento, estado actual, destino, efecto, Cancelar y Confirmar. | Pantalla 3. |
| A11Y-01 | Error asociado al campo, conservación de valores y foco al primer inválido al implementar. | Pantalla 2 y reglas de formulario. |
| UX-02 | Obligatorios y ayudas persistentes antes y durante la escritura. | Pantalla 2. |
| UX-03 | Catálogo vacío y búsqueda sin resultados tienen mensaje y recuperación diferentes. | Pantalla 1 y reglas de estado. |
| A11Y-02 | Nombre accesible conceptual `Desactivar Alivion`. | Tarjetas de Pantalla 1. |
| UX-04 | Mensaje explícito, término y resultados conservados, acción Reintentar. | Pantalla 4 y escenario 1. |
| A11Y-03 | Instrucción fuera del placeholder y color de texto legible. | Buscadores de Pantallas 1 y 4. |
| A11Y-04 | Patrón documentado de foco inicial, ciclo, Escape y retorno al disparador. | Pantalla 3 y especificación de teclado. |

Semana 10 propone cómo resolver estos puntos en móvil, pero no modifica la interfaz funcional.

## 15. Criterios verificables

1. A 320 px no existe scroll horizontal obligatorio para operar el catálogo.
2. Nombre, presentación, concentración y vigencia permanecen legibles entre 320 px y 430 px.
3. Las tarjetas muestran primero el nombre comercial y la vigencia.
4. La búsqueda conserva el término ante un fallo.
5. Un fallo de red no elimina abruptamente los resultados previos y los identifica como anteriores.
6. Una vigencia no cambia visualmente hasta obtener confirmación exitosa del servidor.
7. Cancelar o Escape no altera el medicamento y devuelve el foco al disparador al implementarse.
8. Los campos obligatorios se identifican antes del envío.
9. Las ayudas continúan visibles después de escribir.
10. Las acciones de vigencia pueden distinguir el medicamento correspondiente por su nombre accesible.
11. Catálogo vacío y búsqueda sin resultados muestran mensajes y acciones diferentes.
12. Los controles principales mantienen al menos 44 px de altura.
13. ACTIVO e INACTIVO se distinguen mediante texto además de color.
14. Los textos largos envuelven sin quedar cortados ni salir de la tarjeta.
15. Presentación y concentración se agrupan sin perder sus valores.
16. El formulario permanece en una columna en los tres rangos móviles.
17. Un fallo de vigencia conserva el último estado confirmado y ofrece Reintentar.
18. No se muestran controles administrativos a roles de solo consulta dentro de la variante documentada.

## 16. Limitaciones de la evidencia

- Los cuatro diseños son estáticos y no sustituyen la interfaz funcional.
- Los botones y formularios ilustran jerarquía y estados; no envían datos.
- Sin JavaScript no se demuestra un ciclo de foco modal real; se documenta como requisito de implementación futura.
- No se realizaron pruebas con personas usuarias, lector de pantalla ni dispositivos físicos.
- No se declara cumplimiento WCAG completo.
- No se implementan autenticación o autorización por rol.

## 17. Conclusión

La propuesta adapta las tareas reales del Catálogo de Medicamentos a 320-430 px mediante vistas de una columna, tarjetas en lugar de tabla, formulario con ayuda persistente, confirmación de vigencia y recuperación explícita ante conexión limitada. La jerarquía mantiene nombre comercial, vigencia, presentación, concentración, nombre genérico y acción sin scroll horizontal obligatorio. Los ocho hallazgos de Semana 9 reciben una respuesta de diseño trazable, sin modificar código funcional ni adelantar Semana 11.
