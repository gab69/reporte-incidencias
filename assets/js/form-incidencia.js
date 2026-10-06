/** Formulario de incidencia: pasos dinámicos, imágenes, pegar capturas y avisos. */
(() => {
  const form = document.getElementById('frm');
  const box = document.getElementById('ps');
  const tpl = document.getElementById('tpl');
  if (!form || !box || !tpl) return;

  const uploads = form.dataset.uploads;
  let counter = 0;
  let dirty = false;

  const renumber = () => [...box.children].forEach((el, i) => { el.querySelector('.n').textContent = 'Paso ' + (i + 1); });

  function addStep(texto, imagen) {
    const k = counter++;
    const el = tpl.content.firstElementChild.cloneNode(true);
    el.querySelectorAll('[data-n]').forEach(x => { x.name = 'pasos[' + k + '][' + x.dataset.n + ']'; });
    const file = el.querySelector('[data-f]');
    file.name = 'imagen[' + k + ']';
    const text = el.querySelector('textarea');
    const hidden = el.querySelector('[data-n=img]');
    const img = el.querySelector('img');
    const remove = el.querySelector('[data-r]');
    text.value = texto || '';

    const show = src => { img.hidden = !src; remove.hidden = !src; if (src) img.src = src; };
    if (imagen) { hidden.value = imagen; show(uploads + imagen); }

    file.onchange = () => {
      const f = file.files[0];
      if (!f) return;
      if (!f.type.startsWith('image/')) { file.value = ''; UI.notify('warn', 'El archivo elegido no es una imagen.'); return; }
      if (f.size > 8 * 1048576) UI.notify('warn', 'La imagen pesa ' + (f.size / 1048576).toFixed(1) + ' MB. Se reducirá y convertirá a WebP, pero la subida puede tardar o ser rechazada por el servidor.');
      show(URL.createObjectURL(f));
      hidden.value = '';
      dirty = true;
    };
    text.addEventListener('paste', ev => {
      const item = [...ev.clipboardData.items].find(i => i.type.startsWith('image/'));
      if (!item) return;
      const dt = new DataTransfer();
      dt.items.add(item.getAsFile());
      file.files = dt.files;
      file.onchange();
      ev.preventDefault();
    });
    remove.onclick = () => { hidden.value = ''; file.value = ''; show(''); dirty = true; };
    el.querySelector('[data-x]').onclick = () => {
      if (box.children.length < 2) { UI.notify('warn', 'La incidencia necesita al menos un paso.'); return; }
      UI.ask('Se quitará este paso y su imagen al guardar.', () => { el.remove(); renumber(); dirty = true; },
        { danger: true, ok: 'Quitar paso', title: '¿Eliminar este paso?' });
    };
    el.querySelector('[data-u]').onclick = () => { if (el.previousElementSibling) box.insertBefore(el, el.previousElementSibling); renumber(); dirty = true; };
    el.querySelector('[data-d]').onclick = () => { if (el.nextElementSibling) box.insertBefore(el.nextElementSibling, el); renumber(); dirty = true; };
    box.appendChild(el);
    renumber();
  }

  document.getElementById('ap').onclick = () => { addStep(); dirty = true; box.lastElementChild.querySelector('textarea').focus(); };
  form.addEventListener('input', () => { dirty = true; });
  form.addEventListener('submit', e => {
    if (!form.titulo.value.trim()) { e.preventDefault(); UI.notify('warn', 'Escribe un título para la incidencia.'); form.titulo.focus(); return; }
    const ok = [...box.children].some(s => s.querySelector('textarea').value.trim() || s.querySelector('[data-n=img]').value || s.querySelector('[data-f]').files.length);
    if (!ok) { e.preventDefault(); UI.notify('warn', 'Agrega al menos un paso con texto o imagen.'); return; }
    dirty = false;
  });
  window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

  JSON.parse(document.getElementById('pasos-data').textContent).forEach(p => addStep(p.texto, p.imagen));
  dirty = false;
})();
