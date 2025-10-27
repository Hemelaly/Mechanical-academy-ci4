<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>
Perfil
<?= $this->endSection() ?>

<?= $this->section('profile') ?>
<div class="container-fluid text-white min-vh-100 py-4">
    <!-- Profile Header -->
    <div class="mb-5">
        <h2 class="h4 fw-bold mb-4">Perfil</h2>
        <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center gap-3">
            <!-- User Info -->
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-md-1 gap-3 text-md-start text-center">
                <div class="position-relative me-3">
                    <img src="<?= base_url('assets/img/user-default.png') ?>"
                        class="rounded-circle object-fit-cover" width="80" height="80" alt="Foto de perfil de <?= $user->username ?>" />
                </div>
                <div>
                    <h3 class="h5 fw-semibold mb-1 text-capitalize"><?= $user->username ?></h3>
                    <small class="text-secondary text-capitalize"><?= $user->role ?></small>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="bi bi-pencil-square"></i> Editar
                </button>
            </div>
        </div>
    </div>

    <!-- Personal Information Section -->
    <div class="rounded-3 p-4 mb-4" style="background: #1f293a;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
            <h3 class="h5 fw-semibold mb-0">Informação Pessoal</h3>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
                <label class="form-label text-secondary small mb-1">Nome</label>
                <div class="fw-medium text-capitalize"><?= $user->username ?></div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <label class="form-label text-secondary small mb-1">Apelido</label>
                <div class="fw-medium text-capitalize">-</div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <label class="form-label text-secondary small mb-1">Email</label>
                <div class="fw-medium text-break"><?= $user->email ?></div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <label class="form-label text-secondary small mb-1">Telefone</label>
                <div class="fw-medium">+(258) --- ----</div>
            </div>
        </div>
    </div>

    <!-- Address Section -->
    <div class="rounded-3 p-4" style="background: #1f293a;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
            <h3 class="h5 fw-semibold mb-0">Morada</h3>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
                <label class="form-label text-secondary small mb-1">País</label>
                <div class="fw-medium">Moçambique</div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <label class="form-label text-secondary small mb-1">Provincia</label>
                <div class="fw-medium">Maputo</div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <label class="form-label text-secondary small mb-1">Cidade</label>
                <div class="fw-medium">Maputo</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: #1f293a; border: 1px solid #2d3748;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="editProfileModalLabel">Editar Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="profileForm">
                    <!-- Informações Pessoais -->
                    <div class="mb-4">
                        <h6 class="text-white mb-3 pb-2 border-bottom border-secondary">Informações Pessoais</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label text-white small">Nome</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="firstName" value="<?= $user->username ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label text-white small">Apelido</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="lastName" placeholder="Digite seu apelido">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label text-white small">Email</label>
                                <input type="email" class="form-control bg-dark text-white border-secondary" id="email" value="<?= $user->email ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label text-white small">Telefone</label>
                                <input type="tel" class="form-control bg-dark text-white border-secondary" id="phone" placeholder="+(258) 84 123 4567">
                            </div>
                        </div>
                    </div>

                    <!-- Morada -->
                    <div class="mb-4">
                        <h6 class="text-white mb-3 pb-2 border-bottom border-secondary">Morada</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label text-white small">País</label>
                                <select class="form-select bg-dark text-white border-secondary" id="country">
                                    <option value="Mocambique" selected>Moçambique</option>
                                    <option value="Angola">Angola</option>
                                    <option value="Brasil">Brasil</option>
                                    <option value="Portugal">Portugal</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="province" class="form-label text-white small">Província</label>
                                <select class="form-select bg-dark text-white border-secondary" id="province">
                                    <option value="Maputo" selected>Maputo</option>
                                    <option value="Gaza">Gaza</option>
                                    <option value="Inhambane">Inhambane</option>
                                    <option value="Sofala">Sofala</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label text-white small">Cidade</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="city" value="Maputo">
                            </div>
                        </div>
                    </div>

                    <!-- Foto de Perfil -->
                    <div class="mb-4">
                        <h6 class="text-white mb-3 pb-2 border-bottom border-secondary">Foto de Perfil</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face"
                                    class="rounded-circle object-fit-cover" width="80" height="80" alt="Foto atual de perfil" id="currentProfileImage">
                            </div>
                            <div class="flex-grow-1">
                                <label for="profileImage" class="form-label text-white small">Alterar foto</label>
                                <input class="form-control bg-dark text-white border-secondary" type="file" id="profileImage" accept="image/*">
                                <div class="form-text text-secondary">Formatos suportados: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveProfileChanges">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos adicionais para melhorar a experiência */
    .object-fit-cover {
        object-fit: cover;
    }

    .btn {
        transition: all 0.2s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    /* Estilização do modal */
    .modal-content {
        border-radius: 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        background-color: #1a2029;
    }

    .form-control,
    .form-select {
        color: #fff;
    }

    .form-control::placeholder {
        color: #6c757d;
    }

    /* Melhorar a legibilidade em dispositivos móveis */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .rounded-3 {
            border-radius: 0.5rem !important;
        }

        .modal-dialog {
            margin: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview da imagem de perfil
        const profileImageInput = document.getElementById('profileImage');
        const currentProfileImage = document.getElementById('currentProfileImage');

        if (profileImageInput) {
            profileImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        currentProfileImage.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Simular salvamento das alterações
        const saveButton = document.getElementById('saveProfileChanges');
        if (saveButton) {
            saveButton.addEventListener('click', function() {
                // Aqui você implementaria a lógica para salvar os dados
                // Por enquanto, apenas fechamos o modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                modal.hide();

                // Mostrar mensagem de sucesso
                alert('Perfil atualizado com sucesso!');
            });
        }

        // Validação do formulário
        const profileForm = document.getElementById('profileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // Lógica de validação e envio do formulário
            });
        }
    });
</script>
<?= $this->endSection() ?>