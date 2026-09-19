# Semana 9 - Evaluación de usabilidad y accesibilidad

## Portada de evidencia textual

- **Estudiante:** María Yamilet Lindo Pablo
- **Carné:** 1890-23-14827
- **Asignatura:** Análisis de Sistemas II
- **Módulo:** ASII-14 - Catálogo de Medicamentos
- **Sistema:** Micro-HIS Catálogo de Medicamentos
- **Semana:** 9 - Evaluación del diseño, usabilidad y accesibilidad
- **Rama revisada:** `feature/semana-9-usabilidad-accesibilidad`
- **Fecha de evaluación:** 18 de septiembre de 2026
- **Evidencia visual:** [`semana-09-hallazgos.html`](semana-09-hallazgos.html)
- **Checklist detallado:** [`semana-09-checklist.md`](semana-09-checklist.md)

## 1. Objetivo

Evaluar el flujo UX definido en la Semana 8 y la interfaz funcional actual del Catálogo de Medicamentos mediante heurísticas de usabilidad y criterios WCAG pertinentes al alcance académico. La revisión se concentra en navegación por teclado, foco, contraste, etiquetas, mensajes y prevención de errores, con prioridad sobre alta, presentación, concentración, vigencia, búsqueda y unicidad.

Esta evaluación identifica oportunidades y propone criterios verificables. No declara conformidad completa con WCAG ni implementa las correcciones.

## 2. Alcance

La revisión comprende:

- El diseño de Semana 8: flujo por rol, seis wireframes, estilos y reglas documentadas.
- La interfaz funcional servida por `catalog.php`, sus estilos y comportamiento JavaScript.
- Los recorridos de alta, búsqueda, resultados vacíos, mensajes, cambio de vigencia y prevención de duplicados.
- El respaldo funcional mínimo en controlador, casos de uso, dominio y repositorio cuando era necesario demostrar una regla.

Quedan fuera autenticación, autorización, inventario, recetas, pacientes, rediseño visual, implementación de correcciones, auditoría con lector de pantalla real y certificación WCAG integral. No se adelantan actividades de Semana 10 o Semana 11.

## 3. Metodología

1. Se confirmó la rama y el estado inicial del worktree con `git branch --show-current` y `git status --short`.
2. Se efectuó inspección estática del HTML, CSS, JavaScript y PHP indicado en la consigna.
3. Se comparó la propuesta de Semana 8 con el comportamiento que puede demostrarse en la interfaz funcional.
4. Se aplicaron las diez heurísticas de Nielsen y una revisión acotada de WCAG 2.x: 1.3.1, 1.4.1, 1.4.3, 2.1.1, 2.4.3, 2.4.7, 3.3.1, 3.3.2, 3.3.3, 3.3.4, 4.1.2 y 4.1.3.
5. Se calcularon relaciones de contraste con la fórmula de luminancia relativa de WCAG a partir de colores declarados en los CSS. No se estimaron colores ausentes.
6. Cada hallazgo se clasificó por origen, impacto y prioridad, y se convirtió en una corrección propuesta con aceptación observable.

Escala de prioridad:

- **ALTA:** puede causar una modificación sensible accidental o impedir que una persona complete/corrija el alta.
- **MEDIA:** dificulta una tarea importante, su comprensión o el acceso equivalente, pero existe una vía alternativa.
- **BAJA:** afecta principalmente un artefacto estático o una situación secundaria sin bloquear la interfaz funcional actual.

## 4. Elementos inspeccionados

### Diseño y trazabilidad de Semana 8

- `docs/SEMANA-08-DISENO-UX.md`
- `docs/semana-08-wireframes.html`
- `docs/semana-08-wireframes.css`
- `docs/semana-08-user-flow.puml`

### Interfaz funcional y comportamiento

- `src/Presentation/views/catalog.php`
- `public/assets/styles.css`
- `public/assets/app.js`
- `public/index.php`
- `public/router.php`

### Reglas relacionadas con formularios, búsqueda, mensajes y vigencia

