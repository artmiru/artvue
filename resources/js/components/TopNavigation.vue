<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { login, logout } from '@/routes';
import { edit } from '@/routes/profile';

// Define the navigation items
interface NavItem {
    title: string;
    href: string;
    routeName: string;
}

const navItems: NavItem[] = [
    { title: 'Главная', href: '/', routeName: 'home' },
    { title: 'Рисунок', href: '/kursy-risovaniya', routeName: 'courses.drawing' },
    { title: 'Акварель', href: '/kursy-akvareli', routeName: 'courses.watercolor' },
    { title: 'Масло', href: '/kursy-zhivopisi-maslom', routeName: 'courses.oil' },
    { title: 'Пастель', href: '/kursy-risovaniya-pastelyu', routeName: 'courses.pastel' },
    { title: 'Мастер-классы', href: '/mk', routeName: 'courses.mk.index' },
    { title: 'Сертификаты', href: '/gift', routeName: 'courses.gift.index' },
];

// Get current path for active state
const page = usePage();
const currentPath = computed(() => {
    return page.url;
});

// Check if a nav item is active
const isNavItemActive = (item: NavItem) => {
    // For home route, check if it's exactly '/'
    if (item.routeName === 'home') {
        return currentPath.value === '/' || currentPath.value === '';
    }
    
    // For other routes, check if the current path starts with the item's href
    return currentPath.value.startsWith(item.href);
};

// Mobile menu state
const isMobileMenuOpen = ref(false);

// Toggle mobile menu
const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

// Close mobile menu
const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

// Auth state
const auth = computed(() => page.props.auth);
</script>

<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm p-0">
        <div class="container-xxl">
            <!-- Brand -->
            <Link class="navbar-brand text-dark text-decoration-none d-flex align-items-center me-0" href="/">
                <AppLogo class="me-1" width="48" height="44" />
                <div class="fs-3 lh-1">
                    ARTMIR.RU
                    <div class="fs-6" style="color: darkviolet;">м. Звенигородская</div>
                </div>
            </Link>

            <!-- Mobile Toggle Button -->
            <button 
                class="navbar-toggler ms-3 d-lg-none" 
                type="button" 
                @click="toggleMobileMenu"
                :aria-expanded="isMobileMenuOpen"
                aria-label="Переключить навигацию"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div 
                class="collapse navbar-collapse" 
                :class="{ show: isMobileMenuOpen }" 
                id="mainNav"
            >
                <!-- Main Navigation Links -->
                <ul class="navbar-nav mx-auto">
                    <li 
                        v-for="item in navItems" 
                        :key="item.routeName"
                        class="nav-item"
                    >
                        <Link
                            class="nav-link px-2 py-2"
                            :class="{ 'active fw-bold text-primary': isNavItemActive(item) }"
                            :href="item.href"
                            @click="closeMobileMenu"
                        >
                            {{ item.title }}
                        </Link>
                    </li>
                </ul>

                <!-- Desktop Contact and Auth Links -->
                <div class="d-flex align-items-center gap-3">
                    <a
                        class="btn btn-lg btn-outline-primary fw-bold py-1 d-none d-lg-block"
                        href="tel:+79219076449"
                        schema="tel"
                    >
                        907-64-49
                    </a>
                    <a 
                        class="btn btn-primary bg-gradient p-1 d-none d-lg-block" 
                        href="https://t.me/artmir_zven"
                        target="_blank" 
                        aria-label="Написать в Telegram" 
                        rel="noopener noreferrer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send">
                            <path d="m22 2-7 20-4-9-9-4Z"/>
                            <path d="M22 2 11 13"/>
                        </svg>
                    </a>
                    <a 
                        class="btn btn-success bg-gradient p-1 d-none d-lg-block" 
                        href="https://wa.me/+79219076449"
                        target="_blank" 
                        aria-label="Написать в WhatsApp" 
                        rel="noopener noreferrer"
                    >
                        <svg xmlns="http://www.w3.org/200/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle">
                            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                        </svg>
                    </a>
                    
                    <template v-if="auth.user">
                        <Link class="btn btn-outline-secondary" :href="edit()">
                            Личный кабинет
                        </Link>
                        <Link class="btn btn-link" :href="logout()" method="post" as="button">
                            Выйти
                        </Link>
                    </template>
                    <template v-else>
                        <Link class="btn btn-outline-secondary" :href="login()">
                            Войти
                        </Link>
                    </template>
                </div>

                <!-- Mobile Contact and Auth Links -->
                <div class="d-flex d-lg-none justify-content-center gap-3 my-3 w-100">
                    <a
                        class="btn btn-lg btn-outline-primary fw-bold py-3"
                        href="tel:+79219076449"
                        schema="tel"
                    >
                        907-64-49
                    </a>
                    <a 
                        class="btn btn-primary bg-gradient p-3" 
                        href="https://t.me/artmir_zven"
                        target="_blank" 
                        aria-label="Написать в Telegram" 
                        rel="noopener noreferrer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send">
                            <path d="m22 2-7 20-4-9-4Z"/>
                            <path d="M22 2 11 13"/>
                        </svg>
                    </a>
                    <a 
                        class="btn btn-success bg-gradient p-3" 
                        href="https://wa.me/+79219076449"
                        target="_blank" 
                        aria-label="Написать в WhatsApp" 
                        rel="noopener noreferrer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle">
                            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</template>

