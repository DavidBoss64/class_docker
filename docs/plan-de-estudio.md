# Plan de estudio (lunes → miércoles)

La meta no es memorizar, es **haberlo hecho tú con tus manos al menos 2 veces** antes del live.

## Lunes: la app y los fundamentos (≈ 2–3 h)

1. **Ejecuta la app con XAMPP** siguiendo el `README.md`. Anota tu versión de PHP.
2. **Instala Docker Desktop** (en Windows activa WSL2 cuando te lo pida). Comprueba con:
   ```bash
   docker --version
   docker compose version
   ```
3. **Conceptos** (lee y explícalos en voz alta, como si ya estuvieras en el live):
   - Imagen vs. contenedor (receta vs. plato servido).
   - Docker vs. máquina virtual.
   - Docker Hub (el "almacén" de imágenes).
   - Puertos: `-p PUERTO_PC:PUERTO_CONTENEDOR`.
4. **Practica los comandos básicos** del bloque 3 del guion (hello-world, nginx, `ps`, `logs`, `exec`, `stop`, `rm`) hasta que te salgan sin mirar.

✅ Al terminar el lunes sabes responder: *¿qué es una imagen, qué es un contenedor y qué hace `-p 8080:80`?*

## Martes: dockerizar la app (≈ 3 h)

1. Sigue el bloque 5 del guion **escribiendo tú cada archivo**, sin copiar y pegar: `Dockerfile` → `docker-compose.yml` → volúmenes.
2. **Rompe cosas a propósito** y lee el error:
   - Quita la línea `RUN docker-php-ext-install pdo_mysql` → ¿qué error sale?
   - Pon `DB_HOST: localhost` en vez de `db` → ¿qué error sale?
   - Quita el volumen `db_data` → `down` / `up` → ¿qué pasa con los datos?
   Esos errores son tu mejor material para el live y para responder preguntas.
3. **Primer ensayo completo** en voz alta, cronometrado.

✅ Al terminar el martes puedes borrar todo (`docker compose down -v`) y levantarlo de nuevo sin mirar el guion.

## Miércoles: ensayo y preparación (mañana)

1. Segundo ensayo completo (aquí ya debería durar ≤ 60 min).
2. Deja todo listo (ver "Checklist antes del live" en el guion).
3. Descansa antes del live 😉

## Recursos

- Documentación oficial: https://docs.docker.com/get-started/
- Imagen oficial de PHP (lee la sección "With Apache"): https://hub.docker.com/_/php
- Imagen de MariaDB: https://hub.docker.com/_/mariadb
- Imagen de Adminer: https://hub.docker.com/_/adminer
