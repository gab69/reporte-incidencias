<?php $n = count($rows); ?>
<div class="count"><?= $n ?> <?= $n === 1 ? 'incidencia encontrada' : 'incidencias encontradas' ?><?= $n >= 100 ? ' · mostrando las 100 más recientes' : '' ?></div>
<?php if (!$rows): ?>
<div class="card empty"><svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3"/></svg><b>Sin resultados</b>Prueba con otras palabras o quita algún filtro.</div>
<?php endif; ?>
<?php foreach ($rows as $r): ?>
<a class="card inc <?= $r['activo'] ? '' : 'off' ?>" style="--c:<?= e($r['pcolor']) ?>" href="<?= e(url('incidencias/ver', ['id' => $r['id']])) ?>">
  <div class="tags"><span class="badge"><?= e($r['plat']) ?></span><span class="badge t"><?= e($r['tipo']) ?></span><?php if (!$r['activo']): ?><span class="badge off">Desactivada</span><?php endif; ?></div>
  <h3><?= hl($r['titulo'], $q) ?></h3>
  <div class="mu"><?= hl(mb_substr($r['descripcion'] ?? '', 0, 160), $q) ?></div>
  <div class="meta"><?= (int) $r['np'] ?> pasos · <?= e($r['autor'] ?? 'Sin autor') ?> · <?= fecha($r['actualizado_en']) ?></div>
</a>
<?php endforeach; ?>
