# Semana 9 - Checklist de usabilidad y accesibilidad

**Módulo:** ASII-14 - Catálogo de Medicamentos
**Estudiante:** María Yamilet Lindo Pablo
**Carné:** 1890-23-14827
**Fecha:** 18 de septiembre de 2026

Estados permitidos: **CUMPLE**, **CUMPLE PARCIALMENTE**, **NO CUMPLE**, **NO APLICA**.

## Heurísticas de usabilidad

| Criterio | Estado | Evidencia | Observación |
|---|---|---|---|
| Visibilidad del estado del sistema | CUMPLE PARCIALMENTE | `catalog.php:42-50`, `53-62`, `110`, y `app.js:119-121`, `167-169` muestran conteo, alertas y procesos. | El fallo de búsqueda de `app.js:142` no explica si los resultados quedaron sin actualizar ni cómo recuperarse (UX-04). |
| Correspondencia con el lenguaje del usuario | CUMPLE | `catalog.php:74-93`, `122-146` usa Nombre comercial, Nombre genérico, Presentación, Concentración, ACTIVO/INACTIVO, Activar/Desactivar. | La terminología del catálogo es directa y coincide con Semana 8. |
| Control y libertad | CUMPLE PARCIALMENTE | `catalog.php:109` y `app.js:233-239` permiten limpiar búsqueda y volver al campo. | `catalog.php:140-147` no permite cancelar antes de cambiar vigencia (UX-01). |
| Consistencia y estándares | CUMPLE PARCIALMENTE | La vista mantiene términos y estilos comunes para botones, campos y estados. | `catalog.php:114-118` no distingue catálogo vacío de búsqueda sin coincidencias (UX-03). |
| Prevención de errores | NO CUMPLE | `required`, validación de dominio y unicidad previenen altas inválidas (`catalog.php:74-92`; `Medication.php:19-45`; `RegisterMedication.php:34-41`). | Falta confirmación para la modificación sensible de vigencia (UX-01). |
| Reconocimiento frente a memorización | CUMPLE PARCIALMENTE | Hay etiquetas visibles y placeholders de ejemplo en `catalog.php:74-93`. | No se muestran ayudas persistentes ni una indicación visible de obligatoriedad (UX-02). |
| Flexibilidad y eficiencia de uso | CUMPLE | `app.js:227-231` habilita búsqueda incremental; también hay botón Buscar y enlace Limpiar. | Existen rutas equivalentes para buscar y restablecer el listado. |
| Diseño claro y minimalista | CUMPLE | `catalog.php:64-164` separa alta y catálogo en dos paneles; no solicita datos personales o clínicos. | El alcance visual permanece centrado en medicamentos. |
| Reconocimiento, diagnóstico y recuperación de errores | CUMPLE PARCIALMENTE | `catalog.php:53-60` presenta alertas; `MedicationController.php:80-86` conserva valores. | Los errores no se asocian a campos ni reciben foco, y la búsqueda no ofrece reintento explícito (A11Y-01, UX-04). |
| Ayuda y documentación contextual | CUMPLE PARCIALMENTE | Los placeholders aportan ejemplos; Semana 8 documenta ayudas en `SEMANA-08-DISENO-UX.md:244-254`. | La interfaz funcional no conserva esas ayudas visibles al escribir (UX-02). |

## Accesibilidad y WCAG dentro del alcance

