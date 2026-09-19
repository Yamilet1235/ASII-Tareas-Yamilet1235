# Semana 11 - Prototipo navegable del Catálogo de Medicamentos

## 1. Datos del estudiante

- **Estudiante:** María Yamilet Lindo Pablo
- **Carné:** 1890-23-14827
- **Módulo:** ASII-14 - Catálogo de Medicamentos
- **Semana:** 11
- **Asignatura:** Análisis de Sistemas II
- **Prototipo:** [`semana-11-prototipo.html`](semana-11-prototipo.html)
- **Mapa de navegación:** [`semana-11-mapa-navegacion.md`](semana-11-mapa-navegacion.md)
- **Diagrama editable:** [`semana-11-mapa-navegacion.puml`](semana-11-mapa-navegacion.puml)

## 2. Objetivo

Construir un prototipo navegable desktop y móvil del flujo principal del Catálogo de Medicamentos. La evidencia permite consultar, buscar, registrar y cambiar la vigencia de medicamentos ficticios, además de demostrar un camino feliz completo y el error crítico de duplicidad del nombre comercial.

El prototipo convierte en interacción las decisiones diseñadas en las Semanas 8, 9 y 10. Funciona localmente, no consume la API real y no modifica la aplicación funcional del Micro-HIS.

## 3. Alcance

El prototipo cubre:

- Catálogo inicial con nombre comercial, nombre genérico, presentación, concentración y vigencia.
- Búsqueda por nombre comercial o nombre genérico.
- Resultados y estado sin coincidencias con recuperación explícita.
- Alta simulada de medicamentos para el rol Administrador.
- Validación de campos obligatorios y conservación de valores correctos.
- Unicidad simulada del nombre comercial normalizado.
- Confirmación antes de activar o desactivar un medicamento.
- Retroalimentación de alta y cambio de vigencia exitosos.
- Simulación visual de los roles Administrador, Médico y Enfermera.
- Adaptación progresiva para escritorio y anchos móviles entre 320 y 430 px.
- Operación básica mediante mouse, teclado y controles HTML nativos.

Quedan fuera edición, eliminación, inventario, lotes, precios, recetas, pacientes, diagnósticos, persistencia, autenticación real, autorización de servidor, API REST, backend y base de datos.

## 4. Relación con Semanas 8, 9 y 10

### Semana 8 - Diseño UX

Semana 8 definió el flujo por roles, los seis wireframes, los estados de catálogo, la búsqueda, el alta, la validación de unicidad, la confirmación de vigencia y los mensajes de recuperación. Semana 11 transforma esos wireframes estáticos en vistas navegables y estados controlados mediante JavaScript local.

### Semana 9 - Usabilidad y accesibilidad

El prototipo atiende los ocho hallazgos relevantes:

| Hallazgo | Respuesta implementada en el prototipo |
|---|---|
| UX-01 | La vigencia exige una confirmación antes de cambiar. |
| A11Y-01 | El error se asocia al campo, usa `aria-invalid` y dirige el foco. |
| UX-02 | Los campos indican obligatoriedad y mantienen ayudas visibles. |
| UX-03 | Sin resultados se diferencia del catálogo completo y ofrece recuperación. |
| A11Y-02 | Cada acción de vigencia incluye el medicamento en su nombre accesible. |
| UX-04 | La búsqueda sin coincidencias ofrece Limpiar búsqueda. |
| A11Y-03 | La instrucción de búsqueda es una etiqueta visible y no depende del placeholder. |
| A11Y-04 | El modal gestiona foco inicial, ciclo de Tab, Escape y retorno al disparador. |

Estas respuestas son evidencia académica y no constituyen una declaración de conformidad WCAG completa.

### Semana 10 - Diseño móvil

Semana 10 definió la jerarquía de cada tarjeta móvil, el formulario de una columna, los controles táctiles de al menos 44 px y los rangos 320-359, 360-399 y 400-430 px. Semana 11 aplica esas reglas en el prototipo navegable: la tabla de escritorio se convierte en tarjetas móviles y las acciones se apilan o alinean según el espacio disponible.

## 5. Arquitectura del prototipo

El prototipo es autocontenido en tres archivos locales:

| Archivo | Responsabilidad |
|---|---|
| `semana-11-prototipo.html` | Estructura semántica, vistas, formulario, selector de rol, regiones accesibles y diálogo de vigencia. |
| `semana-11-prototipo.css` | Presentación desktop, transformación móvil a tarjetas, breakpoints, foco visible y estados visuales. |
| `semana-11-prototipo.js` | Datos en memoria, navegación, búsqueda, alta, normalización, roles, cambio de vigencia y gestión del modal. |

