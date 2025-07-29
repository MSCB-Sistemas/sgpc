  <div class="position-fixed bottom-0 end-0 mb-3 me-3 d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary" id="toggleTheme">Cambiar tema</button>
    <?php 
      if (isset($datos['title']) && $datos['title'] != "Login"){
        echo "<a href='" . URL . "/auth/logout' class='btn btn-danger'>Cerrar sesión</a>";
      }
    ?>
  </div>

  <script src="<?= URL ?>/public/js/theme-toggle.js"></script>

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
</body>
</html>
