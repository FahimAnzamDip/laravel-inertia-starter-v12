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
import { computed, reactive, watch } from 'vue';

const { currentRow, formatDate } = useHelpers();

const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
    type: {
        type: String,
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
    type: props.type,
});

// Watch the params object for changes and filter the table
watch(
    params,
    debounce(() => {
        router.get(route('trash.index'), pickBy(params), {
            preserveScroll: true,
            preserveState: true,
        });
    }, 300),
    { deep: true },
);

// Clear table search
let clearSearch = () => {
    params.search = '';
};

// Change type/tab
const changeType = (newType) => {
    params.type = newType;
    params.search = '';
};

// Restore item
const restoreItem = (item) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Restore this item?',
        text: 'This item will be restored and visible again.',
        icon: 'question',
        confirmButtonText: 'Yes, restore it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route('trash.restore'),
                {
                    type: params.type,
                    id: item.id,
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Toast.fire({
                            icon: 'success',
                            title: 'Item Restored!',
                        });
                    },
                    onError: () => {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something Went Wrong!',
                        });
                    },
                },
            );
        }
    });
};

// Permanently delete item
const permanentlyDeleteItem = (item) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone! The item will be permanently deleted.',
        icon: 'warning',
        confirmButtonText: 'Yes, delete permanently!',
        confirmButtonColor: '#dc3545',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('trash.force-delete'), {
                data: {
                    type: params.type,
                    id: item.id,
                },
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Item Permanently Deleted!',
                    });
                },
                onError: () => {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something Went Wrong!',
                    });
                },
            });
        }
    });
};

// Tab configuration
const tabs = [
    { key: 'customers', label: 'Customers', icon: 'ri-user-line' },
    { key: 'suppliers', label: 'Suppliers', icon: 'ri-building-line' },
    { key: 'products', label: 'Products', icon: 'ri-price-tag-3-line' },
    { key: 'product_categories', label: 'Product Categories', icon: 'ri-folder-line' },
    { key: 'quotations', label: 'Quotations', icon: 'ri-file-list-3-line' },
    { key: 'sales', label: 'Sales', icon: 'ri-shopping-cart-line' },
    { key: 'purchases', label: 'Purchases', icon: 'ri-archive-stack-line' },
    { key: 'sale_returns', label: 'Sale Returns', icon: 'ri-arrow-go-back-line' },
    { key: 'sale_payments', label: 'Sale Payments', icon: 'ri-money-rupee-circle-line' },
    { key: 'sale_return_payments', label: 'Sale Return Payments', icon: 'ri-money-rupee-circle-line' },
    { key: 'customer_waivers', label: 'Customer Waivers', icon: 'ri-file-text-line' },
];

// Get display name for type
const getTypeName = computed(() => {
    const tab = tabs.find((t) => t.key === params.type);
    return tab ? tab.label : 'Items';
});
</script>

