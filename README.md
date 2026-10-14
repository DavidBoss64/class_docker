# Inscripciones a cursos (PHP + MySQL con XAMPP)

App sencilla para la masterclass "De XAMPP a Docker": registra alumnos inscritos a cursos (listar, inscribir y eliminar), con cupos por curso y un panel **"¿Dónde estoy corriendo?"** que muestra la versión de PHP, el nombre del servidor y a qué base de datos está conectada.

> Ese panel es el centro de la demo: en XAMPP muestra el nombre de tu PC y `localhost`; dentro de Docker muestra el **ID del contenedor** y `db`. El mismo código, dos entornos.

```
app/
  index.php     → la página (panel de entorno + formulario + cupos + tabla)
  config.php    → conexión a la base de datos
database/
  database.sql  → crea la BD "inscripciones" con datos de ejemplo
docs/
  plan-de-estudio.md
  guion.md
  comandos.md
```

## Requisitos

- **XAMPP** con **PHP 7.4 o superior** (funciona con PHP 7.4, 8.0, 8.1, 8.2 y 8.3).
- MySQL/MariaDB de XAMPP (cualquier versión reciente).

### ¿Qué versión de PHP tengo?

Abre la terminal y ejecuta:

```bash
C:\xampp\php\php.exe -v
```

o abre el panel de XAMPP y fíjate en la versión al iniciar Apache. **Anota esa versión**: la usarás al dockerizar, para que Docker use exactamente el mismo PHP.

## Cómo ejecutarlo

1. Copia la carpeta `app` a `C:\xampp\htdocs\` y renómbrala a `inscripciones`
   → debe quedar `C:\xampp\htdocs\inscripciones\index.php`
2. Abre el **XAMPP Control Panel** y pulsa **Start** en **Apache** y **MySQL**.
3. Entra a http://localhost/phpmyadmin → pestaña **Importar** → elige `database/database.sql` → **Continuar**.
4. Abre http://localhost/inscripciones 🎉

Deberías ver la lista con 3 alumnos de ejemplo y poder inscribir o eliminar.

## Problemas comunes

| Error | Solución |
|---|---|
| `Unknown database 'inscripciones'` | No importaste `database.sql` (paso 3). |
| `Access denied for user 'root'` | Le pusiste contraseña a root en XAMPP. Escríbela en `$password` dentro de `app/config.php`. |
| `could not find driver` | Activa `extension=pdo_mysql` en `C:\xampp\php\php.ini` y reinicia Apache. |
| Apache no inicia (puerto 80 ocupado) | Cierra Skype/IIS o cambia el puerto en la configuración de Apache. |

---

👉 Para la clase, sigue [`docs/plan-de-estudio.md`](docs/plan-de-estudio.md) y luego [`docs/guion.md`](docs/guion.md).
