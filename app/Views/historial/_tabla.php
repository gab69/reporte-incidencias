<div class="count"><?= (int) $total ?> <?= $total === 1 ? 'movimiento' : 'movimientos' ?> · página <?= (int) $page ?> de <?= (int) $pages ?></div>
<div class="card flush"><table>
  <tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Detalle</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td class="nowrap"><?= fecha($r['fecha'], 'd/m/Y H:i') ?></td>
    <td><b><?= e($r['usuario_nombre']) ?></b></td>
    <td><?= e($r['accion']) ?></td>
    <td><?php if ($r['entidad'] === 'incidencia' && $r['entidad_id']): ?><a href="<?= e(url('incidencias/ver', ['id' => $r['entidad_id']])) ?>"><?= e($r['detalle']) ?></a><?php else: ?><?= e($r['detalle']) ?><?php endif; ?></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="4" class="mu">No hay movimientos con esos filtros.</td></tr><?php endif; ?>
</table></div>
<?php include BASE_PATH . '/app/Views/partials/paginacion.php'; ?>
