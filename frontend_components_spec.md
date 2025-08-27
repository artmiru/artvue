# Спецификация обновления фронтенд компонентов

## Общие требования

1. Заменить все поля email на phone
2. Добавить валидацию номера телефона
3. Добавить форматирование номера телефона при вводе
4. Обновить текстовые метки и placeholder'ы
5. Сохранить существующую стилизацию и UX

## 1. Компонент входа (Login.vue)

### Файл: resources/js/pages/auth/Login.vue

### Изменения:
1. Заменить поле ввода email на phone
2. Добавить валидацию номера телефона
3. Добавить форматирование номера телефона при вводе
4. Обновить текстовые метки

### Новый код:
```vue
<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { formatPhone, validatePhone } from '@/helpers/phoneHelper';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

// Функция для форматирования номера телефона при вводе
const formatPhoneInput = (event) => {
    const input = event.target;
    const value = input.value.replace(/\D/g, '');
    const formatted = formatPhone(value);
    input.value = formatted;
};
</script>

<template>
    <AuthBase title="Вход в аккаунт" description="Введите ваш номер телефона и пароль для входа">
        <Head title="Вход" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <Form
            v-bind="AuthenticatedSessionController.store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="phone">Номер телефона</Label>
                    <Input
                        id="phone"
                        type="tel"
                        name="phone"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="tel"
                        placeholder="+7 (921) 924-52-28"
                        @input="formatPhoneInput"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Пароль</Label>
                        <TextLink v-if="canResetPassword" :href="request()" class="text-sm" :tabindex="5"> Забыли пароль? </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Пароль"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Запомнить меня</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="processing">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Войти
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Нет аккаунта?
                <TextLink :href="register()" :tabindex="5">Зарегистрироваться</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
```

## 2. Компонент регистрации (Register.vue)

### Файл: resources/js/pages/auth/Register.vue

### Изменения:
1. Заменить поле ввода email на phone
2. Добавить валидацию номера телефона
3. Добавить форматирование номера телефона при вводе
4. Обновить текстовые метки

### Новый код:
```vue
<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { formatPhone } from '@/helpers/phoneHelper';

const formatPhoneInput = (event) => {
    const input = event.target;
    const value = input.value.replace(/\D/g, '');
    const formatted = formatPhone(value);
    input.value = formatted;
};
</script>

<template>
    <AuthBase title="Создание аккаунта" description="Введите ваши данные для создания аккаунта">
        <Head title="Регистрация" />

        <Form
            v-bind="RegisteredUserController.store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Имя</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name" placeholder="Ваше имя" />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Номер телефона</Label>
                    <Input 
                        id="phone" 
                        type="tel" 
                        required 
                        :tabindex="2" 
                        autocomplete="tel" 
                        name="phone" 
                        placeholder="+7 (921) 924-52-28"
                        @input="formatPhoneInput"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Пароль</Label>
                    <Input id="password" type="password" required :tabindex="3" autocomplete="new-password" name="password" placeholder="Пароль" />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Подтверждение пароля</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Подтверждение пароля"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="processing">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Создать аккаунт
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Уже есть аккаунт?
                <TextLink :href="login()" class="underline underline-offset-4" :tabindex="6">Войти</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
```

## 3. Компонент сброса пароля (ForgotPassword.vue)

### Файл: resources/js/pages/auth/ForgotPassword.vue

### Изменения:
1. Заменить поле ввода email на phone
2. Добавить валидацию номера телефона
3. Добавить форматирование номера телефона при вводе
4. Обновить текстовые метки

### Новый код:
```vue
<script setup lang="ts">
import PasswordResetLinkController from '@/actions/App/Http/Controllers/Auth/PasswordResetLinkController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { formatPhone } from '@/helpers/phoneHelper';

const formatPhoneInput = (event) => {
    const input = event.target;
    const value = input.value.replace(/\D/g, '');
    const formatted = formatPhone(value);
    input.value = formatted;
};
</script>

<template>
    <AuthLayout title="Забыли пароль?" description="Введите ваш номер телефона для получения кода сброса пароля">
        <Head title="Забыли пароль?" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <Form v-bind="PasswordResetLinkController.store.form()" v-slot="{ errors, processing }">
                <div class="grid gap-2">
                    <Label for="phone">Номер телефона</Label>
                    <Input 
                        id="phone" 
                        type="tel" 
                        name="phone" 
                        autocomplete="off" 
                        autofocus 
                        placeholder="+7 (921) 924-52-28"
                        @input="formatPhoneInput"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="processing">
                        <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                        Отправить код сброса
                    </Button>
                </div>
            </Form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Или</span>
                <TextLink :href="login()">войти в аккаунт</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
```

## Helper функции для JavaScript

### Файл: resources/js/helpers/phoneHelper.js

```javascript
/**
 * Извлекает 10 цифр из номера телефона
 * @param {string} phone - Номер телефона в любом формате
 * @returns {string|null} - 10 цифр номера телефона или null
 */
export function extractDigits(phone) {
    if (!phone) return null;
    
    // Извлекаем только цифры
    const digits = phone.replace(/\D/g, '');
    
    // Если начинается с 7 или 8, убираем первую цифру
    if (digits.length === 11 && (digits[0] === '7' || digits[0] === '8')) {
        return digits.substring(1);
    }
    
    // Если уже 10 цифр, возвращаем как есть
    if (digits.length === 10) {
        return digits;
    }
    
    // Если меньше 10 цифр, возвращаем null
    return null;
}

/**
 * Проверяет корректность номера телефона
 * @param {string} phone - Номер телефона в любом формате
 * @returns {boolean} - true, если номер корректный
 */
export function validatePhone(phone) {
    if (!phone) return false;
    
    const digits = extractDigits(phone);
    if (!digits) return false;
    
    // Проверяем, что номер начинается с допустимого кода оператора
    const validPrefixes = [
        '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'
    ];
    
    return validPrefixes.some(prefix => digits.startsWith(prefix));
}

/**
 * Форматирует номер телефона для отображения
 * @param {string} phone - Номер телефона в любом формате
 * @returns {string} - Отформатированный номер телефона
 */
export function formatPhone(phone) {
    if (!phone) return '';
    
    const digits = extractDigits(phone);
    if (!digits) return phone;
    
    // Форматируем как +7 (921) 924-52-28
    return `+7 (${digits.substring(0, 3)}) ${digits.substring(3, 6)}-${digits.substring(6, 8)}-${digits.substring(8, 10)}`;
}

/**
 * Проверяет, является ли номер действительным российским номером
 * @param {string} phone - Номер телефона в любом формате
 * @returns {boolean} - true, если номер является действительным российским номером
 */
export function isValidRussianPhone(phone) {
    return validatePhone(phone);
}
```

## Стилизация

### Общие стили для полей ввода телефона:
```css
/* В файле стилей приложения */
input[type="tel"] {
    font-family: monospace;
}

/* Стили для ошибок валидации */
.phone-error {
    color: #ef444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
```

## Тестирование

### Создать тесты для проверки:
1. Корректного отображения компонентов
2. Валидации номера телефона
3. Форматирования номера телефона при вводе
4. Обработки ошибок
5. Работы с различными форматами номеров

## Безопасность

### Меры безопасности:
1. Валидация входных данных на фронтенде
2. Ограничение длины ввода
3. Экранирование специальных символов
4. Защита от XSS атак
5. Логирование подозрительных действий

## Локализация

### Текстовые метки:
- "Номер телефона" вместо "Email address"
- "Введите ваш номер телефона и пароль для входа" вместо "Enter your email and password below to log in"
- "Забыли пароль?" вместо "Forgot password?"
- и т.д.