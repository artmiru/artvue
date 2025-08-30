<script setup lang="ts">
import { SidebarInset } from '@/components/ui/sidebar';
import { computed, onMounted, onUpdated } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);

// Функция для логирования ширины элементов
const logElementWidths = () => {
    console.log('=== Логирование ширины элементов ===');
    
    // Логирование ширины основного контейнера
    const mainElement = document.querySelector('main');
    if (mainElement) {
        console.log('Ширина <main>:', mainElement.offsetWidth, 'px');
        console.log('clientWidth <main>:', mainElement.clientWidth, 'px');
        console.log('scrollWidth <main>:', mainElement.scrollWidth, 'px');
    }
    
    // Логирование ширины viewport
    console.log('Ширина viewport (window.innerWidth):', window.innerWidth, 'px');
    console.log('Ширина viewport (document.documentElement.clientWidth):', document.documentElement.clientWidth, 'px');
    
    // Логирование ширины body
    console.log('Ширина <body>:', document.body.offsetWidth, 'px');
    console.log('clientWidth <body>:', document.body.clientWidth, 'px');
    console.log('scrollWidth <body>:', document.body.scrollWidth, 'px');
    
    // Логирование ширины html
    console.log('Ширина <html>:', document.documentElement.offsetWidth, 'px');
    console.log('clientWidth <html>:', document.documentElement.clientWidth, 'px');
    console.log('scrollWidth <html>:', document.documentElement.scrollWidth, 'px');
    
    // Проверка на горизонтальную прокрутку
    if (document.documentElement.scrollWidth > document.documentElement.clientWidth) {
        console.warn('Обнаружена горизонтальная прокрутка!');
        console.log('Разница:', document.documentElement.scrollWidth - document.documentElement.clientWidth, 'px');
    }
    
    console.log('=====================================');
};

// Логирование при монтировании компонента
onMounted(() => {
    console.log('AppContent mounted');
    logElementWidths();
});

// Логирование при обновлении компонента
onUpdated(() => {
    console.log('AppContent updated');
    logElementWidths();
});
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <slot />
    </SidebarInset>
    <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl" :class="className">
        <slot />
    </main>
</template>
