<?php
require __DIR__ . '/config.php';

// Catálogo de cursos con sus cupos. Vive aquí (y no en la BD) para que la demo
// siga teniendo una sola tabla: el protagonista del live es Docker, no el SQL.
$CURSOS = [
    'Docker desde cero'   => 30,
    'Laravel profesional' => 20,
    'Python para todos'   => 25,
    'JavaScript moderno'  => 15,
];

// Registrar alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
    $curso  = isset($_POST['curso']) ? $_POST['curso'] : '';

    // El curso tiene que ser uno de la lista: nunca confíes en lo que llega del navegador
    if (!isset($CURSOS[$curso])) {
        header('Location: index.php?error=' . urlencode('Ese curso no existe'));
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO alumnos (nombre, email, curso) VALUES (?, ?, ?)');
    $stmt->execute([$nombre, trim($_POST['email']), $curso]);
    header('Location: index.php?ok=' . urlencode($nombre) . '&curso=' . urlencode($curso));
    exit;
}

// Eliminar alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $stmt = $pdo->prepare('DELETE FROM alumnos WHERE id = ?');
    $stmt->execute([(int) $_POST['eliminar']]);
    header('Location: index.php?borrado=1');
    exit;
}

$alumnos = $pdo->query('SELECT * FROM alumnos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

// Inscritos por curso, para los contadores
$inscritos = [];
foreach ($pdo->query('SELECT curso, COUNT(*) AS total FROM alumnos GROUP BY curso') as $fila) {
    $inscritos[$fila['curso']] = (int) $fila['total'];
}

// --- Datos del entorno: esto es lo que hace visible a Docker en pantalla ---
try {
    $motorBD = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
} catch (PDOException $e) {
    $motorBD = 'desconocido';
}
$enDocker = !in_array($host, ['localhost', '127.0.0.1'], true);

function e($texto) { return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscripciones</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; padding: 24px; font-size: 18px; }
    .contenedor { max-width: 760px; margin: 0 auto; }
    h1 { margin-top: 0; }
    h2 { font-size: 20px; margin: 28px 0 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }

    /* Panel "¿dónde estoy corriendo?" */
    .entorno { background: #1e293b; border: 2px solid #334155; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
    .entorno.docker { border-color: #0ea5e9; }
    .entorno-titulo { display: flex; align-items: center; gap: 10px; font-weight: bold; margin-bottom: 14px; }
    .etiqueta { font-size: 14px; padding: 4px 12px; border-radius: 999px; background: #334155; color: #e2e8f0; }
    .entorno.docker .etiqueta { background: #0ea5e9; color: #052e3f; }
    .datos { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; }
    .dato { background: #0f172a; border-radius: 8px; padding: 10px 12px; }
    .dato .clave { font-size: 13px; color: #94a3b8; display: block; }
    .dato .valor { font-size: 17px; font-weight: bold; word-break: break-all; }
    .dato .valor.resaltado { color: #38bdf8; }

    /* Avisos */
    .aviso { padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; font-weight: bold; }
    .aviso.ok    { background: #052e16; border: 1px solid #22c55e; color: #86efac; }
    .aviso.error { background: #450a0a; border: 1px solid #ef4444; color: #fca5a5; }

    form.registro { display: grid; gap: 10px; background: #1e293b; padding: 16px; border-radius: 12px; margin: 20px 0; }
    input, button, select { font-size: 18px; padding: 10px; border-radius: 8px; border: 1px solid #334155; }
    input, select { background: #0f172a; color: #e2e8f0; }
    button { background: #22c55e; color: #052e16; font-weight: bold; border: 0; cursor: pointer; }

    /* Contadores por curso */
    .cursos { display: grid; gap: 10px; }
    .curso { background: #1e293b; border-radius: 10px; padding: 12px 14px; }
    .curso-cabecera { display: flex; justify-content: space-between; gap: 10px; font-size: 16px; margin-bottom: 8px; }
    .curso-cabecera .cupo { color: #94a3b8; white-space: nowrap; }
    .barra { background: #0f172a; border-radius: 999px; height: 10px; overflow: hidden; }
    .barra div { background: #22c55e; height: 100%; }
    .barra div.lleno { background: #ef4444; }

    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px; border-bottom: 1px solid #334155; }
    th { font-size: 14px; color: #94a3b8; text-transform: uppercase; }
    td.fecha { color: #94a3b8; font-size: 15px; white-space: nowrap; }
    .btn-eliminar { background: #ef4444; color: #fff; padding: 4px 10px; font-size: 14px; }
    .vacio { color: #94a3b8; padding: 20px 10px; }
  </style>
</head>
<body>
  <div class="contenedor">
    <h1>📚 Inscripciones a cursos</h1>

    <!-- Panel del entorno: en XAMPP y en Docker esto se ve DISTINTO -->
    <div class="entorno <?= $enDocker ? 'docker' : '' ?>">
      <div class="entorno-titulo">
        <span>¿Dónde estoy corriendo?</span>
        <span class="etiqueta"><?= $enDocker ? '🐳 Docker' : '💻 XAMPP (local)' ?></span>
      </div>
      <div class="datos">
        <div class="dato">
          <span class="clave">PHP</span>
          <span class="valor"><?= PHP_VERSION ?></span>
        </div>
        <div class="dato">
          <span class="clave">Servidor web</span>
          <span class="valor"><?= e(gethostname()) ?></span>
        </div>
        <div class="dato">
          <span class="clave">Base de datos (host)</span>
          <span class="valor resaltado"><?= e($host) ?></span>
        </div>
        <div class="dato">
          <span class="clave">Motor</span>
          <span class="valor"><?= e($motorBD) ?></span>
        </div>
      </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
      <div class="aviso ok">
        ✅ <?= e($_GET['ok']) ?> fue inscrito<?= isset($_GET['curso']) ? ' en ' . e($_GET['curso']) : '' ?>
      </div>
    <?php elseif (isset($_GET['borrado'])): ?>
      <div class="aviso ok">🗑️ Inscripción eliminada</div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="aviso error">❌ <?= e($_GET['error']) ?></div>
    <?php endif; ?>

    <form method="post" class="registro">
      <input name="nombre" placeholder="Nombre completo" required>
      <input name="email" type="email" placeholder="Correo" required>
      <select name="curso" required>
        <option value="">— Elige un curso —</option>
        <?php foreach ($CURSOS as $nombreCurso => $cupo): ?>
          <option value="<?= e($nombreCurso) ?>"><?= e($nombreCurso) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Inscribir</button>
    </form>

    <h2>Cupos por curso</h2>
    <div class="cursos">
      <?php foreach ($CURSOS as $nombreCurso => $cupo): ?>
        <?php
          $total    = isset($inscritos[$nombreCurso]) ? $inscritos[$nombreCurso] : 0;
          $progreso = $cupo > 0 ? min(100, round($total / $cupo * 100)) : 0;
        ?>
        <div class="curso">
          <div class="curso-cabecera">
            <span><?= e($nombreCurso) ?></span>
            <span class="cupo"><?= $total ?> / <?= $cupo ?></span>
          </div>
          <div class="barra">
            <div class="<?= $total >= $cupo ? 'lleno' : '' ?>" style="width: <?= $progreso ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <h2><?= count($alumnos) ?> inscritos</h2>
    <table>
      <tr><th>Nombre</th><th>Correo</th><th>Curso</th><th>Inscrito</th><th></th></tr>
      <?php foreach ($alumnos as $a): ?>
        <tr>
          <td><?= e($a['nombre']) ?></td>
          <td><?= e($a['email']) ?></td>
          <td><?= e($a['curso']) ?></td>
          <td class="fecha"><?= !empty($a['creado_en']) ? date('d/m H:i', strtotime($a['creado_en'])) : '—' ?></td>
          <td>
            <form method="post">
              <button class="btn-eliminar" name="eliminar" value="<?= (int) $a['id'] ?>">✕</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$alumnos): ?>
        <tr><td colspan="5" class="vacio">Todavía no hay inscripciones. 😮 ¿Se perdieron los datos?</td></tr>
      <?php endif; ?>
    </table>
  </div>
</body>
</html>
