<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';</script>

<template>
    <Link :href="route('home')">
        <application-logo class="max-w-100 h-auto p-6" />
    </Link>
</template>
