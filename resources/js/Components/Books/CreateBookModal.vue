<script setup lang="ts">
import { ref } from "vue";
import { useDialogPluginComponent } from "quasar";

defineEmits([...useDialogPluginComponent.emits]);

const { dialogRef, onDialogHide } = useDialogPluginComponent();
const title = ref("");
const author = ref("");

const createBook = () => {
    console.log("Criando livro:", title.value, author.value);
    onDialogHide();
};
</script>

<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-pa-md">
            <q-card-section>
                <h2 class="text-xl font-bold">Adicionar Novo Livro</h2>
            </q-card-section>

            <q-card-section>
                <q-input v-model="title" label="Título" filled />
                <q-input v-model="author" label="Autor" filled class="mt-2" />
            </q-card-section>

            <q-card-actions align="right">
                <q-btn label="Cancelar" color="grey" @click="onDialogHide" />
                <q-btn label="Salvar" color="primary" @click="createBook" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
