<script setup>
import NavigationMitra from '@/Components/NavigationMitra.vue';
</script>

<template>
    <NavigationMitra>
        <main>
            <slot />
        </main>
        
        <!-- Copyright Footer -->
        <footer class="py-4 mt-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Copyright © 2025 ASIMPENAS.
                    </p>
                </div>
            </div>
        </footer>
    </NavigationMitra>
</template>