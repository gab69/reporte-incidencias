<?php
use App\Core\Auth;
use App\Core\Session;

$user    = Auth::user();
$nombre  = ajuste('app_name');
$primary = ajuste('color_primario');
$menu    = ajuste('color_menu');
$logo    = ajuste('logo');
$favicon = ajuste('favicon');
$flash   = Session::pullFlash();
$route   = $_GET['r'] ?? 'incidencias/index';
$on      = fn(array $routes) => in_array($route, $routes, true) ? 'on' : '';
$scripts = $scripts ?? [];
$bare    = $bare ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title ?? '') ?> · <?= e($nombre) ?></title>
<?php if ($favicon): ?>
<link rel="icon" href="<?= e(upload_url($favicon)) ?>">
<?php else: ?>
<link rel="icon" href="data:image/svg+xml,<?= rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><rect width="32" height="32" rx="8" fill="' . $primary . '"/><path d="M16 6l8 3.5v6c0 5-3.4 8.4-8 10-4.6-1.6-8-5-8-10v-6z" fill="none" stroke="#fff" stroke-width="2"/></svg>') ?>">
<?php endif; ?>
<style>
:root{--ac:<?= $primary ?>;--ach:<?= mix($primary, '#000000', .18) ?>;--ac2:<?= mix($primary, '#ffffff', .9) ?>;--acl:<?= mix($primary, '#ffffff', .45) ?>;--navy:<?= $menu ?>}
</style>
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<script src="<?= e(asset('js/ui.js')) ?>"></script>
</head>
<body>
<?php if ($bare): ?>
<div class="auth"><?= $content ?></div>
<?php else: ?>
<aside class="side">
  <a class="brand" href="<?= e(url()) ?>">
    <?php if ($logo): ?><img src="<?= e(upload_url($logo)) ?>" alt="<?= e($nombre) ?>"><?php else: ?><i><?= ico('shield') ?></i><?php endif; ?>
    <span><?= e($nombre) ?></span>
  </a>
  <nav class="menu">
    <a class="<?= $on(['incidencias/index', 'incidencias/ver']) ?>" href="<?= e(url()) ?>"><?= ico('list') ?>Incidencias</a>
    <?php if ($user): ?>
      <a class="<?= $on(['incidencias/form']) ?>" href="<?= e(url('incidencias/form')) ?>"><?= ico('plus') ?>Nueva incidencia</a>
      <?php if (Auth::isAdmin()): ?>
        <small>Administración</small>
        <a class="<?= $on(['catalogos/index']) ?>" href="<?= e(url('catalogos/index')) ?>"><?= ico('grid') ?>Catálogos</a>
        <a class="<?= $on(['usuarios/index']) ?>" href="<?= e(url('usuarios/index')) ?>"><?= ico('users') ?>Usuarios</a>
        <a class="<?= $on(['historial/index']) ?>" href="<?= e(url('historial/index')) ?>"><?= ico('clock') ?>Historial</a>
        <a class="<?= $on(['ajustes/index']) ?>" href="<?= e(url('ajustes/index')) ?>"><?= ico('cog') ?>Configuración</a>
      <?php endif; ?>
    <?php endif; ?>
  </nav>
  <?php if ($user): ?>
    <div class="user">
      <span class="av"><?= e(mb_strtoupper(mb_substr($user['nombre'], 0, 1))) ?></span>
      <div><b><?= e(cut($user['nombre'], 18)) ?></b><span><?= Auth::isAdmin() ? 'Administrador' : 'Técnico' ?></span></div>
      <form method="post" action="<?= e(url('auth/salir')) ?>"><?= csrf_field() ?><button class="logout" title="Cerrar sesión"><?= ico('out') ?></button></form>
    </div>
  <?php else: ?>
    <div class="user"><a class="btnin" href="<?= e(url('auth/login')) ?>"><?= ico('in') ?>Ingresar</a></div>
  <?php endif; ?>
</aside>
<div class="main"><div class="wrap"><?= $content ?></div></div>
<?php endif; ?>
<?php foreach ($scripts as $s): ?><script src="<?= e(asset('js/' . $s)) ?>"></script>
<?php endforeach; ?>
<?php if ($flash): ?><script>UI.notify(<?= js($flash[1]) ?>,<?= js($flash[0]) ?>)</script><?php endif; ?>
</body>
</html>
