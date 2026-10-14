# Contexto del proyecto y del plan de estudio

> Este archivo resume todo lo decidido hasta ahora. Claude Code lo carga automáticamente (a través de `CLAUDE.md`) para retomar la tutoría sin perder el hilo.
> **Mantenlo actualizado**: al final de cada sesión de estudio, registra el avance en la sección "Bitácora de progreso".

## Quién estudia y para qué

- Desarrollador/a junior en **Nexus Educa**, empresa de cursos en línea.
- Le encargaron una **masterclass introductoria de Docker**, 100 % práctica, transmitida en un **live de TikTok** el **miércoles 14 de octubre de 2026** (≈ 60 min).
- También está aprendiendo Docker: quiere que la preparación sea una oportunidad real de aprender, no solo de presentar.
- Público objetivo: devs que todavía usan **XAMPP**.

## Idea central de la clase

**"De XAMPP a Docker"**: tomar una app PHP + MySQL que funciona con XAMPP y dockerizarla, para que cualquier dev la ejecute con `docker compose up -d` sin instalar nada más.

Orden acordado del live (detalle completo en `docs/guion.md`):
1. Gancho, "en mi máquina sí funciona" (3 min)
2. Teoría mínima: imagen vs. contenedor, Docker vs. VM, Docker Hub (5 min)
3. **Comandos básicos sin el sistema**: hello-world, nginx, `ps`, `logs`, `exec`, `stop`/`rm`, dos versiones de PHP a la vez (15 min)
4. Presentar la app en XAMPP (3 min)
5. **Dockerización al final**: `config.php` con variables de entorno → `Dockerfile` → `docker-compose.yml` (app + MariaDB + Adminer) → volúmenes (25 min)
6. Preguntas y cierre (9 min)

## Decisiones tomadas (y por qué)

| Decisión | Motivo |
|---|---|
| PHP puro + MySQL, **sin Laravel** | Laravel tiene demasiadas piezas que pueden fallar en vivo (Composer, `APP_KEY`, permisos, Node). |
| App mínima: inscripciones a cursos (listar, inscribir, eliminar) | Basta para mostrar app + BD; letra grande y tema oscuro para el formato vertical. |
| **MariaDB** en Docker | Es la misma base de datos que trae XAMPP. |
| **Adminer** en lugar de phpMyAdmin | Es ligero y se configura en pocas líneas; cumple el mismo papel visual. |
| Puertos 8080 (app) y 8081 (Adminer) | Evitan choques con XAMPP (puerto 80) y permiten convivir con él. |
| El repo contiene **solo la app XAMPP** | Los archivos de Docker los escribe quien estudia, siguiendo el guion, para aprender de verdad. |
| Errores "a propósito" en la clase | El error de conexión tras el Dockerfile (`localhost` dentro del contenedor) y la pérdida de datos sin volúmenes enseñan más que una demo perfecta. |

## Estado del repositorio

Repo: `DavidBoss64/class_docker` · rama de trabajo: `claude/stoic-cannon-qess34`

```
app/index.php          → panel "¿Dónde estoy corriendo?" (PHP, hostname, host y motor de la BD),
                         formulario con `<select>` de cursos, cupos por curso, avisos de confirmación,
                         tabla con fecha. PDO, consultas preparadas, htmlspecialchars
app/config.php         → conexión por variables de entorno (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD)
                         con respaldo a los valores de XAMPP: getenv('DB_HOST') ?: 'localhost'
Dockerfile             → escrito por quien estudia (2026-10-12): FROM php:8.2-apache,
                         RUN docker-php-ext-install pdo_mysql, COPY ./app/ /var/www/html/, EXPOSE 80
                         (comentario de la línea de EXPOSE corregido el 2026-10-13: ya no dice que publica/mapea puertos)
docker-compose.yml      → escrito por quien estudia (2026-10-13): servicios app (build, ports, environment,
                         volumen ./app:/var/www/html), db (image mariadb:11, environment MARIADB_ROOT_PASSWORD,
                         volúmenes ./database:/docker-entrypoint-initdb.d y db_data:/var/lib/mysql) y adminer
                         (image, ports 8081:8080, ADMINER_DEFAULT_SERVER). Contraseña usada en los ensayos: passworddb.
                         Sin depends_on/healthcheck todavía (el guion.md sí lo trae; no hizo falta en las pruebas).
database/database.sql  → BD "inscripciones", tabla "alumnos", 3 registros de ejemplo
docs/plan-de-estudio.md  → plan lunes → miércoles
docs/guion.md            → guion del live completo, ≈60 min, con TODOS los archivos de Docker
docs/guion-30min.docx    → guion comprimido a 30 min (el live real dura eso, no 60), generado el 2026-10-13
                         con el código REAL del proyecto (mismas contraseñas y archivos), timing, líneas
                         sugeridas para decir en voz alta y notas de qué cortar si falta tiempo
docs/comandos.md         → hoja de comandos
docs/contexto.md         → este archivo
```

