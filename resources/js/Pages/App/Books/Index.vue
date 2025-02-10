<script setup lang="ts">
import { ref, computed, watch, type PropType } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import type { Book } from "@/Types/app.entity";
import { is } from "quasar";
import axios from "axios";

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
const selectedBook = ref<Book | null>(null); // Estado para edição de livro

const semanticSearch = async () => {
    const formData = new FormData();
    formData.append("search", searchQuery.value);

    const response = await axios.post(route("books.advanced-search"), formData);
    return response.data;
};

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if (new Date().getTime() - start > milliseconds) {
            break;
        }
    }
}

// Computed para filtrar os livros
const filteredBooks = computed(() => {
    if (isSearchLoading.value) {
        return [];
    }
    isSearchLoading.value = true;

    // Busca semântica
    if (isSemanticSearch.value) {
        semanticSearch().then((response) => {
            isSearchLoading.value = false;
            return response;
        });
    }

    if (!searchQuery.value.trim()) {
        isSearchLoading.value = false;
        return props.books ?? [];
    }
    //Adicionando 1s de espera para simular uma busca real
    sleep(1000);
    let response = props.books.filter((book) =>
        book.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
    console.log("de boa");
    isSearchLoading.value = false;
    return response ?? [];
});

// Watch para acionar a busca quando searchQuery mudar
watch(searchQuery, () => {
    filteredBooks.value;
});

// Alternar busca semântica
const toggleSemanticSearch = () => {
    isSemanticSearch.value = !isSemanticSearch.value;
};

// Abrir modal para criar um novo livro
const openCreateModal = () => {
    selectedBook.value = null;
    showCreateModal.value = true;
};

// Abrir modal para editar um livro existente
const openEditModal = (book: Book) => {
    selectedBook.value = book;
    showCreateModal.value = true;
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
                    @click="openCreateModal"
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
                        icon="auto_awesome"
                    ></q-btn>
                </template>
            </q-input>

            <!-- Skeleton para estado de search loading -->
            <div
                v-if="isSearchLoading"
                class="grid grid-cols-1 md:grid-cols-3 gap-6"
            >
                <q-skeleton
                    v-for="n in 6"
                    :key="n"
                    type="rect"
                    class="h-48 w-full"
                />
            </div>

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
                    <q-card-actions align="right">
                        <q-btn
                            icon="edit"
                            color="blue"
                            @click="openEditModal(book)"
                        />
                    </q-card-actions>
                </q-card>
            </div>
        </div>
    </AppLayout>
</template>
