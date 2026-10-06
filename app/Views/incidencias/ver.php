<div class="crumb noprint"><a href="<?= e(url()) ?>">Incidencias</a> / <?= e($inc['plat']) ?></div>
<div class="card hd" style="--c:<?= e($inc['pcolor']) ?>">
  <div class="tags"><span class="badge" style="--c:<?= e($inc['pcolor']) ?>"><?= e($inc['plat']) ?></span><span class="badge t"><?= e($inc['tipo']) ?></span><?php if (!$inc['activo']): ?><span class="badge off">Desactivada</span><?php endif; ?></div>
  <h1><?= e($inc['titulo']) ?></h1>
  <?php if ($inc['descripcion']): ?><div class="t"><?= e($inc['descripcion']) ?></div><?php endif; ?>
  <div class="meta">Actualizada el <?= fecha($inc['actualizado_en']) ?> · Registrada por <?= e($inc['autor'] ?? 'Sin autor') ?></div>
</div>
<h2 class="sec">Solución paso a paso</h2>
<div class="steps">
  <?php foreach ($pasos as $k => $p): ?>
  <div class="step"><div class="num"><?= $k + 1 ?></div>
    <div class="sb"><div class="tt">Paso <?= $k + 1 ?></div>
      <?php if ($p['texto'] !== ''): ?><div class="t"><?= e($p['texto']) ?></div><?php endif; ?>
      <?php if ($p['imagen']): ?><a class="zoom" href="<?= e(upload_url($p['imagen'])) ?>"><img src="<?= e(upload_url($p['imagen'])) ?>" alt="Paso <?= $k + 1 ?>" loading="lazy"></a><?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="row noprint">
  <button type="button" onclick="window.print()">Imprimir</button>
  <a class="btn" href="<?= e(url()) ?>">Volver</a>
  <?php if ($canEdit): ?>
    <a class="btn p" href="<?= e(url('incidencias/form', ['id' => $inc['id']])) ?>">Editar</a>
    <form method="post" action="<?= e(url('incidencias/toggle')) ?>" data-confirm="<?= e($inc['activo'] ? 'La incidencia dejará de mostrarse en el listado público. Podrás reactivarla cuando quieras.' : 'La incidencia volverá a ser visible para todos.') ?>">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $inc['id'] ?>">
      <button class="<?= $inc['activo'] ? 'd' : '' ?>"><?= $inc['activo'] ? 'Desactivar' : 'Reactivar' ?></button>
    </form>
  <?php endif; ?>
</div>
