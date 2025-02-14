<script setup lang="ts">
import { ref, computed, type PropType } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import { useQuasar } from "quasar";
import axios from "axios";
import type { Book } from "@/Types/app.entity";
import CreateBookModal from "@/Components/Books/CreateBookModal.vue";

const props = defineProps({
    books: {
        type: Array as PropType<Book[]>,
        required: false,
        default: () => [],
    },
});

const $q = useQuasar();

const searchQuery = ref(""); // Estado da barra de pesquisa
const isSemanticSearch = ref(false); // Estado do botão de busca semântica
const isSearchLoading = ref(false); // Estado do carregamento da busca
const searchResults = ref<{ book: Book; page_number: number; text: string }[]>(
    []
);

const currentPage = ref(1); // Estado da página atual
const itemsPerPage = ref(6); // Número de itens por página

// Função para formatar o texto removendo marcações e limitando o tamanho
const formatText = (text: string, limit: number = 500) => {
    const formattedText = text.replace(/[\/.]/g, '').replace(/\n/g, ' ');
    return formattedText.length > limit ? formattedText.substring(0, limit) + '...' : formattedText;
};

// Função para realizar a busca semântica
const semanticSearch = async () => {
    try {
        const formData = new FormData();
        formData.append("query", searchQuery.value);
        $q.loading.show({
            message: "Analisando livros...",
        });

        const response = await axios.post(
            route("books.advanced-search"),
            formData
        );

        /* Exemplo de response:
        [
            {
                "book_id": 1,
                "page_number": 1,
                "similarity": 0.9,
                "text": "Lorem ipsum dolor sit amet...",
                "book": { id: 1, title: "Livro Exemplo", author: "Autor" }
            },
            ...
        ]
        */

        searchResults.value = response.data.map((result: any) => ({
            book: result.book,
            page_number: result.page_number,
            text: formatText(result.text), // Formatando o texto e limitando o tamanho
        }));

        if (searchResults.value.length === 0) {
            $q.notify({
                message: "Nenhum resultado relevante encontrado.",
                color: "secondary",
                position: "top",
                timeout: 4000,
            });
        }
    } catch (error) {
        console.error("Erro na busca semântica:", error);
        searchResults.value = [];
    } finally {
        $q.loading.hide();
        isSearchLoading.value = false;
    }
};

// Função para executar a pesquisa
const searchBooks = () => {
    isSearchLoading.value = true;

    if (isSemanticSearch.value) {
        semanticSearch();
    } else {
        isSearchLoading.value = false;
    }
};

