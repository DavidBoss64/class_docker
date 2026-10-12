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
app/index.php          → página (formulario + tabla), PDO, consultas preparadas, htmlspecialchars
app/config.php         → conexión con valores de XAMPP (localhost, root, sin contraseña)
database/database.sql  → BD "inscripciones", tabla "alumnos", 3 registros de ejemplo
docs/plan-de-estudio.md  → plan lunes → miércoles
docs/guion.md            → guion del live con TODOS los archivos de Docker completos
docs/comandos.md         → hoja de comandos
docs/contexto.md         → este archivo
```

### Verificado

- Sintaxis del código PHP: sin errores en PHP 7.4, 8.0, 8.2 y 8.3. La app completa se ejecutó en PHP 8.2 y 8.3.
- `database.sql`: se importa bien en MariaDB 10.4 (la de XAMPP) y en MariaDB 11.
- Dockerización del guion probada de punta a punta: la app lista los alumnos, Adminer responde (HTTP 200), los datos persisten con el volumen `db_data` y el código se actualiza en vivo con el montaje `./app:/var/www/html`.
- Sin volumen, los datos se pierden al hacer `down`/`up` (comportamiento buscado para la demo).

### No verificado

- **Ejecución real en XAMPP** (no se pudo probar fuera de su máquina).
- **Inicio de sesión en Adminer** mostrando la tabla: solo se comprobó que la página carga.

## Pendientes

- [ ] Conocer la **versión de PHP de su XAMPP** (`C:\xampp\php\php.exe -v`) y usar esa versión en `FROM php:X.Y-apache`.
- [ ] Ejecutar la app con XAMPP siguiendo el `README.md`.
- [ ] Instalar Docker Desktop (WSL2 en Windows).
- [ ] Las etiquetas `paso-0-xampp` … `paso-3-volumenes` existen en un historial anterior, pero **no se subieron** (GitHub rechazó el push de etiquetas). Ya no son necesarias con la estructura actual.
- [ ] El "gran final" (`git clone` + `docker compose up -d`) requiere que el repo tenga `Dockerfile` y `docker-compose.yml` en la rama por defecto. Opciones: subirlos después de dockerizar, o preparar una rama `solucion` antes del live.
- [ ] Fusionar la rama de trabajo a `main` cuando esté lista.

## Problemas conocidos

- En entornos sin IPv6, Adminer falla con `Failed to listen on [::]:8080`. Solución: en el servicio `adminer`, agregar `command: ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]`. En Docker Desktop normalmente no ocurre.
- `database.sql` solo se ejecuta cuando el volumen de la BD se crea por primera vez → `docker compose down -v` para reiniciarla.

## Bitácora de progreso

| Fecha | Qué se hizo | Dudas / dificultades | Siguiente paso |
|---|---|---|---|
| 2026-10-12 | Se armó la app XAMPP, el plan de estudio, el guion y este contexto. | — | Lunes del plan: ejecutar con XAMPP, instalar Docker, conceptos y comandos básicos. |
