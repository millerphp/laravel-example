import { ref, onMounted, onUnmounted } from 'vue';

export function useIntersectionObserver(options = {}) {
    const isIntersecting = ref(false);
    const observerRef = ref(null);
    let observer = null;

    onMounted(() => {
        observer = new IntersectionObserver(([entry]) => {
            isIntersecting.value = entry.isIntersecting;
        }, options);

        if (observerRef.value) {
            observer.observe(observerRef.value);
        }
    });

    onUnmounted(() => {
        if (observer) {
            observer.disconnect();
        }
    });

    return {
        isIntersecting,
        observerRef,
    };
} 