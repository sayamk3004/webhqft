<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
        validator: (value) => {
            return [
                'current_page',
                'last_page',
                'per_page',
                'total'
            ].every(key => key in value);
        }
    },
    only: {
        type: Array,
        default: () => []
    },
    preserveScroll: {
        type: Boolean,
        default: true
    }
});

const currentPage = computed(() => props.pagination.current_page);
const lastPage = computed(() => props.pagination.last_page);
const from = computed(() => (currentPage.value - 1) * props.pagination.per_page + 1);
const to = computed(() => Math.min(currentPage.value * props.pagination.per_page, props.pagination.total));

const pages = computed(() => {
    const range = [];
    const start = Math.max(1, currentPage.value - 2);
    const end = Math.min(lastPage.value, currentPage.value + 2);

    for (let i = start; i <= end; i++) {
        range.push(i);
    }

    return range;
});

const showDots = (position) => {
    if (position === 'left') {
        return currentPage.value > 3 && lastPage.value > 5;
    }
    if (position === 'right') {
        return currentPage.value < lastPage.value - 2 && lastPage.value > 5;
    }
    return false;
};

const getQuery = () => {
    const { ziggy } = usePage().props;
    const query = { ...ziggy.query };

    if (props.only.length > 0) {
        Object.keys(query).forEach(key => {
            if (!props.only.includes(key)) {
                delete query[key];
            }
        });
    }

    return query;
};

const buildUrl = (page) => {
    return route(route().current(), { ...getQuery(), page });
};
</script>

<template>
    <div class="flex items-center justify-between px-4 sm:px-0">
        <div class="flex flex-1 justify-between sm:hidden">
      
            <Link
                :href="buildUrl(currentPage - 1)"
                :class="{ 'opacity-50 pointer-events-none': currentPage === 1 }"
                class="relative inline-flex items-center rounded-md border border-gray-300 border border-gray-800 bg-default text-white shadow-sm px-4 py-2 text-sm font-medium text-gray-700 "
                :preserve-scroll="preserveScroll"
            >
                Previous
            </Link>
            <Link
                :href="buildUrl(currentPage + 1)"
                :class="{ 'opacity-50 pointer-events-none': currentPage === lastPage }"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 border border-gray-800 bg-default text-white shadow-sm px-4 py-2 text-sm font-medium text-gray-700 "
                :preserve-scroll="preserveScroll"
            >
                Next
            </Link>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ from }}</span> to <span class="font-medium">{{ to }}</span> of <span class="font-medium">{{ pagination.total }}</span> results
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <Link
                        :href="buildUrl(currentPage - 1)"
                        :class="{ 'opacity-50 pointer-events-none': currentPage === 1 }"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300  focus:z-20 focus:outline-offset-0"
                        :preserve-scroll="preserveScroll"
                    >
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.1l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <Link
                        v-if="showDots('left')"
                        :href="buildUrl(1)"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-white-300 ring-1 ring-inset ring-gray-300  focus:z-20 focus:outline-offset-0"
                        :class="{ 'bg-indigo-600 text-white hover:bg-indigo-500': currentPage === 1 }"
                        :preserve-scroll="preserveScroll"
                    >
                        1
                    </Link>
                    <span v-if="showDots('left')" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">
                        ...
                    </span>

                    <Link
                        v-for="page in pages"
                        :key="page"
                        :href="buildUrl(page)"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-white-300 ring-1 ring-inset ring-gray-300  focus:z-20 focus:outline-offset-0"
                        :class="{ 'bg-indigo-600  hover:bg-indigo-500': page == currentPage }"
                        :preserve-scroll="preserveScroll"
                    >
                        {{ page }}
                    </Link>

                    <span v-if="showDots('right')" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">
                        ...
                    </span>
                    <Link
                        v-if="showDots('right')"
                        :href="buildUrl(lastPage)"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-white-300 ring-1 ring-inset ring-gray-300  focus:z-20 focus:outline-offset-0"
                        :class="{ 'bg-indigo-600 text-white hover:bg-indigo-500': currentPage === lastPage }"
                        :preserve-scroll="preserveScroll"
                    >
                        {{ lastPage }}
                    </Link>

                    <Link
                        :href="buildUrl(currentPage + 1)"
                        :class="{ 'opacity-50 pointer-events-none': currentPage === lastPage }"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300  focus:z-20 focus:outline-offset-0"
                        :preserve-scroll="preserveScroll"
                    >
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.1l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                </nav>
            </div>
        </div>
    </div>
</template>