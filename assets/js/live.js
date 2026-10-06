/**
 * Búsqueda y filtros en vivo + paginación (incidencias e historial).
 * El contenedor [data-live="ruta"] indica la ruta; los controles llevan data-k="parametro".
 */
(() => {
  const root = document.querySelector('[data-live]');
  const res = document.getElementById('res');
  if (!root || !res) return;

  const route = root.dataset.live;
  const state = { page: +root.dataset.page || 1 };
  let controller = null;
  let timer = null;

  const controls = [...document.querySelectorAll('[data-k]')];
  controls.forEach(c => {
    const k = c.dataset.k;
    if (c.classList.contains('chip')) { if (c.classList.contains('on')) state[k] = +c.dataset.v; }
    else if (c.type === 'checkbox') { if (c.checked) state[k] = 1; }
    else if (c.value.trim()) state[k] = c.value.trim();
  });

  function load() {
    controller && controller.abort();
    controller = new AbortController();
    res.classList.add('busy');
    const qs = new URLSearchParams({ r: route });
    Object.keys(state).forEach(k => { if (state[k]) qs.set(k, state[k]); });
    const shown = qs.toString();
    qs.set('ajax', 1);
    fetch('index.php?' + qs, { signal: controller.signal })
      .then(r => { if (!r.ok) throw new Error(); return r.text(); })
      .then(html => {
        res.innerHTML = html;
        res.classList.remove('busy');
        history.replaceState(null, '', 'index.php?' + shown);
      })
      .catch(err => {
        if (err.name === 'AbortError') return;
        res.classList.remove('busy');
        UI.notify('err', 'No se pudo actualizar la lista. Revisa tu conexión e inténtalo otra vez.');
      });
  }

  function change(k, value) {
    if (value) state[k] = value; else delete state[k];
    state.page = 1;
  }

  controls.forEach(c => {
    const k = c.dataset.k;
    if (c.classList.contains('chip')) {
      c.addEventListener('click', () => {
        const v = +c.dataset.v;
        change(k, state[k] === v ? 0 : v);
        document.querySelectorAll('.chip[data-k="' + k + '"]').forEach(x => x.classList.toggle('on', +x.dataset.v === state[k]));
        load();
      });
    } else if (c.type === 'checkbox') {
      c.addEventListener('change', () => { change(k, c.checked ? 1 : 0); load(); });
    } else if (c.tagName === 'SELECT') {
      c.addEventListener('change', () => { change(k, c.value); load(); });
    } else {
      c.addEventListener('input', () => {
        change(k, c.value.trim());
        const clr = document.getElementById('clr');
        if (clr) clr.hidden = !c.value;
        clearTimeout(timer);
        timer = setTimeout(load, 220);
      });
    }
  });

  const clr = document.getElementById('clr');
  if (clr) clr.onclick = () => {
    const q = document.querySelector('[data-k="q"]');
    q.value = '';
    q.dispatchEvent(new Event('input'));
    q.focus();
  };

  // Paginación (botones dentro del resultado)
  res.addEventListener('click', e => {
    const b = e.target.closest('[data-page]');
    if (!b || b.disabled) return;
    state.page = +b.dataset.page;
    load();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();
