<script setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { CheckCircle2 } from 'lucide-vue-next';

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        default: 'Success',
    },
});
</script>

<template>
    <Alert
        variant="default"
        class="bg-green-50 border-green-200 text-green-900 dark:bg-green-950 dark:border-green-800 dark:text-green-100 [&>svg]:text-green-600 dark:[&>svg]:text-green-400"
    >
        <CheckCircle2 class="size-4" />
        <AlertTitle>{{ title }}</AlertTitle>
        <AlertDescription class="text-green-800 dark:text-green-200">
            {{ message }}
        </AlertDescription>
    </Alert>
</template>

