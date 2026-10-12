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

## Paso 3: volúmenes (datos persistentes y código en vivo)

Agregamos dos volúmenes en `docker-compose.yml`:

- `db_data:/var/lib/mysql` → los datos de la BD se guardan fuera del contenedor.
- `./app:/var/www/html` → editas el PHP en tu editor y lo ves al recargar, sin reconstruir la imagen.

```bash
docker compose down
docker compose up -d
```

Ahora los alumnos **siguen ahí** ✅. Para borrar también los datos: `docker compose down -v`.

## 🚀 Resultado final: cualquier dev lo ejecuta con un comando

```bash
git clone https://github.com/DavidBoss64/class_docker.git
cd class_docker
docker compose up -d
```

Sin instalar XAMPP, PHP ni MySQL. Solo Docker.

## Recorrer la clase paso a paso

Cada paso tiene una etiqueta de git:

```bash
git checkout paso-0-xampp
git checkout paso-1-dockerfile
git checkout paso-2-compose
git checkout paso-3-volumenes
```

## Problemas comunes

- **"port is already allocated"**: el puerto 8080/8081 está en uso (¿XAMPP encendido?). Cambia `"8080:80"` por `"8090:80"`.
- **Cambié `database.sql` y no se aplica**: solo se ejecuta cuando la BD se crea por primera vez. Usa `docker compose down -v` y vuelve a levantar.
- **Adminer no abre y en los logs sale `Failed to listen on [::]:8080`**: tu sistema no tiene IPv6. Agrega al servicio `adminer`: `command: ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]`.

Guion de la clase: [`docs/guion.md`](docs/guion.md) · Comandos: [`docs/comandos.md`](docs/comandos.md)
