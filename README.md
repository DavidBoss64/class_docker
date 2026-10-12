# Masterclass Docker: de XAMPP a Docker 🐳

Proyecto de ejemplo para una masterclass práctica de introducción a Docker.
Una app sencilla en **PHP + MySQL** (inscripciones a cursos) que primero se ejecuta con **XAMPP** y luego se migra paso a paso a **Docker**.

## Paso 0: ejecutar con XAMPP (la forma "tradicional")

Requisitos: instalar XAMPP (Apache + PHP + MySQL/MariaDB).

1. Copia la carpeta `app/` dentro de `C:\xampp\htdocs\` y renómbrala a `inscripciones`.
2. Abre el panel de XAMPP e inicia **Apache** y **MySQL**.
3. Entra a http://localhost/phpmyadmin, pestaña **Importar**, y sube `database/database.sql`.
4. Abre http://localhost/inscripciones

¿Y si otro dev tiene otra versión de PHP? ¿Y si usa Linux o Mac? ¿Y si el puerto 80 está ocupado? 🤔
👉 Para eso existe Docker.
