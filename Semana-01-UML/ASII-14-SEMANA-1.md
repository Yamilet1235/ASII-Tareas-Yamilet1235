# ASII-14 — Catálogo de medicamentos

## Evidencia Semana 1

**Estudiante:** María Yamilet Lindo Pablo  
**Usuario de GitHub:** Yamilet1235  
**Módulo asignado:** Catálogo de medicamentos  
**Rama:** `feature/asii-14-catalogo-de-medicamentos-yamilet1235`  
**Rama base:** `origin/develop`  

---

## 1. Descripción general del módulo

El módulo Catálogo de Medicamentos permitirá administrar los medicamentos disponibles de cada hospital dentro del Sistema Hospitalario Integrado.

Cada medicamento almacenará información básica como nombre comercial, nombre genérico, categoría, presentación, concentración y estado. Esta información será utilizada por otros módulos, principalmente el de Prescripciones Médicas.

El sistema trabaja con arquitectura multitenant, por lo que cada medicamento pertenece a un hospital específico mediante el campo tenant_id, evitando que un hospital pueda visualizar o modificar la información de otro.

---

## 2. Diagnóstico inicial

Durante la revisión del proyecto se encontro lo siguiente:

| Elemento                    | Estado actual   |
| --------------------------- | --------------- |
| Tabla `medications`         | Existe          |
| Modelo `Medication`         | Existe          |
| Seeder de medicamentos      | Existe          |
| Relación con prescripciones | Existe          |
| Separación por `tenant_id`  | Existe          |
| Controlador de medicamentos | No existe       |
| Requests de validación      | No existen      |
| Rutas API                   | No existen      |
| CRUD Backend                | No implementado |
| Pantallas del módulo        | No existen      |
| CRUD Frontend               | No implementado |


La tabla actual contiene los campos:

- `id`
- `tenant_id`
- `name`
- `generic_name`
- `category`
- `presentation`
- `concentration`
- `active`
- `created_at`
- `updated_at`

---

## 3. Actores del módulo


### 3.1 Administrador

Es el encargado de administrar el catálogo de medicamentos.

Puede realizar las siguientes acciones:

    - Ver medicamentos.
    - Buscar medicamentos.
    - Registrar nuevos medicamentos.
    - Editar información.
    - Activar o desactivar medicamentos.

### 3.2 Médico

Es el usuario que consulta el catálogo al momento de realizar una prescripción médica.

Puede:

    - Consultar medicamentos activos.
    - Buscar medicamentos por nombre.
    - Ver la presentación y concentración.
    - Seleccionar un medicamento para una receta.

El médico no necesariamente podrá administrar o eliminar registros del catálogo, salvo que se le asigne un permiso específico.

### 3.3 Enfermera

Puede consultar la información de los medicamentos para apoyar la atención del paciente.

Sus funciones son:

    - Consultar medicamentos activos.
    - Ver presentación y concentración.
 
### 3.4 Sistema de prescripciones

Es un módulo relacionado que consume la información del Catálogo de Medicamentos.

Sus funciones relacionadas son:

- Mostrar medicamentos disponibles al médico.
- Asociar un medicamento con una prescripción.
- Conservar la relación histórica entre medicamentos y prescripciones.

### 3.5 Sistema de autenticación y multitenancy

Es el componente responsable de:

- Identificar al usuario autenticado.
- Determinar el hospital activo.
- Restringir el acceso según roles y permisos.
- Evitar que un hospital consulte medicamentos pertenecientes a otro tenant.

---

## 4. Alcance del módulo

### 4.1 Funciones incluidas en el alcance

El módulo incluirá:

- Listar medicamentos pertenecientes al hospital activo.
- Buscar medicamentos por nombre comercial.
- Buscar medicamentos por nombre genérico.
- Registrar un nuevo medicamento.
- Consultar el detalle de un medicamento.
- Modificar los datos de un medicamento.
- Activar o desactivar medicamentos.
- Filtrar medicamentos según su estado.
- Evitar nombres duplicados dentro del mismo hospital.
- Relacionar medicamentos con prescripciones médicas.
- Restringir las operaciones según roles y permisos.
- Mantener el aislamiento de información por `tenant_id`.
- Mostrar resultados paginados.

### 4.2 Funciones fuera del alcance inicial

El módulo no incluirá inicialmente:

