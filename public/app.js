(function () {
    'use strict';

    // ===== ===== ===== Configuración ===== ===== =====
    const API_BASE = '/api';
    const TOKEN_KEY = 'gc_token';
    const USER_KEY = 'gc_user';

    // ===== ===== ===== Referencias al DOM ===== ===== =====
    const $ = (sel) => document.querySelector(sel);
    const authView = $('#auth-view');
    const dashboardView = $('#dashboard-view');

    const loginForm = $('#login-form');
    const registerForm = $('#register-form');
    const tabLogin = $('#tab-login');
    const tabRegister = $('#tab-register');

    const loginError = $('#login-error');
    const registerError = $('#register-error');

    const userElName = $('#user-name');
    const userCoinsValue = $('#user-coins-value');
    const logoutBtn = $('#logout-btn');

    const seedTypeSelect = $('#seed-type-select');
    const plantBtn = $('#plant-btn');
    const plantMsg = $('#plant-msg');

    const treesGrid = $('#trees-grid');
    const treeCountEl = $('#tree-count');
    const refreshBtn = $('#refresh-btn');

    const toastContainer = $('#toast-container');

    // ===== ===== ===== Utilidades ===== ===== =====
    function getToken() {
        return localStorage.getItem(TOKEN_KEY);
    }

    function getUser() {
        const raw = localStorage.getItem(USER_KEY);
        if (!raw) return null;
        try {
            return JSON.parse(raw);
        } catch (e) {
            return null;
        }
    }

    function saveSession(user, token) {
        localStorage.setItem(TOKEN_KEY, token);
        localStorage.setItem(USER_KEY, JSON.stringify(user));
    }

    function clearSession() {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);
    }

    function toast(message, type = 'info') {
        const el = document.createElement('div');
        el.className = `toast ${type}`;
        el.textContent = message;
        toastContainer.appendChild(el);
        requestAnimationFrame(() => el.classList.add('show'));
        setTimeout(() => {
            el.classList.remove('show');
            setTimeout(() => el.remove(), 300);
        }, 3000);
    }

    /**
     * Petición JSON genérica con token Bearer.
     */
    async function apiRequest(path, options = {}) {
        const { method = 'GET', body = null, auth = true } = options;

        const headers = {};
        if (body !== null) headers['Content-Type'] = 'application/json';
        if (auth) {
            const token = getToken();
            if (token) headers['Authorization'] = `Bearer ${token}`;
        }

        const config = { method, headers };
        if (body !== null) config.body = JSON.stringify(body);

        const response = await fetch(`${API_BASE}${path}`, config);

        const contentType = response.headers.get('content-type') || '';
        const payload = contentType.includes('application/json')
            ? await response.json()
            : await response.text();

        if (!response.ok) {
            const message = extractErrorMessage(payload, response.status);
            throw new Error(message);
        }

        return payload;
    }

    function extractErrorMessage(payload, status) {
        if (typeof payload === 'string' && payload) return payload;

        if (payload && payload.message) return payload.message;

        if (payload && payload.errors) {
            const firstKey = Object.keys(payload.errors)[0];
            if (firstKey) return Array.isArray(payload.errors[firstKey])
                ? payload.errors[firstKey][0]
                : payload.errors[firstKey];
        }

        return `Error de servidor (${status}).`;
    }

    function formatDate(value) {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';
        return date.toLocaleDateString('es', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    }

    // ===== ===== ===== Vistas de autenticación ===== ===== =====
    function showAuth() {
        authView.classList.remove('hidden');
        dashboardView.classList.add('hidden');
        switchTab('login');
        resetForms();
    }

    function showDashboard(userName, coins) {
        authView.classList.add('hidden');
        dashboardView.classList.remove('hidden');
        userElName.textContent = userName || '';
        userCoinsValue.textContent = Number(coins || 0);
    }

    function resetForms() {
        loginForm.reset();
        registerForm.reset();
        loginError.textContent = '';
        registerError.textContent = '';
    }

    function switchTab(tabName) {
        const isLogin = tabName === 'login';
        tabLogin.classList.toggle('active', isLogin);
        tabRegister.classList.toggle('active', !isLogin);
        loginForm.classList.toggle('hidden', !isLogin);
        registerForm.classList.toggle('hidden', isLogin);
        loginError.textContent = '';
        registerError.textContent = '';
    }

    // ===== ===== ===== Acciones de autenticación ===== ===== =====
    async function handleLogin(event) {
        event.preventDefault();
        loginError.textContent = '';
        const email = $('#login-email').value.trim();
        const password = $('#login-password').value;

        const submitBtn = loginForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        try {
            const data = await apiRequest('/auth/login', {
                method: 'POST',
                body: { email, password },
                auth: false,
            });
            saveSession(data.user, data.token);
            await enterApp();
        } catch (err) {
            loginError.textContent = err.message;
        } finally {
            submitBtn.disabled = false;
        }
    }

    async function handleRegister(event) {
        event.preventDefault();
        registerError.textContent = '';
        const name = $('#register-name').value.trim();
        const email = $('#register-email').value.trim();
        const password = $('#register-password').value;

        const submitBtn = registerForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        try {
            const data = await apiRequest('/auth/register', {
                method: 'POST',
                body: { name, email, password },
                auth: false,
            });
            saveSession(data.user, data.token);
            await enterApp();
        } catch (err) {
            registerError.textContent = err.message;
        } finally {
            submitBtn.disabled = false;
        }
    }

    async function handleLogout() {
        const token = getToken();
        if (token) {
            try {
                await apiRequest('/auth/logout', { method: 'POST' });
            } catch (err) {
                // El token pudo haber expirado; continuamos limpiando la sesión local.
            }
        }
        clearSession();
        showAuth();
        toast('Sesión cerrada correctamente.', 'success');
    }

    // ===== ===== ===== Dashboard: semillas y árboles ===== ===== =====
    async function loadSeedTypes() {
        seedTypeSelect.innerHTML = '<option value="">Cargando semillas…</option>';
        plantBtn.disabled = true;

        try {
            const seedTypes = await apiRequest('/seed-types');
            seedTypeSelect.innerHTML = '';

            if (!seedTypes.length) {
                seedTypeSelect.innerHTML = '<option value="">No hay semillas disponibles</option>';
                return;
            }

            seedTypes.forEach((seed) => {
                const option = document.createElement('option');
                option.value = seed.id;
                option.textContent = `${seed.name} (${seed.harvest_coins} monedas)`;
                seedTypeSelect.appendChild(option);
            });
            plantBtn.disabled = false;
        } catch (err) {
            seedTypeSelect.innerHTML = '<option value="">Error al cargar semillas</option>';
            toast(`No se pudieron cargar las semillas: ${err.message}`, 'error');
        }
    }

    async function loadTrees() {
        treesGrid.innerHTML = '<div class="empty-state"><span class="empty-icon">⏳</span>Cargando árboles…</div>';

        try {
            const trees = await apiRequest('/trees');
            renderTrees(trees);
        } catch (err) {
            treesGrid.innerHTML = `<div class="empty-state"><span class="empty-icon">⚠️</span>Error al cargar árboles: ${err.message}</div>`;
        }
    }

    function renderTrees(trees) {
        treeCountEl.textContent = trees.length;

        if (!trees.length) {
            treesGrid.innerHTML = `
                <div class="empty-state">
                    <span class="empty-icon">🌱</span>
                    Aún no tienes árboles plantados. ¡Planta el primero!
                </div>`;
            return;
        }

        const fragment = document.createDocumentFragment();

        trees.forEach((tree) => {
            fragment.appendChild(createTreeCard(tree));
        });

        treesGrid.innerHTML = '';
        treesGrid.appendChild(fragment);
    }

    function createTreeCard(tree) {
        const seedType = tree.seed_type || {};
        const card = document.createElement('article');
        card.className = 'tree-card';
        card.dataset.id = tree.id;

        const statusClass = tree.status === 'Dead' ? 'Dead' : 'Active';
        const healthClass = tree.health < 40 ? 'low' : '';

        card.innerHTML = `
            <div class="tree-card-header">
                <span class="tree-icon">🌳</span>
                <span class="tree-name">${escapeHtml(seedType.name || 'Árbol')}</span>
                <span class="tree-status ${escapeHtml(statusClass)}">${escapeHtml(tree.status)}</span>
            </div>
            <p class="tree-description">${escapeHtml(seedType.description || '')}</p>
            <div class="stat">
                <div class="stat-header">
                    <span>Nivel</span><strong>${Number(tree.level) || 0}</strong>
                </div>
                <div class="stat-header">
                    <span>Salud</span><strong>${Number(tree.health) || 0}%</strong>
                </div>
                <div class="stat-bar">
                    <div class="stat-bar-fill ${healthClass}" style="width: ${clamp(Number(tree.health) || 0, 0, 100)}%"></div>
                </div>
                <div class="stat-header" style="margin-top:0.5rem;">
                    <span>Progreso</span><strong>${Number(tree.progress) || 0}%</strong>
                </div>
                <div class="stat-bar">
                    <div class="stat-bar-fill" style="width: ${clamp(Number(tree.progress) || 0, 0, 100)}%"></div>
                </div>
            </div>
            <div class="tree-date">Plantado: ${escapeHtml(formatDate(tree.planted_at))}</div>
        `;

        return card;
    }

    async function handlePlant() {
        plantMsg.textContent = '';
        plantMsg.className = 'form-message';

        const seedTypeId = seedTypeSelect.value;
        if (!seedTypeId) {
            plantMsg.textContent = 'Selecciona un tipo de semilla.';
            plantMsg.classList.add('error');
            return;
        }

        plantBtn.disabled = true;
        plantBtn.textContent = 'Plantando…';

        try {
            const tree = await apiRequest('/trees', {
                method: 'POST',
                body: { seed_type_id: Number(seedTypeId) },
            });
            plantMsg.textContent = `${tree.seed_type?.name || 'Árbol'} plantado con éxito.`;
            plantMsg.classList.add('success');
            toast('¡Árbol plantado! 🌳', 'success');
            await loadTrees();
            await refreshUserCoins();
        } catch (err) {
            plantMsg.textContent = err.message;
            plantMsg.classList.add('error');
        } finally {
            plantBtn.disabled = false;
            plantBtn.textContent = 'Plantar árbol 🌳';
        }
    }

    // ===== ===== ===== Entrada a la aplicación ===== ===== =====
    async function enterApp() {
        const user = getUser();
        showDashboard(user ? user.name : 'Usuario', user ? user.coins : 0);
        try {
            await Promise.all([loadSeedTypes(), loadTrees()]);
        } catch (err) {
            toast(err.message, 'error');
        }
    }

    async function refreshUserCoins() {
        try {
            const me = await apiRequest('/user');
            if (me && me.coins !== undefined) {
                userCoinsValue.textContent = Number(me.coins) || 0;
                const stored = getUser();
                if (stored) saveSession({ ...stored, ...me }, getToken());
            }
        } catch (err) {
            // Silencioso: no bloquea el dashboard.
        }
    }

    function init() {
        if (getToken()) {
            enterApp().catch(() => showAuth());
        } else {
            showAuth();
        }
    }

    // ===== ===== ===== Agregar ruta de seed-types ===== ===== =====
    // Se expone por si se desean cargar las semillas/árboles de forma independiente.

    // ===== ===== ===== Helpers ===== ===== =====
    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value == null ? '' : value);
        return div.innerHTML;
    }

    function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }

    // ===== ===== ===== Eventos ===== ===== =====
    tabLogin.addEventListener('click', () => switchTab('login'));
    tabRegister.addEventListener('click', () => switchTab('register'));
    loginForm.addEventListener('submit', handleLogin);
    registerForm.addEventListener('submit', handleRegister);
    logoutBtn.addEventListener('click', handleLogout);
    plantBtn.addEventListener('click', handlePlant);
    refreshBtn.addEventListener('click', () => {
        loadTrees();
        refreshUserCoins();
    });

    document.addEventListener('DOMContentLoaded', init);
})();
