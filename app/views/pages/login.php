<?php require_once APP . '/views/inc/header.php' ?>

<body id="body" class="bg-light text-dark d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <main class="w-100" style="max-width: 400px;">
        <form class="rounded-4 shadow p-4 bg-white" id="loginForm" method="POST" action="<?= URL ?>/auth/login">
            <h1 class="h3 mb-3 fw-normal text-center">Bienvenido</h1>
            
            <div class="form-floating mb-3">
                <input type="text" name="user" class="form-control" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">Usuario</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Contraseña</label>
            </div>

            <div class="form-check text-start mb-3">
                <input class="form-check-input" type="checkbox" value="remember-me" id="checkDefault">
                <label class="form-check-label" for="checkDefault">Recuérdame</label>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Iniciar sesión</button>
            <p class="mt-5 mb-3 text-center">&copy;2025–2025</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

        <!-- Botón cambio de tema -->
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleTheme">
                    Cambiar tema
                </button>
            </div>
        </form>
    </main>

<?php require_once APP . '/views/inc/footer.php' ?>
