<script setup lang="ts">
import { router } from "@inertiajs/vue3";

const logout = (): void => {
    router.post("/logout");
};
</script>

<template>
  <header class="flex justify-between items-center p-4 bg-blue-600 text-white">
    <h1 class="text-lg font-bold">LibroVault</h1>
    <q-btn label="Sair" color="red" @click="logout" />
  </header>
  <main class="p-6">
    <slot />
  </main>
</template>
