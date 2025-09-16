<script setup>
import Footer from '@/Components/Footer.vue';
</script>

<template>
    <div class="min-h-screen bg-white">
        <!-- Main Content Full Width -->
        <main>
            <slot />
        </main>
        
        <!-- Footer -->
        <Footer />
    </div>
</template>