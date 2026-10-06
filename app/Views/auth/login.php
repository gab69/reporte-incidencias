<div class="card">
  <div class="top">
    <?php if (ajuste('logo')): ?><img src="<?= e(upload_url(ajuste('logo'))) ?>" alt="" class="logo"><?php else: ?><i><?= ico('shield') ?></i><?php endif; ?>
    <h2><?= e(ajuste('app_name')) ?></h2>
    <div class="sub">Acceso para registrar y editar incidencias</div>
  </div>
  <form method="post" action="<?= e(url('auth/entrar')) ?>">
    <?= csrf_field() ?>
    <label>Usuario</label><input name="usuario" required autofocus autocomplete="username">
    <label>Contraseña</label><input type="password" name="clave" required autocomplete="current-password">
    <div class="row"><button class="p wide">Ingresar</button></div>
    <p class="center"><a href="<?= e(url()) ?>">← Ver incidencias sin ingresar</a></p>
  </form>
</div>
