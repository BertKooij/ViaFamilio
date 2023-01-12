<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteTreeForm from '@/Pages/Trees/Partials/DeleteTreeForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TreeMemberManager from '@/Pages/Trees/Partials/TreeMemberManager.vue';
import UpdateTreeNameForm from '@/Pages/Trees/Partials/UpdateTreeNameForm.vue';

defineProps({
    tree: Object,
    availableRoles: Array,
    permissions: Object,
});
</script>

<template>
    <AppLayout title="Tree Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tree Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <UpdateTreeNameForm :tree="tree" :permissions="permissions" />

                <TreeMemberManager
                    class="mt-10 sm:mt-0"
                    :tree="tree"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                />

                <template v-if="permissions.canDeleteTree && ! tree.personal_tree">
                    <SectionBorder />

                    <DeleteTreeForm class="mt-10 sm:mt-0" :tree="tree" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
