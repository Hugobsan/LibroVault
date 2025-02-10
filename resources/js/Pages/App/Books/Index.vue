<script setup lang="ts">
import { ref, computed, type PropType } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import type { Book } from "@/Types/app.entity";

const props = defineProps({
    books: {
        type: Array as PropType<Book[]>,
        required: false,
        default: () => [],
    },
});

const searchQuery = ref(""); // Estado da barra de pesquisa
const isSemanticSearch = ref(false); // Estado do botão de busca semântica
const showCreateModal = ref(false); // Controle do modal de criação
const isSearchLoading = ref(false); // Estado do carregamento da busca

// Computed para filtrar os livros
const filteredBooks = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.books ?? [];
    }
    return props.books.filter((book) =>
        book.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Alternar busca semântica
const toggleSemanticSearch = () => {
    isSemanticSearch.value = !isSemanticSearch.value;
};

// Navegar para detalhes do livro
const viewBook = (id: number) => {
    router.get(`/books/${id}`);
};
</script>

<template>
    <AppLayout>
        <div class="p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">📚 Biblioteca</h1>
                <!-- Botão para Criar Livro -->
                <q-btn
                    color="primary"
                    unelevated
                    icon="add"
                    label="Adicionar Livro"
                    @click="showCreateModal = true"
                    size="sm"
                />
            </div>

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
                <template v-slot:append>
                    <q-btn
                        @click="toggleSemanticSearch"
                        unelevated
                        size="sm"
                        round
                        :color="isSemanticSearch ? 'green' : 'grey'"
                        :title="
                            isSemanticSearch
                                ? 'Desativar busca semântica'
                                : 'Ativar busca semântica'
                        "
                        :icon="
                            isSemanticSearch
                                ? 'auto_awesome'
                                : 'auto_awesome_off'
                        "
                    ></q-btn>
                </template>
            </q-input>
            <!-- Skeleton para estado de search loading -->
            <q-skeleton
                v-if="isSearchLoading"
                class="w-full h-12"
                :rounded="true"
            />

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
                        :src="book.thumbnail || '/assets/imgs/cover.jpg'"
                        alt="Capa do livro"
                        class="h-48 w-full object-cover"
                    />
                    <q-card-section>
                        <div class="text-lg font-semibold">
                            {{ book.title }}
                        </div>
                        <div class="text-sm text-gray-500">
                            Autor: {{ book.author }}
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </AppLayout>
</template>
