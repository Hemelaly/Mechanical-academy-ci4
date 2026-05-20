// comportamento do sidebar 

const sidebar = document.getElementById("sidebar");
const backdrop = document.getElementById("sidebar-backdrop");
const openSidebarBtn = document.getElementById("open-sidebar");
const closeSidebarBtn = document.getElementById("close-sidebar");
const collapseDesktopBtn = document.getElementById("collapse-desktop");
const mainContent = document.getElementById("main-content");
const labels = document.querySelectorAll(".sidebar-label");
const logoText = document.getElementById("logo-text");
const collapseIcon = document.getElementById("collapse-icon");
const navlink = document.querySelectorAll(".side-link");
const favicon = document.getElementById("favicon");

// --- MOBILE: abrir/fechar drawer ---
function openMobileSidebar() {
    if (!sidebar || !backdrop) return;
    sidebar.classList.remove("-translate-x-full");
    backdrop.classList.remove("hidden");
}

function closeMobileSidebar() {
    if (!sidebar || !backdrop) return;
    sidebar.classList.add("-translate-x-full");
    backdrop.classList.add("hidden");
}

openSidebarBtn?.addEventListener("click", openMobileSidebar);
closeSidebarBtn?.addEventListener("click", closeMobileSidebar);
backdrop?.addEventListener("click", closeMobileSidebar);

// --- DESKTOP: colapsar/expandir (mini vs full) ---
let isCollapsed = false;

collapseDesktopBtn?.addEventListener("click", () => {
    if (!sidebar) return;
    isCollapsed = !isCollapsed;

    sidebar.classList.toggle("lg:w-80", !isCollapsed);
    sidebar.classList.toggle("lg:w-20", isCollapsed);
    favicon?.classList.toggle("hidden", !isCollapsed);

    navlink.forEach(link => {
        link.classList.toggle("gap-2", !isCollapsed);
        link.classList.toggle("gap-0", isCollapsed);
        link.classList.toggle("justify-center", isCollapsed);
    });

    labels.forEach((label) => {
        label.classList.toggle("hidden", isCollapsed);
    });

    logoText?.classList.toggle("hidden", isCollapsed);
    collapseIcon?.classList.toggle("rotate-180", isCollapsed);
});

// Garantir que no desktop o sidebar não fique escondido
function handleResize() {
    if (!sidebar || !backdrop) return;
    if (window.innerWidth >= 1024) {
        sidebar.classList.remove("-translate-x-full");
        backdrop.classList.add("hidden");
    }
}

window.addEventListener("resize", handleResize);

// ---------- THEME SWITCHER ----------

const themeToggleBtn = document.getElementById("theme-toggle");
const themeToggleIcon = document.getElementById("theme-toggle-icon");

// novos: logos para claro/escuro
const logoLight = document.getElementById("logo-light");
const logoDark = document.getElementById("logo-dark");

// Atualiza qual logo aparece conforme o tema
function updateLogo(theme) {
    if (!logoLight || !logoDark) return;

    const isDark = theme === "dark";

    logoLight.classList.toggle("hidden", !isDark);
    logoDark.classList.toggle("hidden", isDark);
}

// Aplica tema + ícone + logo
function applyTheme(theme) {
    const root = document.documentElement;

    if (theme === "dark") {
        root.classList.add("dark");
        themeToggleIcon?.classList.remove("bi-sun");
        themeToggleIcon?.classList.add("bi-moon-stars");
    } else {
        root.classList.remove("dark");
        themeToggleIcon?.classList.remove("bi-moon-stars");
        themeToggleIcon?.classList.add("bi-sun");
    }

    // atualiza logo conforme tema
    updateLogo(theme);

    localStorage.setItem("theme", theme);

    document.dispatchEvent(new CustomEvent('themechange', { detail: { theme } }));
}

// Detecta preferência inicial
(function initTheme() {
    const stored = localStorage.getItem("theme");
    if (stored === "dark" || stored === "light") {
        applyTheme(stored);
    } else {
        const prefersDark = window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches;
        applyTheme(prefersDark ? "dark" : "light");
    }
})();

// Click no botão
if (themeToggleBtn) {
    themeToggleBtn.addEventListener("click", () => {
        const isDark = document.documentElement.classList.contains("dark");
        applyTheme(isDark ? "light" : "dark");
    });
}

// Controle do Dropdown de Notificações
document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (dropdownToggle && dropdownMenu) {
        // Abrir/fechar dropdown
        dropdownToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const isHidden = dropdownMenu.classList.contains('hidden');

            if (isHidden) {
                dropdownMenu.classList.remove('hidden');
                setTimeout(() => {
                    dropdownMenu.classList.remove('opacity-0', 'scale-95');
                    dropdownMenu.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                dropdownMenu.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdownMenu.classList.add('hidden');
                }, 200);
            }
        });

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function (e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdownMenu.classList.add('hidden');
                }, 200);
            }
        });

        // Prevenir fechamento ao clicar dentro do dropdown
        dropdownMenu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }
});

