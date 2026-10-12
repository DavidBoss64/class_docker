<?php
require __DIR__ . '/config.php';

// Registrar alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $stmt = $pdo->prepare('INSERT INTO alumnos (nombre, email, curso) VALUES (?, ?, ?)');
    $stmt->execute([trim($_POST['nombre']), trim($_POST['email']), trim($_POST['curso'])]);
    header('Location: index.php');
    exit;
}

// Eliminar alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $stmt = $pdo->prepare('DELETE FROM alumnos WHERE id = ?');
    $stmt->execute([(int) $_POST['eliminar']]);
    header('Location: index.php');
    exit;
}

$alumnos = $pdo->query('SELECT * FROM alumnos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

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
    .badge { display: inline-block; background: #1e293b; border: 1px solid #334155; padding: 4px 12px; border-radius: 999px; font-size: 14px; }
    form.registro { display: grid; gap: 10px; background: #1e293b; padding: 16px; border-radius: 12px; margin: 20px 0; }
    input, button { font-size: 18px; padding: 10px; border-radius: 8px; border: 1px solid #334155; }
    input { background: #0f172a; color: #e2e8f0; }
    button { background: #22c55e; color: #052e16; font-weight: bold; border: 0; cursor: pointer; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px; border-bottom: 1px solid #334155; }
    .btn-eliminar { background: #ef4444; color: #fff; padding: 4px 10px; font-size: 14px; }
  </style>
</head>
<body>
  <div class="contenedor">
    <h1>📚 Inscripciones a cursos</h1>
    <span class="badge">PHP <?= PHP_VERSION ?> · Servidor: <?= e(gethostname()) ?></span>

    <form method="post" class="registro">
      <input name="nombre" placeholder="Nombre completo" required>
      <input name="email" type="email" placeholder="Correo" required>
      <input name="curso" placeholder="Curso" required>
      <button type="submit">Inscribir</button>
    </form>

    <table>
      <tr><th>Nombre</th><th>Correo</th><th>Curso</th><th></th></tr>
      <?php foreach ($alumnos as $a): ?>
        <tr>
          <td><?= e($a['nombre']) ?></td>
          <td><?= e($a['email']) ?></td>
          <td><?= e($a['curso']) ?></td>
          <td>
            <form method="post">
              <button class="btn-eliminar" name="eliminar" value="<?= (int) $a['id'] ?>">✕</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