- Control de existencias.
- Administración de bodegas.
- Compra de medicamentos.
- Gestión de proveedores.
- Facturación.
- Control de precios.
- Control de lotes.
- Fechas de vencimiento.
- Despacho de medicamentos.
- Administración contable.
- Control completo de farmacia.

Estas funciones podrían formar parte de un módulo futuro de farmacia o inventario.

---

## 5. Casos de uso

| Código    | Caso de uso                                   | Actor                            |
| --------- | --------------------------------------------- | -------------------------------- |
| CU-MED-01 | Listar medicamentos                           | Administrador, Médico, Enfermera |
| CU-MED-02 | Buscar medicamentos                           | Administrador, Médico, Enfermera |
| CU-MED-03 | Registrar medicamento                         | Administrador                    |
| CU-MED-04 | Consultar detalle de medicamento              | Usuario autorizado               |
| CU-MED-05 | Editar medicamento                            | Administrador                    |
| CU-MED-06 | Activar o desactivar medicamento              | Administrador                    |
| CU-MED-07 | Seleccionar medicamento para una prescripción | Médico                           |


---

## 6. Descripción de los casos de uso del proceso asignado

La consigna individual consiste en modelar el proceso de alta, búsqueda y control de vigencia de un medicamento.

### CU-MED-02 — Buscar medicamento

**Actor principal:** Administrador, Médico o Enfermera.

**Objetivo:** Encontrar un medicamento mediante su nombre comercial o nombre genérico.

**Precondiciones:**

- El usuario debe haber iniciado sesión.
- El usuario debe pertenecer a un hospital válido.
- Debe poseer permiso de consulta.

**Flujo principal:**

1. El usuario ingresa al Catálogo de Medicamentos.
2. Escribe el nombre comercial o genérico del medicamento.
3. El sistema identifica el hospital activo mediante el `tenant_id`.
4. El sistema busca únicamente los medicamentos pertenecientes a ese hospital.
5. El sistema muestra los resultados encontrados.

**Flujo alternativo:**

- Si no se encuentran coincidencias, el sistema muestra un mensaje indicando que no existen resultados.

**Resultado esperado:**

El usuario visualiza solamente medicamentos pertenecientes a su hospital.

---

### CU-MED-03 — Dar de alta un medicamento

**Actor principal:** Administrador.

**Objetivo:** Registrar un medicamento en el catálogo del hospital.

**Precondiciones:**

- El administrador debe haber iniciado sesión.
- Debe pertenecer a un hospital válido.
- Debe poseer permiso para registrar medicamentos.

**Flujo principal:**

1. El administrador selecciona la opción **Nuevo medicamento**.
2. El sistema muestra el formulario.
3. El administrador ingresa el nombre comercial, nombre genérico, categoría, presentación y concentración.
4. El sistema valida los datos ingresados.
5. El sistema comprueba que no exista otro medicamento con el mismo nombre dentro del hospital.
6. El sistema asigna el `tenant_id` del hospital activo.
7. El sistema registra el medicamento con el campo `active` en `true`.
8. El sistema muestra un mensaje de confirmación.

**Flujos alternativos:**

- Si faltan datos obligatorios, el sistema muestra los errores de validación.
- Si el medicamento ya existe dentro del mismo hospital, el sistema no permite registrarlo.
- Si el usuario no posee permiso, el sistema rechaza la operación.

**Resultado esperado:**

El medicamento queda registrado y disponible dentro del catálogo del hospital.

---

### CU-MED-06 — Controlar vigencia del medicamento

**Actor principal:** Administrador.

**Objetivo:** Activar o desactivar un medicamento sin eliminar su información histórica.

**Precondiciones:**

- El administrador debe haber iniciado sesión.
- Debe poseer permiso para modificar medicamentos.
- El medicamento debe pertenecer al hospital activo.

**Flujo principal:**

1. El administrador selecciona un medicamento.
2. Elige la opción para activar o desactivar.
3. El sistema solicita confirmación.
4. El administrador confirma la operación.
5. El sistema cambia el valor del campo `active`.
6. El sistema guarda el cambio.
7. El sistema muestra un mensaje de confirmación.

**Flujos alternativos:**

- Si el administrador cancela la confirmación, el sistema no realiza cambios.
- Si el medicamento no pertenece al hospital activo, el sistema informa que no fue encontrado.
- Si el usuario no posee permiso, el sistema rechaza la operación.

**Resultado esperado:**

El medicamento cambia su vigencia sin ser eliminado de la base de datos.

---

## 7. Diagramas UML

