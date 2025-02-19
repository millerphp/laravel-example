import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

export const useCartStore = defineStore('cart', () => {
    const items = ref([]);
    const isOpen = ref(false);
    const isLoading = ref(false);

    // Load cart from backend
    const loadCart = async () => {
        try {
            isLoading.value = true;
            const response = await axios.get(route('cart.data'));
            console.log('Cart data from server:', response.data);
            items.value = response.data.items;
            console.log('Items after load:', items.value);
            isLoading.value = false;
        } catch (error) {
            console.error('Failed to load cart:', error);
            isLoading.value = false;
        }
    };

    const addItem = async (product, quantity = 1) => {
        try {
            const response = await axios.post(route('cart.add'), {
                product_id: product.id,
                quantity: quantity + (items.value.find(item => item.product.id === product.id)?.quantity || 0)
            });

            console.log('Add to cart response:', response.data);

            // Update local cart with server response
            const cartItem = response.data.item;
            
            const existingItem = items.value.find(item => item.product.id === product.id);
            if (existingItem) {
                Object.assign(existingItem, {
                    id: cartItem.id,
                    quantity: parseInt(cartItem.quantity),
                    unit_price: parseFloat(cartItem.unit_price).toFixed(2),
                    discounted_price: parseFloat(cartItem.discounted_price).toFixed(2),
                    total: parseFloat(cartItem.total).toFixed(2),
                    product: {
                        id: product.id,
                        title: product.title,
                        slug: product.slug,
                        image: product.image
                    },
                    applied_discounts: cartItem.discount_data.applied_discounts
                });
            } else {
                items.value.push({
                    id: cartItem.id,
                    product: {
                        id: product.id,
                        title: product.title,
                        slug: product.slug,
                        image: product.image
                    },
                    quantity: parseInt(cartItem.quantity),
                    unit_price: parseFloat(cartItem.unit_price).toFixed(2),
                    discounted_price: parseFloat(cartItem.discounted_price).toFixed(2),
                    total: parseFloat(cartItem.total).toFixed(2),
                    applied_discounts: cartItem.discount_data.applied_discounts
                });
            }
            
            isOpen.value = true;
        } catch (error) {
            console.error('Failed to add item to cart:', error);
        }
    };

    const updateQuantity = async (itemId, quantity) => {
        try {
            const response = await axios.patch(route('cart.update', itemId), { quantity });
            const updatedItem = response.data.item;
            
            const item = items.value.find(i => i.id === itemId);
            if (item) {
                Object.assign(item, {
                    quantity: parseInt(updatedItem.quantity),
                    unit_price: parseFloat(updatedItem.unit_price).toFixed(2),
                    discounted_price: parseFloat(updatedItem.discounted_price).toFixed(2),
                    total: parseFloat(updatedItem.total).toFixed(2),
                    applied_discounts: updatedItem.discount_data.applied_discounts
                });
            }
            // Recalculate cart totals
            await loadCart();
        } catch (error) {
            console.error('Failed to update cart item:', error);
        }
    };

    const removeItem = async (itemId) => {
        try {
            await axios.delete(route('cart.destroy', itemId));
            const index = items.value.findIndex(item => item.id === itemId);
            if (index > -1) {
                items.value.splice(index, 1);
            }
        } catch (error) {
            console.error('Failed to remove item from cart:', error);
        }
    };

    // Computed properties for cart totals
    const subtotal = computed(() => {
        if (!items.value) return 0;
        return items.value.reduce((sum, item) => {
            const total = parseFloat(item.total);
            return sum + (isNaN(total) ? 0 : total);
        }, 0);
    });

    const cartDiscounts = computed(() => {
        if (subtotal.value >= 500) {
            return [{
                name: 'Big Spender Discount',
                description: 'Special discount on orders over £500',
                percentage: 15
            }];
        }
        return [];
    });

    const totalSavings = computed(() => {
        const itemSavings = items.value.reduce((sum, item) => {
            const originalTotal = parseFloat(item.unit_price) * parseInt(item.quantity || 1);
            const discountedTotal = parseFloat(item.total);
            return sum + (originalTotal - (isNaN(discountedTotal) ? 0 : discountedTotal));
        }, 0);
        
        const cartDiscount = cartDiscounts.value.length 
            ? subtotal.value * 0.15 
            : 0;

        return parseFloat((itemSavings + cartDiscount).toFixed(2));
    });

    const finalTotal = computed(() => {
        const total = subtotal.value;
        const finalAmount = cartDiscounts.value.length ? total * 0.85 : total;
        return parseFloat(finalAmount.toFixed(2));
    });

    const resetCart = () => {
        items.value = [];
        isOpen.value = false;
    };

    return {
        items,
        isOpen,
        isLoading,
        loadCart,
        addItem,
        updateQuantity,
        removeItem,
        subtotal,
        cartDiscounts,
        totalSavings,
        finalTotal,
        openDropdown: () => isOpen.value = true,
        closeDropdown: () => isOpen.value = false,
        resetCart,
    };
}, {
    persist: true
}); 