- `src/Presentation/MedicationController.php`
- `src/Presentation/MedicationWebRequest.php`
- `src/Application/UseCase/RegisterMedication.php`
- `src/Application/UseCase/SearchMedications.php`
- `src/Application/UseCase/ToggleMedicationStatus.php`
- `src/Domain/Medication.php`
- `src/Domain/MedicationNameNormalizer.php`
- `src/Persistence/PdoMedicationRepository.php`

## 5. Distinción de fuentes

### A. Problemas encontrados en los wireframes de Semana 8

- **A11Y-04:** el diálogo estático expresa la confirmación visual, pero no demuestra modalidad ni gestión de foco para teclado.

### B. Problemas encontrados en la interfaz funcional actual

- **UX-01:** cambio de vigencia sin confirmación previa.
- **A11Y-01:** errores globales sin asociación al campo ni foco en el primer error.
- **UX-02:** obligatoriedad y ayuda insuficientes antes del envío.
- **UX-03:** el catálogo vacío y una búsqueda sin coincidencias comparten el mismo mensaje.
- **A11Y-02:** acciones de vigencia repetidas no incluyen el medicamento en su nombre accesible.
- **UX-04:** el error de búsqueda asíncrona no ofrece una instrucción de recuperación.
- **A11Y-03:** el texto placeholder usa una combinación de contraste 2.56:1.

### C. Elementos que ya cumplen correctamente dentro del alcance

- Los controles funcionales son elementos HTML nativos y su orden DOM permite recorrer alta, búsqueda y tabla con teclado (`catalog.php:71-155`).
- Todos los campos de alta tienen etiqueta envolvente; el buscador usa `label for="q"` (`catalog.php:74-93`, `105-110`).
- Existe foco personalizado visible para `input` y `select`; no se elimina globalmente el foco de botones o enlaces (`styles.css:188-200`).
- La vigencia no depende solo del color: muestra `ACTIVO`/`INACTIVO` y una explicación textual (`catalog.php:134-139`).
- Los mensajes globales usan `role="status"`, `role="alert"` y regiones `aria-live` (`catalog.php:53-62`, `110`, `113`).
- La búsqueda informa actividad mediante texto y `aria-busy`; los botones de mutación se deshabilitan durante el proceso (`app.js:119-121`, `167-169`).
- Al terminar un cambio de vigencia asíncrono se intenta devolver el foco al botón equivalente (`app.js:192-196`).
- La búsqueda puede limpiarse por enlace y luego devuelve el foco al campo (`catalog.php:109`; `app.js:233-239`).
- Los valores enviados se reconstruyen en el formulario después de un error de servidor (`MedicationController.php:26-28`, `80-86`; `catalog.php:74-92`).
- La unicidad no depende solo de la interfaz: se normaliza el nombre, se consulta antes del alta y existe manejo de violación única (`RegisterMedication.php:34-41`; `PdoMedicationRepository.php:48-55`, `86-107`).
- Los campos obligatorios también se validan en dominio tras aplicar `trim` (`Medication.php:19-45`).
- Hay soporte para reducción de movimiento (`styles.css:290-292`).

## 6. Checklist de heurísticas