Para complementar el análisis se elaboraron tres diagramas UML sobre el proceso de alta, búsqueda y control de vigencia de medicamentos.

Los diagramas mantienen los mismos actores, operaciones, validaciones y excepciones.

### 7.1 Diagrama de casos de uso

El diagrama identifica los actores que participan en el módulo y las operaciones que pueden realizar.

![Diagrama de casos de uso](UML/imagenes/ASII-14-casos-de-uso.png)

**Fuente editable:**

`docs/UML/ASII-14-casos-de-uso.puml`

---

### 7.2 Diagrama de actividades

El diagrama representa el flujo de las operaciones de alta, búsqueda y control de vigencia.

También incluye decisiones y excepciones como:

- Credenciales incorrectas.
- Falta de permisos.
- Datos incompletos.
- Medicamento duplicado.
- Búsqueda sin resultados.
- Medicamento perteneciente a otro hospital.
- Cancelación del cambio de vigencia.

![Diagrama de actividades](UML/imagenes/ASII-14-actividades.png)

**Fuente editable:**

`docs/UML/ASII-14-actividades.puml`

---

### 7.3 Diagrama de secuencia

El diagrama muestra la comunicación entre:

- Usuario.
- Interfaz Vue.
- API Laravel.
- Middleware de autenticación y tenant.
- Controlador de medicamentos.
- Base de datos.

También representa los mensajes enviados, las validaciones realizadas y las respuestas del sistema.

![Diagrama de secuencia](UML/imagenes/ASII-14-secuencia.png)

**Fuente editable:**

`docs/UML/ASII-14-secuencia.puml`

---

## 8. Matriz de trazabilidad

La siguiente matriz permite comprobar que los tres diagramas representan el mismo proceso.

| Proceso o requisito              | Caso de uso                                | Diagrama de actividades            | Diagrama de secuencia     |
| -------------------------------- | ------------------------------------------ | ---------------------------------- | ------------------------- |
| Buscar medicamento               | CU-MED-02 Buscar medicamento               | Flujo de búsqueda                  | Búsqueda de medicamento   |
| Registrar medicamento            | CU-MED-03 Registrar medicamento            | Flujo de registro                  | Registro de medicamento   |
| Activar o desactivar medicamento | CU-MED-06 Activar o desactivar medicamento | Cambio de estado del medicamento   | Actualización de estado   |
| Validar datos                    | Incluido en el registro                    | Validación de datos obligatorios   | Validación de datos       |
| Evitar medicamentos duplicados   | Incluido en el registro                    | Verificación de duplicados         | Respuesta 409 (Conflict)  |
| Validar permisos del usuario     | Usuario autorizado                         | Verificación de permisos           | Respuesta 403 (Forbidden) |
| Separar información por hospital | Validación del `tenant_id`                 | Validación del hospital activo     | Middleware Tenant         |
| Medicamento no encontrado        | Flujo alternativo                          | Decisión "¿Existe el medicamento?" | Respuesta 404 (Not Found) |
| Registro exitoso                 | Resultado del registro                     | Confirmación de registro           | Respuesta 201 (Created)   |
| Cambio de estado exitoso         | Resultado de activar/desactivar            | Confirmación de actualización      | Respuesta 200 (OK)        |


---

## 9. Evidencia técnica

Los archivos editables se encuentran dentro de la carpeta:

`docs/UML/`

Archivos incluidos:

- `ASII-14-casos-de-uso.puml`
- `ASII-14-actividades.puml`
- `ASII-14-secuencia.puml`

Las imágenes generadas se encuentran en:

`docs/UML/imagenes/`

Imágenes incluidas:

- `ASII-14-casos-de-uso.png`
- `ASII-14-actividades.png`
- `ASII-14-secuencia.png`

---

## 10. Conclusión de la Semana 1

Durante la revisión del proyecto se comprobó que ya existe la estructura básica del Catálogo de Medicamentos, incluyendo la tabla `medications`, el modelo `Medication`, la relación con prescripciones y los datos de prueba.

Sin embargo, todavía no están implementados el controlador, las rutas API, las validaciones ni la interfaz del módulo.

Además del análisis de actores, alcance y casos de uso, se modeló el proceso de alta, búsqueda y control de vigencia mediante diagramas de casos de uso, actividades y secuencia.

Los tres diagramas mantienen relación entre sus actores, operaciones, validaciones, excepciones y resultados, y servirán como base para el desarrollo de las siguientes semanas.