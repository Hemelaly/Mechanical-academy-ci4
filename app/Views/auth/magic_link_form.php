<?php

$isLoggedIn   = auth()->loggedIn();

$user = service('auth')->user();


?>

<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.useMagicLink') ?> <?= $this->endSection() ?>

<?= $this->section('main3') ?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        height: 100vh;
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
                <h5 class="card-title mb-5 text-white"><?= lang('Auth.login') ?></h5>

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <?= esc($error) ?>
                                <br>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= esc(session('errors')) ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

            <form action="<?= url_to('magic-link') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="form-floating mb-2">
                    <input type="email" class="form-control" id="floatingEmailInput" name="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>"
                           value="<?= old('email', auth()->user()->email ?? null) ?>" required>
                    <label for="floatingEmailInput"><?= lang('Auth.email') ?></label>
                </div>

                <div class="d-grid col-12 col-md-8 mx-auto m-3">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.send') ?></button>
                </div>

            </form>

            <p class="text-center"><a href="<?= url_to('login') ?>"><?= lang('Auth.backToLogin') ?></a></p>

            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
