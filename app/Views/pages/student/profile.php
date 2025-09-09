<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>
Perfil
<?= $this->endSection() ?>

<?= $this->section('profile') ?>
<div class="container-fluid text-white min-vh-100 py-4">
    <!-- Profile Header -->
    <div class="mb-5">
        <h2 class="h4 fw-bold mb-4">Perfil</h2>
        <div class="d-flex justify-content-between align-items-center">
            <!-- User Info -->
            <div class="d-flex align-items-center">
                <div class="position-relative me-3">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face"
                        class="rounded-circle" width="80" height="80" alt="Profile" />
                    <div class="position-absolute bottom-0 end-0 bg-primary rounded-circle d-flex align-items-center justify-content-center border border-dark"
                        style="width:24px; height:24px; font-size:10px; font-weight:600;">PRO</div>
                </div>
                <div>
                    <h3 class="h5 fw-semibold mb-1 text-capitalize"><?= $user->username ?></h3>
                    <small class="text-secondary text-capitalize"><?= $user->role ?></small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-twitter"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm">X</button>
                <button class="btn btn-outline-secondary btn-sm">in</button>
                <button class="btn btn-outline-secondary btn-sm">@</button>
                <button class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-pencil-square"></i> Editar
                </button>
            </div>
        </div>
    </div>

    <!-- Personal Information Section -->
    <div class="rounded-3 p-4 mb-4" style="background: #1f293a;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 fw-semibold mb-0">Informação Pessoal</h3>
            <button class="btn btn-dark btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-pencil-square"></i> Editar
            </button>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">Nome</label>
                <div class="fw-medium text-capitalize"><?= $user->username ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">Apelido</label>
                <div class="fw-medium text-capitalize"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">Email</label>
                <div class="fw-medium"><?= $user->email ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">Telefone</label>
                <div class="fw-medium text-capitalize"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-secondary small">Bio</label>
                <div class="fw-medium text-capitalize"><?= $user->role ?></div>
            </div>
        </div>
    </div>

    <!-- Address Section -->
    <div class="rounded-3 p-4" style="background: #1f293a;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 fw-semibold mb-0">Morada</h3>
            <button class="btn btn-dark btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-pencil-square"></i> Editar
            </button>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">País</label>
                <div class="fw-medium">Estados Unidos</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">Cidade/Estado</label>
                <div class="fw-medium">Arizona, Estados Unidos</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">Código Postal</label>
                <div class="fw-medium">ERT 2489</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-secondary small">NIF</label>
                <div class="fw-medium">AS4568384</div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
