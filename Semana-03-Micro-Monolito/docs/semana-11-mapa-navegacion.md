# Semana 11 - Mapa de navegación

## Datos

- **Estudiante:** María Yamilet Lindo Pablo
- **Carné:** 1890-23-14827
- **Módulo:** ASII-14 - Catálogo de Medicamentos
- **Artefacto navegable:** [`semana-11-prototipo.html`](semana-11-prototipo.html)
- **Fuente del diagrama:** [`semana-11-mapa-navegacion.puml`](semana-11-mapa-navegacion.puml)

## Mapa general

```text
CATÁLOGO
├── BUSCAR
│   ├── RESULTADOS
│   └── SIN RESULTADOS
│       └── REGISTRAR [Administrador]
│
├── REGISTRAR [Administrador]
│   └── VALIDACIÓN
│       ├── ÉXITO
│       │   └── CATÁLOGO
│       └── DUPLICADO
│           └── CORREGIR
│               └── ÉXITO
│                   └── CATÁLOGO
│
└── CAMBIAR VIGENCIA [Administrador]
    └── CONFIRMACIÓN
        ├── CONFIRMAR
        │   └── ÉXITO
        │       └── CATÁLOGO
        └── CANCELAR
            └── CATÁLOGO
```

## Descripción de rutas

| Origen | Acción | Destino | Roles |
|---|---|---|---|
| Catálogo | Buscar por nombre comercial o genérico | Resultados | Administrador, Médico y Enfermera |
| Catálogo | Buscar un término sin coincidencias | Sin resultados | Administrador, Médico y Enfermera |
| Sin resultados | Limpiar búsqueda | Catálogo completo | Administrador, Médico y Enfermera |
| Sin resultados | Registrar medicamento | Alta | Administrador |
| Catálogo | Registrar medicamento | Alta | Administrador |
| Alta | Cancelar o Volver | Catálogo | Administrador |
| Alta | Guardar datos válidos | Éxito de alta | Administrador |
| Alta | Guardar nombre comercial existente | Duplicado | Administrador |
| Duplicado | Corregir nombre y guardar | Éxito de alta | Administrador |
| Éxito de alta | Volver al catálogo | Catálogo con registro nuevo | Administrador |
| Catálogo | Activar o Desactivar | Confirmación | Administrador |
| Confirmación | Cancelar o Escape | Catálogo sin cambios | Administrador |
| Confirmación | Confirmar | Éxito de vigencia | Administrador |
| Éxito de vigencia | Continuar | Catálogo con estado actualizado | Administrador |

## Camino feliz

```text
CATÁLOGO
-> BUSCAR "Calmofin"
-> SIN RESULTADOS
-> REGISTRAR [Administrador]
-> COMPLETAR FORMULARIO
-> VALIDACIÓN CORRECTA
-> ÉXITO
-> CATÁLOGO CON CALMOFIN
```

## Error crítico

```text
CATÁLOGO
-> REGISTRAR [Administrador]
-> NOMBRE COMERCIAL "Alivion"
-> VALIDACIÓN
-> DUPLICADO
-> CONSERVAR OTROS DATOS Y ENFOCAR NOMBRE COMERCIAL
-> CORREGIR
-> VALIDACIÓN CORRECTA
-> ÉXITO
-> CATÁLOGO
```

## Cambio de vigencia

```text
CATÁLOGO / ACTIVO
-> DESACTIVAR [Administrador]
-> CONFIRMACIÓN
   -> CANCELAR O ESCAPE
      -> CATÁLOGO / ACTIVO
   -> CONFIRMAR
      -> ÉXITO
      -> CATÁLOGO / INACTIVO
```

## Rutas por rol

### Administrador

- Consultar el catálogo.
- Buscar por nombre comercial o nombre genérico.
- Limpiar una búsqueda.
- Registrar un medicamento.
- Corregir validaciones y duplicados.
- Activar o desactivar mediante confirmación.

### Médico

- Consultar el catálogo.
- Buscar por nombre comercial o nombre genérico.
- Limpiar una búsqueda.
- No visualiza rutas de registro o cambio de vigencia.

### Enfermera

- Consultar el catálogo.
- Buscar por nombre comercial o nombre genérico.
- Limpiar una búsqueda.
- No visualiza rutas de registro o cambio de vigencia.

## Reglas de navegación

- El catálogo es el punto inicial y de retorno.
- Una búsqueda vacía o Limpiar búsqueda muestra el catálogo completo.
- Registrar y Cambiar vigencia son rutas exclusivas del Administrador.
- Un duplicado no abandona el formulario: conserva los datos válidos y permite corregir.
- Cancelar o Escape en la confirmación no modifica el estado.
- Confirmar es la única ruta que modifica la vigencia simulada.
- El selector de rol es demostrativo y no representa autenticación real.
