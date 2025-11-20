<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import BranchController from '@/actions/App/Http/Controllers/BranchController';
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
    branch: {
        type: Object,
        default: null,
    },
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'success']);

const isEditMode = computed(() => !!props.branch);

const form = useForm({
    name: null,
    address: null,
});

watch(
    () => props.branch,
    (branch) => {
        if (branch) {
            form.name = branch.name;
            form.address = branch.address;
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
        form.patch(BranchController.update.url({ branch: props.branch.id }), {
            preserveScroll: true,
            onSuccess: () => {
                emit('success');
                emit('close');
            },
        });
    } else {
        form.post(BranchController.store.url(), {
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
            <DialogTitle>{{ isEditMode ? 'Edit Branch' : 'Create New Branch' }}</DialogTitle>
            <DialogDescription>
                {{
                    isEditMode
                        ? 'Update branch information.'
                        : 'Add a new branch to the system.'
                }}
            </DialogDescription>
        </DialogHeader>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-name' : 'name'">Name</Label>
            <Input
                :id="isEditMode ? 'edit-name' : 'name'"
                v-model="form.name"
                placeholder="Branch name"
                required
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-address' : 'address'">Address</Label>
            <Input
                :id="isEditMode ? 'edit-address' : 'address'"
                v-model="form.address"
                placeholder="Branch address"
                required
            />
            <InputError :message="form.errors.address" />
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
                            ? 'Update Branch'
                            : 'Create Branch'
                }}
            </Button>
        </DialogFooter>
    </form>
</template>

