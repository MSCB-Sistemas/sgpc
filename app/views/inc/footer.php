  <div class="position-fixed bottom-0 end-0 mb-3 me-3 d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary" id="toggleTheme">Cambiar tema</button>
    <?php 
      if (isset($datos['title']) && $datos['title'] != "Login"){
        echo "<a href='" . URL . "/auth/logout' class='btn btn-danger'>Cerrar sesión</a>";
      }
    ?>
  </div>

  <!-- ✅ Solo una vez Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const abmCollapse = document.getElementById('submenuABM');
      const abmArrow = document.getElementById('abm-arrow');

      if (abmCollapse && abmArrow) {
        abmCollapse.addEventListener('show.bs.collapse', () => {
          abmArrow.innerHTML = '&#x25B2;'; // ▲
        });

        abmCollapse.addEventListener('hide.bs.collapse', () => {
          abmArrow.innerHTML = '&#x25BC;'; // ▼
        });
      }
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
    
      const html = document.documentElement;
      const toggleBtn = document.getElementById('toggleTheme');
      const body = document.body

      if (toggleBtn) {
          const savedTheme = localStorage.getItem('theme') || 'light';
          setTheme(savedTheme);

          toggleBtn.addEventListener('click', () => {
              const currentTheme = html.getAttribute('data-bs-theme');
              const newTheme = currentTheme === 'light' ? 'dark' : 'light';
              setTheme(newTheme);
              localStorage.setItem('theme', newTheme);
          });
      }

      function setTheme(theme) {
          html.setAttribute('data-bs-theme', theme);

          const sidebar = document.getElementById('sidebar');
          const form = document.getElementById('loginForm');
          const logo = document.getElementById('logo');
          const navLinks = document.querySelectorAll('.nav-link');

          if (theme === 'dark') {
              body.classList.add('bg-dark', 'text-white');
              body.classList.remove('bg-light', 'text-dark');

              if (sidebar) {
                  sidebar.classList.add('bg-dark', 'text-white');
                  sidebar.classList.remove('#ff6900', 'text-dark');
              }

              if (form) {
                  form.classList.add('bg-dark', 'text-white');
                  form.classList.remove('bg-light', 'text-dark');
              }

              if (logo) {
                  logo.src = logo.dataset.logoLight;
              }

              navLinks.forEach(link => {
                  link.classList.remove('text-dark');
                  link.classList.add('text-white');
              });

          } else {
              body.classList.add('bg-light', 'text-dark');
              body.classList.remove('bg-dark', 'text-white');

              if (sidebar) {
                  sidebar.classList.add('#ff6900', 'text-dark');
                  sidebar.classList.remove('bg-dark', 'text-white');
              }

              if (form) {
                  form.classList.add('bg-light', 'text-dark');
                  form.classList.remove('bg-dark', 'text-white');
              }

              if (logo) {
                  logo.src = logo.dataset.logoDark;
              }

              navLinks.forEach(link => {
                  link.classList.remove('text-white');
                  link.classList.add('text-dark');
              });
          }
      }
  });
  </script>
</body>
</html>
