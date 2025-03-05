# Proyecto Jerarquía

## Intro

Bienvenido al Proyecto Jerarquía, creado con fines didácticos por Óscar H Caballero, ingeniero de software en PARENTESYS. 
Usaremos Patrones de Software, Principios SOLID y Programación Orientada a Objetos, con el lenguaje de programación PHP.


## Descripción

Vamos a crear un sistema informático para gestionar organizaciones de personas, en las que habrá jefes y subordinados.


* Cuando en una organización un miembro causa baja temporal (se pone enfermo, o necesita un descanso, excedencia, etc), ese miembro desaparece temporalmente de la organización. Todos los subordinados directos que tiene son inmediatamente reasignados al jefe de mayor edad que está en el mismo nivel que su anterior jefe. Si no existe un jefe alternativo disponible, el subordinado directo más antiguo del jefe anterior es ascendido y se convierte en el nuevo jefe de los demás.


* Cuando el miembro que causó baja temporal regresa, inmediatamente recupera su antigua posición en la organización (lo que significa que tendrá el mismo jefe que tenía en el momento de causar baja). Todos sus antiguos subordinados directos son transferidos nuevamente para trabajar bajo su mando, incluso si previamente fueron ascendidos o ahora tienen un jefe diferente.


Vamos a escribir un método para determinar si un jefe tiene más de 50 personas bajo su mando (o un número solicitado).
Vamos a escribir un método que, dados dos miembros de la organización, identifique cuál de ellos tiene un rango más alto.


Crearemos tests unitarios de codificación.


Usaremos buenas prácticas de diseño aplicables al problema, como extensibilidad, mantenibilidad y modularidad, entre otras. Trataremos de desarrollar la estructura de datos y los algoritmos más óptimos posibles para implementar las reglas descritas.

¡Que te diviertas!
Óscar H Caballero )( PARENTESYS