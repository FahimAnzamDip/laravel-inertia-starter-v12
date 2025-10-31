<script>
import usePermission from '@/Composables/usePermission';
import MainLayout from '@/Layouts/MainLayout.vue';
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useAlert } from '@/Composables/useAlert.js';
import { useHelpers } from '@/Composables/useHelpers.js';
import { Head, Link, router } from '@inertiajs/vue3';
import { debounce, pickBy } from 'lodash';
import moment from 'moment-timezone';
import { reactive, ref, watch } from 'vue';

const { currentRow, formatCurrency, formatDate, formatRangePicker } = useHelpers();

const props = defineProps({
    purchases: {
        type: Object,
    },
    filters: {
        type: Object,
    },
});

// Query parameters
let params = reactive({
    search: props.filters.search ?? '',
    per_page: props.filters.per_page ?? 10,
    field: props.filters.field ?? 'purchase_date',
    direction: props.filters.direction ?? 'desc',
    status: props.filters.status ?? '',
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
            route('purchases.index'),
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
    params.status = '';
    params.date_range = '';
};

// Delete purchase
const deletePurchase = (purchase) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('purchases.destroy', purchase), {
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Purchase Deleted!',
                    });
                },
                onError: () => {
                    Toast.fire({
                        icon: 'danger',
                        title: 'Something Went Wrong!',
                    });
                },
            });
        }
    });
};

// Update purchase status
const updateStatus = (purchase, newStatus) => {
    router.patch(
        route('purchases.update.status', purchase),
        {
            status: newStatus,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                let { Toast } = useAlert();

                Toast.fire({
                    icon: 'success',
                    title: 'Purchase Status Updated!',
                });
            },
        },
    );
};
</script>

<template>
    <Head title="Purchases" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Purchases</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div v-if="showFilters" class="card border-0 shadow mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="statusFilter" class="form-label fw-bold">Status</label>
                            <select v-model="params.status" id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dateRange" class="form-label fw-bold">Date Range</label>
                            <v-datepicker
                                v-model="params.date_range"
                                :placeholder="'Select date range'"
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
                            <Link
                                v-if="$page.props.current_branch !== 'all' && usePermission('create_purchases')"
                                :href="route('purchases.create')"
                                class="btn btn-primary"
                            >
                                Add Purchase <i class="ri-add-line"></i>
                            </Link>
                            <button v-else disabled class="btn btn-primary">
                                Add Purchase <i class="ri-add-line"></i>
                            </button>
                            <button @click.prevent="toggleFilters" class="btn btn-tertiary ms-2">
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
                                        @click="sort('purchase_date')"
                                        class="border-0 text-center align-middle cursor-pointer d-flex align-items-center justify-content-center"
                                    >
                                        <span class="me-2">Purchase Date</span>
                                        <span v-if="params.field === 'purchase_date' && params.direction === 'asc'"
                                            ><i class="ri-sort-asc text-secondary"></i
                                        ></span>
                                        <span v-if="params.field === 'purchase_date' && params.direction === 'desc'"
                                            ><i class="ri-sort-desc text-secondary"></i
                                        ></span>
                                    </th>
                                    <th class="border-0 text-center align-middle">Ref.</th>
                                    <th class="border-0 text-center align-middle">Supplier</th>
                                    <th class="border-0 text-center align-middle">Mobile</th>
                                    <th class="border-0 text-center align-middle">Status</th>
                                    <th class="border-0 text-center align-middle">Total</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(purchases.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="8">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(purchase, index) in purchases.data" :key="purchase.id">
                                    <td class="text-center align-middle">
                                        {{ currentRow(index, purchases) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ formatDate(purchase.purchase_date) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link :href="route('purchases.show', purchase)" class="text-tertiary fw-bold">
                                            {{ purchase.reference }}
                                        </Link>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ purchase.supplier?.name }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ purchase.supplier?.mobile ?? 'N/A' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <select
                                            v-if="usePermission('edit_purchases')"
                                            :value="purchase.status"
                                            @change="updateStatus(purchase, $event.target.value)"
                                            :class="[
                                                'tw-text-sm tw-text-center tw-font-medium tw-px-2 tw-py-1 tw-rounded-full tw-border-0 tw-cursor-pointer tw-appearance-none tw-bg-transparent hover:tw-opacity-80 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-1',
                                                purchase.status === 'Pending'
                                                    ? 'tw-text-yellow-700 tw-bg-yellow-200 focus:tw-ring-yellow-500'
                                                    : '',
                                                purchase.status === 'Completed'
                                                    ? 'tw-text-green-700 tw-bg-green-200 focus:tw-ring-green-500'
                                                    : '',
                                            ]"
                                            style="width: auto; display: inline-block; background-image: none"
                                        >
                                            <option value="Pending" class="tw-bg-white">Pending</option>
                                            <option value="Completed" class="tw-bg-white">Completed</option>
                                        </select>
                                        <span
                                            v-else
                                            :class="{
                                                'badge bg-warning': purchase.status === 'Pending',
                                                'badge bg-success': purchase.status === 'Completed',
                                            }"
                                        >
                                            {{ purchase.status }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ formatCurrency(purchase.total) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link
                                            v-if="usePermission('edit_purchases')"
                                            :href="route('purchases.edit', purchase)"
                                            class="btn btn-info btn-sm me-2"
                                        >
                                            <i class="ri-pencil-fill"></i>
                                        </Link>
                                        <a
                                            v-if="usePermission('delete_purchases')"
                                            @click.prevent="deletePurchase(purchase)"
                                            href=""
                                            id="delete"
                                            class="btn btn-danger btn-sm"
                                        >
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </a>
                                        <span
                                            class="text-muted"
                                            v-if="
                                                !usePermission('edit_purchases') && !usePermission('delete_purchases')
                                            "
                                        >
                                            No action available
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 row">
                        <div class="col-md-2 mb-3 mb-md-0">
                            <div class="d-flex align-items-center showing">
                                <div class="show-text me-2">Showing</div>
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
                                    <template v-for="(link, index) in purchases.links">
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

<style scoped>
/* Custom styles if needed */
</style>
