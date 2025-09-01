<script setup lang="ts">
import AppLogoIcon from '@/components/common/AppLogoIcon.vue';

interface Props {
    class?: string;
}

withDefaults(defineProps<Props>(), {
    class: '',
});
</script>

<template>
    <div class="flex items-end">
        <AppLogoIcon />
        <div class="leading-none">
            <div class="text-2xl font-normal leading-none">ARTMIR.RU</div>
            <div class="text-sm text-fuchsia-600 leading-none">м. Звенигородская</div>
        </div>
    </div>
</template>