### Entorno de trabajo (desde 2026-10-12)

**Una sola copia del proyecto.** `C:\xampp\htdocs\class_docker` es un **junction de Windows** que apunta a
`D:\WORK\Class DOCKER\class_docker` (el repo). Se editan los archivos una sola vez y los ven XAMPP, git y Docker.

```powershell
cmd /c mklink /J "C:\xampp\htdocs\class_docker" "D:\WORK\Class DOCKER\class_docker"
```

Si hay que recrearlo: Apache debe estar **detenido** (Windows no renombra/borra una carpeta en uso).
La carpeta anterior quedó respaldada en `C:\xampp\htdocs\class_docker_viejo` (se puede borrar).
Buen puente didáctico: el junction es la misma idea que un bind mount (`./app:/var/www/html`) — una ventana, no una copia.

### Verificado

- Sintaxis del código PHP: sin errores en PHP 7.4, 8.0, 8.2 y 8.3. La app completa se ejecutó en PHP 8.2 y 8.3.
- `database.sql`: se importa bien en MariaDB 10.4 (la de XAMPP) y en MariaDB 11.
- Dockerización del guion probada de punta a punta: la app lista los alumnos, Adminer responde (HTTP 200), los datos persisten con el volumen `db_data` y el código se actualiza en vivo con el montaje `./app:/var/www/html`.
- Sin volumen, los datos se pierden al hacer `down`/`up` (comportamiento buscado para la demo).
- App mejorada (2026-10-11) probada de punta a punta contra el MariaDB 10.4.32 de su XAMPP con PHP 8.2.12:
  HTTP 200, panel correcto, inserción, borrado, rechazo de un curso fuera de la lista, contadores de cupo
  y escapado de HTML (prueba de XSS en los avisos). La fila de prueba se eliminó después.
- `config.php` con `getenv(...) ?: 'valor'` funcionando en XAMPP (2026-10-12): la app carga igual que antes.
- `Dockerfile` escrito por quien estudia y construido con éxito (2026-10-12): `docker build -t inscripciones-app .`
  → 80 s la primera vez (58 s solo de descargar `php:8.2-apache`), segundos en la reconstrucción por caché de capas.
  Imagen `inscripciones-app:latest` (708 MB en disco / 176 MB de contenido).
- Contenedor levantado con `-p 8080:80` → **error de conexión a la BD**, el "error a propósito" del guion: dentro
  del contenedor `localhost` es el propio contenedor. Deducido por quien estudia sin ayuda.
- **Paso 5.3 y 5.4 completos y probados de punta a punta (2026-10-13):** `docker-compose.yml` escrito por quien
  estudia (con un par de rondas de correcciones: `build` e `image` como listas en vez de valor simple,
  `MARIADB_PASSWORD` en vez de `MARIADB_ROOT_PASSWORD`, sintaxis de `environment` en una sola línea). Al primer
  `docker compose up -d --build` los 3 contenedores quedaron `Up`, pero la app mostró `Unknown database
  'inscripciones'` (diferente al error de `localhost` de ayer: esta vez SÍ llegaba al servidor de MariaDB, solo
  que la base no existía) — se resolvió agregando el volumen `./database:/docker-entrypoint-initdb.d`.
  Después, ejercicio "a propósito": inscribió a alguien, `down` + `up -d`, el dato desapareció (y confirmó que
  volvió a ver solo los 3 de siembra, no una tabla vacía — entendió que se re-crea y re-siembra, no que queda
  vacía). Agregó `db_data:/var/lib/mysql` + bloque `volumes: db_data:` al final, y `./app:/var/www/html` en
  `app` (código en vivo). Repitió la prueba: inscribió, `down`, `up -d`, recargó → el dato sobrevivió. Respondió
  bien que `down -v` también borra los volúmenes.
- Corregido el comentario de la línea de `EXPOSE 80` en el `Dockerfile` (pendiente resuelto).
- Generado `docs/guion-30min.docx` con Python (`python-docx`) porque el live de mañana dura 30 min, no 60.

### No verificado

- **Inicio de sesión en Adminer** mostrando la tabla: solo se comprobó que la página carga.

## Pendientes