| Heurística | Estado | Evidencia resumida | Resultado |
|---|---|---|---|
| Visibilidad del estado del sistema | CUMPLE PARCIALMENTE | Contadores, `aria-live`, textos de proceso y éxito; la búsqueda fallida no indica cómo reintentar. | UX-04 |
| Correspondencia con el lenguaje del usuario | CUMPLE | Se muestran Medicamento, Presentación, Concentración, ACTIVO/INACTIVO y efectos de vigencia. | Sin hallazgo adicional. |
| Control y libertad | CUMPLE PARCIALMENTE | La búsqueda se limpia; la interfaz funcional no permite cancelar el cambio de vigencia antes de aplicarlo. | UX-01 |
| Consistencia y estándares | CUMPLE PARCIALMENTE | La terminología es consistente; los estados vacíos no distinguen catálogo vacío de búsqueda sin resultados. | UX-03 |
| Prevención de errores | NO CUMPLE | El alta valida y previene duplicados, pero una acción de vigencia se ejecuta sin confirmación. | UX-01 |
| Reconocimiento frente a memorización | CUMPLE PARCIALMENTE | Hay etiquetas y ejemplos, pero faltan ayudas persistentes e indicación visible de obligatoriedad. | UX-02 |
| Flexibilidad y eficiencia | CUMPLE | Búsqueda incremental, envío explícito, limpieza y controles nativos. | Sin hallazgo adicional. |
| Diseño claro y minimalista | CUMPLE | Alta y catálogo están separados en dos paneles, sin datos clínicos ajenos al módulo. | Sin hallazgo adicional. |
| Recuperación ante errores | CUMPLE PARCIALMENTE | Se conservan valores y hay alertas; faltan asociación, foco y recuperación explícita de búsqueda. | A11Y-01, UX-04 |
| Ayuda contextual | CUMPLE PARCIALMENTE | Semana 8 propone ayudas, pero la interfaz funcional depende principalmente de placeholders. | UX-02 |

El detalle criterio por criterio está en [`semana-09-checklist.md`](semana-09-checklist.md).

## 7. Checklist de accesibilidad

| Área | Estado | Evidencia resumida | Resultado |
|---|---|---|---|
| Teclado | CUMPLE PARCIALMENTE | La interfaz funcional usa controles nativos y orden lógico; el diálogo del wireframe no demuestra ciclo, Escape ni retorno de foco. | A11Y-04 |
| Foco visible | CUMPLE PARCIALMENTE | Entradas y select tienen foco personalizado; tras errores de servidor no se dirige al campo afectado. | A11Y-01 |
| Orden de foco | CUMPLE | El DOM funcional sigue alta, búsqueda, resultados y acciones sin `tabindex` positivo. | Sin hallazgo adicional. |
| Contraste de texto principal | CUMPLE | Relaciones calculadas: 15.55:1 texto principal/papel, 6.70:1 botón, 6.05:1 error y 5.10:1 estado inactivo. | Sin hallazgo adicional. |
| Contraste de placeholder | NO CUMPLE | `#94a3b8` sobre `#ffffff` produce 2.56:1. | A11Y-03 |
| Etiquetas y nombres | CUMPLE PARCIALMENTE | Campos etiquetados; acciones repetidas no nombran el medicamento. | A11Y-02 |
| Campos obligatorios | CUMPLE PARCIALMENTE | `required` comunica la regla al navegador, pero no existe instrucción visible previa. | UX-02 |
| Mensajes y estado | CUMPLE PARCIALMENTE | Alertas y estados vivos presentes; errores de campo no están relacionados programáticamente. | A11Y-01 |
| Prevención de errores | NO CUMPLE | No hay confirmación antes de activar/desactivar. | UX-01 |
| Uso no exclusivo del color | CUMPLE | Estado, error y éxito contienen texto; la vigencia tiene etiqueta y descripción. | Sin hallazgo adicional. |

## 8. Contraste calculado

| Uso y fuente | Primer plano | Fondo | Relación | Evaluación acotada |
|---|---:|---:|---:|---|
| Texto principal funcional | `#172033` | `#f8fafc` | 15.55:1 | Supera 4.5:1. |
| Texto secundario funcional sobre blanco | `#64748b` | `#ffffff` | 4.76:1 | Supera 4.5:1. |
| Texto secundario funcional sobre papel | `#64748b` | `#f8fafc` | 4.55:1 | Supera 4.5:1. |
| Texto blanco del botón primario | `#ffffff` | `#1d4ed8` | 6.70:1 | Supera 4.5:1. |
| Texto de error | `#b42318` | `#fef3f2` | 6.05:1 | Supera 4.5:1. |
| Texto de estado inactivo | `#9a5b13` | `#fff7ed` | 5.10:1 | Supera 4.5:1. |
| Placeholder funcional | `#94a3b8` | `#ffffff` | 2.56:1 | No alcanza 4.5:1. |
| Texto secundario del wireframe | `#666666` | `#ffffff` | 5.74:1 | Supera 4.5:1. |
| Texto blanco del wireframe | `#ffffff` | `#292929` | 14.55:1 | Supera 4.5:1. |
| Texto gris de barra del wireframe | `#d5d5d5` | `#292929` | 9.91:1 | Supera 4.5:1. |

