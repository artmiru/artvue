<script setup lang="ts">
import TopNavigation from '@/components/TopNavigation.vue';
</script>

<template>
    <div>
        <TopNavigation />
        <div class="container-xxl mt-4">
            <h1 class="text-2xl font-bold mb-4">Пример использования TopNavigation</h1>
            <p class="mb-4">
                Этот компонент воссоздает навигационное меню из PHP Blade шаблона, 
                используя Vue 3, Inertia.js и Tailwind CSS.
            </p>
            <p>
                Компонент включает в себя:
            </p>
            <ul class="list-disc pl-6 mt-2">
                <li>Логотип и название сайта</li>
                <li>Навигационные ссылки на курсы</li>
                <li>Контактную информацию (телефон)</li>
                <li>Ссылки на социальные сети (Telegram, WhatsApp)</li>
                <li>Ссылки для аутентификации (Войти/Личный кабинет/Выйти)</li>
                <li>Адаптивное меню для мобильных устройств</li>
            </ul>
        </div>
    </div>
</template>