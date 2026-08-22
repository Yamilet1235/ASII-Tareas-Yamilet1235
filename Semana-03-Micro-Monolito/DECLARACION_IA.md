# Declaración de uso de inteligencia artificial

## Estudiante y trabajo

**Estudiante:** María Yamilet Lindo Pablo  
**Trabajo:** Micro-HIS Catálogo de Medicamentos  
**Módulo y alcance:** Catálogo de medicamentos

## Herramientas utilizadas

- ChatGPT.
- OpenCode.

## Propósito del uso

Las herramientas se utilizaron como apoyo para interpretar la consigna, proponer una estructura de cuatro capas, generar de forma asistida una primera versión del código y la documentación, y revisar la coherencia técnica del resultado. También apoyaron la preparación de pruebas, diagramas PlantUML y pasos reproducibles de ejecución.

## Prompt relevante resumido

Se solicitó construir, exclusivamente dentro de la carpeta de la semana 3, un micro-monolito educativo en PHP 8.2 vanilla para el catálogo de medicamentos. Debía incluir alta, búsqueda, activación y desactivación; arquitectura Presentation/Application/Domain/Persistence; SQLite con PDO y sentencias preparadas; configuración externa; manejo de errores; pruebas sin framework; documentación académica y diagramas editables. Se indicó no usar datos reales, no modificar otras semanas y no realizar commits ni push.

## Partes aceptadas o modificadas

Se aceptó la estructura general por capas, la entidad `Medication`, el enum `MedicationStatus`, los tres casos de uso, el puerto de repositorio, la implementación PDO, la interfaz web, el ejecutor de pruebas, la documentación y los diagramas propuestos con asistencia.

La propuesta se ajustó para:

- Mantener el puerto `MedicationRepository` fuera de Persistence.
- Reforzar la unicidad tanto en Application como en SQLite.
- Mostrar estados técnicos en español sin alterar los valores `ACTIVE`/`INACTIVE`.
- Usar únicamente datos y nombres ficticios.
- Evitar Composer porque no es indispensable.
- Mantener configuración y base local fuera del control de versiones.

## Validación humana realizada

La estudiante revisó la organización de carpetas, los nombres del módulo, las reglas del dominio, los textos de la interfaz y la documentación. Además, debe verificar en su equipo la sintaxis PHP, ejecutar el inicializador SQLite, correr `php tests/run.php`, recorrer manualmente la interfaz y confirmar que las evidencias coinciden con lo presentado en el informe.

## Responsabilidad y comprensión

La generación asistida no reemplaza la responsabilidad académica. La estudiante revisó el resultado y debe comprenderlo antes de entregarlo o defenderlo. Debe ser capaz de explicar cada capa, PDO, las sentencias preparadas, la regla de vigencia, la restricción de duplicados, los dobles de prueba y los comandos de ejecución. Cualquier cambio posterior debe volver a validarse.
