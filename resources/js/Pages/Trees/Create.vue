<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTreeForm from '@/Pages/Trees/Partials/CreateTreeForm.vue';
</script>

<template>
    <AppLayout title="Create Tree">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Tree
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <CreateTreeForm />
            </div>
        </div>
    </AppLayout>
</template>
