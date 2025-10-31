<script>
import usePermission from '@/Composables/usePermission';
import MainLayout from '@/Layouts/MainLayout.vue';
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useHelpers } from '@/Composables/useHelpers.js';
import { Head, Link, router } from '@inertiajs/vue3';
import { debounce, pickBy } from 'lodash';
import moment from 'moment-timezone';
import { reactive, ref, watch } from 'vue';

const { currentRow, formatCurrency, formatDate, formatRangePicker } = useHelpers();

const props = defineProps({
    transactions: {
        type: Object,
        required: true,
    },
    paymentMethods: {
        type: Array,
        required: true,
    },
    customers: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
    },
});

// Query parameters
let params = reactive({
    search: props.filters.search ?? '',
    per_page: props.filters.per_page ?? 10,
    field: props.filters.field ?? 'payment_date',
    direction: props.filters.direction ?? 'desc',
    payment_method_id: props.filters.payment_method_id ?? '',
    customer_id: props.filters.customer_id ?? '',
    date_range: props.filters.date_range ?? '',
});

// Toggle Filters
const showFilters = ref(false);
const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

// Watch the params object for changes and filter the table
watch(
    params,
    debounce(() => {
        router.get(
            route('customers.transactions'),
            {
                ...pickBy(params),
                date_range: params.date_range
                    ? [
                          moment(params.date_range[0]).format('YYYY-MM-DD'),
                          moment(params.date_range[1]).format('YYYY-MM-DD'),
                      ]
                    : '',
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }, 300),
    { deep: true },
);

// Sort the table
let sort = (field) => {
    params.field = field;
    params.direction = params.direction === 'asc' ? 'desc' : 'asc';
};

// Clear table search
let clearSearch = () => {
    params.search = '';
};

// Clear all filters
const clearFilters = () => {
    params.payment_method_id = '';
    params.customer_id = '';
    params.date_range = '';
};
</script>

<template>
    <Head title="Customer Transactions" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('customers.index')" class="text-secondary">Customers</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Transactions</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div v-if="showFilters" class="card border-0 shadow mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="paymentMethod" class="form-label fw-bold">Payment Method</label>
                            <v-select
                                v-model="params.payment_method_id"
                                :options="paymentMethods"
                                :placeholder="'All Payment Methods'"
                                label="method_name"
                                :reduce="(method) => method.id"
                                :clearable="true"
                            ></v-select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="customer" class="form-label fw-bold">Customer</label>
                            <v-select
                                v-model="params.customer_id"
                                :options="customers"
                                :placeholder="'All Customers'"
                                :reduce="(customer) => customer.id"
                                :clearable="true"
                                :filter-by="
                                    (option, label, search) => {
                                        const searchLower = search.toLowerCase();
                                        return (
                                            option.name.toLowerCase().includes(searchLower) ||
                                            (option.mobile && option.mobile.toLowerCase().includes(searchLower))
                                        );
                                    }
                                "
                            >
                                <template #option="option">
                                    <div>
                                        <span>{{ option.name }}</span>
                                        <span v-if="option.mobile" class="text-muted"> ({{ option.mobile }})</span>
                                    </div>
                                </template>
                                <template #selected-option="option">
                                    <div>
                                        <span>{{ option.name }}</span>
                                        <span v-if="option.mobile" class="text-muted"> - {{ option.mobile }}</span>
                                    </div>
                                </template>
                            </v-select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dateRange" class="form-label fw-bold">Payment Date Range</label>
                            <v-datepicker
                                v-model="params.date_range"
                                :placeholder="'Select payment date range'"
                                :teleport="true"
                                :enable-time-picker="false"
                                :format="formatRangePicker"
                                range
                            >
                            </v-datepicker>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button @click="clearFilters" class="btn btn-secondary">
                                <i class="ri-refresh-line me-1"></i> Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <button @click.prevent="toggleFilters" class="btn btn-tertiary">
                                <i v-if="!showFilters" class="ri-filter-line"></i>
                                <i v-else class="ri-filter-off-line"></i>
                            </button>
                        </div>

                        <div class="col-md-4">
                            <div class="navbar-search form-inline" id="navbar-search-main">
                                <div class="input-group input-group-merge search-bar">
                                    <input
                                        v-model="params.search"
                                        type="text"
                                        class="form-control"
                                        id="searchTable"
                                        placeholder="Search ...."
                                        aria-label="searchTable"
                                        aria-describedby="searchTable"
                                    />
                                    <span v-if="!params.search" class="input-group-text" id="searchTable">
                                        <i class="ri-search-line text-secondary"></i>
                                    </span>
                                    <span
                                        @click="clearSearch"
                                        v-else
                                        class="input-group-text"
                                        style="cursor: pointer"
                                        id="removeParameter"
                                    >
                                        <i class="ri-close-line text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0 rounded-start text-center align-middle">No.</th>
                                    <th
                                        @click="sort('payment_date')"
                                        class="border-0 text-center align-middle cursor-pointer d-flex align-items-center justify-content-center"
                                    >
                                        <span class="me-2">Payment Date</span>
                                        <span v-if="params.field === 'payment_date' && params.direction === 'asc'"
                                            ><i class="ri-sort-asc text-secondary"></i
                                        ></span>
                                        <span v-if="params.field === 'payment_date' && params.direction === 'desc'"
                                            ><i class="ri-sort-desc text-secondary"></i
                                        ></span>
                                    </th>
                                    <th class="border-0 text-center align-middle">Sale Ref.</th>
                                    <th class="border-0 text-center align-middle">Customer</th>
                                    <th class="border-0 text-center align-middle">Mobile</th>
                                    <th class="border-0 text-center align-middle">Payment Method</th>
                                    <th class="border-0 text-center align-middle">Amount</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(transactions.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="8">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(transaction, index) in transactions.data" :key="transaction.id">
                                    <td class="text-center align-middle">{{ currentRow(index, transactions) }}</td>
                                    <td class="text-center align-middle">
                                        {{ formatDate(transaction.payment_date) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link
                                            :href="route('sales.show', transaction.sale_id)"
                                            class="text-tertiary fw-bold"
                                        >
                                            {{ transaction.sale?.reference }}
                                        </Link>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ transaction.customer?.name }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ transaction.customer?.mobile }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ transaction.payment_method?.method_name }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="fw-bold">{{ formatCurrency(transaction.amount) }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link
                                            v-if="usePermission('view_sales') && transaction.sale"
                                            :href="
                                                transaction.sale.sale_type === 2
                                                    ? route('event-sales.show', transaction.sale_id)
                                                    : route('sales.show', transaction.sale_id)
                                            "
                                            class="btn btn-info btn-sm"
                                        >
                                            <i class="ri-eye-line"></i>
                                        </Link>
                                        <span class="text-muted" v-else>No action available</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 row">
                        <div class="col-md-2 mb-3 mb-md-0">
                            <div class="d-flex align-items-center showing">
                                <div class="me-2 show-text">Showing</div>
                                <select
                                    v-model="params.per_page"
                                    class="show-select form-select px-3 py-1"
                                    id="perPage"
                                >
                                    <option value="10">10</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 offset-md-4 d-flex justify-content-md-end">
                            <nav>
                                <ul class="pagination pagination-sm">
                                    <template v-for="(link, index) in transactions.links">
                                        <li v-if="link.url === null" class="page-item">
                                            <button
                                                class="page-link text-muted"
                                                tabindex="-1"
                                                v-html="link.label"
                                                disabled
                                            ></button>
                                        </li>

                                        <li v-else class="page-item">
                                            <Link
                                                preserve-scroll
                                                class="page-link"
                                                :href="link.url"
                                                v-html="link.label"
                                                :class="{ active: link.active }"
                                            ></Link>
                                        </li>
                                    </template>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
