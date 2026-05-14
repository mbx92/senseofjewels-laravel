<script setup>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const host = ref(null);
let quill = null;

onMounted(() => {
    if (!host.value) {
        return;
    }

    quill = new Quill(host.value, {
        theme: 'snow',
        modules: {
            toolbar: [['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['link'], ['clean']],
        },
    });

    const initial = props.modelValue || '';
    if (initial) {
        quill.root.innerHTML = initial;
    }

    quill.on('text-change', () => {
        emit('update:modelValue', quill.root.innerHTML);
    });
});

watch(
    () => props.modelValue,
    (html) => {
        if (!quill) {
            return;
        }
        const next = html || '';
        if (next === quill.root.innerHTML) {
            return;
        }
        const sel = quill.getSelection();
        quill.root.innerHTML = next;
        if (sel) {
            quill.setSelection(sel);
        }
    },
);

onBeforeUnmount(() => {
    quill = null;
});
</script>

<template>
    <fieldset class="fieldset">
        <legend class="fieldset-legend">Deskripsi Lengkap</legend>
        <div ref="host" class="product-quill-host rounded-b-lg rounded-t-lg border border-base-300 bg-base-100" />
        <p v-if="error" class="fieldset-label text-error">{{ error }}</p>
    </fieldset>
</template>

<style scoped>
.product-quill-host :deep(.ql-container) {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    border: none;
    font-size: 14px;
}
.product-quill-host :deep(.ql-toolbar) {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    border: none;
    border-bottom: 1px solid color-mix(in oklab, var(--color-base-content) 12%, transparent);
}
.product-quill-host :deep(.ql-editor) {
    min-height: 220px;
}
</style>
