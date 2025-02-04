<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { toast } from 'vue3-toastify';

// Estrutura do formulário
interface LoginForm {
  email: string;
  password: string;
  remember: boolean;
}

// Dados do formulário
const form = ref<LoginForm>({
  email: "",
  password: "",
  remember: false,
});

// Estado de carregamento
const loading = ref<boolean>(false);

// Função de login
const login = (): void => {
  loading.value = true;
  router.post("/login", form.value, {
    onSuccess: () => {
      toast.success("Login realizado com sucesso!");
    },
    onError: (errors) => {
      toast.error(errors.email || "Erro ao fazer login");
    },
    onFinish: () => {
      loading.value = false;
    },
  });
};
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
      <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">
        📚 LibroVault - Login
      </h2>

      <q-form @submit.prevent="login" class="space-y-4">
        <q-input
          v-model="form.email"
          label="E-mail"
          type="email"
          filled
          class="w-full"
        />

        <q-input
          v-model="form.password"
          label="Senha"
          type="password"
          filled
          class="w-full"
        />

        <div class="flex items-center justify-between">
          <q-toggle v-model="form.remember" label="Lembrar-me" />
          <a href="/forgot-password" class="text-blue-500 hover:underline text-sm">
            Esqueceu a senha?
          </a>
        </div>

        <q-btn
          type="submit"
          label="Entrar"
          color="primary"
          unelevated
          class="w-full mt-4"
          :loading="loading"
        />
      </q-form>

      <p class="text-center text-sm text-gray-600 mt-4">
        Ainda não tem uma conta?
        <a href="/register" class="text-blue-500 hover:underline">Cadastre-se</a>
      </p>
    </div>
  </div>
</template>
