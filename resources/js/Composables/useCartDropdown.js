import { onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/useCartStore';

export function useCartDropdown() {
    const cart = useCartStore();

    // Handle clicks outside
    function handleClickOutside(event) {
        const cartDropdown = document.querySelector('#cart-dropdown');
        const cartButton = document.querySelector('#cart-button');
        
        if (cartDropdown && !cartDropdown.contains(event.target) && !cartButton.contains(event.target)) {
            cart.closeDropdown();
        }
    }

    onMounted(() => {
        document.addEventListener('click', handleClickOutside);
        document.addEventListener('inertia:before', () => {
            cart.closeDropdown();
        });
    });

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
        document.removeEventListener('inertia:before', () => {
            cart.closeDropdown();
        });
    });

    return {
        cart
    };
} 