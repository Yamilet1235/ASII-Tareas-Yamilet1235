# Semana 10 - Reglas de breakpoints móviles

**Módulo:** ASII-14 - Catálogo de Medicamentos

**Estudiante:** María Yamilet Lindo Pablo

**Carné:** 1890-23-14827

**Rango principal:** 320 px a 430 px

## Matriz responsive

| Rango | Diseño | Navegación | Formulario | Listado | Acciones | Justificación |
|---|---|---|---|---|---|---|
| **320-359 px** | Una columna compacta; 12 px de margen lateral; texto y valores pueden envolver; sin alturas fijas. | Encabezado reducido; Volver conserva etiqueta textual; la tarea actual permanece visible. | Todos los campos en una columna; 12 px entre campos; etiqueta y ayuda persistentes; controles de al menos 44 px. | Tarjetas a ancho completo; nombre y vigencia en bloques si no caben; presentación y concentración debajo; sin tabla ni scroll horizontal. | Botones de formulario, confirmación y recuperación apilados a ancho completo; 8 px entre controles. | Protege legibilidad y evita compresión en el ancho mínimo de 320 px. |
| **360-399 px** | Una columna con 16 px laterales y mayor separación entre secciones. | Encabezado simple; acción Registrar visible sin desplazar horizontalmente; orden de foco igual al visual. | Una columna; ayudas completas; Guardar y Cancelar siguen apilados cuando las etiquetas compiten. | Tarjetas con nombre y vigencia en la misma fila cuando caben; metadatos debajo. | Buscar puede compartir fila con el campo; acciones largas permanecen a ancho completo. | Aprovecha espacio sin introducir una segunda columna ni aumentar carga cognitiva. |
| **400-430 px** | Una columna amplia con 18 px laterales; tarjetas con más respiración. | Encabezado y acción corta pueden compartir fila; no se ocultan etiquetas. | Una columna; Presentación y Concentración siguen separadas verticalmente para mantener ayudas claras. | Nombre y vigencia en una fila; presentación y concentración agrupadas; nombre genérico en línea secundaria. | Cancelar y Confirmar pueden compartir fila si cada control conserva altura mínima y texto íntegro; Reintentar sigue destacado. | Usa el ancho adicional para reducir scroll vertical sin convertir la vista en diseño de escritorio. |
| **Más de 430 px** | Comportamiento progresivo de la evidencia; cada propuesta móvil conserva ancho máximo de 430 px. | La navegación documental puede expandirse, pero la navegación interna móvil no cambia. | Se mantiene una columna hasta que una futura implementación defina un breakpoint fuera del alcance de Semana 10. | Las tarjetas no regresan automáticamente a tabla dentro de esta propuesta. | Las acciones pueden alinearse cuando siguen siendo legibles y distinguibles. | Evita inventar un rediseño de tablet o escritorio; el objetivo evaluado termina en 430 px. |

## Reglas transversales

### Espaciado y controles

- Margen de contenido: 12 px en 320-359, 16 px en 360-399 y 18 px en 400-430.
- Separación mínima entre controles: 8 px.
- Separación recomendada entre grupos de contenido: 16 px.
- Altura mínima de botones, entradas y selectores: 44 px.
- El área táctil no depende de un enlace de texto pequeño aislado.

### Texto y desbordamiento

- No se usa truncamiento con puntos suspensivos para medicamento, presentación, concentración o mensajes.
- Los textos largos envuelven con `overflow-wrap: anywhere` cuando sea necesario.
- Se conserva tamaño de texto base legible; no se reduce texto para forzarlo en una sola línea.
- Las etiquetas permanecen visibles después de escribir.
- El placeholder es solo apoyo y no contiene la única instrucción.

### Tarjetas del catálogo

- Cada tarjeta representa exactamente un medicamento.
- Orden: Nombre comercial, Vigencia, Presentación + Concentración, Nombre genérico, Acción.
- Presentación y concentración se muestran juntas, separadas visualmente por `·`, pero conservan significado propio.
- ACTIVO e INACTIVO incluyen texto y explicación; el color es redundante.
- La acción expone conceptualmente `Activar/Desactivar + nombre del medicamento`.
- No existe desplazamiento horizontal para llegar a la acción.

### Formulario

- Siempre una columna entre 320 px y 430 px.
- Todos los campos se identifican como obligatorios antes del envío.
- Nombre comercial explica la regla de unicidad.
- Presentación y Concentración mantienen ejemplos visibles.
- Estado inicial explica la consecuencia de INACTIVO.
- Un error se presenta junto al campo, conserva valores válidos y dirige el foco al primer error al implementarse.

### Confirmación

- El diálogo cabe con 12 px de separación respecto a los bordes a 320 px.
- El título, medicamento, estado actual, estado destino y efecto se muestran sin cortar.
- En 320-399 px las acciones se apilan; en 400-430 px pueden alinearse si caben completas.
- Cancelar o Escape no mutan y devuelven foco al control de origen al implementarse.
- No se actualiza la vigencia visualmente antes de la respuesta exitosa.

### Conexión limitada

- El término buscado permanece en el campo.
- Los resultados previos pueden conservarse, identificados como anteriores.
- El mensaje distingue fallo de red de cero coincidencias.
- `Reintentar` es la acción principal.
- Un fallo de vigencia conserva el estado confirmado por el servidor.

## Correspondencia con media queries de la evidencia

La hoja [`semana-10-mobile.css`](semana-10-mobile.css) contiene media queries reales para:

```css
@media (max-width: 430px) { /* base móvil de la evidencia */ }
@media (max-width: 399px) { /* 320-399: acciones apiladas */ }
@media (max-width: 359px) { /* 320-359: espaciado compacto */ }
@media (min-width: 400px) and (max-width: 430px) { /* acciones en línea cuando caben */ }
```

Los marcos de la página documental representan además anchos de 320, 375 y 430 px para comparar las decisiones sin requerir una aplicación móvil nativa.
