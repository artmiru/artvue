<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import { ref } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    class?: HTMLAttributes['class'];
    width?: number;
    height?: number;
    alt?: string;
}

const props = withDefaults(defineProps<Props>(), {
    width: 48,
    height: 44,
    alt: "Школа рисования для взрослых 'АртМир'",
});

// Fallback image handling
const imgError = ref(false);
const imgError2 = ref(false);
const imgLoaded = ref(false);

const handleImageError = () => {
    imgError.value = true;
};

const handleImageError2 = () => {
    imgError2.value = true;
};

const handleImageLoad = () => {
    imgLoaded.value = true;
};

// Fallback image sources
const primarySrc = '/assets/img/logo.webp';
const fallbackSrc = '/assets/img/logo.png';
const fallbackPngSrc = '/assets/img/logo_s.png';
</script>

<template>
    <img
        v-if="!imgError"
        :class="cn('mr-1', props.class)"
        :width="props.width"
        :height="props.height"
        :alt="props.alt"
        :src="primarySrc"
        loading="lazy"
        @error="handleImageError"
        @load="handleImageLoad"
    />
    <img
        v-else-if="!imgError2"
        :class="cn('mr-1', props.class)"
        :width="props.width"
        :height="props.height"
        :alt="props.alt"
        :src="fallbackSrc"
        loading="lazy"
        @error="handleImageError2"
        @load="handleImageLoad"
    />
    <img
        v-else
        :class="cn('mr-1', props.class)"
        :width="props.width"
        :height="props.height"
        :alt="props.alt"
        :src="fallbackPngSrc"
        loading="lazy"
        @load="handleImageLoad"
    />
</template>
