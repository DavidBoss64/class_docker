# Guion: Masterclass "De XAMPP a Docker" (≈ 60 min, live de TikTok)

> Consejo: antes del live ejecuta `docker pull php:8.3-apache mariadb:11 adminer hello-world nginx`
> y deja el repo en `git checkout paso-0-xampp`.

## 1. Gancho (3 min)
- "¿Cuántos han dicho *en mi máquina sí funciona*?" 🙋
- Muestra la app corriendo en XAMPP (http://localhost/inscripciones).
- "Para que otro dev la ejecute necesita: instalar XAMPP, la misma versión de PHP, importar la BD a mano, que el puerto 80 esté libre… Hoy lo vamos a reducir a **un solo comando**."

## 2. Lo mínimo de teoría (5 min)
- **Imagen** = la receta (o el instalador). **Contenedor** = el plato servido (la app ejecutándose).
- Una imagen → muchos contenedores.
- Docker vs. máquina virtual: el contenedor no trae un sistema operativo completo, comparte el kernel. Por eso arranca en segundos.
- Docker Hub = la "tienda" de imágenes listas (php, mariadb, adminer…).

## 3. Primer contacto (8 min)
```bash
docker run hello-world
docker run -d -p 8080:80 --name web nginx    # abre http://localhost:8080
docker ps
docker stop web && docker rm web
```
- Explica `-p 8080:80`: **puerto de tu PC : puerto del contenedor**.

## 4. Paso 1: Dockerfile (12 min) — `git checkout paso-1-dockerfile`
- Escribe el `Dockerfile` línea por línea (`FROM`, `RUN`, `COPY`, `EXPOSE`).
- Comparación: `php:8.3-apache` ≈ el Apache + PHP de XAMPP.
- `config.php` ahora lee variables de entorno, con los valores de XAMPP como respaldo.
```bash
docker build -t inscripciones-app .
docker images
docker run -d -p 8080:80 --name app inscripciones-app
```
- 💥 Error de conexión → **momento clave**: "dentro del contenedor, `localhost` es el contenedor, no tu PC". Necesitamos una BD.
```bash
docker rm -f app
```

## 5. Paso 2: Docker Compose (12 min) — `git checkout paso-2-compose`
- "Levantar 3 contenedores a mano es tedioso → `docker-compose.yml` describe todo el sistema."
- Explica los 3 servicios: `app`, `db` (MariaDB, la misma de XAMPP), `adminer` (como phpMyAdmin).
- `DB_HOST: db` → el nombre del servicio es el "dominio" dentro de la red de Docker.
- `/docker-entrypoint-initdb.d` → importa `database.sql` automáticamente (adiós "Importar" en phpMyAdmin).
```bash
docker compose up -d --build
docker compose ps
```
- Abre la app (8080) y Adminer (8081: servidor `db`, usuario `root`, contraseña `secreto`).
- Inscribe a alguien del chat 🎉 y muéstralo también en Adminer.

## 6. Paso 3: Volúmenes (5 min) — el "error a propósito"
```bash
docker compose down
docker compose up -d
```
- 😱 ¡El alumno desapareció! Los contenedores son desechables.
- `git checkout paso-3-volumenes` → agrega `db_data` y `./app:/var/www/html`.
- Repite: inscribe → `down` → `up` → ✅ sigue ahí.
- Bonus: cambia el título en `index.php`, recarga y el cambio aparece sin reconstruir.

## 7. Gran final (5 min)
- "Así lo ejecuta cualquier dev del equipo":
```bash
git clone https://github.com/DavidBoss64/class_docker.git
cd class_docker
docker compose up -d
```
- Resumen en una pantalla: XAMPP → Dockerfile → Compose → Volúmenes.

## 8. Preguntas y cierre (10 min)
Preguntas probables:
- **¿Funciona en Windows?** Sí, con Docker Desktop + WSL2.
- **¿Es gratis?** Docker Engine sí. Docker Desktop es gratis para uso personal, educación y empresas pequeñas.
- **¿Docker vs. Kubernetes?** Docker crea y corre contenedores; Kubernetes los orquesta a gran escala en muchos servidores.
- **¿Puedo usar Laravel?** Sí, es la misma idea con más servicios (o con Laravel Sail).
- **¿Y en producción?** Se usa la imagen construida (sin el volumen `./app`) y contraseñas seguras en variables o *secrets*.

Cierre: invitación al curso completo de Nexus Educa.
