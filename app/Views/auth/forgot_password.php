<?php

$isLoggedIn   = auth()->loggedIn();

$user = service('auth')->user();

?>

<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>Recuperar Senha<?= $this->endSection() ?>

<?= $this->section('main2') ?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        min-height: 100vh;
        width: 100%;
        background-image: url(https://kajabi-storefronts-production.kajabi-cdn.com/kajabi-storefronts-production/file-uploads/themes/2152537062/settings_images/a66be81-c84a-a41c-a2bb-044835e5116_Landing_BG.webp);
        font-family: 'Poppins', sans-serif;
    }

    .overlay {
        height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, 0.8);
    }
</style>

<nav class="navbar navbar-expand-lg sticky-top bg-black navbar-dark py-3">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="<?= base_url('./assets/img/logo.png') ?>" alt="Logo" style="width: 150px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item me-3">
                        <a class="nav-link active" href="<?= base_url($user->role . '/dashboard/meus_cursos') ?>">Meus Cursos</a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a href="<?= base_url($user->role . '/dashboard/perfil') ?>" class="d-flex align-items-center text-decoration-none">
                            <img src="<?= base_url('assets/img/user-default.png') ?>" alt="User" class="rounded-circle me-2" width="35" height="35">
                            <span class="text-white fw-semibold text-nowrap"><?= $user->username ?></span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-3">
                        <a class="nav-link active" href="<?= base_url('/#cursos') ?>">Cursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('login') ?>">Entrar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="d-flex align-items-center pt-5">
    <div class="container d-flex justify-content-center p-5">
        <div class="col-12 col-md-5 pt-5">
            <div class="card-body">
                <h5 class="card-title mb-4 text-white">Recuperar Senha</h5>
                <p class="text-white-50 mb-4">Digite seu email para receber o link de redefinicao.</p>

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
                <?php elseif (session('message') !== null) : ?>
                    <div class="alert alert-success" role="alert"><?= esc(session('message')) ?></div>
                <?php endif ?>

                <form action="<?= site_url('reset-password/request') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-floating mb-4">
                        <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="Email" value="<?= old('email') ?>" required>
                        <label for="floatingEmailInput">Email</label>
                    </div>

                    <div class="d-block w-100">
                        <button type="submit" class="btn btn-primary btn-block px-5 py-2">Enviar link</button>
                    </div>

                    <div class="mt-3 text-center">
                        <a class="link-primary text-decoration-underline" href="<?= base_url('login') ?>">Voltar para login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