<style scoped>
/* Bootstrap-like styles converted to Tailwind */
.navbar {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

.navbar-brand {
    padding-top: 0.3125rem;
    padding-bottom: 0.3125rem;
    margin-right: 1rem;
    font-size: 1.25rem;
    text-decoration: none;
    white-space: nowrap;
}

.navbar-nav {
    display: flex;
    flex-direction: column;
    padding-left: 0;
    margin-bottom: 0;
    list-style: none;
}

.nav-item {
    margin-bottom: 0;
}

.nav-link {
    display: block;
    padding: 0.5rem 1rem;
    text-decoration: none;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

@media (min-width: 992px) {
    .navbar-expand-lg .navbar-nav {
        flex-direction: row;
    }
    
    .navbar-expand-lg .navbar-nav .nav-link {
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }
    
    .navbar-expand-lg .navbar-collapse {
        display: flex !important;
        flex-basis: auto;
    }
    
    .navbar-expand-lg .navbar-toggler {
        display: none;
    }
}

.navbar-toggler {
    padding: 0.25rem 0.75rem;
    font-size: 1.25rem;
    line-height: 1;
    background-color: transparent;
    border: 1px solid transparent;
    border-radius: 0.375rem;
    transition: box-shadow 0.15s ease-in-out;
}

.navbar-toggler-icon {
    display: inline-block;
    width: 1.5em;
    height: 1.5em;
    vertical-align: middle;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 100%;
}

.btn {
    display: inline-block;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    border-radius: 0.375rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.btn-lg {
    padding: 0.5rem 1rem;
    font-size: 1.25rem;
    border-radius: 0.5rem;
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-success {
    color: #fff;
    background-color: #198754;
    border-color: #198754;
}

.btn-link {
    font-weight: 400;
    color: #0d6efd;
    text-decoration: underline;
    background-color: transparent;
    border: 0;
    padding: 0.375rem 0.75rem;
}

.btn-link:hover {
    color: #0a58ca;
    text-decoration: underline;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.bg-white {
    background-color: #fff !important;
}

.text-dark {
    color: #212529 !important;
}

.text-primary {
    color: #0d6efd !important;
}

.fw-bold {
    font-weight: 700 !important;
}

.fs-3 {
    font-size: 1.5rem !important;
}

.fs-6 {
    font-size: 0.875rem !important;
}

.lh-1 {
    line-height: 1 !important;
}

.d-flex {
    display: flex !important;
}

.d-none {
    display: none !important;
}

.d-lg-none {
    display: none !important;
}

.d-lg-block {
    display: block !important;
}

.align-items-center {
    align-items: center !important;
}

.justify-content-center {
    justify-content: center !important;
}

.mx-auto {
    margin-right: auto !important;
    margin-left: auto !important;
}

.ms-3 {
    margin-left: 1rem !important;
}

.me-0 {
    margin-right: 0 !important;
}

.me-1 {
    margin-right: 0.25rem !important;
}

.my-3 {
    margin-top: 1rem !important;
    margin-bottom: 1rem !important;
}

.gap-3 {
    gap: 1rem !important;
}

.w-100 {
    width: 100% !important;
}

.py-1 {
    padding-top: 0.25rem !important;
    padding-bottom: 0.25rem !important;
}

.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.py-3 {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
}

.p-0 {
    padding: 0 !important;
}

.p-1 {
    padding: 0.25rem !important;
}

.p-3 {
    padding: 1rem !important;
}

.container-xxl {
    width: 100%;
    padding-right: var(--bs-gutter-x, 0.75rem);
    padding-left: var(--bs-gutter-x, 0.75rem);
    margin-right: auto;
    margin-left: auto;
}

@media (min-width: 1400px) {
    .container-xxl {
        max-width: 1320px;
    }
    
    .d-lg-none {
        display: none !important;
    }
    
    .d-lg-block {
        display: block !important;
    }
}
</style>