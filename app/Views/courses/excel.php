<?php

$isLoggedIn   = auth()->loggedIn();

$user = service('auth')->user();

// dd($course)


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Excel Profissional</title>

  <!-- Bootstrap Icons -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('./assets/css/output.css') ?>">

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

    body {
      font-family: "Poppins", sans-serif;
    }
  </style>
</head>

<body class="bg-[#070707]">
  
  <nav class="sticky top-0 z-50 bg-black text-white py-4">
    <div class="container mx-auto px-4">
      <div class="flex h-16 items-center justify-between py-3">
        <!-- Brand -->
        <a class="inline-flex items-center" href="/">
          <img src="<?= base_url('./assets/img/logo.png') ?>" alt="Logo" class="w-[150px] h-auto">
        </a>

        <!-- Toggler (mobile only) -->
        <button
          id="navToggle"
          type="button"
          class="lg:hidden inline-flex items-center justify-center rounded-md p-2 ring-1 ring-white/20 hover:bg-white/10 focus:outline-none focus-visible:ring"
          aria-controls="mobileMenu"
          aria-expanded="false"
          aria-label="Abrir menu">
          <svg id="iconOpen" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg id="iconClose" class="hidden h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Menu (desktop only) -->
        <div class="hidden lg:flex lg:items-center">
          <ul class="flex items-center gap-8">
            <?php if ($isLoggedIn): ?>
              <li>
                <a class="text-white/80 hover:text-white font-medium transition"
                  href="<?= base_url($user->role . '/dashboard/meus_cursos') ?>">
                  Meus Cursos
                </a>
              </li>
              <li>
                <a href="<?= base_url($user->role . '/dashboard/perfil') ?>"
                  class="inline-flex items-center gap-3 text-white/90 hover:text-white no-underline">
                  <img src="<?= base_url('assets/img/user-default.png') ?>"
                    alt="User" class="h-auto rounded-full object-cover" style="width: 50px;">
                  <span class="font-semibold truncate max-w-[10rem]"><?= $user->username ?></span>
                </a>
              </li>
            <?php else: ?>
              <li>
                <a class="text-white/80 hover:text-white font-medium transition"
                  href="<?= base_url('/#cursos') ?>">
                  Cursos
                </a>
              </li>
              <li>
                <a class="text-white/80 hover:text-white font-medium transition"
                  href="<?= base_url('login') ?>">
                  Entrar
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <!-- Menu (mobile) -->
      <div id="mobileMenu" class="hidden lg:hidden border-t border-white/10">
        <ul class="flex flex-col gap-2 py-3">
          <?php if ($isLoggedIn): ?>
            <li>
              <a class="block px-2 py-2 text-white/90 hover:bg-white/10 rounded-md"
                href="<?= base_url($user->role . '/dashboard/meus_cursos') ?>">
                Meus Cursos
              </a>
            </li>
            <li>
              <a href="<?= base_url($user->role . '/dashboard/perfil') ?>"
                class="flex items-center gap-3 px-2 py-2 hover:bg-white/10 rounded-md no-underline">
                <img src="<?= base_url('assets/img/user-default.png') ?>"
                  alt="User" class="h-9 w-9 rounded-full object-cover">
                <span class="font-semibold"><?= $user->username ?></span>
              </a>
            </li>
          <?php else: ?>
            <li>
              <a class="block px-2 py-2 text-white/90 hover:bg-white/10 rounded-md"
                href="<?= base_url('/#cursos') ?>">
                Cursos
              </a>
            </li>
            <li>
              <a class="block px-2 py-2 text-white/90 hover:bg-white/10 rounded-md"
                href="<?= base_url('login') ?>">
                Entrar
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <section
    id="hero"
    class="bg-cover bg-center"
    style="background-image: url(<?= base_url('./assets/img/background.jpg') ?>);">
    <div class="bg-[#000000ee] h-full py-16">
      <div class="row py-10 px-5">
        <div class="max-w-[1140px] mx-auto px-4">
          <div
            class="mx-auto w-full flex flex-col justify-center items-center">
            <span
              class="inline-flex items-center rounded-md bg-green-400/10 px-2 py-1 text-xs font-medium text-green-400 inset-ring inset-ring-green-500/20">
              Excel Para Todos
            </span>
            <h2
              class="text-4xl md:text-5xl md:mb-3 text-center text-white font-bold mt-5">
              Domine o Excel Profissional
            </h2>

            <div class="m-5 w-full">
              <img
                class="w-full lg:w-1/2 lg:mx-auto rounded-sm"
                src="<?= base_url('./assets/img/Excell.jpg') ?>"
                alt="" />
            </div>

            <h2 class="text-3xl md:mt-3 text-center text-white font-bold">
              Destaque-se no Mercado de Trabalho
            </h2>
            <p class="text-lime-50 text-center mt-4 md:w-1/2">
              Curso completo com foco em aplicações práticas, dashboards
              interativos e técnicas usadas por empresas.
            </p>

            <div class="mt-10 w-full md:w-1/3 lg:w-1/6 border">
              <a
                href="<?= base_url('/checkout/' . $course->id_course) ?>"
                class="btn bg-green-400 text-white font-bold block rounded-sm w-full py-3 text-center">
                Comprar Agora
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-10">
    <div class="container mx-auto px-4">
      <div class="row">
        <div class="grid grid-cols-1 md:grid-cols-4">
          <div class="col-span-1 py-5">
            <div
              class="flex flex-col items-center justify-center gap-2 md:gap-4">
              <i class="fa fa-graduation-cap text-green-400 text-5xl"></i>
              <h2 class="text-2xl text-white font-bold">1000+</h2>
              <p class="text-center text-white" style="margin-top: -0.8rem">
                Alunos matriculados
              </p>
            </div>
          </div>
          <div class="col-span-1 py-5">
            <div
              class="flex flex-col items-center justify-center gap-2 md:gap-4">
              <i class="fa fa-thumbs-up text-green-400 text-5xl"></i>
              <h2 class="text-2xl text-white font-bold">5.0</h2>
              <p class="text-center text-white" style="margin-top: -0.8rem">
                Avaliado
              </p>
            </div>
          </div>
          <div class="col-span-1 py-5">
            <div
              class="flex flex-col items-center justify-center gap-2 md:gap-4">
              <i class="fa fa-language text-green-400 text-5xl"></i>
              <h2 class="text-2xl text-white font-bold">Português</h2>
              <p class="text-center text-white" style="margin-top: -0.8rem">
                Idioma do Curso
              </p>
            </div>
          </div>
          <div class="col-span-1 py-5">
            <div
              class="flex flex-col items-center justify-center gap-2 md:gap-4">
              <i class="fa fa-arrows-rotate text-green-400 text-5xl"></i>
              <h2 class="text-2xl text-white font-bold text-center">
                Última Actualização
              </h2>
              <p class="text-center text-white" style="margin-top: -0.8rem">
                10/2025
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-[#0D301A] py-20 px-5">
    <div class="container mx-auto px-4">
      <div class="row px-5">
        <h2 class="text-center text-3xl font-bold text-white">
          Visão Geral do Curso
        </h2>

        <div class="mt-5 md:h-100 lg:w-1/2 md:mx-auto">
          <iframe
            width="100%"
            height="100%"
            class="rounded-sm"
            src="https://www.youtube.com/embed/aSWVS-win1A?list=PLIGlfkzBSfnCHmuA0khugp0YZRCfYdimc"
            title="01-Introdução ao Excel Básico"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen></iframe>
        </div>
      </div>

      <div class="row mt-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="col-span-1 p-2">
            <div
              class="flex flex-col justify-between gap-3 border rounded-sm bg-white p-5 h-full">
              <i class="fa-solid fa-certificate text-2xl text-green-500"></i>
              <h2 class="text-xl font-semibold text-gray-950">
                Certificação Profissional
              </h2>
              <p class="text-gray-600">
                Receba um certificado reconhecido para valorizar seu
                currículo.
              </p>
            </div>
          </div>

          <div class="col-span-1 p-2">
            <div
              class="flex flex-col justify-between gap-3 border rounded-sm bg-white p-5 h-full">
              <i class="fa-solid fa-briefcase text-2xl text-green-500"></i>
              <h2 class="text-xl font-semibold text-gray-950">
                Oportunidades de Carreira
              </h2>
              <p class="text-gray-600">
                Abra portas para novas posições que exigem conhecimento em
                Excel.
              </p>
            </div>
          </div>

          <div class="col-span-1 p-2">
            <div
              class="flex flex-col justify-between gap-3 border rounded-sm bg-white p-5 h-full">
              <i class="fa-solid fa-database text-2xl text-green-500"></i>
              <h2 class="text-xl font-semibold text-gray-950">
                Gestão de Dados
              </h2>
              <p class="text-gray-600">
                Aprenda a gerenciar grandes volumes de dados com eficiência.
              </p>
            </div>
          </div>

          <div class="col-span-1 p-2">
            <div
              class="flex flex-col justify-between gap-3 border rounded-sm bg-white p-5 h-full">
              <i class="fa-solid fa-chart-bar text-2xl text-green-500"></i>
              <h2 class="text-xl font-semibold text-gray-950">
                Análise de Dados
              </h2>
              <p class="text-gray-600">
                Transforme dados brutos em insights valiosos para tomada de
                decisões.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container mx-auto px-4 py-20">
      <div class="row">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
          <div class="col-span-1 lg:col-span-2">
            <div class="bg-white rounded-sm shadow p-10">
              <h2 class="text-xl mb-5 text-center font-bold text-green-600">
                O que Você Aprenderá
              </h2>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Dominar fórmulas e funções avançadas do Excel
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Criar dashboards interativos e profissionais
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Automatizar tarefas repetitivas com macros
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Analisar grandes volumes de dados com tabelas dinâmicas
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Criar visualizações de dados impactantes
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Dominar ferramentas de análise estatística
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Integrar Excel com outras ferramentas e sistemas
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Aplicar técnicas de modelagem financeira
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Implementar validações e proteção de dados
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Otimizar planilhas para melhor desempenho
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Criar soluções personalizadas com VBA
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Extrair insights através de análise de dados
                </p>
              </div>
            </div>
          </div>

          <div class="col-span-1">
            <div
              class="bg-white rounded-sm shadow flex flex-col justify-center items-center p-7">
              <h2 class="text-xl font-semibold text-center text-green-600">
                Curso de Excel Profissional
              </h2>
              <p class="text-green-800 font-bold text-center text-4xl">
                999 MZN
              </p>
              <p class="text-center">Compra única</p>

              <a
                href="<?= base_url('/checkout/' . $course->id_course) ?>"
                class="block btn mt-5 bg-green-500 hover:bg-green-700 text-white p-3 rounded-sm font-semibold">Comprar Agora</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-[#0D301A] py-20 px-5">
    <div class="container mx-auto px-4">
      <div class="row">
        <h2 class="text-3xl font-bold text-white mb-3 text-center">
          Módulos do Curso
        </h2>
        <p class="text-gray-300 text-center">
          Conteúdo completo e estruturado para garantir seu domínio do Excel
        </p>
      </div>

      <div class="row mt-5">
        <div class="max-w-4xl mx-auto space-y-4">
          <!-- ================= MÓDULO 1 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 1: Introdução ao Excel</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>O que é o Excel?</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Definição e principais usos.</li>
                <li>
                  Apresentação da interface: barra de ferramentas, guias,
                  células, colunas e linhas.
                </li>
                <li>O conceito de pastas de trabalho e planilhas.</li>
              </ul>
              <p>Navegação Básica</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Como navegar pelas células.</li>
                <li>Selecionar, copiar e colar células.</li>
                <li>
                  Atalhos básicos do Excel para navegação e produtividade.
                </li>
              </ul>
              <p>Inserção e Manipulação de Dados</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Inserção de texto, números e datas.</li>
                <li>Ajuste de largura de colunas e altura de linhas.</li>
                <li>Mesclar células e centralizar texto.</li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 2 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 2: Formatação de Células</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>Formatação Básica</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Alterar fontes, tamanhos, cores e alinhamento de texto.
                </li>
                <li>Aplicar bordas e preenchimentos de células.</li>
                <li>
                  Formatação de números: moeda, porcentagem, datas, frações.
                </li>
              </ul>
              <p>Formatação Condicional</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Introdução à formatação condicional.</li>
                <li>Aplicar regras básicas (maior, menor, igual, etc.).</li>
                <li>
                  Utilizando formatação condicional com base em fórmulas.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 3 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 3: Fórmulas e Funções Básicas</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>O que são Fórmulas?</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Como inserir fórmulas.</li>
                <li>
                  Operadores básicos: soma, subtração, multiplicação, divisão.
                </li>
                <li>Referências relativas e absolutas ($A$1).</li>
              </ul>
              <p>Funções Básicas</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Função SOMA.</li>
                <li>Função MÉDIA.</li>
                <li>Função MÁXIMO e MÍNIMO.</li>
                <li>Função CONT.SE e CONT.VALORES.</li>
              </ul>
              <p>Uso de Referências Absolutas e Relativas</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Diferença entre referência absoluta (com $) e relativa.
                </li>
                <li>
                  Como usar referências relativas e absolutas em fórmulas.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 4 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 4: Funções Lógicas e Funções de Texto</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>Funções Lógicas</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Função SE: lógica condicional básica.</li>
                <li>Funções E e OU: combinar condições lógicas.</li>
                <li>Exemplos práticos de uso de SE com várias condições.</li>
              </ul>
              <p>Funções de Texto</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Funções CONCATENAR, ESQUERDA, DIREITA, TEXTO.</li>
                <li>Manipulação de texto em planilhas.</li>
                <li>
                  Uso prático para manipulação de strings em relatórios.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 5 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 5: Funções de Procura e Referência</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>Função PROCV</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Como funciona a função PROCV.</li>
                <li>Aplicação para procurar dados em tabelas.</li>
                <li>Vlookup para pesquisa exata e aproximada.</li>
              </ul>
              <p>Função PROCH</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Diferença entre PROCH e PROCV.</li>
                <li>Quando utilizar PROCH para buscar em linhas.</li>
              </ul>
              <p>ÍNDICE e CORRESP</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Como usar ÍNDICE e CORRESP para buscas mais avançadas.
                </li>
                <li>
                  Combinação das funções para procurar dados de forma
                  eficiente.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 6 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 6: Gráficos e Visualização de Dados</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>Criando Gráficos</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Tipos de gráficos: colunas, barras, linhas, pizza.</li>
                <li>Como inserir e personalizar gráficos.</li>
                <li>Gráficos dinâmicos e atualizáveis.</li>
              </ul>
              <p>Gráficos de Dispersão e Tendência</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Gráficos de dispersão e sua utilidade.</li>
                <li>
                  Inserção de linhas de tendência para análise preditiva.
                </li>
              </ul>
              <p>Minigráficos</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>O que são minigráficos.</li>
                <li>
                  Como usar minigráficos em células para visualização rápida
                  de tendências.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 7 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 7: Tabelas Dinâmicas</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>Introdução às Tabelas Dinâmicas</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>O que é uma Tabela Dinâmica.</li>
                <li>
                  Como criar e configurar tabelas dinâmicas a partir de um
                  conjunto de dados.
                </li>
                <li>Agrupamento de dados por categorias.</li>
              </ul>
              <p>Campos Calculados e Filtros</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Criar campos calculados em Tabelas Dinâmicas.</li>
                <li>
                  Usar filtros e segmentação de dados em tabelas dinâmicas.
                </li>
                <li>
                  Análise de grandes volumes de dados de forma eficiente.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 8 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 8: Ferramentas de Análise de Dados</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>Validação de Dados</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Como usar a validação de dados para criar listas suspensas.
                </li>
                <li>Configurar regras de entrada de dados e alertas.</li>
              </ul>
              <p>Análise de Cenários</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>O que é a Análise de Cenários.</li>
                <li>
                  Usar a ferramenta Gerenciador de Cenários para comparar
                  diferentes cenários.
                </li>
                <li>
                  Função Tabelas de Dados para simulação de vários valores.
                </li>
              </ul>
              <p>Atingir Meta</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Como usar a ferramenta Atingir Meta para fazer previsões com
                  base em condições específicas.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 9 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 9: Macros e Automatização</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>O que são Macros?</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Introdução à gravação de macros.</li>
                <li>Criar e executar uma macro.</li>
                <li>Automatizando tarefas repetitivas com macros.</li>
              </ul>
              <p>Noções de VBA (Opcional)</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Introdução à linguagem de programação VBA (Visual Basic for
                  Applications).
                </li>
                <li>
                  Exemplos simples de códigos VBA para automação de planilhas.
                </li>
              </ul>
            </div>
          </div>

          <!-- ================= MÓDULO 10 ================= -->
          <div class="accordion-item bg-white rounded-lg shadow">
            <button
              class="accordion-header w-full flex justify-between items-center p-4 text-left">
              <span class="font-semibold text-gray-800">Módulo 10: Proteção e Colaboração</span>
              <i class="fas fa-chevron-down text-gray-600"></i>
            </button>
            <div
              class="accordion-content hidden border-t border-gray-200 p-6 text-gray-700 space-y-4">
              <p>Proteção de Planilhas e Células</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Como proteger uma planilha ou uma célula específica contra
                  edição.
                </li>
                <li>Uso de senhas para controle de acesso.</li>
              </ul>
              <p>Compartilhamento e Colaboração</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>
                  Como compartilhar uma planilha para colaboração em tempo
                  real.
                </li>
                <li>
                  Uso do Excel no OneDrive e Google Sheets para colaboração em
                  nuvem.
                </li>
              </ul>
              <p>Versões e Controle de Alterações</p>
              <ul class="list-disc ml-5 space-y-1">
                <li>Controle de versões de planilhas.</li>
                <li>
                  Como rastrear alterações feitas em uma planilha por
                  diferentes usuários.
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 px-5">
    <div class="container mx-auto px-4 mx-auto px-4">
      <div class="row">
        <div
          class="bg-white rounded-sm py-2 px-3.5 grid grid-cols-1 md:grid-cols-5 gap-5">
          <div class="col-span-1 md:col-span-2 md:flex md:items-center">
            <img src="<?= base_url('./assets/img/Excell.jpg') ?>" class="rounded-sm md:h-auto" alt="" />
          </div>
          <div class="col-span-1 md:col-span-3 py-5">
            <h2 class="text-2xl font-semibold text-green-600 mb-3">
              Projeto Final: Criação de um Dashboard Interativo
            </h2>
            <ul>
              <p>
                Com base nos conhecimentos adquiridos, criar um dashboard que
                inclua:
              </p>
              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  O projeto final deve ser interativo e permitir uma análise
                  visual clara dos dados.
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">
                  Análise de cenários e segmentações.
                </p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">Validação de dados.</p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">Gráficos dinâmicos.</p>
              </div>

              <div class="flex items-start gap-3 sm:gap-4 mb-2">
                <i
                  class="fas fa-check-circle text-green-600 text-lg sm:text-xl mt-1"></i>
                <p class="text-sm sm:text-base">Tabelas dinâmicas.</p>
              </div>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer
    class="patterns text-white"
    style="background-image: url(<?= base_url('./assets/img/pattern.png') ?>)">
    <div class="bg-[#0D301A10]">
      <div class="max-w-[1140px] mx-auto px-4 py-12">
        <div class="grid gric-cols-1 lg:grid-cols-6 px-5">
          <div class="col-span-1 lg:flex lg:items-center h-1/1">
            <img src="<?= base_url('./assets/img/logo.png') ?>" alt="Mechanical Academy" class="w-40" />
          </div>
          <div class="col-span-1 lg:col-span-5 flex flex-col">
            <div
              class="w-full flex flex-col md:flex-row justify-between border-b pb-5 mb-5 border-b-gray-800">
              <div class="flex flex-col md:flex-row gap-5 mt-5">
                <a href="#">Inicio</a>
                <a href="#">Cursos</a>
                <a href="#">Termos & Condicoes</a>
                <a href="#">Politicas de Privacidade</a>
              </div>
              <div class="flex items-center gap-5 mt-5">
                <i class="fab fa-facebook-f"></i>
                <i class="fab fa-x-twitter"></i>
                <i class="fab fa-linkedin-in"></i>
                <i class="fab fa-youtube"></i>
              </div>
            </div>
            <p class="text-center lg:text-left">
              Mechanical Academy © 2025 Todos os direitos reservados.
              Desenvolvido do Jeito <span class="text-2xl">✓</span> pela
              <a href="https://mechanical.co.mz" class="text-custom-orange-1">Mechanical Tecnologia.</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script>
    (function() {
      const btn = document.getElementById('navToggle');
      const menu = document.getElementById('mobileMenu');
      const openI = document.getElementById('iconOpen');
      const closeI = document.getElementById('iconClose');

      if (!btn || !menu) return;

      btn.addEventListener('click', () => {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!expanded));
        menu.classList.toggle('hidden');
        openI?.classList.toggle('hidden');
        closeI?.classList.toggle('hidden');
      });
    })();

    // Seleciona todos os elementos com a classe 'counter'
    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
      const updateCount = () => {
        const target = +counter.getAttribute('data-target'); // valor final
        let count = parseInt(counter.innerText); // pega só o número (ignora o +)
        const increment = target / 100; // menor incremento = mais lento

        if (count < target) {
          count = Math.ceil(count + increment);
          counter.innerText = count + '+';
          setTimeout(updateCount, 50); // mais lento (50ms)
        } else {
          counter.innerText = target + '+';
        }
      }

      updateCount();
    });

    // Script para funcionamento dos acordiones

    document.querySelectorAll(".accordion-header").forEach(header => {
      header.addEventListener("click", () => {
        const item = header.parentElement;
        const content = header.nextElementSibling;
        const icon = header.querySelector("i");

        // alterna ativo
        const isActive = item.classList.contains("active");
        if (isActive) {
          // fechar
          item.classList.remove("active");
          content.classList.add("hidden");
          // ícone pra baixo
          icon.classList.remove("fa-chevron-up");
          icon.classList.add("fa-chevron-down");
        } else {
          // abrir
          item.classList.add("active");
          content.classList.remove("hidden");
          // ícone pra cima
          icon.classList.remove("fa-chevron-down");
          icon.classList.add("fa-chevron-up");
        }
      });
    });

    document.querySelectorAll(".faq-header").forEach(button => {
      button.addEventListener("click", () => {
        const faqItem = button.parentElement;
        const faqContent = faqItem.querySelector(".faq-content");

        // Fecha os outros FAQs
        document.querySelectorAll(".faq-item").forEach(item => {
          if (item !== faqItem) {
            item.querySelector(".faq-content").classList.add("hidden");
            item.querySelector("i").classList.replace("fa-chevron-up", "fa-chevron-down");
          }
        });

        // Toggle do FAQ clicado
        faqContent.classList.toggle("hidden");
        const icon = button.querySelector("i");
        if (faqContent.classList.contains("hidden")) {
          icon.classList.replace("fa-chevron-up", "fa-chevron-down");
        } else {
          icon.classList.replace("fa-chevron-down", "fa-chevron-up");
        }
      });
    });

    // Simple accordion functionality
    document.addEventListener('DOMContentLoaded', function() {
      const accordionButtons = document.querySelectorAll('[data-accordion]');

      accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
          const content = this.nextElementSibling;
          const icon = this.querySelector('i');

          if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            this.parentElement.classList.add('active');
          } else {
            content.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            this.parentElement.classList.remove('active');
          }
        });
      });
    });
  </script>

</body>
</html>