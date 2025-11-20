<script setup>
import Toast from './Toast.vue';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const toasts = ref([]);
const processedFlashIds = ref(new Set());

const addToast = (toast) => {
    const id = Date.now() + Math.random();
    toasts.value.push({ ...toast, id });

    if (toast.duration && toast.duration > 0) {
        setTimeout(() => {
            removeToast(id);
        }, toast.duration);
    }
};

const removeToast = (id) => {
    const index = toasts.value.findIndex((toast) => toast.id === id);
    if (index > -1) {
        toasts.value.splice(index, 1);
    }
};

const checkFlashMessages = () => {
    const flash = page.props.flash;
    if (flash?.success) {
        const flashId = `success-${flash.success}-${Date.now()}`;
        if (!processedFlashIds.value.has(flashId)) {
            processedFlashIds.value.add(flashId);
            addToast({
                title: 'Success',
                description: flash.success,
                variant: 'success',
                duration: 5000,
            });

            setTimeout(() => {
                processedFlashIds.value.delete(flashId);
            }, 100);
        }
    }
};

const handleToastEvent = (event) => {
    addToast(event.detail);
};

onMounted(() => {
    checkFlashMessages();
    window.addEventListener('toast:show', handleToastEvent);
});

onUnmounted(() => {
    window.removeEventListener('toast:show', handleToastEvent);
});

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            checkFlashMessages();
        }
    },
    { deep: true },
);

defineExpose({
    addToast,
    removeToast,
});
</script>

<template>
    <Teleport to="body">
        <div
            data-toaster
            class="pointer-events-none fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]"
        >
            <TransitionGroup
                name="toast"
                tag="div"
                class="flex flex-col gap-2"
            >
                <Toast
                    v-for="toast in toasts"
                    :key="toast.id"
                    :title="toast.title"
                    :description="toast.description"
                    :variant="toast.variant"
                    @close="removeToast(toast.id)"
                />
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.toast-move {
    transition: transform 0.3s ease;
}
</style>