| Criterio | Estado | Evidencia | Observación |
|---|---|---|---|
| Operación por teclado de la interfaz funcional | CUMPLE | Formularios, botones, enlace y select son controles HTML nativos en `catalog.php:71-155`; no hay control solo por puntero en `app.js`. | La revisión estática no sustituye prueba manual con tecnologías de asistencia. |
| Orden lógico de foco | CUMPLE | El DOM recorre alta, búsqueda, resultados y acciones en ese orden; no se usa `tabindex` positivo. | La secuencia coincide con el orden visual general. |
| Foco visible en campos | CUMPLE | `styles.css:199-200` conserva una combinación visible de borde azul y sombra para input/select. | Botones y enlaces mantienen el indicador predeterminado del navegador porque no se elimina globalmente. |
| Foco después de un error de alta | NO CUMPLE | `app.js:182-205` actualiza alertas, pero no enfoca campos inválidos; `catalog.php:74-92` no marca errores. | Contradice la regla de Semana 8 de enfocar el primer error (A11Y-01). |
| Foco después de cambio de vigencia | CUMPLE | `app.js:192-196` busca la acción equivalente después de reemplazar el catálogo y llama `focus()`. | Es un cumplimiento específico del toggle asíncrono exitoso. |
| Gestión de foco en confirmación modal de Semana 8 | NO CUMPLE | `semana-08-wireframes.html:127-133` no tiene `aria-modal`, script, foco inicial, ciclo ni retorno. | Limitación del wireframe estático a resolver junto con UX-01 (A11Y-04). |
| Etiquetas de campos de alta | CUMPLE | `catalog.php:74-93` envuelve cada input/select en su `label`. | La relación nombre-control es programática. |
| Etiqueta del buscador | CUMPLE | `catalog.php:105-107` relaciona `label for="q"` con `id="q"`. | La etiqueta está disponible para tecnología de asistencia. |
| Nombres accesibles de acciones repetidas | CUMPLE PARCIALMENTE | `catalog.php:144-146` da nombre “Activar” o “Desactivar”. | No incluye el medicamento y resulta ambiguo al listar controles (A11Y-02). |
| Campos obligatorios comprensibles | CUMPLE PARCIALMENTE | Todos los controles usan `required` en `catalog.php:74-92`. | Falta instrucción visible previa e indicador persistente (UX-02). |
| Identificación de errores | CUMPLE PARCIALMENTE | Cada error global usa `role="alert"` en `catalog.php:58-60`. | No hay error junto al campo, `aria-invalid` ni `aria-describedby` (A11Y-01). |
| Mensajes de éxito y estado | CUMPLE | `catalog.php:53-62`, `110`, `113` usa `role=status`, `role=alert`, `aria-live` y `aria-atomic`. | Los cambios principales tienen canal programático de anuncio. |
| Recuperación de fallo de búsqueda | CUMPLE PARCIALMENTE | `app.js:142` escribe el fallo en una región `role=status`. | No instruye reintento ni aclara que los resultados no se actualizaron (UX-04). |
| Prevención de cambio accidental de vigencia | NO CUMPLE | `catalog.php:140-147` y `app.js:208-224` envían directamente la mutación. | No existe confirmación o cancelación previa (UX-01). |
| Prevención de duplicados | CUMPLE | `RegisterMedication.php:34-41` y `PdoMedicationRepository.php:48-55`, `86-107` comprueban nombre normalizado y restricción única. | La regla funcional existe; la presentación accesible del error es parcial (A11Y-01). |
| Conservación de valores correctos | CUMPLE | `MedicationController.php:26-28`, `80-86` devuelve `formValues`; `catalog.php:74-92` los representa. | El formulario solo se reinicia tras éxito en `app.js:187-190`. |
| Estado no comunicado solo por color | CUMPLE | `catalog.php:134-139` incluye ACTIVO/INACTIVO y Vigente/No vigente; alertas contienen texto. | El color refuerza, pero no sustituye la información. |
| Contraste de texto principal y secundario | CUMPLE | Cálculos: `#172033/#f8fafc` 15.55:1; `#64748b/#fff` 4.76:1; `#64748b/#f8fafc` 4.55:1. | Los pares revisados superan 4.5:1. |
| Contraste del botón primario | CUMPLE | `#fff/#1d4ed8` produce 6.70:1 según `styles.css:203-214`. | Supera 4.5:1 para texto normal. |
| Contraste del error | CUMPLE | `#b42318/#fef3f2` produce 6.05:1 según `styles.css:153-154`. | El error también contiene texto y `role=alert`. |
| Contraste del estado inactivo | CUMPLE | `#9a5b13/#fff7ed` produce 5.10:1 según `styles.css:243-246`. | El estado también incluye etiqueta textual. |
| Contraste del placeholder | NO CUMPLE | `styles.css:195`, `199`: `#94a3b8` sobre blanco produce 2.56:1. | No alcanza 4.5:1 para texto normal (A11Y-03). |
| Contraste textual de wireframes revisado | CUMPLE | `#666/#fff` 5.74:1, `#fff/#292929` 14.55:1, `#d5d5d5/#292929` 9.91:1 en `semana-08-wireframes.css`. | Solo cubre estos pares explícitos; no declara cumplimiento completo. |
| Reducción de movimiento | CUMPLE | `styles.css:290-292` elimina transiciones y scroll suave con `prefers-reduced-motion`. | Se respeta la preferencia del sistema. |
| Autenticación/autorización por rol | NO APLICA | `SEMANA-08-DISENO-UX.md:41` documenta que no está implementada. | Se mantiene como limitación previa, fuera de esta evaluación de interfaz; no se inventa como hallazgo de Semana 9. |

## Resumen

| Estado | Cantidad |
|---|---:|
| CUMPLE | 19 |
| CUMPLE PARCIALMENTE | 10 |
| NO CUMPLE | 5 |
| NO APLICA | 1 |
| **Total de criterios** | **35** |

El estado se asigna únicamente a los criterios y evidencias inspeccionados. Este checklist no constituye certificación ni declaración de conformidad WCAG completa.
