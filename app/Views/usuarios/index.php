<div class="ph">
  <div><h1>Usuarios</h1><p><b>Técnico</b>: consulta, crea y edita solo sus incidencias. <b>Administrador</b>: edita todas y gestiona catálogos, usuarios, historial y configuración. Los usuarios no se eliminan; puedes desactivarlos.</p></div>
  <button type="button" class="btn p" id="addUser"><?= ico('plus') ?>Agregar usuario</button>
</div>
<div class="bar"><?= ico('search') ?><input id="fq" placeholder="Buscar usuario por nombre o login…" autocomplete="off"></div>
<div class="count" id="fc"></div>
<div class="card flush"><table>
  <tr><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Estado</th><th></th></tr>
  <?php foreach ($usuarios as $u): ?>
  <tr data-t="<?= e(mb_strtolower($u['usuario'] . ' ' . $u['nombre'])) ?>">
    <td><b><?= e($u['usuario']) ?></b></td><td><?= e($u['nombre']) ?></td>
    <td><span class="pill a"><?= $u['rol'] === 'admin' ? 'Administrador' : 'Técnico' ?></span></td>
    <td><span class="pill <?= $u['activo'] ? '' : 'x' ?>"><?= $u['activo'] ? 'Activo' : 'Desactivado' ?></span></td>
    <td class="right"><button type="button" class="sm" data-edit="<?= e(js(['id' => (int) $u['id'], 'usuario' => $u['usuario'], 'nombre' => $u['nombre'], 'rol' => $u['rol'], 'activo' => (int) $u['activo']])) ?>">Editar</button></td>
  </tr>
  <?php endforeach; ?>
</table></div>

<div class="ov" id="userModal" hidden>
  <div class="dlg form">
    <h3 id="ft">Agregar usuario</h3>
    <form method="post" action="<?= e(url('usuarios/guardar')) ?>" id="uf">
      <?= csrf_field() ?><input type="hidden" name="id" value="0">
      <div class="g2">
        <div><label class="first">Usuario (para ingresar)</label><input name="usuario" required maxlength="50" autocomplete="off"></div>
        <div><label class="first">Nombre completo</label><input name="nombre" maxlength="100"></div>
        <div><label id="cl">Contraseña (mín. 6)</label><input type="password" name="clave" minlength="6" autocomplete="new-password"></div>
        <div><label>Rol</label><select name="rol"><option value="tecnico">Técnico</option><option value="admin">Administrador</option></select></div>
      </div>
      <label class="chk" id="actw" hidden><input type="checkbox" name="activo" value="1" checked> Usuario activo</label>
      <div class="row end"><button type="button" id="cn">Cancelar</button><button class="p" id="sb">Crear usuario</button></div>
    </form>
  </div>
</div>
