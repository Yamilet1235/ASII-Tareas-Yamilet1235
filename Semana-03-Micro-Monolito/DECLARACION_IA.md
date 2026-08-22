# Declaración de uso de inteligencia artificial

## Estudiante y trabajo

**Estudiante:** María Yamilet Lindo Pablo  
**Trabajo:** Micro-HIS Catálogo de Medicamentos  
**Módulo y alcance:** Catálogo de medicamentos

## Herramientas utilizadas

- ChatGPT
- OpenCode

## Propósito del uso

ChatGPT y OpenCode se utilizaron como herramientas de apoyo para analizar e interpretar la consigna, comprender mejor el código y las responsabilidades de sus capas, recibir una propuesta de estructura por capas y orientar la organización de la documentación y los diagramas. Su uso fue complementario al trabajo y criterio de la estudiante.

## Orientación solicitada

Se solicitó orientación para organizar un micro-monolito educativo en PHP 8.2 vanilla que incluyera alta y búsqueda de medicamentos, control de vigencia, separación en las capas Presentation, Application, Domain y Persistence, acceso a SQLite mediante PDO, pruebas automatizadas y documentación académica.

## Aportes revisados y adaptados

Las propuestas y sugerencias fueron revisadas y adaptadas al módulo asignado. Los principales aspectos considerados fueron:

- La separación de responsabilidades entre las cuatro capas.
- La ubicación del puerto `MedicationRepository` fuera de Persistence.
- Las validaciones obligatorias y el control de unicidad de nombres.
- El uso de los estados `ACTIVE` e `INACTIVE`.
- El empleo exclusivo de datos ficticios.
- La exclusión de la configuración local y la base SQLite del control de versiones.

También se revisaron orientaciones para estructurar el informe, la guía de defensa y los diagramas editables.

## Validación realizada por la estudiante

María Yamilet ejecutó personalmente `php tests/run.php` y confirmó el resultado de 6 pruebas aprobadas y 0 fallidas. Además, abrió la aplicación en localhost, registró medicamentos ficticios, comprobó la búsqueda automática, verificó el rechazo de nombres duplicados, probó la activación y desactivación de medicamentos, y revisó los mensajes y la interfaz web.

## Responsabilidad y comprensión

La estudiante es responsable del contenido final, de comprender las decisiones implementadas y de defender el proyecto. El uso de ChatGPT y OpenCode como apoyo no sustituye su revisión, aprendizaje ni responsabilidad académica.