No se emplean frameworks, paquetes, CDN, fuentes remotas ni recursos de terceros. Los medicamentos agregados y los cambios de vigencia viven únicamente en memoria durante la sesión del navegador. Al recargar el archivo se restablecen los datos iniciales.

## 6. Ejecución local

El prototipo no requiere servidor. Se abre directamente desde el sistema de archivos:

```text
file:///.../Semana-03-Micro-Monolito/docs/semana-11-prototipo.html
```

Los archivos CSS y JavaScript se cargan mediante rutas relativas desde la misma carpeta `docs/`. Esta separación permite revisar estructura, presentación y comportamiento sin depender de PHP, base de datos o conexión de red.

## 7. Roles

El encabezado contiene un selector denominado **Rol de demostración**. El selector modifica la interfaz de forma visual; no autentica personas ni sustituye autorización en backend.

| Acción | Administrador | Médico | Enfermera |
|---|---|---|---|
| Consultar catálogo | Sí | Sí | Sí |
| Buscar por nombre comercial | Sí | Sí | Sí |
| Buscar por nombre genérico | Sí | Sí | Sí |
| Registrar medicamento | Sí | No | No |
| Activar o desactivar | Sí | No | No |

Al seleccionar Médico o Enfermera se ocultan `Registrar medicamento`, `Registrar otro` y las acciones de vigencia. Si el rol cambia mientras está abierto el formulario administrativo, el prototipo vuelve al catálogo.

## 8. Estados y pantallas navegables

El mismo archivo HTML presenta las vistas y estados siguientes:

1. Inicio y catálogo completo.
2. Resultados de búsqueda.
3. Búsqueda sin resultados.
4. Alta de medicamento.
5. Formulario con error crítico de duplicidad.
6. Alta exitosa.
7. Confirmación de cambio de vigencia.
8. Catálogo con cambio de vigencia exitoso.

Las vistas principales se alternan con el atributo `hidden`. La confirmación aparece como diálogo modal sobre el catálogo y no modifica el estado hasta que se confirma.

## 9. Diseño desktop

Desde aproximadamente 900 px, el prototipo aprovecha el ancho disponible con una estructura de contenido limitada a 1180 px:

- Encabezado con identidad del Micro-HIS y selector de rol.
- Presentación del catálogo con contador visible.
- Buscador horizontal con acciones claramente diferenciadas.
- Tabla con columnas Medicamento, Presentación, Vigencia y Acción.
- Nombre genérico y concentración como información secundaria asociada.
- Formulario de alta con contexto lateral y panel de campos.
- Modal centrado que muestra estado actual, estado destino y consecuencia.

La vigencia se representa mediante `ACTIVO` o `INACTIVO`, una descripción textual y color redundante. Las acciones administrativas usan nombres específicos y no etiquetas ambiguas como Aceptar.

## 10. Diseño móvil de 320 a 430 px

En móvil no se utiliza una tabla horizontal. Cada medicamento se presenta como tarjeta en este orden:

1. Nombre comercial.
2. Vigencia textual.
3. Presentación y concentración.
4. Nombre genérico.
5. Acción administrativa, cuando corresponde.

El formulario conserva una sola columna y las ayudas permanecen debajo de cada control. Los rangos aplicados son:

| Rango | Comportamiento principal |
|---|---|
| 320-359 px | Márgenes compactos, búsqueda y acciones apiladas, tarjeta con nombre y vigencia en bloques. |
| 360-399 px | Una columna con mayor separación; acciones largas continúan apiladas. |
| 400-430 px | Formulario de una columna; acciones de formulario y modal pueden compartir fila. |

Los controles interactivos tienen una altura mínima de 44 px. Los textos pueden envolver, no se truncan y el diseño evita anchos fijos que obliguen a desplazar horizontalmente la interfaz.

## 11. Navegación

El catálogo es la vista de entrada. Desde él se puede buscar, limpiar la búsqueda, abrir el alta o iniciar un cambio de vigencia según el rol. El alta permite cancelar, volver, corregir errores o finalizar en éxito. La pantalla de éxito permite regresar al catálogo o registrar otro medicamento.

El mapa completo está documentado en [`semana-11-mapa-navegacion.md`](semana-11-mapa-navegacion.md). La versión PlantUML puede renderizarse externamente desde [`semana-11-mapa-navegacion.puml`](semana-11-mapa-navegacion.puml). No se requiere instalar PlantUML para usar el prototipo.

## 12. Búsqueda

La búsqueda compara el término con:

