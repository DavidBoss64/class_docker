# Guion: Masterclass "De XAMPP a Docker" (≈ 60 min, live de TikTok)

| # | Bloque | Tiempo |
|---|---|---|
| 1 | Gancho | 3 min |
| 2 | Lo mínimo de teoría | 5 min |
| 3 | Comandos básicos (sin el sistema) | 15 min |
| 4 | Presentamos la app en XAMPP | 3 min |
| 5 | Dockerizamos la app | 25 min |
| 6 | Preguntas y cierre | 9 min |

---

## ✅ Checklist antes del live

- [ ] Docker Desktop abierto y funcionando (`docker ps` no da error).
- [ ] Imágenes descargadas (el internet del live no es confiable):
  ```bash
  docker pull hello-world
  docker pull nginx
  docker pull php:7.4-cli
  docker pull php:8.3-cli
  docker pull php:8.2-apache      # ← usa TU versión de PHP de XAMPP
  docker pull mariadb:11
  docker pull adminer
  ```
- [ ] Todo limpio de ensayos anteriores: `docker compose down -v` y `docker rm -f web`.
- [ ] XAMPP con la app funcionando (para el bloque 4), y luego **detenido** antes del bloque 5.
- [ ] Letra de la terminal y del editor en 20–24 pt, tema oscuro.
- [ ] Este guion abierto en una segunda pantalla o en el celular.
- [ ] Una grabación de un ensayo completo, como plan B.

---

## 1. Gancho (3 min)

> "¿Cuántos han dicho alguna vez *en mi máquina sí funciona*? 🙋 Escríbanlo en el chat."

- "Hoy tenemos una app PHP que funciona con XAMPP. Para que otro dev la ejecute necesita: instalar XAMPP, tener la misma versión de PHP, importar la BD a mano, que el puerto 80 esté libre…"
- "Al final de este live, cualquiera la va a levantar con **un solo comando**, sin instalar PHP ni MySQL."

## 2. Lo mínimo de teoría (5 min)

- **Imagen** = la receta o el instalador. Está congelada y no cambia.
- **Contenedor** = el plato servido: una imagen **ejecutándose**. Con una imagen puedes crear muchos contenedores.
- **Docker vs. máquina virtual**: el contenedor no trae un sistema operativo completo; por eso arranca en segundos y pesa poco.
- **Docker Hub** = el "almacén" de imágenes listas: php, mysql, nginx, etc.

> Frase para recordar: "XAMPP instala cosas en tu PC; Docker las trae empaquetadas y aisladas."

## 3. Comandos básicos, sin el sistema todavía (15 min)

### 3.1 El "hola mundo"
```bash
docker --version
docker run hello-world
```
Lee en voz alta lo que imprime: Docker buscó la imagen, la descargó, creó un contenedor, lo ejecutó y terminó.

### 3.2 Un servidor web en 1 comando
```bash
docker run -d -p 8080:80 --name web nginx
```
- `-d` = en segundo plano · `-p 8080:80` = **puerto de mi PC : puerto del contenedor** · `--name` = un nombre fácil.
- Abre http://localhost:8080 → "Welcome to nginx!" 🎉

```bash
docker ps              # ¿qué contenedores están corriendo?
docker logs web        # recarga la página y vuelve a ejecutarlo: verás cada visita
docker exec -it web bash   # "entrar" al contenedor
  ls /usr/share/nginx/html
  exit
docker stop web        # detener
docker ps -a           # sigue existiendo, pero detenido
docker start web       # vuelve a arrancar
docker rm -f web       # eliminarlo
docker images          # las imágenes siguen descargadas
```

### 3.3 El momento "wow": dos versiones de PHP a la vez
```bash
docker run --rm php:7.4-cli php -v
docker run --rm php:8.3-cli php -v
```
> "Con XAMPP tendrías que instalar dos XAMPP distintos. Aquí cada proyecto usa su versión, sin conflictos." (`--rm` borra el contenedor al terminar.)

