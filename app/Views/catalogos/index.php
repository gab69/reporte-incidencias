<div class="ph"><div><h1>Catálogos</h1><p>Agrega o edita plataformas y tipos. Los elementos no se eliminan: si ya no se usan, desactívalos y dejarán de aparecer al crear incidencias.</p></div></div>
<?php
$bloques = ['plataforma' => ['Plataformas', $plataformas], 'tipo' => ['Tipos de incidencia', $tipos]];
foreach ($bloques as $k => [$titulo, $filas]): ?>
<div class="card"><h2><?= e($titulo) ?></h2>
  <?php foreach (array_merge($filas, [['id' => 0, 'nombre' => '', 'color' => '#1d4ed8', 'activo' => 1]]) as $r): ?>
  <form method="post" action="<?= e(url('catalogos/guardar')) ?>" class="catrow">
    <?= csrf_field() ?><input type="hidden" name="k" value="<?= $k ?>"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
    <?php if ($k === 'plataforma'): ?><input type="color" name="color" value="<?= e($r['color']) ?>" title="Color"><?php endif; ?>
    <input type="text" name="nombre" value="<?= e($r['nombre']) ?>" placeholder="<?= $r['id'] ? '' : 'Agregar nuevo…' ?>" maxlength="60" required>
    <?php if ($r['id']): ?><label class="chk"><input type="checkbox" name="activo" value="1" data-was="<?= $r['activo'] ? 1 : 0 ?>" <?= $r['activo'] ? 'checked' : '' ?>> Activo</label><?php endif; ?>
    <button class="sm <?= $r['id'] ? '' : 'p' ?>"><?= $r['id'] ? 'Guardar' : '+ Agregar' ?></button>
  </form>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>
