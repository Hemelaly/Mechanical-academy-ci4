<?php
// dd($user);
?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Perfil<?= $this->endSection() ?>

<?= $this->section('profile') ?>

<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <div class="container mx-auto">

        <!-- Profile Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                <div class="flex-1">
                    <h1 class="text-2xl lg:text-2xl font-bold text-slate-800 dark:text-white mb-3">
                        Meu Perfil
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Gerencie suas informações pessoais e preferências de conta
                    </p>
                </div>

                <button id="openProfileModal"
                    class="group inline-flex items-center gap-3 bg-gradient-to-br from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 px-6 py-3.5 text-white font-semibold rounded-2xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="bi bi-pencil-square text-sm"></i>
                    </div>
                    <span class="text-sm">Editar Perfil</span>
                </button>
            </div>

            <!-- User Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 p-6 mb-8">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <!-- Avatar -->
                    <div class="relative">
                        <img src="<?= base_url($user->img ?? 'assets/img/user-default.png') ?>"
                            class="w-24 h-24 rounded-2xl object-cover border-4 border-white dark:border-slate-800 shadow-lg"
                            alt="Foto de perfil de <?= esc($user->username) ?>" />
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white dark:border-slate-800 flex items-center justify-center">
                            <i class="bi bi-check text-white text-xs"></i>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 capitalize">
                            <?= esc($user->username) ?>
                        </h2>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-sm">
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <i class="bi bi-envelope text-blue-500"></i>
                                <span><?= esc($user->email ?? 'Não definido') ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <i class="bi bi-telephone text-green-500"></i>
                                <span><?= esc($user->phone ?? 'Não definido') ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <i class="bi bi-person-badge text-purple-500"></i>
                                <span class="capitalize"><?= $user->role ?></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Personal Information -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-person text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Informação Pessoal</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Seus dados básicos</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-person-circle text-blue-500"></i>
                                <span>Nome Completo</span>
                            </div>
                            <div class="font-medium text-slate-800 dark:text-white text-sm capitalize">
                                <?= esc($user->username ?? 'Não definido') ?>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-at text-purple-500"></i>
                                <span>Email</span>
                            </div>
                            <div class="font-medium text-slate-800 dark:text-white text-sm break-words">
                                <?= esc($user->email ?? 'Não definido') ?>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-telephone text-green-500"></i>
                                <span>Telefone</span>
                            </div>
                            <div class="font-medium text-slate-800 dark:text-white text-sm">
                                <?= esc($user->phone ?? 'Não definido') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-geo-alt text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Morada</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Sua localização</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-globe text-amber-500"></i>
                                <span>País</span>
                            </div>
                            <div class="font-medium text-slate-800 dark:text-white text-sm">
                                <?= esc($user->country ?? 'Não definido') ?>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-building text-red-500"></i>
                                <span>Província</span>
                            </div>
                            <div class="font-medium text-slate-800 dark:text-white text-sm">
                                <?= esc($user->province ?? 'Não definido') ?>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-geo text-cyan-500"></i>
                                <span>Cidade</span>
                            </div>
                            <div class="font-medium text-slate-800 dark:text-white text-sm">
                                <?= esc($user->city ?? 'Não definido') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="mt-6 bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="bi bi-shield-check text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Configurações da Conta</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Preferências e segurança</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button id="openPasswordSettings" type="button" class="group p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 text-left">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="bi bi-key text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <div class="text-sm font-medium text-slate-800 dark:text-white mb-1">Alterar Senha</div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs">Atualize sua senha de acesso</div>
                    </button>

                    <button id="openNotificationSettings" type="button" class="group p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-600 transition-all duration-300 text-left">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="bi bi-bell text-green-600 dark:text-green-400 text-sm"></i>
                        </div>
                        <div class="text-sm font-medium text-slate-800 dark:text-white mb-1">Notificações</div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs">Gerencie alertas e emails</div>
                    </button>

                    <button id="toggleAppearanceSettings" type="button" class="group p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 text-left">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="bi bi-palette text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        <div class="text-sm font-medium text-slate-800 dark:text-white mb-1">Aparência</div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs">Tema claro/escuro</div>
                    </button>

                    <button id="profileLogout" type="button" data-href="<?= site_url('logout') ?>" class="group p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-red-300 dark:hover:border-red-600 transition-all duration-300 text-left">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="bi bi-box-arrow-right text-red-600 dark:text-red-400 text-sm"></i>
                        </div>
                        <div class="text-sm font-medium text-slate-800 dark:text-white mb-1">Sair</div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs">Encerrar sessão</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-t-3xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="bi bi-pencil-square text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Editar Perfil</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Atualize suas informações pessoais</p>
                    </div>
                </div>
                <button id="closeProfileModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <form action="<?= site_url('/student/dashboard/perfil') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">

                <!-- Profile Image Section -->
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="relative mb-4">
                        <img id="profilePreview" src="<?= base_url($user->img ?? 'assets/img/user-default.png') ?>"
                            class="w-32 h-32 rounded-2xl object-cover border-4 border-white dark:border-slate-800 shadow-lg">
                        <label for="profileImage" class="absolute bottom-2 right-2 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:bg-blue-600 transition-colors">
                            <i class="bi bi-camera text-white text-sm"></i>
                        </label>
                    </div>
                    <input type="file" id="profileImage" name="imagem" accept="image/*" class="hidden">
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Clique no ícone da câmera para alterar a foto</p>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">Formatos: JPG, PNG, GIF • Máx: 2MB</p>
                </div>

                <!-- Personal Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="firstName">
                            <i class="bi bi-person text-blue-500 mr-2"></i>
                            Nome Completo
                        </label>
                        <input type="text" name="nome" id="firstName"
                            value="<?= esc($user->username ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="email">
                            <i class="bi bi-envelope text-purple-500 mr-2"></i>
                            Email
                        </label>
                        <input type="email" name="email" id="email"
                            value="<?= esc($user->email ?? '') ?>"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="phone">
                            <i class="bi bi-telephone text-green-500 mr-2"></i>
                            Telefone
                        </label>
                        <input type="tel" name="telefone" id="phone"
                            value="<?= esc($user->phone ?? '') ?>"
                            placeholder="+(258) 84 123 4567"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                <!-- Address -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="country">
                            <i class="bi bi-globe text-amber-500 mr-2"></i>
                            País
                        </label>
                        <select id="country" name="pais" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="Mocambique" <?= ($user->country ?? '') == 'Mocambique' ? 'selected' : '' ?>>Moçambique</option>
                            <option value="Angola" <?= ($user->country ?? '') == 'Angola' ? 'selected' : '' ?>>Angola</option>
                            <option value="Brasil" <?= ($user->country ?? '') == 'Brasil' ? 'selected' : '' ?>>Brasil</option>
                            <option value="Portugal" <?= ($user->country ?? '') == 'Portugal' ? 'selected' : '' ?>>Portugal</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="province">
                            <i class="bi bi-building text-red-500 mr-2"></i>
                            Província
                        </label>
                        <select id="province" name="provincia" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="Maputo" <?= ($user->province ?? '') == 'Maputo' ? 'selected' : '' ?>>Maputo</option>
                            <option value="Gaza" <?= ($user->province ?? '') == 'Gaza' ? 'selected' : '' ?>>Gaza</option>
                            <option value="Inhambane" <?= ($user->province ?? '') == 'Inhambane' ? 'selected' : '' ?>>Inhambane</option>
                            <option value="Sofala" <?= ($user->province ?? '') == 'Sofala' ? 'selected' : '' ?>>Sofala</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="city">
                            <i class="bi bi-geo text-cyan-500 mr-2"></i>
                            Cidade
                        </label>
                        <input type="text" id="city" name="cidade"
                            value="<?= esc($user->city ?? 'Maputo') ?>"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                <!-- Password Change -->
                <h3 class="text-lg font-bold text-slate-800 dark:text-white pt-6 border-t border-slate-200 dark:border-slate-700">
                    <i class="bi bi-key text-blue-500 mr-2"></i>
                    Alterar Senha
                </h3>

                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    Preencha estes campos apenas se deseja alterar sua senha.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Current Password -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            <i class="bi bi-lock-fill text-red-500 mr-2"></i>
                            Senha Atual
                        </label>
                        <input type="password" name="password_actual" id="currentPassword"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900
                      border border-slate-200 dark:border-slate-700
                      text-slate-800 dark:text-white
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                      transition-all duration-200"
                            placeholder="Digite sua senha atual">
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            <i class="bi bi-shield-lock text-green-500 mr-2"></i>
                            Nova Senha
                        </label>
                        <input type="password" name="new_password"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900
                      border border-slate-200 dark:border-slate-700
                      text-slate-800 dark:text-white
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                      transition-all duration-200"
                            placeholder="Mínimo 6 caracteres">
                    </div>

                    <!-- Confirm New Password -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            <i class="bi bi-check-circle text-purple-500 mr-2"></i>
                            Confirmar Nova Senha
                        </label>
                        <input type="password" name="confirm_password"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900
                      border border-slate-200 dark:border-slate-700
                      text-slate-800 dark:text-white
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                      transition-all duration-200"
                            placeholder="Repita a nova senha">
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" id="cancelProfile"
                        class="px-6 py-3 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium transition-all duration-200">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profileModal = document.getElementById('profileModal');
        const openBtn = document.getElementById('openProfileModal');
        const closeBtn = document.getElementById('closeProfileModal');
        const cancelBtn = document.getElementById('cancelProfile');

        // Modal controls
        openBtn.addEventListener('click', () => profileModal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => profileModal.classList.add('hidden'));
        cancelBtn.addEventListener('click', () => profileModal.classList.add('hidden'));

        // Close modal on backdrop click
        profileModal.addEventListener('click', (e) => {
            if (e.target === profileModal) {
                profileModal.classList.add('hidden');
            }
        });

        // Image preview functionality
        const input = document.getElementById('profileImage');
        const img = document.getElementById('profilePreview');
        const MAX_SIZE = 2 * 1024 * 1024;
        const ALLOWED = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const changePasswordBtn = document.getElementById('openPasswordSettings');
        const notificationBtn = document.getElementById('openNotificationSettings');
        const appearanceBtn = document.getElementById('toggleAppearanceSettings');
        const logoutBtn = document.getElementById('profileLogout');

        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;

            if (!ALLOWED.includes(file.type)) {
                alert('Formato de imagem não suportado. Use JPG, PNG, GIF ou WebP.');
                input.value = '';
                return;
            }

            if (file.size > MAX_SIZE) {
                alert('A imagem é muito grande. Tamanho máximo: 2MB');
                input.value = '';
                return;
            }

            img.src = URL.createObjectURL(file);
        });

        if (changePasswordBtn) {
            changePasswordBtn.addEventListener('click', () => {
                profileModal.classList.remove('hidden');
                const passwordInput = document.getElementById('currentPassword');
                if (passwordInput) {
                    passwordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    passwordInput.focus();
                }
            });
        }

        if (appearanceBtn) {
            appearanceBtn.addEventListener('click', () => {
                const themeToggle = document.getElementById('theme-toggle');
                if (themeToggle) {
                    themeToggle.click();
                    return;
                }
                const root = document.documentElement;
                const nextTheme = root.classList.contains('dark') ? 'light' : 'dark';
                root.classList.toggle('dark');
                localStorage.setItem('theme', nextTheme);
            });
        }

        if (notificationBtn) {
            notificationBtn.addEventListener('click', () => {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Em breve',
                        text: 'Configurações de notificações ainda não estão disponíveis.'
                    });
                } else {
                    alert('Configurações de notificações ainda não estão disponíveis.');
                }
            });
        }

        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                const href = logoutBtn.getAttribute('data-href');
                if (!href) return;

                if (window.Swal) {
                    Swal.fire({
                        title: 'Tem certeza?',
                        text: 'Deseja realmente sair da sua conta?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, sair',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = href;
                        }
                    });
                } else if (confirm('Deseja realmente sair da sua conta?')) {
                    window.location.href = href;
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>
