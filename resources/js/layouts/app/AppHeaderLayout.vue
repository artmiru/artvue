<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
  /**
   * Массив элементов навигационной цепочки (breadcrumbs)
   * @example [{ title: 'Home', href: '/' }, { title: 'Dashboard', href: '/dashboard' }]
   */
  breadcrumbs?: BreadcrumbItemType[];
}

/**
 * Props for the AppHeaderLayout component with default values
 *
 * @prop {BreadcrumbItemType[]} breadcrumbs - Array of breadcrumb items (default: empty array)
 */
const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});
</script>

<template>
  <AppShell class="flex-col">
    <AppHeader :breadcrumbs="props.breadcrumbs" />
    <AppContent>
      <slot />
    </AppContent>
  </AppShell>
</template>
