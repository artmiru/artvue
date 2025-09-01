<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { computed } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
  /**
   * Text to display below the spinner
   * @default 'Загрузка...'
   */
  text?: string
  /**
   * Color of the spinner
   * @default 'blue'
   */
  color?: 'red' | 'green' | 'blue' | 'indigo' | 'purple' | 'gray'
  /**
   * Size of the spinner
   * @default 'md'
   */
  size?: 'sm' | 'md' | 'lg'
  /**
   * Additional CSS classes to apply to the spinner container
   */
 class?: HTMLAttributes['class']
}

const props = withDefaults(defineProps<Props>(), {
  text: 'Загрузка...',
  color: 'blue',
  size: 'md',
  class: ''
})

// Map color names to Tailwind classes
const colorClasses = {
  red: 'border-red-500',
  green: 'border-green-500',
  blue: 'border-blue-500',
  indigo: 'border-indigo-500',
  purple: 'border-purple-500',
  gray: 'border-gray-500'
} as const

// Map size names to Tailwind classes
const sizeClasses = {
  sm: 'h-8 w-8',
  md: 'h-12 w-12',
  lg: 'h-16 w-16'
} as const

// Computed classes for the spinner element
const spinnerClasses = computed(() => {
  return cn(
    'animate-spin rounded-full border-t-2 border-b-2',
    colorClasses[props.color],
    sizeClasses[props.size]
  )
})

// Computed classes for the text element
const textClasses = computed(() => {
  const baseClasses = 'text-neutral-700 mt-2'
 const sizeClasses = {
    sm: 'text-lg',
    md: 'text-xl',
    lg: 'text-2xl'
  } as const
  
  return cn(baseClasses, sizeClasses[props.size])
})
</script>

<template>
  <div :class="cn('flex flex-col items-center justify-center', props.class)">
    <div :class="spinnerClasses" />
    <p :class="textClasses">
      {{ text }}
    </p>
  </div>
</template>

<style scoped>
/* Additional styles if needed */
</style>