## 4. Presentamos la app en XAMPP (3 min)

- Muestra http://localhost/inscripciones funcionando.
- Recorre rápido el código: `index.php` (formulario + tabla) y `config.php` (`localhost`, `root`, sin contraseña).
- Muestra la BD en phpMyAdmin.
- **Detén Apache y MySQL en XAMPP.** "Desde aquí, XAMPP ya no existe para nosotros."

## 5. Dockerizamos la app (25 min)

### 5.1 Preparar `config.php` para Docker (2 min)

Cambia las 4 variables para que lean **variables de entorno**, y que usen los valores de XAMPP si no existen:

```php
$host     = getenv('DB_HOST')     ?: 'localhost';
$dbname   = getenv('DB_NAME')     ?: 'inscripciones';
$user     = getenv('DB_USER')     ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
```

> "Así el mismo código funciona en XAMPP y en Docker."

### 5.2 El `Dockerfile` (8 min)

Crea `Dockerfile` (sin extensión) en la raíz del proyecto y escríbelo **línea por línea**, explicando cada una:

```dockerfile
# 1. Imagen base: PHP con Apache (lo mismo que nos daba XAMPP)
#    Usa la MISMA versión de PHP de tu XAMPP: php:7.4-apache, php:8.1-apache, php:8.2-apache...
FROM php:8.2-apache

# 2. Extensión para conectarnos a MySQL (en XAMPP ya venía incluida)
RUN docker-php-ext-install pdo_mysql

# 3. Copiamos nuestro código al "htdocs" del contenedor
COPY app/ /var/www/html/

# 4. El contenedor escucha en el puerto 80
EXPOSE 80
```

```bash
docker build -t inscripciones-app .
docker images
docker run -d -p 8080:80 --name app inscripciones-app
```

Abre http://localhost:8080 → 💥 **error de conexión a la base de datos**.

> **Momento clave**: "La app ya corre en Docker, pero dentro de un contenedor `localhost` es **el propio contenedor**, no mi PC. Y aquí no hay MySQL. Necesitamos otro contenedor con la base de datos… y ya serían varios comandos largos. Para eso existe Docker Compose."

```bash
docker rm -f app
```

### 5.3 `docker-compose.yml`: app + base de datos + Adminer (10 min)

Crea `docker-compose.yml` en la raíz:

```yaml
services:
  # 🐘 Nuestra app PHP + Apache (se construye con el Dockerfile)
  app:
    build: .
    ports:
      - "8080:80"
    environment:
      DB_HOST: db            # el nombre del servicio funciona como "dominio" dentro de Docker
      DB_NAME: inscripciones
      DB_USER: root
      DB_PASSWORD: secreto
    depends_on:
      db:
        condition: service_healthy   # espera a que la BD esté lista

  # 🗄️ Base de datos MariaDB (la misma que trae XAMPP)
  db:
    image: mariadb:11
    environment:
      MARIADB_ROOT_PASSWORD: secreto
    volumes:
      - ./database:/docker-entrypoint-initdb.d   # importa database.sql automáticamente
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 5s
      timeout: 5s
      retries: 10

  # 🖥️ Adminer: administrador visual de la BD (como phpMyAdmin)
  adminer:
    image: adminer
    ports:
      - "8081:8080"
    environment:
      ADMINER_DEFAULT_SERVER: db
```

```bash
docker compose up -d --build
docker compose ps
```

- App: http://localhost:8080 ✅
- Adminer: http://localhost:8081 → servidor `db`, usuario `root`, contraseña `secreto`.
- Inscribe a alguien del chat 🎉 y muéstralo también en Adminer.

| XAMPP | Docker Compose |
|---|---|
| Apache + PHP | servicio `app` |
| MySQL/MariaDB | servicio `db` |
| phpMyAdmin | servicio `adminer` |
| Importar el `.sql` a mano | `/docker-entrypoint-initdb.d` |

### 5.4 Volúmenes: el "error a propósito" (4 min)

