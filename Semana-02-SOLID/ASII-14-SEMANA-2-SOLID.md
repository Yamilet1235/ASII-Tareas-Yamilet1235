# ASII-14 — Catálogo de Medicamentos

## Evidencia Semana 2 — SOLID

**Estudiante:** María Yamilet Lindo Pablo  
**GitHub:** Yamilet1235  
**Módulo:** Catálogo de Medicamentos  
**Rama:** `feature/semana-2-solid`  
**Principio SOLID asignado:** ISP — Interface Segregation Principle  

---

# 1. Introducción

Esta actividad tiene como objetivo aplicar el principio ISP al diseño del flujo de alta, búsqueda y control de vigencia de un medicamento dentro del módulo Catálogo de Medicamentos.

El análisis parte de los requisitos funcionales y no funcionales del módulo, definiendo criterios de aceptación y comparando un diseño inicial con una propuesta mejorada que separa las responsabilidades mediante interfaces específicas.

---

# 2. Requisitos funcionales

## RF-01 — Registrar medicamento

El sistema deberá permitir que un administrador registre un medicamento proporcionando nombre comercial, nombre genérico, categoría, presentación y concentración.

## RF-02 — Validar datos del medicamento

El sistema deberá validar que los campos obligatorios hayan sido ingresados antes de registrar el medicamento.

## RF-03 — Evitar medicamentos duplicados

El sistema deberá impedir el registro de un medicamento con el mismo nombre dentro del mismo hospital.

## RF-04 — Buscar medicamentos

El sistema deberá permitir buscar medicamentos por nombre comercial o nombre genérico.

## RF-05 — Consultar medicamentos por hospital

El sistema deberá mostrar únicamente los medicamentos correspondientes al hospital activo mediante el `tenant_id`.

## RF-06 — Controlar vigencia

El sistema deberá permitir al administrador activar o desactivar un medicamento sin eliminar su información.

## RF-07 — Restringir operaciones administrativas

Solo los usuarios con permisos administrativos podrán registrar medicamentos o modificar su vigencia.

---

# 3. Requisitos no funcionales

## RNF-01 — Seguridad

El sistema deberá validar la autenticación y los permisos del usuario antes de ejecutar operaciones administrativas.

## RNF-02 — Aislamiento multitenant

Los medicamentos de un hospital no deberán ser visibles ni modificables por usuarios pertenecientes a otro hospital.

## RNF-03 — Mantenibilidad

El diseño deberá separar las responsabilidades de registro, búsqueda y control de vigencia para facilitar cambios futuros.

## RNF-04 — Usabilidad

El sistema deberá mostrar mensajes claros cuando una operación sea exitosa o cuando ocurra un error de validación.

## RNF-05 — Integridad de datos

La activación o desactivación de un medicamento no deberá eliminar su información histórica.