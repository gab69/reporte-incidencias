<?php use App\Core\Auth; use App\Core\View; ?>
<div data-live="incidencias/index" data-page="1">
  <div class="ph">
    <div><h1>Incidencias</h1><p>Escribe el problema y filtra: los resultados se actualizan al instante.</p></div>
    <?php if (Auth::check()): ?><a class="btn p" href="<?= e(url('incidencias/form')) ?>"><?= ico('plus') ?>Nueva incidencia</a><?php endif; ?>
  </div>
  <div class="bar">
    <?= ico('search') ?>
    <input data-k="q" id="q" value="<?= e($f['q']) ?>" placeholder="Ej: no puedo ingresar a Moodle, error en sala Zoom, calificaciones…" autofocus autocomplete="off">
    <button type="button" id="clr" <?= $f['q'] === '' ? 'hidden' : '' ?> title="Limpiar">✕</button>
  </div>
  <div class="fl"><span class="lb">Plataforma</span>
    <?php foreach ($plataformas as $p): ?>
      <button type="button" class="chip <?= $f['p'] == $p['id'] ? 'on' : '' ?>" data-k="p" data-v="<?= (int) $p['id'] ?>" style="--c:<?= e($p['color']) ?>"><i></i><?= e($p['nombre']) ?> <small><?= (int) $p['n'] ?></small></button>
    <?php endforeach; ?>
  </div>
  <div class="fl"><span class="lb">Tipo</span>
    <?php foreach ($tipos as $t): ?>
      <button type="button" class="chip <?= $f['t'] == $t['id'] ? 'on' : '' ?>" data-k="t" data-v="<?= (int) $t['id'] ?>"><?= e($t['nombre']) ?></button>
    <?php endforeach; ?>
    <?php if (Auth::check()): ?>
      <span class="grow"></span>
      <label class="chk"><input type="checkbox" data-k="mias" <?= $f['mias'] ? 'checked' : '' ?>> Solo mías</label>
      <label class="chk"><input type="checkbox" data-k="inact" <?= $f['inact'] ? 'checked' : '' ?>> Incluir desactivadas</label>
    <?php endif; ?>
  </div>
  <div id="res"><?= View::fragment('incidencias/_items', ['rows' => $rows, 'q' => $f['q']]) ?></div>
</div>
