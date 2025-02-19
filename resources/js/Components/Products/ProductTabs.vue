<script setup>
import { computed } from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    modelValue: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const tabs = [
    { key: 'description', label: 'Description' },
    { key: 'specifications', label: 'Specifications' },
    { key: 'shipping', label: 'Shipping' },
];

const selectedIndex = computed({
    get: () => tabs.findIndex(tab => tab.key === props.modelValue),
    set: (index) => emit('update:modelValue', tabs[index].key)
});
</script>

<template>
    <div class="border border-gray-200 rounded-lg bg-white">
        <TabGroup 
            v-model="selectedIndex"
            as="div"
            class="w-full"
        >
            <TabList class="flex border-b border-gray-200">
                <Tab
                    v-for="tab in tabs"
                    :key="tab.key"
                    v-slot="{ selected }"
                    as="template"
                >
                    <button
                        class="px-6 py-4 text-sm font-medium focus:outline-none"
                        :class="[
                            selected 
                                ? 'text-primary-600 border-b-2 border-primary-600' 
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </Tab>
            </TabList>

            <TabPanels class="p-6">
                <TabPanel class="prose max-w-none">
                    <div v-html="product.description"></div>
                </TabPanel>

                <TabPanel>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div v-for="(value, key) in product.specifications" :key="key">
                            <dt class="text-sm font-medium text-gray-500">{{ key }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ value }}</dd>
                        </div>
                    </dl>
                </TabPanel>

                <TabPanel class="prose max-w-none">
                    <h3>Shipping Information</h3>
                    <p>Standard shipping: 3-5 business days</p>
                    <p>Express shipping: 1-2 business days</p>
                    <p>Free shipping on orders over £100</p>
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>
</template> 