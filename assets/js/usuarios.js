/** Usuarios: el formulario aparece solo al pulsar "Agregar usuario" o "Editar". */
(() => {
  const modal = document.getElementById('userModal');
  const form = document.getElementById('uf');
  if (!modal || !form) return;

  const title = document.getElementById('ft');
  const passLabel = document.getElementById('cl');
  const submit = document.getElementById('sb');
  const activeBox = document.getElementById('actw');
  const active = form.elements['activo'];

  function open(user) {
    form.reset();
    const editing = !!user;
    form.elements['id'].value = editing ? user.id : 0;
    if (editing) {
      form.elements['usuario'].value = user.usuario;
      form.elements['nombre'].value = user.nombre;
      form.elements['rol'].value = user.rol;
      active.checked = !!user.activo;
      active.dataset.was = user.activo ? '1' : '0';
    } else {
      active.dataset.was = '0';
    }
    form.elements['clave'].required = !editing;
    activeBox.hidden = !editing;
    title.textContent = editing ? 'Editar usuario: ' + user.usuario : 'Agregar usuario';
    passLabel.textContent = editing ? 'Nueva contraseña (vacía = no cambiar)' : 'Contraseña (mín. 6)';
    submit.textContent = editing ? 'Guardar cambios' : 'Crear usuario';
    modal.hidden = false;
    form.elements['usuario'].focus();
  }
  const close = () => { modal.hidden = true; };

  document.getElementById('addUser').onclick = () => open(null);
  document.querySelectorAll('[data-edit]').forEach(b => b.onclick = () => open(JSON.parse(b.dataset.edit)));
  document.getElementById('cn').onclick = close;
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) close(); });

  form.addEventListener('submit', e => {
    const pass = form.elements['clave'].value;
    const isNew = form.elements['id'].value === '0';
    if ((isNew && pass.length < 6) || (pass && pass.length < 6)) {
      e.preventDefault();
      UI.notify('warn', 'La contraseña debe tener al menos 6 caracteres.');
    }
  });

  // Búsqueda instantánea en la tabla
  const input = document.getElementById('fq');
  const rows = [...document.querySelectorAll('tr[data-t]')];
  const count = document.getElementById('fc');
  const filter = () => {
    const q = input.value.trim().toLowerCase();
    let n = 0;
    rows.forEach(r => { const ok = !q || r.dataset.t.includes(q); r.hidden = !ok; if (ok) n++; });
    count.textContent = n + (n === 1 ? ' usuario' : ' usuarios');
  };
  input.addEventListener('input', filter);
  filter();
})();
