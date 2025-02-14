<script setup lang="ts">
import { defineProps } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import type { Book } from "@/Types/app.entity";
import { useQuasar } from "quasar";
import CreateBookModal from "@/Components/Books/CreateBookModal.vue";

const props = defineProps<{ book: Book }>();

const $q = useQuasar();

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
