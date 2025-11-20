<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import ProductClassController from '@/actions/App/Http/Controllers/ProductClassController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    DialogClose,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps({
    productClass: {
        type: Object,
        default: null,
    },
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'success']);

const isEditMode = computed(() => !!props.productClass);

const form = useForm({
    name: null,
    description: null,
});

watch(
    () => props.productClass,
    (productClass) => {
        if (productClass) {
            form.name = productClass.name;
            form.description = productClass.description;
            form.clearErrors();
        } else {
            form.reset();
            form.clearErrors();
        }
    },
    { immediate: true },
);

watch(
    () => props.open,
    (open) => {
        if (!open) {
            form.reset();
            form.clearErrors();
        }
    },
);

const handleSubmit = () => {
    if (isEditMode.value) {
        form.patch(
            ProductClassController.update.url({
                product_class: props.productClass.id,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    emit('success');
                    emit('close');
                },
            },
        );
    } else {
        form.post(ProductClassController.store.url(), {
            preserveScroll: true,
            onSuccess: () => {
                emit('success');
                emit('close');
            },
        });
    }
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <DialogHeader>
            <DialogTitle>{{
                isEditMode ? 'Edit Product Class' : 'Create New Product Class'
            }}</DialogTitle>
            <DialogDescription>
                {{
                    isEditMode
                        ? 'Update product class information.'
                        : 'Add a new product class to the system.'
                }}
            </DialogDescription>
        </DialogHeader>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-name' : 'name'">Name</Label>
            <Input
                :id="isEditMode ? 'edit-name' : 'name'"
                v-model="form.name"
                placeholder="Product class name"
                required
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-description' : 'description'"
                >Description</Label
            >
            <Input
                :id="isEditMode ? 'edit-description' : 'description'"
                v-model="form.description"
                placeholder="Product class description (optional)"
            />
            <InputError :message="form.errors.description" />
        </div>

        <DialogFooter>
            <DialogClose as-child>
                <Button
                    type="button"
                    variant="secondary"
                    :disabled="form.processing"
                >
                    Cancel
                </Button>
            </DialogClose>
            <Button type="submit" :disabled="form.processing">
                {{
                    form.processing
                        ? isEditMode
                            ? 'Updating...'
                            : 'Creating...'
                        : isEditMode
                            ? 'Update Product Class'
                            : 'Create Product Class'
                }}
            </Button>
        </DialogFooter>
    </form>
</template>

