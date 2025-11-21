<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import UnitController from '@/actions/App/Http/Controllers/UnitController';
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
    unit: {
        type: Object,
        default: null,
    },
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'success']);

const isEditMode = computed(() => !!props.unit);

const form = useForm({
    name: null,
    description: null,
});

watch(
    () => props.unit,
    (unit) => {
        if (unit) {
            form.name = unit.name;
            form.description = unit.description;
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
            UnitController.update.url({
                unit: props.unit.id,
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
        form.post(UnitController.store.url(), {
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
                isEditMode ? 'Edit Unit' : 'Create New Unit'
            }}</DialogTitle>
            <DialogDescription>
                {{
                    isEditMode
                        ? 'Update unit information.'
                        : 'Add a new unit to the system.'
                }}
            </DialogDescription>
        </DialogHeader>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-name' : 'name'">Name</Label>
            <Input
                :id="isEditMode ? 'edit-name' : 'name'"
                v-model="form.name"
                placeholder="Unit name"
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
                placeholder="Unit description (optional)"
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
                            ? 'Update Unit'
                            : 'Create Unit'
                }}
            </Button>
        </DialogFooter>
    </form>
</template>