- [x] Versión de PHP de XAMPP: **8.2.12** → usar `FROM php:8.2-apache` (verificado 2026-10-11).
- [x] App ejecutada en XAMPP por quien estudia: funciona (2026-10-11).
- [x] Docker instalado y funcionando: Docker 29.7.2, Compose v5.3.1 (verificado 2026-10-11).
- [x] Paso 5.1: `config.php` con variables de entorno, escrito por quien estudia (2026-10-12).
- [x] Paso 5.2: `Dockerfile` escrito, construido y probado (2026-10-12).
- [x] Paso 5.3: `docker-compose.yml` con los 3 servicios (app, db, adminer), escrito y probado (2026-10-13).
- [x] Paso 5.4: volúmenes (`db_data` para persistencia + `./app` para código en vivo), escritos y probados
      de punta a punta, incluyendo los dos ejercicios "a propósito" (2026-10-13).
- [x] Corregir el comentario de la línea de `EXPOSE` del `Dockerfile` (resuelto 2026-10-13).
- [ ] **El live dura 30 min, no 60** (dato nuevo de hoy). `docs/guion.md` sigue siendo la referencia completa
      de 60 min; para mañana usar `docs/guion-30min.docx` (ya generado, con el código real del proyecto).
      Falta decidir si conviene actualizar `docs/guion.md` también o dejarlo solo como "versión larga".
- [ ] Faltan 2 de los 3 ejercicios de "romper cosas a propósito" del martes (quedaron para "más tarde"):
      quitar `RUN docker-php-ext-install pdo_mysql` y ver el error, y poner `DB_HOST: localhost` en el
      compose (en vez de `db`) y ver el error. (El tercero, el de los volúmenes, ya se hizo).
- [ ] **Falta el primer ensayo completo**, cronometrado, en voz alta, usando `docs/guion-30min.docx`.
- [ ] Limpieza pendiente en su máquina: contenedor parado `app-php` (de `inscripciones-app`) y la carpeta
      de respaldo `C:\xampp\htdocs\class_docker_viejo`.
- [ ] Checklist del live: `docker pull php:8.2-apache` **antes** de transmitir (la descarga tardó 58 s).
- [ ] Las etiquetas `paso-0-xampp` … `paso-3-volumenes` existen en un historial anterior, pero **no se subieron** (GitHub rechazó el push de etiquetas). Ya no son necesarias con la estructura actual.
- [ ] El "gran final" (`git clone` + `docker compose up -d`) requiere que el repo tenga `Dockerfile` y `docker-compose.yml` en la rama por defecto. Ya existen en el working tree de la rama de trabajo pero **no están comiteados todavía** (ver `git status`).
- [ ] Fusionar la rama de trabajo a `main` cuando esté lista.

## Problemas conocidos

- En entornos sin IPv6, Adminer falla con `Failed to listen on [::]:8080`. Solución: en el servicio `adminer`, agregar `command: ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]`. En Docker Desktop normalmente no ocurre.
- `database.sql` solo se ejecuta cuando el volumen de la BD se crea por primera vez → `docker compose down -v` para reiniciarla.

## Bitácora de progreso

