<script setup>
import ProductClassController from '@/actions/App/Http/Controllers/ProductClassController';
import { index as productClassesIndex } from '@/routes/product-classes';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import AlertSuccess from '@/components/AlertSuccess.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import ProductClassForm from '@/components/ProductClassForm.vue';
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
import { Package, Pencil, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    productClasses: {
        type: Array,
        required: true,
    },
});

const page = usePage();

const breadcrumbs = [
    {
        title: 'Product Classes',
        href: productClassesIndex().url,
    },
];

const createDialogOpen = ref(false);
const editDialogOpen = ref(null);
const deleteDialogOpen = ref(null);

const editingProductClass = ref(null);

const openEditDialog = (productClass) => {
    editingProductClass.value = productClass;
    editDialogOpen.value = productClass.id;
};

const closeEditDialog = () => {
    editingProductClass.value = null;
    editDialogOpen.value = null;
};

const openDeleteDialog = (productClassId) => {
    deleteDialogOpen.value = productClassId;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = null;
};

const handleDelete = (productClassId) => {
    router.delete(
        ProductClassController.destroy.url({
            product_class: productClassId,
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
    <Head title="Product Classes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <AlertSuccess
                v-if="page.props.flash?.success"
                :message="page.props.flash.success"
            />

            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Product Classes"
                    description="Manage your product classes"
                />

                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add Product Class
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <ProductClassForm
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
                                v-for="productClass in productClasses"
                                :key="productClass.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <Package class="h-4 w-4 text-muted-foreground" />
                                        <span class="font-medium">{{
                                            productClass.name
                                        }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ productClass.description || '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ productClass.created_at }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <Dialog
                                            :open="editDialogOpen === productClass.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openEditDialog(productClass)
                                                        : closeEditDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(productClass)"
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <ProductClassForm
                                                    :product-class="productClass"
                                                    :open="editDialogOpen === productClass.id"
                                                    @close="closeEditDialog"
                                                />
                                            </DialogContent>
                                        </Dialog>

                                        <Dialog
                                            :open="deleteDialogOpen === productClass.id"
                                            @update:open="
                                                (val) =>
                                                    val
                                                        ? openDeleteDialog(productClass.id)
                                                        : closeDeleteDialog()
                                            "
                                        >
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openDeleteDialog(productClass.id)"
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
                                                        the product class
                                                        <strong>{{ productClass.name }}</strong>
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
                                                        @click="handleDelete(productClass.id)"
                                                    >
                                                        Delete Product Class
                                                    </Button>
                                                </DialogFooter>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="productClasses.length === 0">
                                <td
                                    colspan="4"
                                    class="px-4 py-8 text-center text-muted-foreground"
                                >
                                    No product classes found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

