/**
 * UI: ventanas emergentes (mensajes temporales y confirmaciones), visor de imágenes
 * y confirmación automática de formularios con data-confirm.
 */
const UI = (() => {
  const ICON = { ok: '✓', err: '✕', warn: '!', info: 'i' };
  const TITLE = { ok: 'Listo', err: 'No se pudo completar', warn: 'Atención', info: 'Información' };
  const TIME = { ok: 1000, info: 1000, warn: 1000, err: 1000 }; // milisegundos
  let current = null;

  function close() {
    if (!current) return;
    clearTimeout(current.timer);
    const el = current.el;
    el.classList.add('out');
    setTimeout(() => el.remove(), 160);
    current = null;
  }

  function open(type, title, message, autoMs, buttons) {
    close();
    const el = document.createElement('div');
    el.className = 'ov';
    el.innerHTML = '<div class="dlg ' + type + '" role="alertdialog"><div class="ic">' + ICON[type] + '</div><h3></h3><p></p>'
      + (buttons ? '<div class="acts"></div>' : '')
      + (autoMs ? '<div class="bar2"><i style="animation-duration:' + autoMs + 'ms"></i></div>' : '') + '</div>';
    el.querySelector('h3').textContent = title;
    el.querySelector('p').textContent = message;
    if (buttons) {
      const box = el.querySelector('.acts');
      buttons.forEach(([label, cls, fn]) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.textContent = label;
        b.className = cls || '';
        b.onclick = () => { close(); if (fn) fn(); };
        box.appendChild(b);
      });
    } else {
      el.onclick = close;
    }
    document.body.appendChild(el);
    current = { el, timer: autoMs ? setTimeout(close, autoMs) : 0 };
  }

  /** Mensaje temporal que se cierra solo. */
  function notify(type, message, ms) { open(type, TITLE[type], message, ms || TIME[type]); }

  /** Pide confirmación (no se cierra solo). */
  function ask(message, onOk, opts) {
    opts = opts || {};
    open(opts.type || 'warn', opts.title || '¿Confirmas esta acción?', message, 0,
      [['Cancelar', '', null], [opts.ok || 'Sí, continuar', opts.danger ? 'p d2' : 'p', onOk]]);
  }

  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

  // Formularios con data-confirm, o que desactivan un elemento "activo", piden confirmación.
  document.addEventListener('submit', e => {
    const f = e.target;
    if (e.defaultPrevented || f.dataset.go) return;
    let msg = f.dataset.confirm;
    const cb = f.querySelector('input[name=activo]');
    if (!msg && cb && cb.dataset.was === '1' && !cb.checked) msg = 'Al desactivarlo dejará de estar disponible. ¿Deseas continuar?';
    if (msg) {
      e.preventDefault();
      ask(msg, () => { f.dataset.go = 1; f.submit(); });
    }
  });

  // Visor de imágenes a pantalla completa.
  document.addEventListener('click', e => {
    const a = e.target.closest('a.zoom');
    if (!a) return;
    e.preventDefault();
    close();
    const el = document.createElement('div');
    el.className = 'ov lb';
    el.innerHTML = '<img alt="">';
    el.firstChild.src = a.href;
    el.onclick = () => el.remove();
    document.body.appendChild(el);
  });

  return { notify, ask, close };
})();
