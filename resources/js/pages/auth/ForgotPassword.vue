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
import { formatPhone } from '@/helpers/phoneHelper.js';

defineProps<{
    status?: string;
}>();

const formatPhoneInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
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