Los resultados se limitan a pares explícitos y fondos sólidos identificables. No se infiere conformidad visual completa de todos los estados, tamaños, bordes o composiciones.

## 9. Hallazgos

### UX-01 - Cambio de vigencia sin confirmación

- **Origen:** B - interfaz funcional actual.
- **Componente/pantalla:** catálogo, columna Acción, control de vigencia.
- **Descripción:** Activar o Desactivar envía inmediatamente la mutación; no existe una etapa para revisar medicamento, estado destino y efecto, ni una opción Cancelar.
- **Evidencia concreta:** `catalog.php:140-147` contiene un formulario POST cuyo único control visible es el botón; `app.js:208-224` intercepta el envío y llama directamente a `submitMutation`; `ToggleMedicationStatus.php:33-37` calcula y persiste el estado opuesto. En contraste, `semana-08-wireframes.html:127-132` sí muestra diálogo y Cancelar.
- **Criterio/heurística:** prevención de errores, control y libertad; WCAG 3.3.4 como referencia académica para confirmación de modificación de datos.
- **Impacto funcional:** un envío accidental cambia la vigencia y, por tanto, si el medicamento aparece como seleccionable. Afecta el control de catálogo sin eliminar el registro.
- **Prioridad:** **ALTA**, porque la acción sensible se aplica con una sola activación y no puede cancelarse antes de persistir.
- **Corrección propuesta:** incorporar una confirmación accesible que indique nombre, estado actual, estado resultante y efecto; incluir Cancelar y una acción específica Activar/Desactivar.
- **Criterio de aceptación verificable:** al accionar Activar/Desactivar, el estado no cambia hasta confirmar; Cancelar y Escape cierran sin mutación; confirmar actualiza una sola vez y anuncia el resultado; el foco vuelve a la acción del mismo medicamento.

### A11Y-01 - Errores globales sin relación con campos ni foco dirigido

- **Origen:** B - interfaz funcional actual.
- **Componente/pantalla:** alta de medicamento y validación de unicidad.
- **Descripción:** los errores del servidor aparecen como alertas globales, pero el campo causante no recibe mensaje asociado, `aria-invalid`, `aria-describedby` ni foco. La persona debe deducir qué control corregir.
- **Evidencia concreta:** `MedicationController.php:63-65` agrega texto a una lista global; `catalog.php:53-60` lo imprime fuera del formulario; los campos de `catalog.php:74-92` no reciben estado de error. `app.js:182-205` actualiza mensajes y catálogo, pero solo gestiona foco para un toggle exitoso (`192-196`). Semana 8 exigía asociación y primer foco en `SEMANA-08-DISENO-UX.md:204-210`.
- **Criterio/heurística:** recuperación ante errores; WCAG 3.3.1, 3.3.3, 1.3.1 y 2.4.3.
- **Impacto funcional:** dificulta corregir nombre, nombre genérico, presentación o concentración; el caso de duplicado compromete la recuperación de la regla de unicidad aunque el backend sí la aplica.
- **Prioridad:** **ALTA**, porque puede impedir completar el alta de forma autónoma, especialmente con lector de pantalla o solo teclado.
- **Corrección propuesta:** mapear cada validación al campo, conservar la alerta resumen, marcar el control inválido, asociar ayuda específica y llevar el foco al primer error.
- **Criterio de aceptación verificable:** tras un alta inválida, cada error aparece junto a su campo y está referenciado por `aria-describedby`; el control tiene `aria-invalid="true"`; el foco llega al primer campo inválido; los valores correctos permanecen; un duplicado enfoca Nombre comercial e indica probar otro nombre.

### UX-02 - Obligatoriedad y ayuda insuficientes antes del alta

