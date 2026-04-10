<template>
    <div class="min-h-screen flex flex-col" style="background-color: #F2EDE4; font-family: 'Be Vietnam Pro', sans-serif;">
        <TheHeader />
        <main class="flex-1">
            <slot />
        </main>
        <TheFooter />
    </div>
</template>

<script setup>
import TheHeader from '@/Components/TheHeader.vue';
import TheFooter from '@/Components/TheFooter.vue';
</script>
