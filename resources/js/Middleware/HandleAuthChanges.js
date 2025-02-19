import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { useCartStore } from '@/Stores/useCartStore';

export default function HandleAuthChanges() {
    const cart = useCartStore();
    
    watch(() => usePage().props.auth.user, (newUser, oldUser) => {
        // If user logged out (was logged in, now null)
        if (oldUser && !newUser) {
            cart.resetCart();
        }
        // If user logged in (was null, now logged in)
        else if (!oldUser && newUser) {
            cart.loadCart();
        }
    });
} 