- **Origen:** B - interfaz funcional actual.
- **Componente/pantalla:** formulario de alta, especialmente Presentación y Concentración.
- **Descripción:** todos los campos son obligatorios, pero no hay una instrucción visible ni indicador en sus etiquetas. Los ejemplos se presentan como placeholder y desaparecen al escribir; tampoco explican de forma persistente presentación, concentración o efecto de Estado inicial.
- **Evidencia concreta:** `catalog.php:74-93` declara `required`, pero las etiquetas no incluyen indicador ni texto introductorio; `catalog.php:82` y `85` usan únicamente `placeholder` como ejemplo. El wireframe sí presenta instrucción y ayudas en `semana-08-wireframes.html:77-84`.
- **Criterio/heurística:** prevención de errores, reconocimiento frente a memorización y ayuda contextual; WCAG 3.3.2.
- **Impacto funcional:** aumenta intentos fallidos y dudas en nombre genérico, presentación, concentración y estado inicial.
- **Prioridad:** **MEDIA**, porque el navegador bloquea vacíos mediante `required`, pero la regla solo se hace evidente al intentar enviar.
- **Corrección propuesta:** indicar antes del formulario que todos los campos son obligatorios o marcar cada uno textualmente; añadir ayudas persistentes breves con ejemplos y explicar el efecto de INACTIVO.
- **Criterio de aceptación verificable:** antes de enviar se identifica visual y programáticamente la obligatoriedad; Presentación y Concentración mantienen ayuda visible asociada aun después de escribir; Estado inicial explica la consecuencia de INACTIVO.

### UX-03 - Estado vacío indistinguible de búsqueda sin resultados

- **Origen:** B - interfaz funcional actual.
- **Componente/pantalla:** resultados del catálogo y búsqueda.
- **Descripción:** cuando no hay filas se presenta el mismo título y mensaje tanto para un catálogo sin registros como para una consulta sin coincidencias.
- **Evidencia concreta:** `catalog.php:114-118` solo comprueba `$medications === []` y siempre muestra “Sin resultados / No hay medicamentos que coincidan con la consulta”; el template cliente repite el texto en `157-161`. El diseño de Semana 8 diferencia ambos estados en `semana-08-wireframes.html:147-160`.
- **Criterio/heurística:** visibilidad del estado, correspondencia, consistencia y recuperación.
- **Impacto funcional:** la persona no sabe si debe registrar el primer medicamento o corregir/limpiar la búsqueda; afecta consulta y búsqueda.
- **Prioridad:** **MEDIA**, porque la tarea puede recuperarse con Limpiar cuando existe consulta, pero la causa no se comunica correctamente.
- **Corrección propuesta:** condicionar el contenido por `$search`: catálogo vacío con acción de alta autorizada; consulta vacía con término, contador cero y opción Limpiar.
- **Criterio de aceptación verificable:** con catálogo vacío y `q` vacío se anuncia “No hay medicamentos registrados”; con `q=Nuberal` sin coincidencias se anuncia “No se encontraron medicamentos para ‘Nuberal’” y se ofrece Limpiar; ambos estados son distinguibles sin color.

### A11Y-02 - Acciones de vigencia con nombres accesibles repetidos

- **Origen:** B - interfaz funcional actual.
- **Componente/pantalla:** tabla del catálogo, columna Acción.
- **Descripción:** varios botones comparten el nombre accesible “Activar” o “Desactivar” sin incluir el medicamento. En navegación por controles o reconocimiento de voz, la acción queda ambigua fuera del contexto visual de la fila.
- **Evidencia concreta:** `catalog.php:124-149` crea un botón por fila y su texto en `144-146` depende solo del estado; no existe `aria-label` con el nombre del medicamento.
- **Criterio/heurística:** reconocimiento frente a memorización; WCAG 2.4.6 y 4.1.2.
- **Impacto funcional:** aumenta el riesgo de cambiar la vigencia del medicamento equivocado.
- **Prioridad:** **MEDIA**, porque la tabla aporta contexto visual y estructural, pero la lista de controles contiene nombres repetidos.
- **Corrección propuesta:** conservar el texto visual breve y aportar un nombre accesible específico, por ejemplo “Desactivar Alivion”.
- **Criterio de aceptación verificable:** cada botón de vigencia expone una combinación única de acción y nombre; el nombre sigue siendo correcto después de actualizar el catálogo.

