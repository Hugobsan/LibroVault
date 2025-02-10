<script setup lang="ts">
import { ref, computed, type PropType } from "vue";
import { router } from "@inertiajs/vue3";
import type { Book } from "@/Types/app.entity";

const props = defineProps({
    books: {
        type: Array as PropType<Book[]>,
        required: false,
        default: () => [],
    },
});

// Estado da barra de pesquisa
const searchQuery = ref("");

// Computed para filtrar os livros
const filteredBooks = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.books ?? [];
    }
    return props.books.filter((book) =>
        book.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Navegar para detalhes do livro
const viewBook = (id: number) => {
    router.get(`/books/${id}`);
};
</script>

<template>
    <div class="p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-6">📚 Biblioteca</h1>

        <!-- Barra de Pesquisa -->
        <q-input
            v-model="searchQuery"
            filled
            placeholder="Pesquisar livros..."
            class="mb-4"
            @keydown.enter="searchQuery = searchQuery.trim()"
        >
            <template v-slot:prepend>
                <q-icon name="search" />
            </template>
        </q-input>

        <!-- Placeholder se não houver livros -->
        <div
            v-if="props.books.length === 0"
            class="text-center text-gray-500 mt-4"
        >
            Nenhum livro encontrado.
        </div>

        <!-- Lista de Livros -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <q-card
                v-for="book in filteredBooks"
                :key="book.id"
                class="cursor-pointer"
                @click="viewBook(book.id)"
            >
                <q-img
                    :src="book.thumbnail || '/placeholder.jpg'"
                    alt="Capa do livro"
                    class="h-48 w-full object-cover"
                />
                <q-card-section>
                    <div class="text-lg font-semibold">{{ book.title }}</div>
                    <div class="text-sm text-gray-500">
                        Autor: {{ book.author }}
                    </div>
                </q-card-section>
            </q-card>
        </div>
    </div>
</template>
