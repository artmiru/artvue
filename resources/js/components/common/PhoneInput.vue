<script setup lang="ts">
import { ref, watch } from 'vue';
import { formatPhone, validatePhone } from '@/helpers/phoneHelper';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/common/InputError.vue';

// Определяем props
const props = withDefaults(defineProps<{
  modelValue?: string;
  id?: string;
  name?: string;
  placeholder?: string;
  required?: boolean;
  autofocus?: boolean;
  tabindex?: number;
  autocomplete?: string;
  label?: string;
  error?: string;
}>(), {
  modelValue: '',
  placeholder: '+7 (921) 924-52-28',
  required: false,
  autofocus: false,
  tabindex: 0,
  autocomplete: 'tel',
  label: 'Номер телефона'
});

// Определяем emits
const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
  (e: 'valid', isValid: boolean): void;
}>();

// Реактивные переменные
const internalValue = ref(props.modelValue);
const isValid = ref(true);
const errorMessage = ref('');

// Функция для форматирования номера телефона при вводе
const formatPhoneInput = (event: Event) => {
  const input = event.target as HTMLInputElement;
  const value = input.value.replace(/\D/g, '');
  const formatted = formatPhone(value);
  input.value = formatted;
  internalValue.value = formatted;
  emit('update:modelValue', formatted);
  
  // Проверяем валидность
  const valid = validatePhone(formatted);
  isValid.value = valid;
 emit('valid', valid);
  
  // Устанавливаем сообщение об ошибке, если номер невалидный
  if (!valid && formatted.length > 0) {
    errorMessage.value = 'Пожалуйста, введите корректный номер телефона';
  } else {
    errorMessage.value = '';
  }
};

// Следим за изменением modelValue извне
watch(() => props.modelValue, (newValue) => {
  internalValue.value = newValue;
});
</script>

<template>
  <div class="grid gap-2">
    <Label v-if="label" :for="id">{{ label }}</Label>
    <Input
      :id="id"
      :name="name"
      type="tel"
      :required="required"
      :autofocus="autofocus"
      :tabindex="tabindex"
      :autocomplete="autocomplete"
      :placeholder="placeholder"
      :value="internalValue"
      @input="formatPhoneInput"
    />
    <InputError :message="error || errorMessage" />
  </div>
</template>