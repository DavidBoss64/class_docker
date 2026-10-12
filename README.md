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

## Paso 1: nuestra primera imagen con un `Dockerfile`

```bash
docker build -t inscripciones-app .
docker run -d -p 8080:80 --name app inscripciones-app
```

Abre http://localhost:8080. Verás un **error de conexión**: la app ya corre en Docker, pero aún no tiene base de datos.
Dentro de un contenedor, `localhost` es el propio contenedor y no tu PC 😉

```bash
docker rm -f app   # limpiar
```

## Paso 2: app + base de datos + Adminer con Docker Compose

```bash
docker compose up -d --build
```

| Servicio | URL | Equivale en XAMPP a… |
|---|---|---|
| App | http://localhost:8080 | Apache + PHP |
| Adminer | http://localhost:8081 | phpMyAdmin |
| Base de datos | (interna, servicio `db`) | MySQL/MariaDB |

Datos de acceso en Adminer: servidor `db`, usuario `root`, contraseña `secreto`.

```bash
docker compose ps          # ver los servicios
docker compose logs -f app # ver logs
docker compose down        # apagar y eliminar los contenedores
```

⚠️ Prueba esto: inscribe un alumno, ejecuta `docker compose down` y luego `up` otra vez. **¡El alumno desapareció!**
