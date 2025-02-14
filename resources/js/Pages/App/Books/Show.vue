<script setup lang="ts">
import { defineProps, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import type { Book } from "@/Types/app.entity";
import { useQuasar } from "quasar";
import axios from "axios";
import CreateBookModal from "@/Components/Books/CreateBookModal.vue";

const props = defineProps<{ book: Book }>();

const $q = useQuasar();

const searchQuery = ref(""); // Estado da barra de pesquisa
const isSearchLoading = ref(false); // Estado do carregamento da busca
const searchResults = ref<{ page_number: number; text: string }[]>([]);

const goBack = () => {
    router.get(route("books.index"));
};

const editBook = () => {
    $q.dialog({
        component: CreateBookModal,
        componentProps: {
            book: props.book,
            isEdit: true,
        },
    }).onOk(() => {
        router.reload();
    });
};

const deleteBook = () => {
    if (confirm("Tem certeza que deseja excluir este livro?")) {
        router.delete(route("books.destroy", { book: props.book.id }));
    }
};

// Função para formatar o texto removendo marcações e limitando o tamanho
const formatText = (text: string, limit: number = 500) => {
    const formattedText = text.replace(/[\/\.]/g, '').replace(/\n/g, ' ');
    return formattedText.length > limit ? formattedText.substring(0, limit) + '...' : formattedText;
};

// Função para realizar a busca semântica
const semanticSearch = async () => {
    try {
        const formData = new FormData();
        formData.append("query", searchQuery.value);
        formData.append("book_id", props.book.id.toString());
        $q.loading.show({
            message: "Analisando livro...",
        });

        const response = await axios.post(
            route("books.advanced-search"),
            formData
        );

        searchResults.value = response.data.map((result: any) => ({
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
const searchBook = () => {
    isSearchLoading.value = true;
    semanticSearch();
};

// Função para destacar as palavras correspondentes à pesquisa
const highlightMatch = (text: string) => {
    if (!searchQuery.value.trim()) return text;

    const words = searchQuery.value.split(' ').filter(word => word.trim().length > 3); // Filtrando palavras com mais de 3 letras
    const regex = new RegExp(`(${words.join('|')})`, 'gi');
    return text.replace(regex, "<strong class='text-red-600'>$1</strong>");
};

// Função para limpar os resultados relevantes
const clearSearchResults = () => {
    searchResults.value = [];
};
</script>

<template>
    <AppLayout>
        <div class="p-6 max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">📖 {{ book.title }}</h1>
                <div class="flex space-x-2">
                    <q-btn
                        color="primary"
                        unelevated
                        outline
                        icon="edit"
                        label="Editar"
                        @click="editBook"
                        size="sm"
                    />
                    <q-btn
                        color="red"
                        unelevated
                        outline
                        icon="delete"
                        label="Excluir"
                        @click="deleteBook"
                        size="sm"
                    />
                    <q-btn
                        color="primary"
                        unelevated
                        outline
                        icon="arrow_back"
                        label="Voltar"
                        @click="goBack"
                        size="sm"
                    />
                </div>
            </div>

            <q-input
                v-model="searchQuery"
                filled
                placeholder="Pesquisar no livro..."
                class="mb-4"
                @keydown.enter="searchBook"
            >
                <template v-slot:prepend>
                    <q-btn flat round @click="searchBook" title="Buscar">
                        <q-icon name="search" />
                    </q-btn>
                </template>
            </q-input>

            <div
                v-if="searchResults.length > 0"
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
                        :key="result.page_number"
                        class="cursor-pointer hover:bg-gray-200 p-2 rounded"
                    >
                        Página
                        <span class="font-semibold">{{ result.page_number }}</span>
                        <br />
                        <span
                            v-html="highlightMatch(result.text)"
                            class="text-gray-700"
                        ></span>
                    </li>
                </ul>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <q-img
                    :src="
                        book.thumbnail_file
                            ? book.thumbnail_file.url
                            : '/assets/imgs/cover.jpg'
                    "
                    alt="Capa do livro"
                    class="h-64 w-full object-cover"
                />
                <div>
                    <p><strong>Autor:</strong> {{ book.author }}</p>
                    <p><strong>Volume:</strong> {{ book.volume }}</p>
                    <p><strong>Edição:</strong> {{ book.edition }}</p>
                    <p><strong>Páginas:</strong> {{ book.pages }}</p>
                    <p><strong>ISBN:</strong> {{ book.isbn }}</p>
                    <p><strong>Gênero:</strong> {{ book.genre }}</p>
                    <p><strong>Editora:</strong> {{ book.publisher }}</p>
                    <p><strong>Ano de Publicação:</strong> {{ book.year }}</p>
                    <p><strong>Descrição:</strong> {{ book.description }}</p>
                    <q-btn
                        v-if="book.pdf_file"
                        color="primary"
                        unelevated
                        outline
                        icon="download"
                        label="Baixar PDF"
                        :href="book.pdf_file.url"
                        target="_blank"
                        size="sm"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
