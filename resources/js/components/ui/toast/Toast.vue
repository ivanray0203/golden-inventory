<script setup>
import { cn } from '@/lib/utils';
import { X } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'success', 'destructive'].includes(value),
    },
    duration: {
        type: Number,
        default: 5000,
    },
});

const emit = defineEmits(['close']);

const variantStyles = {
    default: 'bg-background border-border',
    success: 'bg-green-50 border-green-200 dark:bg-green-950 dark:border-green-800',
    destructive: 'bg-red-50 border-red-200 dark:bg-red-950 dark:border-red-800',
};

const textStyles = {
    default: 'text-foreground',
    success: 'text-green-900 dark:text-green-100',
    destructive: 'text-red-900 dark:text-red-100',
};

const iconStyles = {
    default: 'text-foreground',
    success: 'text-green-600 dark:text-green-400',
    destructive: 'text-red-600 dark:text-red-400',
};
</script>

<template>
    <div
        :class="
            cn(
                'group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-md border p-6 pr-8 shadow-lg transition-all',
                variantStyles[variant],
            )
        "
    >
        <div class="grid gap-1">
            <div :class="cn('text-sm font-semibold', textStyles[variant])">
                {{ title }}
            </div>
            <div
                v-if="description"
                :class="cn('text-sm opacity-90', textStyles[variant])"
            >
                {{ description }}
            </div>
        </div>
        <button
            @click="emit('close')"
            :class="
                cn(
                    'absolute right-2 top-2 rounded-md p-1 opacity-70 transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring',
                    iconStyles[variant],
                )
            "
        >
            <X class="h-4 w-4" />
        </button>
    </div>
</template>