- Nombre comercial.
- Nombre genérico.

El término se recorta y se normaliza para ignorar mayúsculas, minúsculas y acentos. Buscar `Alivion` muestra coincidencias por nombre comercial. Buscar `Compuesto Alfa` también muestra Alivion mediante su nombre genérico.

Cuando no hay coincidencias se presenta el mensaje `No encontramos medicamentos para “X”.` y la acción `Limpiar búsqueda`. El Administrador también conserva una acción para registrar un medicamento. Limpiar vacía el campo, recupera el catálogo completo y devuelve el foco al buscador.

## 13. Alta de medicamento

El alta está disponible únicamente en la simulación del rol Administrador. Solicita:

- Nombre comercial.
- Nombre genérico.
- Presentación.
- Concentración.
- Estado inicial `ACTIVE / ACTIVO` o `INACTIVE / INACTIVO`.

Todos los campos se presentan como obligatorios y tienen ayuda persistente. Al guardar se recortan espacios externos. Si falta un valor, el prototipo muestra el error junto al campo, conserva los demás valores y enfoca el primer control inválido.

Después de una validación correcta se agrega el medicamento al arreglo en memoria, se abre la vista de éxito y el registro queda disponible en el catálogo hasta recargar el archivo.

## 14. Camino feliz completo

**Rol:** Administrador.

**Recorrido:**

```text
Catálogo
-> Buscar Calmofin
-> Sin resultados
-> Registrar medicamento
-> Nombre comercial: Calmofin
-> Nombre genérico: Compuesto Gamma
-> Presentación: Tabletas
-> Concentración: 500 mg
-> Estado: ACTIVE / ACTIVO
-> Guardar medicamento
-> Medicamento registrado correctamente
-> Volver al catálogo
-> Calmofin aparece en el catálogo
```

Pantallas y estados participantes:

| Paso | Vista o estado |
|---|---|
| Entrada | Inicio / Catálogo. |
| Búsqueda | Sin resultados para Calmofin. |
| Recuperación | Acción Registrar medicamento. |
| Captura | Alta de medicamento. |
| Confirmación | Alta exitosa. |
| Resultado | Catálogo con Calmofin visible. |

La búsqueda inicial no encuentra Calmofin porque los datos iniciales contienen Alivion y Alivion Plus. Después del alta, Calmofin se incorpora a la colección en memoria y puede buscarse por nombre comercial o por `Compuesto Gamma`.

## 15. Error crítico de duplicidad

**Rol:** Administrador.

**Recorrido:**

```text
Registrar medicamento
-> Nombre comercial: Alivion
-> Completar los demás campos
-> Guardar medicamento
-> Detectar duplicado
-> Mostrar "Revise los campos indicados."
-> Mostrar "Ya existe un medicamento con ese nombre."
-> Conservar nombre genérico, presentación, concentración y estado
-> Enfocar Nombre comercial
-> Corregir el nombre, por ejemplo: Alivion Nuevo
-> Guardar nuevamente
-> Medicamento registrado correctamente
```

El prototipo no agrega un segundo Alivion. El resumen de error usa `role="alert"`; el campo Nombre comercial recibe `aria-invalid="true"`; el mensaje específico está relacionado mediante `aria-describedby`; y el foco vuelve al campo para facilitar la corrección. Los otros valores permanecen escritos.

## 16. Regla de unicidad

La unicidad se simula únicamente en JavaScript y se aplica al nombre comercial. La normalización realiza:

- `trim` de espacios externos.
- Conversión a minúsculas para ignorar mayúsculas y minúsculas.
- Separación Unicode y eliminación de marcas diacríticas para ignorar acentos.

Por lo tanto, estos tres valores se consideran el mismo medicamento:

```text
ALIVION
alivion
" Alivion "
```

Presentación, concentración y nombre genérico no forman parte de la clave de unicidad simulada.

## 17. Cambio de vigencia

El control de vigencia solo aparece al Administrador. El flujo de desactivación es:

```text
ACTIVO
-> Desactivar
-> Confirmación: medicamento, estado actual, estado destino y efecto
-> Cancelar o Confirmar desactivación
-> INACTIVO únicamente al confirmar
```

Abrir el diálogo no modifica el medicamento. Cancelar o presionar Escape cierra la confirmación, conserva `ACTIVO` y devuelve el foco al botón que abrió el diálogo. Confirmar cambia el estado en memoria a `INACTIVO`, actualiza la tabla o tarjeta y anuncia que el medicamento fue desactivado correctamente.

