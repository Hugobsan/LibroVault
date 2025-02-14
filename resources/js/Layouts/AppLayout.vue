<script setup lang="ts">
import { router } from "@inertiajs/vue3";

const logout = (): void => {
    router.post("/logout");
};

const home = (): void => {
    router.get("/books");
};

</script>

<template>
  <header class="flex justify-between items-center p-4 bg-blue-6 text-white">
    <h1 class="text-xl font-bold cursor-pointer" @click="home">📚 LibroVault</h1>
    <q-btn icon="logout" flat round color="red-6" @click="logout" />
  </header>
  <main class="p-6">
    <slot />
  </main>
</template>