| Fecha | Qué se hizo | Dudas / dificultades | Siguiente paso |
|---|---|---|---|
| 2026-10-12 | Se armó la app XAMPP, el plan de estudio, el guion y este contexto. | — | Lunes del plan: ejecutar con XAMPP, instalar Docker, conceptos y comandos básicos. |
| 2026-10-11 (dom, noche) | Repaso del bloque 3 en terminal: `run -d -p --name`, `ps`, `logs`, `exec -it bash`, `rm -f`, `images`. Errores provocados y leidos: imagen inexistente (olvido de `--name`), mapeo a `:90` (contenedor `Up` pero navegador en silencio), `exec` sin comando. Comprobado que la edicion hecha dentro del contenedor se pierde al recrearlo. | Venia del entorno grafico de Docker Desktop y no tenia los comandos en la memoria. Confundia el puerto de la derecha con una eleccion libre, y creia que el puerto servia la pagina (lo sirve nginx). | Martes: bloque 5 del guion. Primero `COPY` vs. volumen (bind mount) y luego escribir el `Dockerfile` con `FROM php:8.2-apache`. |
| 2026-10-11 (dom, noche) | Mejoras a la app de demo (las 4 acordadas): panel "¿Dónde estoy corriendo?", `<select>` de cursos con cupos y contadores, avisos de confirmación y columna de fecha. No hizo falta tocar `database.sql` (`creado_en` ya existía) ni `config.php` (el paso 5.1 del guion sigue igual). | Veía la app demasiado simple. Preguntó si `php:8.2-apache` era obligatorio: no lo es, pero se mantiene por riesgo (es su versión ya probada) y porque fijar la versión a propósito ES la lección. | Bajar los cambios y volver a probar en XAMPP; luego el `Dockerfile`. |
| 2026-10-11 (dom, noche) | Cierre: ronda de 10 preguntas de repaso, **7.5/10**. Limpias: imagen vs. contenedor, izquierda/derecha de `-p`, por qué un cambio hecho dentro del contenedor se pierde al recrearlo, `COPY` (fotocopia) vs. volumen (ventana), y por qué un servicio por contenedor. **Le gustó esta modalidad de estudio** y pidió mantenerla: concepto + pista (no la solución), él escribe el comando, y los errores se leen juntos hasta deducir la causa. | **Repasar 2 cosas, ambas de memoria:** (1) creía que un contenedor desaparece de `docker ps -a` tras `stop` — solo desaparece con `rm`; (2) no recordaba que si `bash` falla en una imagen mínima se usa `sh`. Menores: dijo "construir un contenedor" (se *crea*; `build` es para imágenes), y le faltó el argumento del tamaño de las imágenes (241 MB vs. 74.5 MB de alpine, capas compartidas, `docker system df`). | Martes: calentar con `stop`/`start`/`ps -a` (30 s) y luego el `Dockerfile` con `FROM php:8.2-apache` (4 líneas, las escribe él); después `docker-compose.yml` con app + db + adminer y los volúmenes. |
| 2026-10-12 (lun, mañana) | Calentamiento: `stop` vs. `rm` **bien** (de los 2 puntos flojos de anoche, este quedó resuelto). Se unificó el entorno con un **junction** de `C:\xampp\htdocs\class_docker` al repo. **Paso 5.1**: `config.php` con `getenv(...) ?: 'valor'`, escrito por él y probado en XAMPP. **Paso 5.2**: `Dockerfile` completo (4 líneas + comentarios propios), `docker build` con éxito y contenedor levantado → **error de conexión a la BD**, cuya causa dedujo solo y con la palabra correcta ("está aislado"). | Typo `gatenv` → leyó el `Fatal error` y lo ubicó (de paso: el `try/catch` no atrapa un `Error` fatal de PHP). Escribió `DB_PASS` en vez de `DB_PASSWORD` → se explicó que el nombre es un **contrato** con el compose y que no coincidir falla **en silencio**. **Confusión principal: creía que `EXPOSE` publica/mapea puertos** → escribió `EXPOSE 8080:80`; el `build` no falló, solo advirtió (`ExposeInvalidFormat`), lo que reforzó que `EXPOSE` es solo metadato. No recordaba por qué `sh` en vez de `bash` (imágenes mínimas / BusyBox). | **Paso 5.3: `docker-compose.yml`.** Retomar con el servicio `app` ya pedido (`build: .`, `ports`, `environment` con las 4 variables y `DB_HOST: db`), avisando de la sangría YAML (2 espacios, nunca Tab); luego `db` y `adminer`, y cerrar con los volúmenes (5.4). Va **un día adelantado** respecto al plan. |
| 2026-10-13 (mar, mañana) | **Paso 5.3 completo**: escribió `app`, `db` y `adminer` en `docker-compose.yml`, con varias rondas de corrección guiada (sin dárselas resueltas): `build:` e `image:` escritos como lista en vez de valor simple, `environment` en una sola línea en vez de mapeo YAML, y `MARIADB_PASSWORD` en vez de `MARIADB_ROOT_PASSWORD`. Primer `docker compose up -d --build` con los 3 contenedores arriba, pero la app dio `Unknown database 'inscripciones'` — identificó solo que era un error distinto al de `localhost` (sí llegaba al servidor). Se resolvió con el volumen `./database:/docker-entrypoint-initdb.d`. **Paso 5.4 completo**: vivió el "error a propósito" de perder datos sin volumen (inscribió, `down`+`up`, el alumno desapareció; confirmó que volvió a ver los 3 de siembra, no una tabla vacía — entendió "re-crea y re-siembra" vs. "queda vacía"), luego agregó `db_data:/var/lib/mysql` + bloque `volumes:` + `./app:/var/www/html` y confirmó persistencia real. Corrigió el comentario del `EXPOSE` en el `Dockerfile` (pendiente desde ayer). Pidió un **guion para el live en Word/PDF**: se generó `docs/guion-30min.docx` (con Python/`python-docx`) porque le avisaron que el live dura **30 min, no 60** — con el código y contraseñas reales del proyecto, timing, líneas para decir en voz alta y notas de qué cortar si falta tiempo. | Ninguna duda de fondo nueva; los errores de sintaxis YAML (listas vs. valores simples) fueron el obstáculo principal, no de concepto. | Quedan 2 de los 3 drills de "romper cosas" (quitar `pdo_mysql`; `DB_HOST: localhost` en el compose) y el **primer ensayo completo cronometrado** con `docs/guion-30min.docx`, antes del ensayo final de mañana miércoles. |
