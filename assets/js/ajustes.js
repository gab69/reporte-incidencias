/** Configuración: previsualiza los colores al instante y permite restablecerlos. */
(() => {
  const form = document.getElementById('af');
  if (!form) return;
  const defaults = JSON.parse(form.dataset.defaults);
  const root = document.documentElement.style;

  const shade = (hex, withHex, ratio) => {
    const a = hex.match(/\w\w/g).map(h => parseInt(h, 16));
    const b = withHex.match(/\w\w/g).map(h => parseInt(h, 16));
    return '#' + a.map((v, i) => Math.round(v * (1 - ratio) + b[i] * ratio).toString(16).padStart(2, '0')).join('');
  };
  const applyPrimary = c => {
    root.setProperty('--ac', c);
    root.setProperty('--ach', shade(c.slice(1), '000000', .18));
    root.setProperty('--ac2', shade(c.slice(1), 'ffffff', .9));
    root.setProperty('--acl', shade(c.slice(1), 'ffffff', .45));
  };
  const applyMenu = c => root.setProperty('--navy', c);

  function link(picker, text, apply) {
    picker.addEventListener('input', () => { text.value = picker.value; apply(picker.value); });
    text.addEventListener('input', () => {
      if (/^#[0-9a-fA-F]{6}$/.test(text.value)) { picker.value = text.value; apply(text.value); }
    });
  }
  const cp = document.getElementById('cp'), cpt = document.getElementById('cp_t');
  const cm = document.getElementById('cm'), cmt = document.getElementById('cm_t');
  link(cp, cpt, applyPrimary);
  link(cm, cmt, applyMenu);

  document.getElementById('reset').onclick = () => {
    cp.value = cpt.value = defaults.color_primario;
    cm.value = cmt.value = defaults.color_menu;
    applyPrimary(defaults.color_primario);
    applyMenu(defaults.color_menu);
    UI.notify('info', 'Colores originales cargados. Pulsa "Guardar configuración" para aplicarlos.');
  };

  form.addEventListener('submit', e => {
    if (!/^#[0-9a-fA-F]{6}$/.test(cpt.value) || !/^#[0-9a-fA-F]{6}$/.test(cmt.value)) {
      e.preventDefault();
      UI.notify('warn', 'Los colores deben tener el formato #RRGGBB, por ejemplo #1d4ed8.');
    }
  });
})();