### UX-04 - Fallo de búsqueda sin instrucción de recuperación

- **Origen:** B - interfaz funcional actual.
- **Componente/pantalla:** búsqueda asíncrona.
- **Descripción:** ante un fallo de red o respuesta no válida se informa que la búsqueda no se completó, pero no se indica que los resultados visibles pueden ser anteriores ni cómo reintentar.
- **Evidencia concreta:** `app.js:131-143` captura el fallo y fija únicamente “No se pudo completar la búsqueda.”; no crea acción Reintentar. Semana 8 especifica mantener contexto y permitir reintento en `semana-08-user-flow.puml:25-28`.
- **Criterio/heurística:** visibilidad del estado y recuperación ante errores; WCAG 3.3.3 y 4.1.3.
- **Impacto funcional:** puede confundirse el catálogo conservado con el resultado del nuevo término y dificulta recuperar la consulta.
- **Prioridad:** **MEDIA**, porque el usuario puede volver a pulsar Buscar, aunque el mensaje no lo explica.
- **Corrección propuesta:** indicar que no se actualizaron los resultados y ofrecer Reintentar preservando el término.
- **Criterio de aceptación verificable:** al simular un fallo, un mensaje anunciado informa “No se actualizaron los resultados” y ofrece Reintentar; el término y los resultados previos se conservan; al reintentar con éxito se limpia el error.

### A11Y-03 - Contraste insuficiente del placeholder

- **Origen:** B - interfaz funcional actual.
- **Componente/pantalla:** buscador y ejemplos del formulario.
- **Descripción:** el placeholder gris claro no alcanza el contraste mínimo de texto normal sobre el fondo blanco.
- **Evidencia concreta:** `styles.css:188-199` define fondo `white` y placeholder `#94a3b8`; la relación calculada es **2.56:1**. El buscador depende visualmente de ese placeholder para describir el alcance porque su etiqueta está oculta (`catalog.php:105-108`).
- **Criterio/heurística:** diseño claro; WCAG 1.4.3.
- **Impacto funcional:** dificulta descubrir que la búsqueda admite nombre comercial o genérico y leer ejemplos de presentación/concentración.
- **Prioridad:** **MEDIA**, porque la etiqueta accesible existe y los campos de alta tienen etiqueta visible, pero la instrucción visible de búsqueda pierde legibilidad.
- **Corrección propuesta:** usar un color de placeholder con al menos 4.5:1 o trasladar la instrucción principal a una etiqueta visible de contraste suficiente.
- **Criterio de aceptación verificable:** la combinación final del placeholder alcanza al menos 4.5:1; el alcance de búsqueda permanece visible fuera del placeholder y puede leerse al 200% de zoom.

### A11Y-04 - El diálogo del wireframe no demuestra gestión de foco

- **Origen:** A - wireframes de Semana 8.
- **Componente/pantalla:** Wireframe 04, confirmación de cambio de vigencia.
- **Descripción:** el diálogo cuenta con nombre y descripción, pero el artefacto no declara `aria-modal="true"`, no implementa apertura/cierre y no demuestra foco inicial, ciclo con Tab, cierre con Escape ni retorno de foco.
- **Evidencia concreta:** `semana-08-wireframes.html:127-133` usa `role="dialog"`, `aria-labelledby` y `aria-describedby`, pero carece de `aria-modal`; el documento solo enlaza CSS (`7`) y todos sus botones son estáticos. La anotación describe Cancelar, no comportamiento de teclado (`135-137`).
- **Criterio/heurística:** control y libertad; WCAG 2.1.1, 2.4.3 y 4.1.2.
- **Impacto funcional:** si se implementara literalmente, una persona de teclado podría recorrer contenido fuera del diálogo o perder su posición al cerrarlo.
- **Prioridad:** **BAJA**, porque es una limitación de la evidencia estática y el diálogo aún no existe en la interfaz funcional; debe resolverse al implementar UX-01.
- **Corrección propuesta:** documentar e implementar patrón de diálogo modal accesible junto con la futura confirmación.
- **Criterio de aceptación verificable:** al abrir, el foco entra al diálogo; Tab y Shift+Tab permanecen en sus controles; Escape y Cancelar cierran sin cambios; el fondo no queda operable; al cerrar, el foco vuelve al botón que abrió la confirmación.

