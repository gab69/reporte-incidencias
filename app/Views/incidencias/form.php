<?php $id = (int) $inc['id']; ?>
<div class="ph"><div><h1><?= $id ? 'Editar incidencia' : 'Nueva incidencia' ?></h1><p>Tip: dentro de un paso puedes pegar una captura con <b>Ctrl+V</b>. Las imágenes se convierten a WebP automáticamente.</p></div></div>
<form method="post" action="<?= e(url('incidencias/guardar')) ?>" enctype="multipart/form-data" id="frm" data-uploads="<?= e(upload_url('')) ?>">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <div class="card">
    <div class="g2">
      <div><label class="first">Plataforma</label>
        <select name="plataforma_id"><?php foreach ($plataformas as $p): ?><option value="<?= (int) $p['id'] ?>" <?= $p['id'] == $inc['plataforma_id'] ? 'selected' : '' ?>><?= e($p['nombre']) ?></option><?php endforeach; ?></select></div>
      <div><label class="first">Tipo</label>
        <select name="tipo_id"><?php foreach ($tipos as $t): ?><option value="<?= (int) $t['id'] ?>" <?= $t['id'] == $inc['tipo_id'] ? 'selected' : '' ?>><?= e($t['nombre']) ?></option><?php endforeach; ?></select></div>
    </div>
    <label>Título del problema</label>
    <input name="titulo" maxlength="200" value="<?= e($inc['titulo']) ?>" placeholder="Ej: Docente no puede ingresar a Moodle">
    <label>Descripción / síntomas</label>
    <textarea name="descripcion" placeholder="¿Cómo se presenta el problema? ¿Qué mensaje aparece?"><?= e($inc['descripcion']) ?></textarea>
  </div>
  <h2 class="sec">Pasos de la solución</h2>
  <div id="ps"></div>
  <button type="button" id="ap">+ Agregar otro paso</button>
  <div class="row top"><button class="p big">Guardar incidencia</button><a class="btn" href="<?= e($id ? url('incidencias/ver', ['id' => $id]) : url()) ?>">Cancelar</a></div>
</form>
<template id="tpl">
  <div class="fs"><strong class="n"></strong>
    <textarea data-n="texto" placeholder="Describe qué debe hacer el técnico en este paso…"></textarea>
    <input type="hidden" data-n="img"><img class="prev" hidden alt="">
    <div class="row">
      <input type="file" data-f accept="image/*" class="file">
      <button type="button" class="sm" data-r hidden>Quitar imagen</button>
      <button type="button" class="sm" data-u title="Subir">↑</button>
      <button type="button" class="sm" data-d title="Bajar">↓</button>
      <button type="button" class="sm d" data-x>Eliminar paso</button>
    </div>
  </div>
</template>
<script type="application/json" id="pasos-data"><?= js($pasos) ?></script>
