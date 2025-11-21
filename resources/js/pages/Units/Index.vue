<script setup>
import UnitController from '@/actions/App/Http/Controllers/UnitController';
import { index as unitsIndex } from '@/routes/units';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import AlertSuccess from '@/components/AlertSuccess.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import UnitForm from '@/components/UnitForm.vue';
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
import { Pencil, Plus, Ruler, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    units: {
        type: Array,
        required: true,
    },
});

const page = usePage();

const breadcrumbs = [
    {
        title: 'Units',
        href: unitsIndex().url,
    },
];

const createDialogOpen = ref(false);
const editDialogOpen = ref(null);
const deleteDialogOpen = ref(null);

const editingUnit = ref(null);

const openEditDialog = (unit) => {
    editingUnit.value = unit;
    editDialogOpen.value = unit.id;
};

const closeEditDialog = () => {
    editingUnit.value = null;
    editDialogOpen.value = null;
};

const openDeleteDialog = (unitId) => {
    deleteDialogOpen.value = unitId;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = null;
};

const handleDelete = (unitId) => {
    router.delete(
        UnitController.destroy.url({
            unit: unitId,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteDialog();
            },
        },
    );
};
</script>

<template>
    <Head title="Units" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <AlertSuccess
                v-if="page.props.flash?.success"
                :message="page.props.flash.success"
            />

            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Units"
                    description="Manage your units"
                />

                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add Unit
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <UnitForm
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
                                    Description
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
                                v-for="unit in units"
                                :key="unit.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <Ruler class="h-4 w-4 text-muted-foreground" />
                                        <span class="font-medium">{{ unit.name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ unit.description || '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ unit.created_at }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <Dialog
                                            :open="editDialogOpen === unit.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openEditDialog(unit)
                                                        : closeEditDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(unit)"
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <UnitForm
                                                    :unit="unit"
                                                    :open="editDialogOpen === unit.id"
                                                    @close="closeEditDialog"
                                                />
                                            </DialogContent>
                                        </Dialog>

                                        <Dialog
                                            :open="deleteDialogOpen === unit.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openDeleteDialog(unit.id)
                                                        : closeDeleteDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openDeleteDialog(unit.id)"
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
                                                        the unit
                                                        <strong>{{ unit.name }}</strong>
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
                                                        @click="handleDelete(unit.id)"
                                                    >
                                                        Delete Unit
                                                    </Button>
                                                </DialogFooter>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="units.length === 0">
                                <td
                                    colspan="4"
                                    class="px-4 py-8 text-center text-muted-foreground"
                                >
                                    No units found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

