<?php

function getSidebar($page)
{
    return '
        <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <!-- Sidebar Toggle Button -->
            <div class="border bg-card cursor-pointer py-1 px-2 rounded-2 me-3" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </div>

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="./assets/img/logo_light.png" class="w-100" />
            </a>

            <!-- Search Bar -->
            <div class="flex-grow-1 mx-4">
                <div class="position-relative lg-w-75" style="max-width: 500px; width: 100%;">
                    <input type="text" class="form-control bg-dark border border-custom-secondary text-light"
                        placeholder="Pesquisar curso..." style="padding-left: 2.5rem;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                </div>
            </div>

            <!-- Right Side Icons -->
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <div class="px-2 py-2 rounded-2 border border-custom-color dropdown-toggle text-white" type="button"
                        data-bs-toggle="dropdown">
                        <img src="./assets/img/user.jpg" class="rounded-circle me-2" width="24" height="24"
                            alt="Profile">
                        John Doe
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end bg-card">
                        <li><a class="dropdown-item text-white navbar-dopdown" href="./profile.php">Perfil</a></li>
                        <!-- <li><hr class="dropdown-divider"></li> -->
                        <li><a class="dropdown-item text-white navbar-dopdown" href="./logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <div class="sidebar-section">
                <h6 class="sidebar-title">MENU</h6>
                <ul class="sidebar-menu">
                    <li class="sidebar-item ' . (($page == 'home') ? 'active' : '') . '">
                        <a href="/" class="sidebar-link">
                            <i class="bi bi-house-door"></i>
                            <span>Início</span>
                        </a>
                    </li>
                    <li class="sidebar-item ' . (($page == 'Usuarios') ? 'active' : '') . '">
                        <a href="users.php" class="sidebar-link">
                            <i class="bi bi-journals"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li class="sidebar-item ' . (($page == 'Todos Cursos') ? 'active' : '') . '">
                        <a href="all_courses.php" class="sidebar-link">
                            <i class="bi bi-journals"></i>
                            <span>Cursos</span>
                        </a>
                    </li>
                    <li class="sidebar-item ' . (($page == 'Perfil') ? 'active' : '') . '">
                        <a href="profile.php" class="sidebar-link">
                            <i class="bi bi-person-circle"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
';
}