// Estado da aplicação
let currentCategory = 'inbox';
let currentChat = null;
let isNewMessage = false;

// Dados de exemplo para mensagens
const messagesData = {
    'Maria João': [
        { id: 1, sender: 'Maria João', content: 'Olá Edson, gostaria de confirmar a visita para amanhã às 15h no apartamento T3 em Sommerschield.', time: '15:30', type: 'received' },
        { id: 2, sender: 'Você', content: 'Olá Maria! Confirmado, estarei lá às 15h. Pode me enviar o endereço exato?', time: '15:32', type: 'sent' },
        { id: 3, sender: 'Maria João', content: 'Perfeito! O endereço é Rua da Mesquita 123, Sommerschield. Apartamento 3B.', time: '15:33', type: 'received' }
    ],
    'Carlos Manuel': [
        { id: 1, sender: 'Carlos Manuel', content: 'Boa tarde, a torneira da cozinha está com vazamento constante. Pode enviar alguém para verificar?', time: '14:15', type: 'received' },
        { id: 2, sender: 'Você', content: 'Olá Carlos! Vou enviar o técnico amanhã pela manhã. Por favor confirme se estará em casa.', time: '14:20', type: 'sent' }
    ],
    'Marta Zimba': [
        { id: 1, sender: 'Marta Zimba', content: 'Olá Edson, acabei de realizar a transferência do aluguel. Valor: 25.000 MT. Pode confirmar o recebimento?', time: '12:45', type: 'received' },
        { id: 2, sender: 'Você', content: 'Obrigado Marta! Acabei de confirmar o recebimento. O comprovante já está disponível no seu portal.', time: '13:00', type: 'sent' }
    ]
};

// Funções do Sidebar
function setActiveCategory(category) {
    // Remove a classe active de todos os itens
    document.querySelectorAll('.sidebar-nav-item').forEach(item => {
        item.classList.remove('active', 'bg-indigo-50', 'dark:bg-indigo-900/20', 'text-indigo-600', 'dark:text-indigo-400');
        item.classList.add('text-slate-600', 'dark:text-slate-400');
    });

    // Adiciona a classe active ao item clicado
    const activeItem = document.querySelector(`[data-category="${category}"]`);
    if (activeItem) {
        activeItem.classList.add('active', 'bg-indigo-50', 'dark:bg-indigo-900/20', 'text-indigo-600', 'dark:text-indigo-400');
        activeItem.classList.remove('text-slate-600', 'dark:text-slate-400');
    }

    currentCategory = category;

    // Aqui você pode adicionar lógica para filtrar as mensagens por categoria
    console.log('Categoria selecionada:', category);
    filterMessagesByCategory(category);
}

function filterMessagesByCategory(category) {
    // Esta função filtraria as mensagens baseado na categoria selecionada
    // Por enquanto, apenas mostra um log
    const messageItems = document.querySelectorAll('[data-message-category]');

    messageItems.forEach(item => {
        if (category === 'inbox' || item.getAttribute('data-message-category') === category) {
            item.style.display = 'grid';
        } else {
            item.style.display = 'none';
        }
    });
}

// Funções do Chat
function openChat(contactName, contactInitials, contactColor) {
    const messageList = document.getElementById('messageList');
    const chatArea = document.getElementById('chatArea');
    const newMessageArea = document.getElementById('newMessageArea');
    const messageInputArea = document.getElementById('messageInputArea');
    const chatMessages = document.getElementById('chatMessages');
    const emptyChat = document.getElementById('emptyChat');

    // Esconde a lista de mensagens e mostra o chat
    if (messageList) messageList.style.display = 'none';
    chatArea.classList.remove('hidden');

    // Configura para chat existente (não nova mensagem)
    isNewMessage = false;
    newMessageArea.classList.add('hidden');
    messageInputArea.classList.remove('hidden');

    // Atualiza informações do contato
    document.getElementById('chatContactInfo').innerHTML = `
    <div class="flex flex-row gap-3">
        <div class="w-10 h-10 ${contactColor} rounded-full flex items-center justify-center text-white font-semibold">
            ${contactInitials}
        </div>
        <div>
            <h3 class="font-semibold text-slate-800 dark:text-white">${contactName}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Online</p>
        </div>
    </div>
    `;

    // Carrega as mensagens
    loadChatMessages(contactName);
    currentChat = contactName;
}

function openNewMessage() {
    const messageList = document.getElementById('messageList');
    const chatArea = document.getElementById('chatArea');
    const newMessageArea = document.getElementById('newMessageArea');
    const messageInputArea = document.getElementById('messageInputArea');
    const chatMessages = document.getElementById('chatMessages');

    // Esconde a lista de mensagens e mostra o chat
    if (messageList) messageList.style.display = 'none';
    chatArea.classList.remove('hidden');

    // Configura para nova mensagem
    isNewMessage = true;
    newMessageArea.classList.remove('hidden');
    messageInputArea.classList.add('hidden');
    chatMessages.classList.add('hidden');

    // Atualiza informações do contato para "Nova Mensagem"
    document.getElementById('chatContactInfo').innerHTML = `
    <div class="flex flex-row gap-3">
        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <h3 class="font-semibold text-slate-800 dark:text-white">Nova Mensagem</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Enviar para novo contato</p>
        </div>
    </div>
    `;

    currentChat = null;
}

