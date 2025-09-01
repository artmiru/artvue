<script lang="ts" setup>
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <ol
    data-slot="breadcrumb-list"
    :class="cn('text-muted-foreground flex flex-wrap items-center gap-1.5 break-words bg-neutral-50 px-3 text-xs rounded-b', props.class)"
  >
    <slot />
  </ol>
</template>