<template>
    <Head title="Trash" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Recycle Bin</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <!-- Tabs Navigation -->
            <div class="card border-0 shadow mb-3">
                <div class="card-body">
                    <div class="nav nav-pills flex-wrap gap-2" role="tablist">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="changeType(tab.key)"
                            class="nav-link d-flex align-items-center"
                            :class="{ active: params.type === tab.key }"
                            type="button"
                        >
                            <i :class="tab.icon" class="me-2"></i>
                            {{ tab.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="ri-information-line me-2 fs-4"></i>
                                <div>
                                    <strong>Recycle Bin:</strong> Items can be restored or permanently deleted.
                                    Permanently deleted items cannot be recovered.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-8 mb-3 mb-md-0"></div>

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

                    <!-- Table Content -->
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0 rounded-start text-center align-middle">No.</th>

                                    <!-- Customers -->
                                    <template v-if="params.type === 'customers'">
                                        <th class="border-0 text-center align-middle">Name</th>
                                        <th class="border-0 text-center align-middle">Mobile</th>
                                        <th class="border-0 text-center align-middle">Email</th>
                                    </template>

                                    <!-- Suppliers -->
                                    <template v-else-if="params.type === 'suppliers'">
                                        <th class="border-0 text-center align-middle">Name</th>
                                        <th class="border-0 text-center align-middle">Mobile</th>
                                        <th class="border-0 text-center align-middle">Email</th>
                                        <th class="border-0 text-center align-middle">Company</th>
                                    </template>

                                    <!-- Products -->
                                    <template v-else-if="params.type === 'products'">
                                        <th class="border-0 text-center align-middle">Name</th>
                                        <th class="border-0 text-center align-middle">SKU</th>
                                        <th class="border-0 text-center align-middle">Category</th>
                                        <th class="border-0 text-center align-middle">Price</th>
                                    </template>

                                    <!-- Product Categories -->
                                    <template v-else-if="params.type === 'product_categories'">
                                        <th class="border-0 text-center align-middle">Name</th>
                                        <th class="border-0 text-center align-middle">Status</th>
                                    </template>

                                    <!-- Quotations -->
                                    <template v-else-if="params.type === 'quotations'">
                                        <th class="border-0 text-center align-middle">Reference</th>
                                        <th class="border-0 text-center align-middle">Customer</th>
                                        <th class="border-0 text-center align-middle">Total</th>
                                    </template>

                                    <!-- Sales -->
                                    <template v-else-if="params.type === 'sales'">
                                        <th class="border-0 text-center align-middle">Reference</th>
                                        <th class="border-0 text-center align-middle">Customer</th>
                                        <th class="border-0 text-center align-middle">Total</th>
                                    </template>

                                    <!-- Purchases -->
                                    <template v-else-if="params.type === 'purchases'">
                                        <th class="border-0 text-center align-middle">Reference</th>
                                        <th class="border-0 text-center align-middle">Supplier</th>
                                        <th class="border-0 text-center align-middle">Total</th>
                                    </template>

                                    <!-- Sale Returns -->
                                    <template v-else-if="params.type === 'sale_returns'">
                                        <th class="border-0 text-center align-middle">Reference</th>
                                        <th class="border-0 text-center align-middle">Customer</th>
                                        <th class="border-0 text-center align-middle">Total</th>
                                    </template>

                                    <!-- Sale Payments -->
                                    <template v-else-if="params.type === 'sale_payments'">
                                        <th class="border-0 text-center align-middle">Customer</th>
                                        <th class="border-0 text-center align-middle">Amount</th>
                                        <th class="border-0 text-center align-middle">Payment Method</th>
                                        <th class="border-0 text-center align-middle">Date</th>
                                    </template>

                                    <!-- Sale Return Payments -->
                                    <template v-else-if="params.type === 'sale_return_payments'">
                                        <th class="border-0 text-center align-middle">Customer</th>
                                        <th class="border-0 text-center align-middle">Amount</th>
                                        <th class="border-0 text-center align-middle">Payment Method</th>
                                        <th class="border-0 text-center align-middle">Date</th>
                                    </template>

                                    <!-- Customer Waivers -->
                                    <template v-else-if="params.type === 'customer_waivers'">
                                        <th class="border-0 text-center align-middle">Customer ID</th>
                                        <th class="border-0 text-center align-middle">Valid Until</th>
                                    </template>

                                    <th class="border-0 text-center align-middle">Deleted At</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(items.data.length > 0)">
                                    <td class="text-center align-middle text-muted text-danger" colspan="10">
                                        <i class="ri-delete-bin-line fs-1 mb-2 d-block"></i>
                                        No deleted {{ getTypeName.toLowerCase() }} found
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in items.data" :key="item.id">
                                    <td class="text-center align-middle">
                                        {{ currentRow(index, items) }}
                                    </td>

                                    <!-- Customers -->
                                    <template v-if="params.type === 'customers'">
                                        <td class="text-center align-middle">{{ item.name }}</td>
                                        <td class="text-center align-middle">{{ item.mobile }}</td>
                                        <td class="text-center align-middle">{{ item.email }}</td>
                                    </template>

                                    <!-- Suppliers -->
                                    <template v-else-if="params.type === 'suppliers'">
                                        <td class="text-center align-middle">{{ item.name }}</td>
                                        <td class="text-center align-middle">{{ item.mobile }}</td>
                                        <td class="text-center align-middle">{{ item.email }}</td>
                                        <td class="text-center align-middle">{{ item.company_name || 'N/A' }}</td>
                                    </template>

                                    <!-- Products -->
                                    <template v-else-if="params.type === 'products'">
                                        <td class="text-center align-middle">{{ item.name }}</td>
                                        <td class="text-center align-middle">{{ item.sku }}</td>
                                        <td class="text-center align-middle">{{ item.category?.name || 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            {{ $page.props.currency?.symbol }}{{ item.price }}
                                        </td>
                                    </template>

                                    <!-- Product Categories -->
                                    <template v-else-if="params.type === 'product_categories'">
                                        <td class="text-center align-middle">{{ item.name }}</td>
                                        <td class="text-center align-middle">
                                            <span v-if="item.is_active" class="badge bg-success">Active</span>
                                            <span v-else class="badge bg-danger">Inactive</span>
                                        </td>
                                    </template>

                                    <!-- Quotations -->
                                    <template v-else-if="params.type === 'quotations'">
                                        <td class="text-center align-middle">{{ item.reference }}</td>
                                        <td class="text-center align-middle">{{ item.customer?.name || 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            {{ $page.props.currency?.symbol }}{{ item.total }}
                                        </td>
                                    </template>

                                    <!-- Sales -->
                                    <template v-else-if="params.type === 'sales'">
                                        <td class="text-center align-middle">{{ item.reference }}</td>
                                        <td class="text-center align-middle">{{ item.customer?.name || 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            {{ $page.props.currency?.symbol }}{{ item.total }}
                                        </td>
                                    </template>

                                    <!-- Purchases -->
                                    <template v-else-if="params.type === 'purchases'">
                                        <td class="text-center align-middle">{{ item.reference }}</td>
                                        <td class="text-center align-middle">{{ item.supplier?.name || 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            {{ $page.props.currency?.symbol }}{{ item.total }}
                                        </td>
                                    </template>

                                    <!-- Sale Returns -->
                                    <template v-else-if="params.type === 'sale_returns'">
                                        <td class="text-center align-middle">{{ item.reference }}</td>
                                        <td class="text-center align-middle">{{ item.customer?.name || 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            {{ $page.props.currency?.symbol }}{{ item.total }}
                                        </td>
                                    </template>

                                    <!-- Sale Payments -->
                                    <template v-else-if="params.type === 'sale_payments'">
                                        <td class="text-center align-middle">{{ item.customer?.name || 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            {{ $page.props.currency?.symbol }}{{ item.amount }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ item.paymentMethod?.name || 'N/A' }}
                                        </td>
                                        <td class="text-center align-middle">{{ formatDate(item.created_at) }}</td>
                                    </template>

                                    <!-- Sale Return Payments -->
                                    <template v-else-if="params.type === 'sale_return_payments'">
                                        <td class="text-center align-middle">{{ item.customer?.name || 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            {{ $page.props.currency?.symbol }}{{ item.amount }}
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ item.paymentMethod?.name || 'N/A' }}
                                        </td>
                                        <td class="text-center align-middle">{{ formatDate(item.created_at) }}</td>
                                    </template>

                                    <!-- Customer Waivers -->
                                    <template v-else-if="params.type === 'customer_waivers'">
                                        <td class="text-center align-middle">{{ item.customer_id }}</td>
                                        <td class="text-center align-middle">{{ formatDate(item.valid_until) }}</td>
                                    </template>

                                    <td class="text-center align-middle">
                                        <span>{{ formatDate(item.deleted_at) }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button
                                            v-if="usePermission('restore_trash')"
                                            @click.prevent="restoreItem(item)"
                                            class="btn btn-success btn-sm me-2 text-white"
                                            title="Restore"
                                        >
                                            <i class="ri-arrow-go-back-line"></i> Restore
                                        </button>
                                        <button
                                            v-if="usePermission('force_delete_trash')"
                                            @click.prevent="permanentlyDeleteItem(item)"
                                            class="btn btn-danger btn-sm"
                                            title="Delete Permanently"
                                        >
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </button>
                                        <span
                                            class="text-muted"
                                            v-if="
                                                !usePermission('restore_trash') && !usePermission('force_delete_trash')
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
                                    <template v-for="(link, index) in items.links">
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
