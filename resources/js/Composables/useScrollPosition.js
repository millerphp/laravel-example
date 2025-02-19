import { ref, onMounted, onUnmounted } from 'vue';

export function useScrollPosition(threshold = 50) {
    const isScrolled = ref(false);

    function updateScrollPosition() {
        isScrolled.value = window.scrollY > threshold;
    }

    onMounted(() => {
        window.addEventListener('scroll', updateScrollPosition, { passive: true });
        updateScrollPosition();
    });

    onUnmounted(() => {
        window.removeEventListener('scroll', updateScrollPosition);
    });

    return isScrolled;
} 