<script setup lang="ts">
import { ref, computed, defineProps, defineEmits } from "vue";
import { useDialogPluginComponent } from "quasar";
import type { Book } from "@/Types/app.entity";

defineEmits([...useDialogPluginComponent.emits]);

const { dialogRef, onDialogHide } = useDialogPluginComponent();

// Prop opcional para edição de livro
const props = defineProps<{ book?: Book }>();

// Estado do formulário (inicia vazio ou preenchido se estiver editando)
const title = ref(props.book?.title ?? "");
const author = ref(props.book?.author ?? "");

// Computed para verificar se está editando ou criando
const isEditing = computed(() => !!props.book);

// Função para salvar o livro
const saveBook = () => {
    if (isEditing.value) {
        console.log("Editando livro:", {
            title: title.value,
            author: author.value,
        });
    } else {
        console.log("Criando livro:", {
            title: title.value,
            author: author.value,
        });
    }
    onDialogHide();
};
</script>

<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-pa-md">
            <q-card-section>
                <h2 class="text-xl font-bold">
                    {{ isEditing ? "Editar Livro" : "Adicionar Novo Livro" }}
                </h2>
            </q-card-section>

            <q-card-section>
                <q-input v-model="title" label="Título" filled />
                <q-input v-model="author" label="Autor" filled class="mt-2" />
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
