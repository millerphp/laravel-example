import { ref, computed } from 'vue';

export function useParallax(intensity = 0.05) {
    const mouseX = ref(0);
    const mouseY = ref(0);

    const handleMouseMove = (event) => {
        mouseX.value = event.clientX;
        mouseY.value = event.clientY;
    };

    const parallaxStyle = computed(() => {
        const x = (window.innerWidth / 2 - mouseX.value) * intensity;
        const y = (window.innerHeight / 2 - mouseY.value) * intensity;
        
        return {
            transform: `translate(${x}px, ${y}px)`,
        };
    });

    return {
        handleMouseMove,
        parallaxStyle,
    };
} 