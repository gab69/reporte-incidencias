<?php use App\Core\View; ?>
<div data-live="historial/index" data-page="<?= (int) $page ?>">
  <div class="ph"><div><h1>Historial de modificaciones</h1><p>Quién hizo qué y cuándo. Los filtros se aplican al instante.</p></div></div>
  <div class="bar"><?= ico('search') ?><input data-k="q" value="<?= e($f['q']) ?>" placeholder="Buscar en el detalle, acción o usuario…" autocomplete="off"></div>
  <div class="fl">
    <span class="lb">Usuario</span>
    <select data-k="u" class="auto">
      <option value="">Todos</option>
      <?php foreach ($usuarios as $n): ?><option value="<?= e($n['usuario_nombre']) ?>" <?= $f['u'] === $n['usuario_nombre'] ? 'selected' : '' ?>><?= e($n['usuario_nombre']) ?> (<?= (int) $n['n'] ?>)</option><?php endforeach; ?>
    </select>
    <span class="lb ml">Sobre</span>
    <select data-k="e" class="auto">
      <?php foreach ([''=>'Todo','incidencia'=>'Incidencias','usuario'=>'Usuarios','plataforma'=>'Plataformas','tipo'=>'Tipos','ajuste'=>'Configuración'] as $v => $l): ?>
        <option value="<?= $v ?>" <?= $f['e'] === $v ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div id="res"><?= View::fragment('historial/_tabla', compact('rows', 'total', 'page', 'pages')) ?></div>
</div>
