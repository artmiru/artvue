<script setup lang="ts">
import AppLogo from '@/components/common/AppLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { login, logout } from '@/routes';
import { edit } from '@/routes/profile';
import { Phone, Send, MessageCircle, SquareMenu } from 'lucide-vue-next';

// Mobile menu state
const isMobileMenuOpen = ref(false);

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
const currentPath = computed(() => page.url);

// Check if a nav item is active
const isNavItemActive = (item: NavItem) => {
    // For home route, check if it's exactly '/'
    if (item.routeName === 'home') {
        return currentPath.value === '/' || currentPath.value === '';
    }

    // For other routes, check if the current path starts with the item's href
    return currentPath.value.startsWith(item.href);
};

// Auth state
const auth = computed(() => page.props.auth);
</script>

<template>
    <nav class="relative flex flex-wrap items-center justify-between bg-white shadow-sm px-2">
        <div class="mx-auto w-full max-w-7xl">
            <!-- Brand and Navigation -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <Link class="flex items-end text-foreground no-underline" href="/">
                        <AppLogo class="me-1" />
                    </Link>
                </div>
                   <!-- Mobile menu button -->
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="ml-2 p-2 text-gray-700 hover:bg-gray-100 focus:outline-none lg:hidden"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Открыть меню</span>
                        <SquareMenu class="block h-9 w-9" />
                    </button>

                <!-- Main Navigation Links -->
                <ul class="hidden flex-row lg:flex">
                    <li
                        v-for="item in navItems"
                        :key="item.routeName"
                        class="border-b-0"
                    >
                        <Link
                            class="block px-3 py-4 text-foreground no-underline transition-colors hover:text-primary"
                            :class="{ 'font-bold text-primary': isNavItemActive(item) }"
                            :href="item.href"
                        >
                            {{ item.title }}
                        </Link>
                    </li>
                </ul>

                <!-- Contact and Auth Links -->
                <div class="flex-row items-center gap-3 hidden lg:flex">
                    <a
                        class="flex h-10 w-10 items-center justify-center rounded-md text-white bg-gradient-to-r bg-rose-400 hover:opacity-80"
                        href="tel:+79219076449"
                        aria-label="Позвонить"
                    >
                        <Phone class="h-5 w-5" />
                    </a>
                    <a
                        class="flex h-10 w-10 items-center justify-center rounded-md from-blue-500 to-blue-600 bg-gradient-to-r text-white hover:opacity-90"
                        href="https://t.me/artmir_zven"
                        target="_blank"
                        aria-label="Написать в Telegram"
                        rel="noopener noreferrer"
                    >
                        <Send class="h-5 w-5" />
                    </a>
                    <a
                        class="flex h-10 w-10 items-center justify-center rounded-md from-green-500 to-green-600 bg-gradient-to-r text-white hover:opacity-90"
                        href="https://wa.me/+79219076449"
                        target="_blank"
                        aria-label="Написать в WhatsApp"
                        rel="noopener noreferrer"
                    >
                        <MessageCircle class="h-5 w-5" />
                    </a>

                    <template v-if="auth?.user">
                        <Link class="rounded-md border border-secondary px-4 py-2 text-secondary hover:bg-secondary hover:text-white" :href="edit()">
                            Личный кабинет
                        </Link>
                        <Link class="px-4 py-2 text-foreground no-underline hover:text-primary" :href="logout()" method="post" as="button">
                            Выйти
                        </Link>
                    </template>
                    <template v-else>
                        <Link class="rounded-md px-4 py-2" :href="login()">
                            ЛК
                        </Link>
                    </template>
                </div>

                <!-- Mobile menu overlay -->
                <div
                    v-show="isMobileMenuOpen"
                    class="fixed inset-0 z-40 bg-black/50 lg:hidden"
                    @click="isMobileMenuOpen = false"
                ></div>

                <!-- Mobile menu -->
                <div v-show="isMobileMenuOpen" class="absolute right-0 z-50 w-10/12 overflow-y-auto bg-white shadow-md lg:hidden border border-neutral-200 rounded-b-md -mt-1 top-14">
                    <div class="space-y-1 px-2 pb-3 pt-2">
                        <Link
                            v-for="item in navItems"
                            :key="item.routeName"
                            :href="item.href"
                            :class="[
                                'block rounded-md px-3 py-2 text-base font-medium border-2 border-neutral-200',
                                isNavItemActive(item)
                                    ? 'bg-primary text-white'
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                            ]"
                            @click="isMobileMenuOpen = false"
                        >
                            {{ item.title }}
                        </Link>
                    </div>

                    <!-- Mobile contact and auth links -->
                    <div class="border-t border-gray-200 px-2 pb-3 pt-4">
                        <div class="flex flex-col gap-3">

                            <div class="flex w-full justify-center gap-5">
                                <a
                                class="flex h-12 w-12 items-center justify-center rounded-md text-white bg-gradient-to-r bg-rose-400"
                                href="tel:+79219076449"
                                aria-label="Позвонить"
                            >
                                <Phone class="h-8 w-8" />
                            </a>
                            <a
                                    class="flex h-12 w-12 items-center justify-center rounded-md from-blue-500 to-blue-600 bg-gradient-to-r text-white"
                                    href="https://t.me/artmir_zven"
                                    target="_blank"
                                    aria-label="Написать в Telegram"
                                    rel="noopener noreferrer"
                                >
                                    <Send class="h-8 w-8" />
                                </a>
                                <a
                                    class="flex h-12 w-12 items-center justify-center rounded-md from-green-500 to-green-600 bg-gradient-to-r text-white"
                                    href="https://wa.me/+79219076449"
                                    target="_blank"
                                    aria-label="Написать в WhatsApp"
                                    rel="noopener noreferrer"
                                >
                                    <MessageCircle class="h-8 w-8" />
                                </a>
                            </div>

                            <template v-if="auth?.user">
                                <div class="flex w-full flex-col gap-2">
                                    <Link class="w-full rounded-md border border-secondary px-4 py-3 text-center text-secondary hover:bg-secondary hover:text-white" :href="edit()">
                                        Личный кабинет
                                    </Link>
                                    <Link class="w-full rounded-md border border-gray-300 px-4 py-3 text-center text-foreground no-underline hover:text-primary" :href="logout()" method="post" as="button">
                                        Выйти
                                    </Link>
                                </div>
                            </template>
                            <template v-else>
                                <Link class="w-full rounded-md border-secondary px-4 py-3 text-center bg-yellow-300" :href="login()">
                                    Войти
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
