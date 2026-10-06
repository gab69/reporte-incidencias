<?php $logo = $ajustes['logo']; $fav = $ajustes['favicon']; ?>
<div class="ph"><div><h1>Configuración</h1><p>Personaliza el nombre, los colores, el logo y el favicon. Los colores se previsualizan al instante.</p></div></div>
<form method="post" action="<?= e(url('ajustes/guardar')) ?>" enctype="multipart/form-data" id="af" data-defaults="<?= e(js(['color_primario' => $defaults['color_primario'], 'color_menu' => $defaults['color_menu']])) ?>">
  <?= csrf_field() ?>
  <div class="card"><h2>Identidad</h2>
    <label class="first">Nombre de la plataforma</label>
    <input name="app_name" value="<?= e($ajustes['app_name']) ?>" maxlength="60" required>
    <p class="mu">Aparece en el menú, en la pestaña del navegador y en el inicio de sesión.</p>
  </div>
  <div class="card"><h2>Colores</h2>
    <div class="g2">
      <div><label class="first">Color principal (botones y acentos)</label>
        <div class="colorrow"><input type="color" id="cp" value="<?= e($ajustes['color_primario']) ?>"><input name="color_primario" id="cp_t" value="<?= e($ajustes['color_primario']) ?>" maxlength="7"></div></div>
      <div><label class="first">Color del menú lateral</label>
        <div class="colorrow"><input type="color" id="cm" value="<?= e($ajustes['color_menu']) ?>"><input name="color_menu" id="cm_t" value="<?= e($ajustes['color_menu']) ?>" maxlength="7"></div></div>
    </div>
    <div class="row"><button type="button" id="reset">Restablecer colores originales</button></div>
  </div>
  <div class="card"><h2>Logo y favicon</h2>
    <div class="g2">
      <div><label class="first">Logo (se muestra en el menú y en el login)</label>
        <?php if ($logo): ?><div class="thumb dark"><img src="<?= e(upload_url($logo)) ?>" alt="Logo actual"></div>
          <label class="chk"><input type="checkbox" name="quitar_logo" value="1"> Quitar logo actual</label><?php endif; ?>
        <input type="file" name="logo" accept="image/*"><p class="mu">PNG con fondo transparente recomendado. Se convierte a WebP.</p></div>
      <div><label class="first">Favicon (ícono de la pestaña)</label>
        <?php if ($fav): ?><div class="thumb"><img src="<?= e(upload_url($fav)) ?>" alt="Favicon actual" class="fav"></div>
          <label class="chk"><input type="checkbox" name="quitar_favicon" value="1"> Quitar favicon actual</label><?php endif; ?>
        <input type="file" name="favicon" accept="image/*,.ico"><p class="mu">Imagen cuadrada o archivo .ico. Se ajusta a 64×64.</p></div>
    </div>
  </div>
  <div class="row"><button class="p big">Guardar configuración</button></div>
</form>
