<script setup>
import BranchController from '@/actions/App/Http/Controllers/BranchController';
import { index as branchesIndex } from '@/routes/branches';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import AlertSuccess from '@/components/AlertSuccess.vue';
import BranchForm from '@/components/BranchForm.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Building2, Pencil, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    branches: {
        type: Array,
        required: true,
    },
});

const page = usePage();

const breadcrumbs = [
    {
        title: 'Branches',
        href: branchesIndex().url,
    },
];

const createDialogOpen = ref(false);
const editDialogOpen = ref(null);
const deleteDialogOpen = ref(null);

const editingBranch = ref(null);

const openEditDialog = (branch) => {
    editingBranch.value = branch;
    editDialogOpen.value = branch.id;
};

const closeEditDialog = () => {
    editingBranch.value = null;
    editDialogOpen.value = null;
};

const openDeleteDialog = (branchId) => {
    deleteDialogOpen.value = branchId;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = null;
};

const handleDelete = (branchId) => {
    router.delete(BranchController.destroy.url({ branch: branchId }), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteDialog();
        },
    });
};
</script>

<template>
    <Head title="Branches" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <AlertSuccess
                v-if="page.props.flash?.success"
                :message="page.props.flash.success"
            />

            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Branches"
                    description="Manage your company branches"
                />

                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add Branch
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <BranchForm
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
                                    Address
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
                                v-for="branch in branches"
                                :key="branch.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <Building2 class="h-4 w-4 text-muted-foreground" />
                                        <span class="font-medium">{{ branch.name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ branch.address }}
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ branch.created_at }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <Dialog
                                            :open="editDialogOpen === branch.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openEditDialog(branch)
                                                        : closeEditDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(branch)"
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <BranchForm
                                                    :branch="branch"
                                                    :open="editDialogOpen === branch.id"
                                                    @close="closeEditDialog"
                                                />
                                            </DialogContent>
                                        </Dialog>

                                        <Dialog
                                            :open="deleteDialogOpen === branch.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openDeleteDialog(branch.id)
                                                        : closeDeleteDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openDeleteDialog(branch.id)"
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
                                                        the branch
                                                        <strong>{{ branch.name }}</strong>
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
                                                        @click="handleDelete(branch.id)"
                                                    >
                                                        Delete Branch
                                                    </Button>
                                                </DialogFooter>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="branches.length === 0">
                                <td
                                    colspan="4"
                                    class="px-4 py-8 text-center text-muted-foreground"
                                >
                                    No branches found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

