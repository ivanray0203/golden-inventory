<script setup>
import UserController from '@/actions/App/Http/Controllers/UserController';
import { index as usersIndex } from '@/routes/users';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import AlertSuccess from '@/components/AlertSuccess.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import UserForm from '@/components/UserForm.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Pencil, Plus, Trash2, Users } from 'lucide-vue-next';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
});

const page = usePage();

const breadcrumbs = [
    {
        title: 'Users',
        href: usersIndex().url,
    },
];

const createDialogOpen = ref(false);
const editDialogOpen = ref(null);
const deleteDialogOpen = ref(null);

const editingUser = ref(null);

const openEditDialog = (user) => {
    editingUser.value = user;
    editDialogOpen.value = user.id;
};

const closeEditDialog = () => {
    editingUser.value = null;
    editDialogOpen.value = null;
};

const openDeleteDialog = (userId) => {
    deleteDialogOpen.value = userId;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = null;
};

const handleDelete = (userId) => {
    router.delete(UserController.destroy.url({ user: userId }), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteDialog();
        },
    });
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <AlertSuccess
                v-if="page.props.flash?.success"
                :message="page.props.flash.success"
            />

            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Users"
                    description="Manage your application users and their roles"
                />

                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add User
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <UserForm
                            :roles="roles"
                            :open="createDialogOpen"
                            @close="createDialogOpen = false"
                        />
                    </DialogContent>
                </Dialog>
            </div>

            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th
                                    class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                                >
                                    Role
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                                >
                                    Created At
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-sm font-medium text-muted-foreground"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users"
                                :key="user.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <Users class="h-4 w-4 text-muted-foreground" />
                                        <span class="font-medium">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ user.email }}
                                </td>
                                <td class="px-4 py-3">
                                    <Badge variant="default">
                                        {{
                                            user.role.charAt(0).toUpperCase() +
                                            user.role.slice(1)
                                        }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ user.created_at }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <Dialog
                                            :open="editDialogOpen === user.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openEditDialog(user)
                                                        : closeEditDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(user)"
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <UserForm
                                                    :user="user"
                                                    :roles="roles"
                                                    :open="editDialogOpen === user.id"
                                                    @close="closeEditDialog"
                                                />
                                            </DialogContent>
                                        </Dialog>

                                        <Dialog
                                            :open="deleteDialogOpen === user.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openDeleteDialog(user.id)
                                                        : closeDeleteDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openDeleteDialog(user.id)"
                                                >
                                                    <Trash2 class="h-4 w-4 text-destructive" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <DialogHeader>
                                                    <DialogTitle
                                                        >Are you sure?</DialogTitle
                                                    >
                                                    <DialogDescription>
                                                        This action cannot be undone.
                                                        This will permanently delete
                                                        the user
                                                        <strong>{{ user.name }}</strong>
                                                        and remove all associated data.
                                                    </DialogDescription>
                                                </DialogHeader>

                                                <DialogFooter>
                                                    <DialogClose as-child>
                                                        <Button
                                                            type="button"
                                                            variant="secondary"
                                                        >
                                                            Cancel
                                                        </Button>
                                                    </DialogClose>
                                                    <Button
                                                        type="button"
                                                        variant="destructive"
                                                        @click="handleDelete(user.id)"
                                                    >
                                                        Delete User
                                                    </Button>
                                                </DialogFooter>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td
                                    colspan="5"
                                    class="px-4 py-8 text-center text-muted-foreground"
                                >
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
