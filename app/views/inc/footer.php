  <div class="position-fixed bottom-0 end-0 mb-3 me-3 d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary" id="toggleTheme">
        Cambiar tema
    </button>
    <?php 
      if (isset($datos['title']) && $datos['title'] != "Login"){
        echo "<a href='" . URL . "/auth/logout' class='btn btn-danger'>Cerrar sesión</a>";
      }
    ?>
  </div>  
  
  <script src="<?= URL . '/js/theme-switcher.js' ?>"></script>
  <script src="<?= URL . '/js/bootstrap.bundle.min.js' ?>"></script>
</body>
</html>