## 10. Backlog priorizado

| Orden | ID | Prioridad | Hallazgo | Impacto | Corrección propuesta | Criterio verificable resumido |
|---:|---|---|---|---|---|---|
| 1 | UX-01 | ALTA | Vigencia sin confirmación | Activación/desactivación accidental. | Diálogo con medicamento, efecto, Cancelar y acción específica. | Sin cambio antes de confirmar; Cancelar/Escape no mutan; foco retorna. |
| 2 | A11Y-01 | ALTA | Errores sin campo ni foco | Bloquea recuperación del alta y duplicados. | Errores por campo, relaciones ARIA y foco inicial. | Mensaje asociado, `aria-invalid`, valores conservados y foco al primer error. |
| 3 | UX-02 | MEDIA | Reglas y ayudas no persistentes | Errores en presentación, concentración y estado. | Indicar obligatorios y ayudas asociadas. | Reglas visibles antes de enviar y ayudas presentes al escribir. |
| 4 | UX-03 | MEDIA | Estados vacíos indistintos | Confusión entre alta inicial y búsqueda. | Mensajes/acciones según catálogo o término. | Dos causas muestran textos y recuperaciones diferentes. |
| 5 | A11Y-02 | MEDIA | Botones de vigencia ambiguos | Riesgo de actuar sobre otro medicamento. | Nombre accesible con acción + medicamento. | Cada acción tiene un nombre único y actualizado. |
| 6 | UX-04 | MEDIA | Búsqueda fallida sin recuperación | Resultados potencialmente desactualizados. | Mensaje explícito y Reintentar. | Conserva término/resultados e informa que no se actualizaron. |
| 7 | A11Y-03 | MEDIA | Placeholder 2.56:1 | Baja legibilidad de búsqueda y ejemplos. | Color >= 4.5:1 e instrucción persistente. | Medición final >= 4.5:1. |
| 8 | A11Y-04 | BAJA | Modal estático sin gestión de foco | Patrón incompleto para teclado si se implementa literalmente. | Patrón modal accesible al implementar UX-01. | Foco contenido, Escape/Cancelar y retorno al disparador. |

## 11. Criterios verificables consolidados

| Área | Prueba verificable propuesta | Resultado esperado |
|---|---|---|
| Alta obligatoria | Intentar enviar vacío con teclado. | La regla es visible antes del envío; el primer inválido recibe foco y nombre comprensible. |
| Alta con error de servidor | Enviar presentación formada solo por espacios. | Se conserva el resto, Presentación queda marcada y su error se anuncia/asocia. |
| Unicidad | Registrar el mismo nombre con otra combinación de mayúsculas y espacios externos. | No se crea duplicado; Nombre comercial conserva el valor, recibe foco y explicación. |
| Presentación y concentración | Escribir en ambos campos y revisar su ayuda. | Los ejemplos/instrucciones siguen visibles y asociados al control. |
| Búsqueda sin resultados | Buscar `Nuberal` sin coincidencias. | Se anuncia término, cero resultados y una acción para limpiar. |
| Catálogo vacío | Abrir el catálogo sin registros ni término. | Se informa que no existen medicamentos, sin hablar de coincidencias. |
| Fallo de búsqueda | Simular respuesta HTTP fallida. | Se conservan término/resultados previos, se explica el estado y se ofrece reintento. |
| Vigencia | Intentar desactivar un medicamento. | Aparece confirmación con nombre y estado destino; todavía no hay mutación. |
| Cancelación de vigencia | Pulsar Escape y luego probar Cancelar. | El estado no cambia y el foco regresa al disparador. |
| Confirmación de vigencia | Confirmar una vez mediante teclado. | Ocurre una mutación, se actualiza texto ACTIVO/INACTIVO y se anuncia éxito. |
| Nombre accesible de acciones | Inspeccionar botones con lector de pantalla. | Se oye “Activar/Desactivar + nombre del medicamento”. |
| Contraste | Recalcular el color de texto de ayuda/placeholder sobre su fondo. | Todo texto normal evaluado alcanza al menos 4.5:1. |
| Zoom y reflujo | Ampliar a 200% y recorrer alta/búsqueda. | Instrucciones y acciones permanecen visibles y operables sin pérdida de contenido. |

