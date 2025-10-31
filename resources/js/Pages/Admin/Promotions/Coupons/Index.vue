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
import { reactive, watch } from 'vue';

const { currentRow, formatCurrency, formatDateTime } = useHelpers();

const props = defineProps({
    filters: {
        type: Object,
    },
    coupons: {
        type: Object,
    },
});

// Query parameters
let params = reactive({
    search: props.filters.search ?? '',
    per_page: props.filters.per_page ?? 10,
});

// Watch params object for changes and filter the table
watch(
    params,
    debounce(() => {
        router.get(route('coupons.index'), pickBy(params), {
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

// Delete coupon
const deleteCoupon = (coupon) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Are you sure?',
        text: 'This will delete the coupon and all its promo codes!',
        icon: 'warning',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('coupons.destroy', coupon), {
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Coupon Deleted!',
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

// Format discount value
const formatDiscount = (value, type) => {
    return type === 'percentage' ? `${value}%` : `${value} INR`;
};
</script>

<template>
    <Head title="Coupons" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Coupons</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <Link
                                v-if="$page.props.current_branch !== 'all' && usePermission('create_coupons')"
                                :href="route('coupons.create')"
                                class="btn btn-primary"
                            >
                                Add Coupon <i class="ri-add-line"></i>
                            </Link>
                            <button v-else disabled class="btn btn-primary">
                                Add Coupon <i class="ri-add-line"></i>
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

                    <div class="table-responsive-xl">
                        <table class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0 rounded-start text-center align-middle">No.</th>
                                    <th class="border-0 text-center align-middle">Coupon Name</th>
                                    <th class="border-0 text-center align-middle">Discount</th>
                                    <th class="border-0 text-center align-middle">Generated</th>
                                    <th class="border-0 text-center align-middle">Active</th>
                                    <th class="border-0 text-center align-middle">Used</th>
                                    <th class="border-0 text-center align-middle">Validity</th>
                                    <th class="border-0 text-center align-middle">Status</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(coupons.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="9">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(coupon, index) in coupons.data" :key="coupon.id">
                                    <td class="text-center align-middle">
                                        {{ currentRow(index, coupons) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link :href="route('coupons.show', coupon.id)" class="text-tertiary fw-bold">
                                            {{ coupon.name }}
                                        </Link>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span v-if="coupon.discount_type === 'Percentage'">
                                            {{ coupon.discount_value }} %
                                        </span>
                                        <span v-else>
                                            {{ formatCurrency(coupon.discount_value) }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ coupon.promo_codes_count }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ coupon.active_promo_codes_count }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ coupon.used_promo_codes_count }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <div>
                                            {{ formatDateTime(coupon.valid_from) }}
                                        </div>
                                        <div>to</div>
                                        <div>
                                            {{ formatDateTime(coupon.valid_until) }}
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span
                                            v-if="coupon.is_active"
                                            class="badge bg-success p-2"
                                            style="font-size: 0.8rem"
                                            >Active</span
                                        >
                                        <span v-else class="badge bg-danger p-2" style="font-size: 0.8rem"
                                            >Inactive</span
                                        >
                                    </td>
                                    <td class="text-center align-middle">
                                        <Link
                                            v-if="usePermission('edit_coupons')"
                                            :href="route('coupons.edit', coupon)"
                                            class="btn btn-info btn-sm me-2"
                                        >
                                            <i class="ri-pencil-fill"></i>
                                        </Link>
                                        <a
                                            v-if="usePermission('delete_coupons')"
                                            @click.prevent="deleteCoupon(coupon)"
                                            href=""
                                            id="delete"
                                            class="btn btn-danger btn-sm"
                                        >
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </a>
                                        <span
                                            class="text-muted"
                                            v-if="!usePermission('edit_coupons') && !usePermission('delete_coupons')"
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
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 offset-md-4 d-flex justify-content-md-end">
                            <nav>
                                <ul class="pagination pagination-sm">
                                    <template v-for="(link, index) in coupons.links">
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
