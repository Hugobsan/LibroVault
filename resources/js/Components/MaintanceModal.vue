<script setup lang="ts">
import { useDialogPluginComponent } from "quasar";

defineEmits([...useDialogPluginComponent.emits]);

const { dialogRef, onDialogHide } = useDialogPluginComponent();
</script>

<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card class="q-pa-md">
            <q-card-section class="text-center">
                <h1 class="text-xl font-bold mt-2 flex items-center justify-center gap-2">
                    🚧 <span>Em Manutenção</span> 🚧
                </h1>

                <!-- Container FLEX para alinhar lado a lado -->
                <div class="flex items-center justify-center mt-4 gap-6">
                    <q-img
                        src="/assets/imgs/picapau-manutencao.png"
                        alt="Em manutenção"
                        class="w-32 h-32"
                        fit="contain"
                    />

                    <p class="font-semibold text-lg ">
                        Este módulo ainda está em desenvolvimento. <br />
                        Por favor, volte mais tarde!
                    </p>
                </div>
            </q-card-section>

            <q-card-actions align="center">
                <q-btn @click="onDialogHide" size="md" unelevated outline color="orange">
                    Entendido!
                </q-btn>
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
