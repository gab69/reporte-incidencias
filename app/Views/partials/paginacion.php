<?php
/** Paginación: usa $page, $pages y $total. Los botones los maneja live.js (data-page). */
if ($pages <= 1) {
    return;
}
$items = [1];
for ($i = max(2, $page - 2); $i <= min($pages - 1, $page + 2); $i++) {
    $items[] = $i;
}
$items[] = $pages;
$items = array_values(array_unique($items));
$prev = 0;
?>
<nav class="pager" aria-label="Paginación">
  <button type="button" class="pg" data-page="<?= $page - 1 ?>" <?= $page <= 1 ? 'disabled' : '' ?>>‹ Anterior</button>
  <?php foreach ($items as $n): ?>
    <?php if ($n - $prev > 1): ?><span class="gap">…</span><?php endif; ?>
    <button type="button" class="pg <?= $n === $page ? 'on' : '' ?>" data-page="<?= $n ?>"><?= $n ?></button>
    <?php $prev = $n; ?>
  <?php endforeach; ?>
  <button type="button" class="pg" data-page="<?= $page + 1 ?>" <?= $page >= $pages ? 'disabled' : '' ?>>Siguiente ›</button>
</nav>
