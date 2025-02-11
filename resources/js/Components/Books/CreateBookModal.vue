<script setup lang="ts">
import { ref, computed, defineProps, defineEmits } from "vue";
import { useDialogPluginComponent, Notify } from "quasar";
import type { Book } from "@/Types/app.entity";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";

defineEmits([...useDialogPluginComponent.emits]);

const { dialogRef, onDialogHide } = useDialogPluginComponent();

const props = defineProps<{ book?: Book }>();

// Estado do formulário
const title = ref(props.book?.title ?? "");
const volume = ref(props.book?.volume ?? "");
const edition = ref(props.book?.edition ?? "");
const pages = ref(props.book?.pages ?? "");
const isbn = ref(props.book?.isbn ?? "");
const author = ref(props.book?.author ?? "");
const genre = ref(props.book?.genre ?? "");
const publisher = ref(props.book?.publisher ?? "");
const description = ref(props.book?.description ?? "");
const year = ref(props.book?.year ?? "");

// Estado do upload de arquivos
const thumbnail = ref<File | null>(null);
const pdf = ref<File | null>(null);

// Computed para verificar se está editando ou criando
const isEditing = computed(() => !!props.book);

// Função para processar uploads
const handleFileUpload = (files: File[], type: "thumbnail" | "pdf") => {
    if (files.length > 0) {
        if (type === "thumbnail") {
            thumbnail.value = files[0];
        } else if (type === "pdf") {
            pdf.value = files[0];
        }
    }
};

// Função para salvar o livro
const saveBook = () => {
    const form = useForm({
        title: title.value,
        volume: volume.value,
        edition: edition.value,
        pages: pages.value.toString(),
        isbn: isbn.value,
        author: author.value,
        genre: genre.value,
        publisher: publisher.value,
        description: description.value,
        year: year.value.toString(),
        thumbnail: thumbnail.value,
        pdf: pdf.value
    });

    form.post(route("books.store"),
        {
            preserveState: true,
            onSuccess: () => {
                toast.success(isEditing.value ? "Livro editado com sucesso!" : "Livro criado com sucesso!");
                onDialogHide();
            },
            onError: (e) => {
                console.error("Erro ao salvar o livro:", e);
                toast.error("Erro ao salvar o livro.");
            },
        }
    );

    if (isEditing.value) {
        console.log("Editando livro:", formData);
        Notify.create({
            message: "Livro editado com sucesso!",
            color: "positive",
        });
    } else {
        console.log("Criando livro:", formData);
        Notify.create({
            message: "Livro criado com sucesso!",
            color: "positive",
        });
    }

    onDialogHide();
};
</script>

<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-pa-md" style="width: 800px; max-width: 80vw">
            <q-card-section>
                <h2 class="text-xl font-bold">
                    {{ isEditing ? "Editar Livro" : "Adicionar Novo Livro" }}
                </h2>
            </q-card-section>

            <q-card-section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <q-input v-model="title" label="Título *" filled />
                <q-input v-model="author" label="Autor *" filled />

                <q-input v-model="volume" label="Volume" filled type="number" />
                <q-input v-model="edition" label="Edição" filled />

                <q-input v-model="pages" label="Páginas *" filled type="number" />
                <q-input v-model="isbn" label="ISBN *" filled />

                <q-input v-model="genre" label="Gênero *" filled />
                <q-input v-model="publisher" label="Editora *" filled />

                <q-input
                    v-model="year"
                    label="Ano de Publicação"
                    filled
                    type="number"
                />
            </q-card-section>

            <q-card-section>
                <q-input
                    v-model="description"
                    label="Descrição"
                    filled
                    type="textarea"
                />
            </q-card-section>

            <q-card-section>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Upload de Thumbnail -->
                    <q-uploader
                        label="Upload da Thumbnail (5mb)"
                        accept="image/*"
                        max-files="1"
                        color="blue-6"
                        auto-upload
                        style="width: 100%"
                        max-file-size="5242880"
                        @added="handleFileUpload($event, 'thumbnail')"
                    />

                    <!-- Upload de PDF -->
                    <q-uploader
                        label="Upload do PDF (100mb)"
                        accept=".pdf"
                        max-files="1"
                        color="blue-6"
                        auto-upload
                        class="mt-4 md:mt-0"
                        style="width: 100%"
                        max-file-size="104857600"
                        @added="handleFileUpload($event, 'pdf')"
                    />
                </div>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn label="Cancelar" color="grey" @click="onDialogHide" />
                <q-btn
                    :label="isEditing ? 'Salvar Alterações' : 'Criar Livro'"
                    color="primary"
                    @click="saveBook"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