function closeChat() {
    const messageList = document.getElementById('messageList');
    const chatArea = document.getElementById('chatArea');

    if (messageList) messageList.style.display = 'block';
    chatArea.classList.add('hidden');
    currentChat = null;
    isNewMessage = false;
}

function loadChatMessages(contactName) {
    const chatMessages = document.getElementById('chatMessages');
    const emptyChat = document.getElementById('emptyChat');
    const messages = messagesData[contactName] || [];

    if (messages.length === 0) {
        emptyChat.classList.remove('hidden');
        chatMessages.innerHTML = '';
        chatMessages.appendChild(emptyChat);
    } else {
        emptyChat.classList.add('hidden');
        chatMessages.innerHTML = messages.map(message => `
            <div class="flex ${message.type === 'sent' ? 'justify-end' : 'justify-start'} mb-4">
                <div class="max-w-[70%]">
                    <div class="${message.type === 'sent' ? 'bg-blue-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white'} rounded-2xl px-4 py-2">
                        <p class="text-sm">${message.content}</p>
                    </div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block ${message.type === 'sent' ? 'text-right' : 'text-left'}">${message.time}</span>
                </div>
            </div>
        `).join('');
    }
}

function toggleRecipientField() {
    const recipientType = document.getElementById('recipientType').value;
    const phoneField = document.getElementById('phoneField');
    const emailField = document.getElementById('emailField');

    if (recipientType === 'phone') {
        phoneField.classList.remove('hidden');
        emailField.classList.add('hidden');
    } else {
        phoneField.classList.add('hidden');
        emailField.classList.remove('hidden');
    }
}

function sendNewMessage(event) {
    event.preventDefault();

    const recipientType = document.getElementById('recipientType').value;
    const recipient = recipientType === 'phone'
        ? document.getElementById('phoneNumber').value
        : document.getElementById('emailAddress').value;
    const message = document.getElementById('messageContent').value;

    if (!recipient || !message) {
        alert('Por favor, preencha todos os campos.');
        return;
    }

    // Simular envio da mensagem
    console.log('Enviando mensagem:', { recipient, message, recipientType });

    // Mostrar mensagem de sucesso
    alert('Mensagem enviada com sucesso!');

    // Fechar o chat e voltar para a lista
    closeChat();

    // Limpar o formulário
    document.getElementById('newMessageForm').reset();
}

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();

    if (!message) return;

    // Adicionar mensagem ao chat
    if (currentChat && messagesData[currentChat]) {
        const newMessage = {
            id: messagesData[currentChat].length + 1,
            sender: 'Você',
            content: message,
            time: 'Agora',
            type: 'sent'
        };

        messagesData[currentChat].push(newMessage);
        loadChatMessages(currentChat);
    }

    // Limpar input
    messageInput.value = '';
}

// Adicionar event listeners para as mensagens
document.addEventListener('DOMContentLoaded', function () {
    // Adicionar click listeners para as mensagens
    const messageItems = document.querySelectorAll('[onclick*="openChat"]');
    messageItems.forEach(item => {
        const onclickAttr = item.getAttribute('onclick');
        // Extrair parâmetros do onclick
        const match = onclickAttr.match(/openChat\('([^']+)', '([^']+)', '([^']+)'\)/);
        if (match) {
            item.style.cursor = 'pointer';
        }
    });

    // Inicializar categoria ativa
    setActiveCategory('inbox');
});

// Funções para o formulário de editar perfil
function triggerAvatarUpload() {
    document.getElementById('avatarUpload').click();
}

function handleAvatarChange(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            // Aqui você pode atualizar a imagem de perfil
            const avatarDiv = document.querySelector('.bg-gradient-to-br.from-blue-500.to-blue-600');
            avatarDiv.innerHTML = `<img src="${e.target.result}" class="w-24 h-24 rounded-2xl object-cover" alt="Novo Avatar">`;
        };
        reader.readAsDataURL(file);
    }
}

function updateProfile(event) {
    event.preventDefault();

    // Coletar dados do formulário
    const formData = {
        fullName: document.getElementById('editFullName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        position: document.getElementById('editPosition').value,
        department: document.getElementById('editDepartment').value,
        bio: document.getElementById('editBio').value
    };

    // Simular atualização (substituir por chamada AJAX real)
    console.log('Atualizando perfil:', formData);

    // Mostrar mensagem de sucesso
    alert('Perfil atualizado com sucesso!');

    // Fechar o drawer
    const drawer = document.getElementById('drawer-edit-profile');
    drawer.classList.add('translate-x-full');

    // Aqui você atualizaria os dados na página principal
    updateProfileDisplay(formData);
}

function updateProfileDisplay(data) {
    // Atualizar os dados exibidos na página principal
    // Esta função seria chamada após salvar as alterações
    console.log('Atualizando display com:', data);
}
