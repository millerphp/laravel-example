<script setup>
import { ref, computed } from 'vue';
import { TransitionRoot } from '@headlessui/vue';

const props = defineProps({
    images: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
});

const selectedImage = ref(0);
const isZoomed = ref(false);
const imageRef = ref(null);
const zoomPosition = ref({ x: 0, y: 0 });

const mainImage = computed(() => props.images[selectedImage.value]);

function handleMouseMove(event) {
    if (!imageRef.value) return;
    
    const { left, top, width, height } = imageRef.value.getBoundingClientRect();
    const x = ((event.clientX - left) / width) * 100;
    const y = ((event.clientY - top) / height) * 100;
    
    zoomPosition.value = { x, y };
}
</script>

<template>
    <div class="space-y-4">
        <!-- Main Image -->
        <div 
            ref="imageRef"
            class="relative aspect-square overflow-hidden rounded-lg bg-gray-100"
            @mouseenter="isZoomed = true"
            @mouseleave="isZoomed = false"
            @mousemove="handleMouseMove"
        >
            <img 
                :src="mainImage"
                :alt="title"
                class="h-full w-full object-cover"
                :class="{ 'cursor-zoom-in': !isZoomed, 'cursor-zoom-out': isZoomed }"
            />
            
            <!-- Zoomed Image -->
            <TransitionRoot
                :show="isZoomed"
                enter="transition-opacity duration-200"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="transition-opacity duration-150"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div 
                    class="absolute inset-0 bg-white pointer-events-none"
                    :style="{
                        backgroundImage: `url(${mainImage})`,
                        backgroundPosition: `${zoomPosition.x}% ${zoomPosition.y}%`,
                        backgroundSize: '200%',
                    }"
                />
            </TransitionRoot>
        </div>

        <!-- Thumbnail Grid -->
        <div class="grid grid-cols-4 gap-4">
            <button
                v-for="(image, index) in images"
                :key="index"
                class="aspect-square rounded-lg overflow-hidden bg-gray-100 relative"
                :class="{ 'ring-2 ring-primary-500': selectedImage === index }"
                @click="selectedImage = index"
            >
                <img 
                    :src="image"
                    :alt="`${title} - Image ${index + 1}`"
                    class="h-full w-full object-cover"
                />
            </button>
        </div>
    </div>
</template> 