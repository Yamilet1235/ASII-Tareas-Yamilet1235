# Guía sencilla para la defensa

## ¿Qué es este Micro-HIS?

Es una aplicación educativa pequeña que representa un módulo de un sistema de información de salud. Su único alcance es el catálogo de medicamentos ficticios: alta, búsqueda y control de vigencia. No gestiona pacientes ni datos clínicos.

## ¿Por qué se llama micro-monolito?

Porque es una aplicación pequeña que se ejecuta y despliega como una sola unidad, pero su código está dividido internamente en capas bien definidas. “Micro” se refiere al alcance reducido del módulo, no a que sea un microservicio.

## ¿Cuál es la diferencia entre monolito y microservicio?

Un monolito reúne sus partes en una aplicación desplegable. Un microservicio es un proceso independiente, suele comunicarse por red y puede desplegarse por separado. Este proyecto es monolítico: Presentation, Application, Domain y Persistence se ejecutan juntas en PHP.

## ¿Qué responsabilidad tiene Presentation?

Recibe los datos HTTP y muestra HTML. `MedicationController` interpreta la acción y la vista `catalog.php` presenta formularios, listado y mensajes. No decide las reglas principales del medicamento.

## ¿Qué responsabilidad tiene Application?

Coordina las operaciones que el usuario quiere realizar. Por ejemplo, `RegisterMedication` crea la entidad, consulta si el nombre ya existe y solicita guardarla mediante el repositorio.

## ¿Qué responsabilidad tiene Domain?

Contiene el significado y las reglas centrales. `Medication` exige campos obligatorios y determina la vigencia; `MedicationStatus` limita los estados. También declara `MedicationRepository`, que es el contrato que necesita la aplicación.

## ¿Qué responsabilidad tiene Persistence?

Implementa el acceso a SQLite. Prepara y ejecuta SQL, convierte filas en objetos `Medication` y transforma errores de PDO a excepciones controladas.

## ¿Qué es PDO?

PDO significa PHP Data Objects. Es una interfaz de PHP para acceder a bases de datos. En este proyecto se utiliza el controlador SQLite y la clase `PDO` para conectar, preparar y ejecutar consultas.

## ¿Para qué sirven las sentencias preparadas?

Separan el texto SQL de los valores. Primero se prepara una consulta con marcadores como `:name` y después se envían los datos con `execute()`. Esto evita concatenar entradas del usuario y reduce el riesgo de inyección SQL.

## ¿Qué reglas se implementaron?

- Todos los campos del medicamento son obligatorios.
- El estado solo puede ser `ACTIVE` o `INACTIVE`.
- No se repite el nombre aunque cambien las mayúsculas.
- Un registro `INACTIVE` no está vigente ni se considera seleccionable.

## ¿Dónde está la regla de duplicado?

El caso de uso consulta `findByName()` antes de insertar. Además, se genera una clave `normalized_name` en minúsculas y SQLite aplica `UNIQUE` sobre ella. La primera comprobación ofrece un mensaje claro y la segunda garantiza la integridad incluso si dos inserciones se acercan en el tiempo.

## ¿Cómo se controla la vigencia?

`Medication::isCurrent()` devuelve verdadero solamente cuando `MedicationStatus` es `ACTIVE`. `isSelectable()` usa la misma regla. La vista consulta esos métodos; no vuelve a implementar la condición.

## ¿Cómo se manejan los errores?

El dominio genera errores comprensibles de validación o duplicado. Persistence captura `PDOException` y la transforma. Application evita que un detalle técnico llegue a la interfaz. Presentation muestra el mensaje dentro de una alerta.

## ¿Cómo ejecuto el proyecto?

Primero creo `config/config.php` desde el ejemplo. Después ejecuto:

```bash
php scripts/init_database.php
php -S localhost:8000 -t public
```

Finalmente abro `http://localhost:8000`.

## ¿Cómo ejecuto las pruebas?

```bash
php tests/run.php
```

La salida indica `APROBADA` o `FALLIDA` por cada caso. Si una prueba falla, el proceso termina con un código diferente de cero.

## ¿Cómo explico el doble de prueba?

`InMemoryMedicationRepository` reemplaza SQLite durante varias pruebas y guarda objetos en un arreglo. `FailingMedicationRepository` lanza intencionalmente una excepción para comprobar que Application la transforma. Ambos cumplen la misma interfaz que el repositorio PDO.

## ¿Cómo modificaría una parte?

Si necesito cambiar una regla de vigencia, modificaría `src/Domain/Medication.php` y agregaría o ajustaría una prueba. Si necesito cambiar la consulta SQL, modificaría `src/Persistence/PdoMedicationRepository.php`. Si solo cambia la apariencia, editaría la vista y `public/assets/styles.css` sin mover reglas a Presentation.

## ¿Cómo agregaría una nueva funcionalidad?

Primero definiría qué regla pertenece al dominio. Después crearía un caso de uso en Application, ampliaría el puerto si requiere persistencia, implementaría ese método con PDO y finalmente conectaría el controlador y la vista. También agregaría pruebas.

## ¿Qué datos utiliza?

Solo medicamentos y compuestos inventados para la demostración. No hay nombres de pacientes, diagnósticos, recetas ni información clínica identificable.

## Respuesta breve de cierre

“El proyecto cumple el catálogo solicitado con PHP 8.2 y SQLite. La separación por capas evita mezclar HTML, casos de uso, reglas y SQL. La vigencia vive en el dominio, el repositorio PDO implementa una interfaz y seis pruebas automáticas verifican los casos principales, un fallo simulado y la persistencia real.”