```bash
docker compose down
docker compose up -d
```

😱 "¡El alumno que inscribimos desapareció! Los contenedores son **desechables**: si lo borras, se borra lo que tenía dentro."

Agrega dos volúmenes al `docker-compose.yml`:

```yaml
  app:
    # ...
    volumes:
      - ./app:/var/www/html          # código en vivo: editas en tu PC y se ve al recargar

  db:
    # ...
    volumes:
      - ./database:/docker-entrypoint-initdb.d
      - db_data:/var/lib/mysql       # 💾 los datos viven fuera del contenedor

# al final del archivo, sin sangría:
volumes:
  db_data:
```

```bash
docker compose down
docker compose up -d
```

Inscribe → `docker compose down` → `docker compose up -d` → ✅ **sigue ahí**.
Bonus: cambia el título en `app/index.php`, recarga y el cambio aparece sin reconstruir nada.

> `docker compose down -v` borra también los volúmenes (los datos). Úsalo con cuidado.

### 5.5 Gran final (1 min)

> "Ahora, cualquier dev del equipo solo necesita Docker:"

```bash
git clone https://github.com/DavidBoss64/class_docker.git
cd class_docker
docker compose up -d
```

Resumen en una frase: **XAMPP → Dockerfile → Compose → Volúmenes**.

## 6. Preguntas y cierre (9 min)

Preguntas probables:
- **¿Funciona en Windows?** Sí, con Docker Desktop + WSL2. También en Mac y Linux.
- **¿Es gratis?** Docker Engine sí. Docker Desktop es gratis para uso personal, educación y empresas pequeñas.
- **¿Puedo tener XAMPP y Docker a la vez?** Sí, pero cuidado con los puertos (80, 3306). Por eso usamos 8080 y 8081.
- **¿Docker vs. Kubernetes?** Docker crea y corre contenedores; Kubernetes los organiza a gran escala en muchos servidores.
- **¿Sirve para Laravel?** Sí, es la misma idea con más servicios (o con Laravel Sail).
- **¿Y en producción?** Se usa la imagen construida (sin el volumen `./app`) y contraseñas seguras, nunca `secreto` 😅

**Si no sabes una respuesta:** "Buena pregunta, la vemos en el curso completo." Es totalmente válido.

Cierre: invitación al curso completo de Nexus Educa.

---

## 🛟 Si algo falla en vivo

| Error | Causa | Solución rápida |
|---|---|---|
| `port is already allocated` | XAMPP u otro programa usa el puerto | `docker ps`, detén XAMPP o cambia `8080:80` por `8090:80` |
| `could not find driver` | Falta `RUN docker-php-ext-install pdo_mysql` | Agrégalo y `docker compose up -d --build` |
| `Unknown database` / tabla vacía tras cambiar el `.sql` | El `.sql` solo se ejecuta la 1.ª vez | `docker compose down -v` y `up -d` |
| `Connection refused` justo al arrancar | La BD aún inicia | Espera unos segundos y recarga |
| Adminer no abre y en `docker compose logs adminer` sale `[::]:8080` | El sistema no tiene IPv6 | En `adminer` agrega `command: ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]` |

**Regla de oro**: si algo falla, léelo en voz alta y resuélvelo con calma. Ver cómo se depura un error enseña más que una demo perfecta.

## 💡 Recomendaciones para el live de TikTok

- **Interactúa cada 5–7 minutos**: "Escriban 1 si ya les cargó nginx", "¿Quién usa XAMPP?". En TikTok la gente entra y sale todo el tiempo.
- **Repite el objetivo** cada vez que entra gente nueva: "Estamos pasando una app de XAMPP a Docker".
- **No copies y pegues bloques enteros**: escribe y explica. Si vas lento, pega y luego explica línea por línea.
- **Haz zoom** en la terminal y el navegador (Ctrl + +). En vertical todo se ve pequeño.
- **Pide los nombres del chat** para inscribirlos en la app: engancha muchísimo.
