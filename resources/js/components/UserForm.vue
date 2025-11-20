<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import UserController from '@/actions/App/Http/Controllers/UserController';
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
    user: {
        type: Object,
        default: null,
    },
    roles: {
        type: Array,
        required: true,
    },
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'success']);

const isEditMode = computed(() => !!props.user);

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    role: null,
});

watch(
    () => props.user,
    (user) => {
        if (user) {
            form.name = user.name;
            form.email = user.email;
            form.password = null;
            form.password_confirmation = null;
            form.role = user.role;
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
        form.patch(UserController.update.url({ user: props.user.id }), {
            preserveScroll: true,
            onSuccess: () => {
                emit('success');
                emit('close');
            },
        });
    } else {
        form.post(UserController.store.url(), {
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
            <DialogTitle>{{ isEditMode ? 'Edit User' : 'Create New User' }}</DialogTitle>
            <DialogDescription>
                {{
                    isEditMode
                        ? 'Update user information and role.'
                        : 'Add a new user to the system. Assign a role to control their permissions.'
                }}
            </DialogDescription>
        </DialogHeader>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-name' : 'name'">Name</Label>
            <Input
                :id="isEditMode ? 'edit-name' : 'name'"
                v-model="form.name"
                placeholder="Full name"
                required
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-email' : 'email'">Email</Label>
            <Input
                :id="isEditMode ? 'edit-email' : 'email'"
                type="email"
                v-model="form.email"
                placeholder="email@example.com"
                required
            />
            <InputError :message="form.errors.email" />
        </div>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-password' : 'password'">Password</Label>
            <Input
                :id="isEditMode ? 'edit-password' : 'password'"
                type="password"
                v-model="form.password"
                :placeholder="
                    isEditMode
                        ? 'Leave blank to keep current password'
                        : 'Password'
                "
                :required="!isEditMode"
            />
            <InputError :message="form.errors.password" />
        </div>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-password_confirmation' : 'password_confirmation'"
                >Confirm Password</Label
            >
            <Input
                :id="isEditMode ? 'edit-password_confirmation' : 'password_confirmation'"
                type="password"
                v-model="form.password_confirmation"
                placeholder="Confirm password"
                :required="!isEditMode"
            />
        </div>

        <div class="grid gap-2">
            <Label :for="isEditMode ? 'edit-role' : 'role'">Role</Label>
            <select
                :id="isEditMode ? 'edit-role' : 'role'"
                v-model="form.role"
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                required
            >
                <option :value="null" disabled>Select a role</option>
                <option
                    v-for="role in roles"
                    :key="role"
                    :value="role"
                >
                    {{ role.charAt(0).toUpperCase() + role.slice(1) }}
                </option>
            </select>
            <InputError :message="form.errors.role" />
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
                            ? 'Update User'
                            : 'Create User'
                }}
            </Button>
        </DialogFooter>
    </form>
</template>

