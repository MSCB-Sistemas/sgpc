<?php require_once APP . '/views/inc/header.php' ?>

<body id="body" class="bg-light text-dark d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <main class="w-100" style="max-width: 400px;">
        <form id="loginForm" class="rounded-4 shadow p-4 bg-light text-dark" method="POST" action="<?= URL ?>/auth/login">
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
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Recuérdame</label>
            </div>
            <div>
                <button class="btn btn-primary w-100 py-2" type="submit">Iniciar sesión</button>
            </div><br>
            <div>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
            </div>
            <p class="mt-5 mb-3 text-center">&copy;2025–2025</p>
        </form>
    </main>

<?php require_once APP . '/views/inc/footer.php' ?>
