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
import { reactive, ref, watch } from 'vue';

const { currentRow, formatDate, formatRangePicker } = useHelpers();

const props = defineProps({
    customers: {
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
    waiver_status: props.filters.waiver_status ?? '',
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
            route('customers.index'),
            {
                ...pickBy(params),
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }, 300),
    { deep: true },
);

// Clear table search
let clearSearch = () => {
    params.search = '';
};

// Clear all filters
const clearFilters = () => {
    params.waiver_status = '';
};

// Delete customer
const deleteCustomer = (customer) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('customers.destroy', customer), {
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Customer Deleted!',
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
</script>

<template>
    <Head title="Customers" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Customers</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div v-if="showFilters" class="card border-0 shadow mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="waiverStatus" class="form-label fw-bold">Waiver Status</label>
                            <select v-model="params.waiver_status" id="waiverStatus" class="form-select">
                                <option value="">All</option>
                                <option value="filled">Waiver Filled</option>
                                <option value="expired">Waiver Expired</option>
                                <option value="not_filled">Waiver Not Filled</option>
                            </select>
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
                                v-if="$page.props.current_branch !== 'all' && usePermission('create_customers')"
                                :href="route('customers.create')"
                                class="btn btn-primary"
                            >
                                Add Customer <i class="ri-add-line"></i>
                            </Link>
                            <button v-else disabled class="btn btn-primary">
                                Add Customer <i class="ri-add-line"></i>
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
                                    <th class="border-0 text-center align-middle">Name</th>
                                    <th class="border-0 text-center align-middle">Mobile</th>
                                    <th class="border-0 text-center align-middle">Email</th>
                                    <!-- <th class="border-0 text-center align-middle">Address</th> -->
                                    <th class="border-0 text-center align-middle">Waiver</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(customers.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="6">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(customer, index) in customers.data" :key="customer.id">
                                    <td class="text-center align-middle">
                                        {{ currentRow(index, customers) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link class="text-tertiary" :href="route('customers.show', customer)">
                                            <span class="me-2">{{ customer.name }}</span>
                                        </Link>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ customer.mobile }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ customer.email }}
                                    </td>
                                    <!-- <td class="text-center align-middle" style="max-width: 200px; text-wrap: wrap">
                                        {{ customer.address ?? 'N/A' }}
                                    </td> -->
                                    <td class="text-center align-middle">
                                        <div
                                            v-if="customer.waiver"
                                            class="text-success tw-font-medium d-flex align-items-center"
                                        >
                                            <template v-if="new Date(customer.waiver.valid_until) >= new Date()">
                                                <i
                                                    class="ri-checkbox-circle-fill text-success me-1"
                                                    style="font-size: 1.2rem"
                                                ></i>
                                                <span>{{ formatDate(customer.waiver?.valid_until) }}</span>
                                            </template>
                                            <template v-else>
                                                <Link
                                                    class="text-danger d-flex align-items-center tw-font-medium"
                                                    :href="
                                                        route('customers.waiver', {
                                                            id: customer.id,
                                                            name: customer.name,
                                                            mobile: customer.mobile,
                                                            email: customer.email,
                                                        })
                                                    "
                                                >
                                                    <i
                                                        class="ri-error-warning-fill tw-text-orange-500 me-1"
                                                        style="font-size: 1.2rem"
                                                    ></i>
                                                    <span class="tw-text-orange-500">
                                                        {{ formatDate(customer.waiver?.valid_until) }} - Expired
                                                    </span>
                                                </Link>
                                            </template>
                                        </div>
                                        <div v-else>
                                            <Link
                                                class="text-danger d-flex align-items-center tw-font-medium"
                                                :href="
                                                    route('customers.waiver', {
                                                        id: customer.id,
                                                        name: customer.name,
                                                        mobile: customer.mobile,
                                                        email: customer.email,
                                                    })
                                                "
                                            >
                                                <i
                                                    class="ri-close-circle-fill text-danger me-1"
                                                    style="font-size: 1.2rem"
                                                ></i>
                                                <span>Fill Waiver</span>
                                            </Link>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link
                                            v-if="usePermission('edit_customers')"
                                            :href="route('customers.edit', customer)"
                                            class="btn btn-info btn-sm me-2"
                                        >
                                            <i class="ri-pencil-fill"></i>
                                        </Link>
                                        <a
                                            v-if="usePermission('delete_customers')"
                                            @click.prevent="deleteCustomer(customer)"
                                            href=""
                                            id="delete"
                                            class="btn btn-danger btn-sm"
                                        >
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </a>
                                        <span
                                            class="text-muted"
                                            v-if="
                                                !usePermission('edit_customers') && !usePermission('delete_customers')
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
                                    <template v-for="(link, index) in customers.links">
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
