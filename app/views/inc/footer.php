
  <script src="<?= URL . '/js/bootstrap.bundle.min.js' ?>"></script>
  <script src="../assets/dist/js/bootstrap.bundle.min.js" class="astro-vvvwv3sm"></script>
  <script>
  const body = document.getElementById('body');
  const form = document.getElementById('loginForm');
  const toggleBtn = document.getElementById('toggleTheme');

  // Cargar tema guardado
  if (localStorage.getItem('theme') === 'dark') {
    activarModoOscuro();
  }

  toggleBtn.addEventListener('click', () => {
    if (body.classList.contains('bg-light')) {
      activarModoOscuro();
      localStorage.setItem('theme', 'dark');
    } else {
      activarModoClaro();
      localStorage.setItem('theme', 'light');
    }
  });

  function activarModoOscuro() {
    body.classList.replace('bg-light', 'bg-dark');
    body.classList.replace('text-dark', 'text-white');
    form.classList.replace('bg-white', 'bg-dark');
    form.classList.replace('text-dark', 'text-white');
  }

  function activarModoClaro() {
    body.classList.replace('bg-dark', 'bg-light');
    body.classList.replace('text-white', 'text-dark');
    form.classList.replace('bg-dark', 'bg-white');
    form.classList.replace('text-white', 'text-dark');
  }
</script>
</body>
</html>

