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

const { currentRow, formatCurrency } = useHelpers();

const props = defineProps({
    products: {
        type: Object,
    },
    filters: {
        type: Object,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

// Query parameters
let params = reactive({
    search: props.filters.search ?? '',
    per_page: props.filters.per_page ?? 10,
    category_id: props.filters.category_id ?? '',
    product_type: props.filters.product_type ?? '',
    is_active: props.filters.is_active ?? '',
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
        router.get(route('products.index'), pickBy(params), {
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

// Clear all filters
const clearFilters = () => {
    params.category_id = '';
    params.product_type = '';
    params.is_active = '';
};

// Delete product
const deleteProduct = (product) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('products.destroy', product), {
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Product Deleted!',
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
    <Head title="Products" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Products</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div v-if="showFilters" class="card border-0 shadow mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="categoryFilter" class="form-label fw-bold">Category</label>
                            <select v-model="params.category_id" id="categoryFilter" class="form-select">
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="productTypeFilter" class="form-label fw-bold">Product Type</label>
                            <select v-model="params.product_type" id="productTypeFilter" class="form-select">
                                <option value="">All Types</option>
                                <option value="1">Product</option>
                                <option value="2">Service</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="statusFilter" class="form-label fw-bold">Status</label>
                            <select v-model="params.is_active" id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
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
                                v-if="$page.props.current_branch !== 'all' && usePermission('create_products')"
                                :href="route('products.create')"
                                class="btn btn-primary"
                            >
                                Add Product <i class="ri-add-line"></i>
                            </Link>
                            <button v-else disabled class="btn btn-primary">
                                Add Product <i class="ri-add-line"></i>
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
                                    <!-- <th class="border-0 text-center align-middle">Image</th> -->
                                    <th class="border-0 text-center align-middle">Name</th>
                                    <th class="border-0 text-center align-middle">Code</th>
                                    <th class="border-0 text-center align-middle">Category</th>
                                    <th class="border-0 text-center align-middle">Price <sup>ex. Tax</sup></th>
                                    <th class="border-0 text-center align-middle">Tax</th>
                                    <th class="border-0 text-center align-middle">Stock</th>
                                    <th class="border-0 text-center align-middle">Duration</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(products.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="9">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(product, index) in products.data" :key="product.id">
                                    <td class="text-center align-middle">
                                        {{ currentRow(index, products) }}
                                    </td>
                                    <!-- <td class="text-center align-middle">
                                        <img
                                            style="min-width: 50px; height: 50px"
                                            class="rounded-2"
                                            :src="
                                                product.media.length > 0
                                                    ? product.media[0]?.original_url
                                                    : '/images/product_placeholder.jpg'
                                            "
                                            alt="Image"
                                        />
                                    </td> -->
                                    <td class="text-center align-middle">
                                        <Link class="text-tertiary" :href="route('products.show', product)">
                                            <div class="me-2">{{ product.name }}</div>
                                            <span v-if="product.product_type === 2" class="badge bg-info me-1">
                                                <i class="ri-time-line me-1"></i>Service
                                            </span>
                                            <span v-else class="badge bg-warning me-1">
                                                <i class="ri-box-3-line me-1"></i>Product
                                            </span>
                                            <span v-if="product.is_active == 1" class="badge bg-success"> Active </span>
                                            <span v-else class="badge bg-danger"> Inactive </span>
                                        </Link>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ product.code }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ product.category?.name }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <span>{{ formatCurrency(product.price) }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span v-if="product.tax">
                                            {{ product.tax.name }} ({{ product.tax.rate }}%)
                                        </span>
                                        <span v-else class="text-muted">No Tax</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span v-if="product.product_type === 2" class="text-muted">N/A</span>
                                        <span
                                            v-else
                                            :class="{
                                                'text-danger fw-bold':
                                                    product.quantity <= (product.alert_quantity || 0) &&
                                                    product.quantity > 0,
                                            }"
                                        >
                                            {{ product.quantity || 0 }} {{ product.unit?.short_name || '' }}
                                        </span>
                                        <small
                                            v-if="
                                                product.quantity <= (product.alert_quantity || 0) &&
                                                product.quantity > 0 &&
                                                product.product_type === 1
                                            "
                                            class="d-block text-danger"
                                        >
                                            Low Stock!
                                        </small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span v-if="product.duration_in_minutes && product.duration_in_minutes > 0">
                                            {{ product.duration_in_minutes }} mins
                                        </span>
                                        <span v-else class="text-muted">N/A</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link
                                            v-if="usePermission('edit_products')"
                                            :href="route('products.edit', product)"
                                            class="btn btn-info btn-sm me-2"
                                        >
                                            <i class="ri-pencil-fill"></i>
                                        </Link>
                                        <a
                                            v-if="usePermission('delete_products')"
                                            @click.prevent="deleteProduct(product)"
                                            href=""
                                            id="delete"
                                            class="btn btn-danger btn-sm"
                                        >
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </a>
                                        <span
                                            class="text-muted"
                                            v-if="!usePermission('edit_products') && !usePermission('delete_products')"
                                            >No action available</span
                                        >
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
                                    <template v-for="(link, index) in products.links">
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