El flujo inverso usa Activar, muestra `INACTIVE / INACTIVO` como origen y `ACTIVE / ACTIVO` como destino, y solo aplica el cambio al confirmar.

## 18. Accesibilidad

El prototipo incluye las siguientes prácticas:

- Estructura semántica con `header`, `main`, `section`, `form`, tabla y artículos móviles.
- Encabezados ordenados y títulos asociados a las vistas.
- Enlace para saltar al contenido principal.
- Etiqueta visible y asociada con cada campo.
- Controles nativos para botones, entradas y selector.
- Indicador de foco visible de alto contraste.
- Orden de teclado equivalente al orden visual.
- Regiones `aria-live` para búsqueda, éxito y cambio de estado.
- Resumen de error con `role="alert"`.
- Errores específicos vinculados mediante `aria-describedby`.
- `aria-invalid="true"` aplicado al campo con error.
- Estados ACTIVO e INACTIVO expresados con texto y descripción.
- Nombre accesible de vigencia con acción y medicamento.
- Diálogo con `role="dialog"`, `aria-modal="true"`, título y descripción asociados.
- Foco inicial en Cancelar para reducir confirmaciones accidentales.
- Ciclo de Tab y Shift+Tab dentro del diálogo.
- Cierre mediante Escape sin modificar datos.
- Retorno de foco al disparador después de cerrar el diálogo.
- Preferencia `prefers-reduced-motion` respetada en CSS.

Estas medidas permiten demostrar accesibilidad básica dentro del alcance académico. No equivalen a una auditoría con lector de pantalla ni a una certificación WCAG.

## 19. Responsive

La hoja de estilos utiliza una base desktop con adaptación progresiva:

- Antes de 900 px, el formulario pasa de dos áreas a una sola columna de contenido.
- Antes de 700 px, la tabla deja de mostrarse y se habilitan las tarjetas móviles.
- Hasta 430 px, se reducen márgenes sin reducir el área táctil.
- Hasta 399 px, acciones de formulario, éxito y diálogo se apilan.
- Hasta 359 px, buscador, botones y cabecera de tarjeta se apilan para proteger el ancho mínimo.
- Entre 400 y 430 px, las acciones pueden compartir una fila cuando caben completas.

No se fijan anchos superiores al viewport móvil, los campos usan `min-width: 0`, los textos pueden envolver y no existe una tabla horizontal operativa entre 320 y 430 px.

## 20. Capturas y evidencia

El archivo navegable permite obtener las cuatro evidencias mínimas siguientes:

1. `semana-11-desktop-camino-feliz.png`: ancho igual o superior a 900 px, pantalla de alta exitosa de Calmofin o catálogo con Calmofin visible.
2. `semana-11-mobile-camino-feliz.png`: ancho de 375 px, tarjeta de Calmofin después del alta.
3. `semana-11-desktop-error-duplicado.png`: ancho igual o superior a 900 px, resumen de error y Nombre comercial Alivion marcado.
4. `semana-11-mobile-error-duplicado.png`: ancho de 375 px, formulario de una columna con el mismo error y los demás valores conservados.

Si estas imágenes no están presentes en `docs/imagenes/`, deben tomarse manualmente desde `semana-11-prototipo.html` con las herramientas responsive del navegador. La ausencia de capturas automáticas no afecta la navegación del prototipo, pero las imágenes siguen siendo evidencia pendiente para la presentación de la tarea.

## 21. Criterios verificables