// Computed para filtrar os livros quando a busca semântica está desativada
const filteredBooks = computed(() => {
    if (isSearchLoading.value || isSemanticSearch.value) {
        return [];
    }

    if (!searchQuery.value.trim()) {
        return props.books ?? [];
    }

    return props.books.filter((book) =>
        book.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Computed para filtrar os livros com base na página atual e no número de itens por página
const paginatedBooks = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredBooks.value.slice(start, end);
});

// Função para mudar a página
const changePage = (page: number) => {
    currentPage.value = page;
};

// Alternar busca semântica
const toggleSemanticSearch = () => {
    isSemanticSearch.value = !isSemanticSearch.value;

    if (isSemanticSearch.value) {
        $q.notify({
            message:
                "A busca semântica considera a similaridade entre o termo pesquisado e o conteúdo dos PDFs. Pode ser mais precisa, mas pode demorar mais.",
            color: "primary",
            position: "top",
            timeout: 4000,
        });
    }
};

// Função para destacar as palavras correspondentes à pesquisa
const highlightMatch = (text: string) => {
    if (!searchQuery.value.trim()) return text;

    const words = searchQuery.value.split(' ').filter(word => word.trim().length > 3); // Filtrando palavras com mais de 3 letras
    const regex = new RegExp(`(${words.join('|')})`, 'gi');
    return text.replace(regex, "<strong class='text-red-600'>$1</strong>");
};

// Abrir modal para criar um novo livro
const openCreateModal = () => {
    $q.dialog({
        component: CreateBookModal,
        componentProps: {
            book: null,
        },
    });
};

// Abrir modal para editar um livro existente
const openEditModal = (book: Book) => {
    $q.dialog({
        component: CreateBookModal,
        componentProps: {
            book,
            isEdit: true,
        },
    });
};

// Navegar para detalhes do livro
const viewBook = (id: number) => {
    router.get(`/books/${id}`);
};

// Função para limpar os resultados relevantes
const clearSearchResults = () => {
    searchResults.value = [];
};

// Função para abrir o menu de ações do livro
const openBookActions = (book: Book, event: MouseEvent) => {
    event.stopPropagation(); // Evita que o clique acione a rota de show do livro
    // Lógica para abrir o menu de ações
};

// Função para deletar um livro
const deleteBook = async (id: number) => {
    try {
        await router.delete(route("books.destroy", id));
        $q.notify({
            message: "Livro excluído com sucesso.",
            color: "positive",
            position: "top",
            timeout: 4000,
        });
    } catch (error) {
        console.error("Erro ao excluir o livro:", error);
        $q.notify({
            message: "Erro ao excluir o livro.",
            color: "negative",
            position: "top",
            timeout: 4000,
        });
    }
};
</script>

<template>
    <AppLayout>
        <div class="p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">📚 Biblioteca</h1>
                <q-btn
                    color="primary"
                    unelevated
                    outline
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
                @keydown.enter="searchBooks"
            >
                <template v-slot:prepend>
                    <q-btn flat round @click="searchBooks" title="Buscar">
                        <q-icon name="search" />
                    </q-btn>
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
                    />
                </template>
            </q-input>

            <!-- Sugestões da Busca Semântica -->
            <div
                v-if="isSemanticSearch && searchResults.length > 0"
                class="bg-gray-100 p-3 rounded shadow-md mb-4 relative max-h-64 overflow-y-auto"
            >
                <q-btn
                    @click="clearSearchResults"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
                    flat
                    round
                    icon="close"
                />
                <h3 class="text-lg font-bold mb-2">🔍 Resultados Relevantes</h3>
                <ul>
                    <li
                        v-for="result in searchResults"
                        :key="result.book.id + '-' + result.page_number"
                        class="cursor-pointer hover:bg-gray-200 p-2 rounded"
                        @click="viewBook(result.book.id)"
                    >
                        <span class="font-bold text-blue-600">{{
                            result.book.title
                        }}</span>
                        - Página
                        <span class="font-semibold">{{
                            result.page_number
                        }}</span>
                        <br />
                        <span
                            v-html="highlightMatch(result.text)"
                            class="text-gray-700"
                        ></span>
                    </li>
                </ul>
            </div>

            <!-- Skeleton Loader -->
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
                    v-for="book in paginatedBooks"
                    :key="book.id"
                    class="cursor-pointer"
                    @click="viewBook(book.id)"
                >
                    <q-img
                        :src="
                            book.thumbnail_file
                                ? book.thumbnail_file.url
                                : '/assets/imgs/cover.jpg'
                        "
                        alt="Capa do livro"
                        class="h-48 w-full object-cover"
                    />
                    <q-card-section>
                        <div class="flex flex-row items-center justify-between space-x-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-lg font-semibold truncate">
                                    {{ book.title }}
                                </div>
                                <div class="text-sm text-gray-500 truncate">
                                    Autor: {{ book.author }}
                                </div>
                                <div class="text-sm text-gray-500 truncate">
                                    {{ book.description }}
                                </div>
                            </div>
                            <q-btn-dropdown color="grey-9" size="sm" outline square @click.stop>
                                <q-list>
                                    <q-item
                                        clickable
                                        v-ripple
                                        @click="openEditModal(book)"
                                    >
                                        <q-item-section>
                                            Editar
                                        </q-item-section>
                                    </q-item>
                                    <q-item
                                        clickable
                                        v-ripple
                                        @click="deleteBook(book.id)"
                                    >
                                        <q-item-section class="text-red-600">
                                            Excluir
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </q-btn-dropdown>
                        </div>
                    </q-card-section>
                    <q-card-actions align="right"> </q-card-actions>
                </q-card>
            </div>

            <!-- Paginação -->
            <div class="w-full flex justify-center mt-4">
                <q-pagination
                    v-if="filteredBooks.length > itemsPerPage"
                    v-model="currentPage"
                    :max="Math.ceil(filteredBooks.length / itemsPerPage)"
                    @update:model-value="changePage"
                    color="primary"
                />
            </div>
        </div>
    </AppLayout>
</template>