## 12. Trazabilidad con Semana 8

| Regla o evidencia de Semana 8 | Estado en interfaz funcional | Hallazgo relacionado |
|---|---|---|
| Confirmar antes de activar/desactivar (`SEMANA-08-DISENO-UX.md:156-170`) | No implementada; el formulario envía directamente. | UX-01 |
| Errores junto al campo y foco al primero (`204-210`) | Solo alertas globales; no hay foco dirigido. | A11Y-01 |
| Ayudas contextuales (`244-254`) | Sustituidas mayormente por placeholders. | UX-02 |
| Diferenciar catálogo vacío y sin resultados (`318-327`) | Se usa un único mensaje. | UX-03 |
| Recuperar fallo de carga/búsqueda (`145-150`, user flow `25-28`) | Se informa fallo, sin instrucción ni acción específica. | UX-04 |
| Diálogo visual de confirmación (`semana-08-wireframes.html:127-133`) | Buen nombre/descripción, pero sin patrón de foco demostrable. | A11Y-04 |
| Vigencia textual, no solo color (`SEMANA-08-DISENO-UX.md:172`, `190`) | Implementada con etiqueta y explicación. | Cumple en alcance. |
| Conservar datos correctos (`204-210`) | El servidor devuelve `formValues` y la ruta asíncrona no limpia el formulario al fallar. | Cumple parcialmente; A11Y-01 por recuperación incompleta. |
| Unicidad por nombre normalizado (`135`) | Aplicada en caso de uso y repositorio. | Cumple; A11Y-01 afecta solo la comunicación del error. |
| Limpiar búsqueda (`137-154`) | Enlace disponible y retorno de foco al buscador. | Cumple. |
| Estados de progreso (`174-190`) | “Buscando...”, “Guardando...” y “Actualizando...” presentes. | Cumple parcialmente; UX-04 ante fallo. |

## 13. Limitaciones

- La revisión fue estática sobre archivos reales; no se ejecutó una prueba formal con personas usuarias.
- No se utilizó lector de pantalla, analizador automático de accesibilidad ni matriz de navegadores/dispositivos.
- Los ratios documentados cubren combinaciones explícitas seleccionadas, no una certificación de todas las capas visuales.
- Los wireframes son evidencia estática y no un prototipo navegable; A11Y-04 registra lo que no pueden demostrar, no afirma una falla de un diálogo funcional inexistente.
- La ausencia de autenticación/autorización ya estaba documentada en Semana 8 y no se convierte en un hallazgo de usabilidad inventado para esta evaluación.
- No se modificó PHP, JavaScript ni CSS funcional; las correcciones permanecen en backlog según la consigna.

## 14. Conclusión

La evaluación encontró **8 hallazgos reales**: 2 de prioridad ALTA, 5 MEDIA y 1 BAJA. Los riesgos principales son el cambio de vigencia sin confirmación y la recuperación insuficiente de errores de alta y unicidad. También se comprobaron fortalezas: controles nativos, etiquetas, estados textuales, mensajes vivos, conservación de valores, búsqueda limpiable, prevención de duplicados en backend y contrastes adecuados en los pares principales.

Semana 9 queda cubierta como evaluación académica y backlog verificable, sin declarar cumplimiento WCAG completo y sin implementar anticipadamente las correcciones.