| N.° | Área | Prueba | Resultado esperado |
|---:|---|---|---|
| 1 | Ejecución | Abrir `semana-11-prototipo.html` mediante `file:///`. | El prototipo carga sin servidor ni conexión externa. |
| 2 | Navegación | Recorrer las vistas con mouse. | Catálogo, alta, éxito y confirmación son navegables. |
| 3 | Teclado | Usar Tab, Shift+Tab, Enter y Espacio. | Las acciones principales pueden operarse con teclado. |
| 4 | Rol | Seleccionar Administrador. | Se muestran alta y cambio de vigencia. |
| 5 | Rol | Seleccionar Médico. | Solo se muestran consulta y búsqueda. |
| 6 | Rol | Seleccionar Enfermera. | Solo se muestran consulta y búsqueda. |
| 7 | Rol | Cambiar de Administrador a Médico dentro del alta. | El prototipo vuelve al catálogo y oculta controles administrativos. |
| 8 | Búsqueda | Buscar `Alivion`. | Se muestran coincidencias por nombre comercial. |
| 9 | Búsqueda | Buscar `Compuesto Alfa`. | Se muestra Alivion por nombre genérico. |
| 10 | Búsqueda | Buscar `Calmofin` antes de registrarlo. | Aparece el estado sin resultados con el término. |
| 11 | Recuperación | Activar Limpiar búsqueda. | Vuelve el catálogo completo y el foco regresa al buscador. |
| 12 | Alta | Registrar Calmofin con todos los datos válidos. | Se muestra la vista de alta exitosa. |
| 13 | Alta | Volver al catálogo después del éxito. | Calmofin aparece entre los medicamentos. |
| 14 | Alta | Buscar `Compuesto Gamma` después del alta. | Se encuentra Calmofin por nombre genérico. |
| 15 | Duplicado | Intentar registrar `Alivion`. | No se agrega un segundo medicamento. |
| 16 | Normalización | Probar `ALIVION`, `alivion` y ` Alivion `. | Los tres intentos se detectan como duplicados. |
| 17 | Error | Revisar Nombre comercial tras duplicado. | Se muestra `Ya existe un medicamento con ese nombre.` junto al campo. |
| 18 | Error | Inspeccionar el campo duplicado. | Tiene `aria-invalid="true"` y error en `aria-describedby`. |
| 19 | Error | Revisar los otros campos tras duplicado. | Nombre genérico, presentación, concentración y estado se conservan. |
| 20 | Foco | Guardar un nombre duplicado mediante teclado. | El foco llega a Nombre comercial. |
| 21 | Corrección | Cambiar a `Alivion Nuevo` y guardar. | El alta se completa correctamente. |
| 22 | Vigencia | Pulsar Desactivar Alivion. | Se abre la confirmación y Alivion sigue ACTIVO. |
| 23 | Modal | Revisar la confirmación. | Muestra medicamento, estado actual, destino y efecto. |
| 24 | Cancelación | Pulsar Cancelar. | El diálogo cierra y no cambia la vigencia. |
| 25 | Escape | Abrir la confirmación y pulsar Escape. | Cierra sin modificar y devuelve el foco al disparador. |
| 26 | Modal | Recorrer con Tab y Shift+Tab. | El foco permanece entre Cancelar y Confirmar. |
| 27 | Vigencia | Confirmar la desactivación. | Alivion cambia a INACTIVO y se anuncia el éxito. |
| 28 | Estado | Revisar tarjeta o fila desactivada. | INACTIVO y `No vigente ni seleccionable` aparecen como texto. |
| 29 | Región viva | Ejecutar búsqueda, alta o cambio de vigencia. | El resultado importante se comunica mediante `aria-live` o `role="status"`. |
| 30 | Responsive | Revisar a 320 px. | No existe scroll horizontal obligatorio para operar. |
| 31 | Responsive | Revisar a 320, 375 y 430 px. | El catálogo usa tarjetas y no una tabla horizontal. |
| 32 | Formulario móvil | Abrir alta entre 320 y 430 px. | Todos los campos permanecen en una columna. |
| 33 | Área táctil | Medir botones, entradas y selectores en móvil. | Los controles principales mantienen al menos 44 px de alto. |
| 34 | Texto | Usar nombres o mensajes largos. | El texto envuelve y no se trunca con puntos suspensivos. |
| 35 | Desktop | Revisar a 900 px o más. | Se muestra una tabla profesional y el contenido conserva ancho máximo razonable. |

## 22. Limitaciones

- Es un prototipo académico de navegación, no la aplicación real del Micro-HIS.
- Todos los medicamentos y compuestos mostrados son ficticios.
- No contiene pacientes, recetas, diagnósticos ni información clínica identificable.
- No consume API REST, no ejecuta PHP y no modifica la base de datos.
- Los datos agregados y cambios de vigencia se pierden al recargar el archivo.
- El selector de roles es una simulación visual y no autenticación o autorización real.
- No se realizaron pruebas formales con personas usuarias, lector de pantalla o dispositivos físicos.
- Las prácticas incorporadas no permiten declarar cumplimiento WCAG completo.
- El diagrama PlantUML se entrega como fuente `.puml`; puede renderizarse externamente sin instalar software en este proyecto.

## 23. Conclusión

El prototipo navegable de Semana 11 integra el diseño UX de Semana 8, las correcciones prioritarias de Semana 9 y la propuesta responsive de Semana 10. Permite demostrar búsqueda, alta, unicidad, confirmación de vigencia, camino feliz y recuperación del error crítico de duplicidad en desktop y móvil. La solución permanece aislada en `docs/`, utiliza únicamente HTML, CSS y JavaScript local, y no altera backend, API, base de datos ni funcionalidades existentes del Micro-HIS.
