<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Certificados<?= $this->endSection() ?>

<?= $this->section('certificates') ?>
<style>
    /* Estilos para o modal de visualização */
    .modal-enter {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Estilos para os certificados */
    .certificate-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .certificate-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="h-full text-dark-800 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col">

        <!-- Conteúdo principal -->
        <main class="flex-grow container mx-auto py-2 px-4">
            <div class="mb-10">
                <h2 class="text-3xl font-bold mb-2">Seus Certificados de Conclusão</h2>
                <p class="text-gray-600 dark:text-gray-400">Visualize e faça download dos seus certificados de cursos concluídos</p>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 mb-8">
                    <div class="bg-blue-100 dark:bg-blue-900/30 rounded-xl p-5 border border-blue-500 dark:border-blue-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Total de Certificados</p>
                                <p class="text-3xl font-bold mt-1">8</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                                <i class="fas fa-certificate text-blue-600 dark:text-blue-300 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-100 dark:bg-green-900/30 rounded-xl p-5 border border-green-500 dark:border-green-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-700 dark:text-green-300">Concluídos Recentemente</p>
                                <p class="text-3xl font-bold mt-1 text-black">3</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-check text-green-600 dark:text-green-300 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/30 rounded-xl p-5 border border-purple-100 dark:border-purple-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-700 dark:text-purple-300">Download Disponíveis</p>
                                <p class="text-3xl font-bold mt-1">8</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center">
                                <i class="fas fa-download text-purple-600 dark:text-purple-300 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-semibold">Todos os Certificados</h3>
                    <p class="text-gray-600 dark:text-gray-400">Clique em um certificado para visualizá-lo</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center space-x-2">
                        <i class="fas fa-filter"></i>
                        <span>Filtrar</span>
                    </button>

                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition flex items-center space-x-2">
                        <i class="fas fa-sort"></i>
                        <span>Ordenar</span>
                    </button>
                </div>
            </div>

            <!-- Grid de certificados -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" id="certificates-grid">
                <!-- Os certificados serão carregados aqui via JavaScript -->
            </div>
        </main>

    </div>

    <!-- Modal para visualização de certificado -->
    <div id="certificate-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden modal-enter">
        <div class="absolute inset-0 bg-black bg-opacity-70"></div>

        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-11/12 max-w-4xl max-h-[90vh] overflow-hidden z-10">
            <div class="flex justify-between items-center p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 id="modal-title" class="text-xl font-bold">Visualização do Certificado</h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 overflow-y-auto max-h-[70vh]">
                <div class="flex flex-col items-center">
                    <!-- Imagem do certificado no modal -->
                    <img id="modal-image" src="" alt="Certificado" class="w-full max-w-2xl rounded-lg shadow-lg mb-6">

                    <!-- Detalhes do certificado -->
                    <div class="w-full max-w-2xl grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Curso</p>
                            <p id="modal-course" class="font-semibold">-</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Data de Conclusão</p>
                            <p id="modal-date" class="font-semibold">-</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Código do Certificado</p>
                            <p id="modal-code" class="font-semibold font-mono">-</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Carga Horária</p>
                            <p id="modal-hours" class="font-semibold">-</p>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="flex flex-wrap justify-center gap-4">
                        <button id="download-pdf" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center space-x-2">
                            <i class="fas fa-file-pdf"></i>
                            <span>Baixar PDF</span>
                        </button>

                        <button id="share-certificate" class="px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center space-x-2">
                            <i class="fas fa-share-alt"></i>
                            <span>Compartilhar</span>
                        </button>

                        <button id="print-certificate" class="px-5 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition flex items-center space-x-2">
                            <i class="fas fa-print"></i>
                            <span>Imprimir</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dados dos certificados
        const certificates = [{
                id: 1,
                title: "JavaScript Moderno ES6+",
                course: "Desenvolvimento Web Avançado",
                date: "15 de Outubro de 2023",
                hours: "40 horas",
                code: "JS-2023-001",
                image: "https://images.unsplash.com/photo-1627398242454-45a1465c2479?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
                pdfUrl: "#"
            },
            {
                id: 2,
                title: "UI/UX Design Principles",
                course: "Design de Interface do Usuário",
                date: "5 de Setembro de 2023",
                hours: "35 horas",
                code: "UX-2023-045",
                image: "https://images.unsplash.com/photo-1561070791-2526d30994b5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
                pdfUrl: "#"
            },
            {
                id: 3,
                title: "Data Science Fundamentals",
                course: "Ciência de Dados e Machine Learning",
                date: "22 de Agosto de 2023",
                hours: "60 horas",
                code: "DS-2023-012",
                image: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
                pdfUrl: "#"
            },
            {
                id: 4,
                title: "React.js & Next.js",
                course: "Desenvolvimento Front-end",
                date: "10 de Julho de 2023",
                hours: "50 horas",
                code: "RE-2023-023",
                image: "https://images.unsplash.com/photo-1633356122544-f134324a6cee?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
                pdfUrl: "#"
            },
            {
                id: 5,
                title: "Cloud Computing AWS",
                course: "Infraestrutura em Nuvem",
                date: "28 de Junho de 2023",
                hours: "45 horas",
                code: "CC-2023-034",
                image: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
                pdfUrl: "#"
            },
            {
                id: 6,
                title: "Cybersecurity Essentials",
                course: "Segurança da Informação",
                date: "12 de Maio de 2023",
                hours: "30 horas",
                code: "CS-2023-018",
                image: "https://images.unsplash.com/photo-1550751827-4bd374c3f58b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
                pdfUrl: "#"
            }
        ];

        // Elementos do DOM
        const certificatesGrid = document.getElementById('certificates-grid');
        const modal = document.getElementById('certificate-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const modalImage = document.getElementById('modal-image');
        const modalTitle = document.getElementById('modal-title');
        const modalCourse = document.getElementById('modal-course');
        const modalDate = document.getElementById('modal-date');
        const modalCode = document.getElementById('modal-code');
        const modalHours = document.getElementById('modal-hours');
        const downloadPdfBtn = document.getElementById('download-pdf');
        const darkModeToggle = document.getElementById('dark-mode-toggle');

        // Certificado atualmente selecionado
        let currentCertificate = null;

        // Carregar certificados na grid
        function loadCertificates() {
            certificatesGrid.innerHTML = '';

            certificates.forEach(cert => {
                const card = document.createElement('div');
                card.className = 'certificate-card bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-xl cursor-pointer';
                card.setAttribute('data-id', cert.id);

                card.innerHTML = `
                    <div class="relative overflow-hidden">
                        <img src="${cert.image}" alt="${cert.title}" class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105">
                        <div class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                            ${cert.hours}
                        </div>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-lg mb-2 line-clamp-1">${cert.title}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">${cert.course}</p>
                        
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-500">Concluído em</p>
                                <p class="font-medium text-sm">${cert.date}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-500">Código</p>
                                <p class="font-mono text-sm">${cert.code}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <button class="view-btn px-3 py-2 bg-blue-100 dark:bg-blue-900/40 hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium transition flex items-center space-x-1">
                                <i class="fas fa-eye"></i>
                                <span>Visualizar</span>
                            </button>
                            <button class="download-btn px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-sm font-medium transition flex items-center space-x-1">
                                <i class="fas fa-download"></i>
                                <span>PDF</span>
                            </button>
                        </div>
                    </div>
                `;

                certificatesGrid.appendChild(card);

                // Adicionar evento para visualizar
                const viewBtn = card.querySelector('.view-btn');
                viewBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openCertificateModal(cert);
                });

                // Adicionar evento para download
                const downloadBtn = card.querySelector('.download-btn');
                downloadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    downloadCertificatePdf(cert);
                });

                // Adicionar evento para abrir modal ao clicar no card
                card.addEventListener('click', () => {
                    openCertificateModal(cert);
                });
            });
        }

        // Abrir modal com certificado
        function openCertificateModal(certificate) {
            currentCertificate = certificate;

            modalImage.src = certificate.image;
            modalTitle.textContent = certificate.title;
            modalCourse.textContent = certificate.course;
            modalDate.textContent = certificate.date;
            modalCode.textContent = certificate.code;
            modalHours.textContent = certificate.hours;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Fechar modal
        function closeCertificateModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Baixar PDF do certificado
        function downloadCertificatePdf(certificate) {
            // Em um cenário real, aqui seria feita a requisição para o servidor
            alert(`Iniciando download do certificado: ${certificate.title}\n\nEm um sistema real, o PDF seria baixado do servidor.`);

            // Simular download
            const link = document.createElement('a');
            link.href = certificate.pdfUrl;
            link.download = `certificado-${certificate.code}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Compartilhar certificado
        function shareCertificate() {
            if (navigator.share && currentCertificate) {
                navigator.share({
                        title: `Meu certificado: ${currentCertificate.title}`,
                        text: `Concluí o curso ${currentCertificate.course} e obtive este certificado!`,
                        url: window.location.href,
                    })
                    .catch(console.error);
            } else {
                alert(`Compartilhe o link para o certificado: ${currentCertificate ? currentCertificate.title : 'Certificado'}`);
            }
        }

        // Alternar modo dark/light
        function toggleDarkMode() {
            const html = document.documentElement;

            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Inicializar tema
        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const html = document.documentElement;

            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                html.classList.add('dark');
                darkModeToggle.checked = true;
            } else {
                html.classList.remove('dark');
                darkModeToggle.checked = false;
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            loadCertificates();
            initTheme();

            // Eventos do modal
            closeModalBtn.addEventListener('click', closeCertificateModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeCertificateModal();
                }
            });

            // Eventos dos botões do modal
            downloadPdfBtn.addEventListener('click', () => {
                if (currentCertificate) {
                    downloadCertificatePdf(currentCertificate);
                }
            });

            document.getElementById('share-certificate').addEventListener('click', shareCertificate);

            document.getElementById('print-certificate').addEventListener('click', () => {
                window.print();
            });

            // Evento do toggle dark/light
            darkModeToggle.addEventListener('change', toggleDarkMode);

            // Evento de tecla ESC para fechar modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeCertificateModal();
                }
            });
        });
    </script>
</div>

<?= $this->endSection() ?>