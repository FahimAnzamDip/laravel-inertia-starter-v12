<script>
import { useAlert } from '@/Composables/useAlert';
import { useHelpers } from '@/Composables/useHelpers';
import MainLayout from '@/Layouts/MainLayout.vue';
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { debounce, pickBy } from 'lodash';
import { computed, reactive, watch } from 'vue';

const { formatDate, formatCurrency, formatDateTime, currentRow } = useHelpers();
const { Toast } = useAlert();

const props = defineProps({
    coupon: {
        type: Object,
        required: true,
    },
    promoCodes: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
    },
});

// Query parameters
let params = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    per_page: props.filters.per_page ?? 50,
});

// Watch params object for changes
watch(
    params,
    debounce(() => {
        router.get(route('coupons.show', props.coupon.id), pickBy(params), {
            preserveScroll: true,
            preserveState: true,
        });
    }, 300),
    { deep: true },
);

// Calculate usage percentage
const usagePercentage = computed(() => {
    if (props.coupon.promo_codes_count === 0) return 0;
    return Math.round((props.coupon.used_promo_codes_count / props.coupon.promo_codes_count) * 100);
});

// Copy code to clipboard
const copyCode = async (code) => {
    try {
        await navigator.clipboard.writeText(code);
        Toast.fire({
            icon: 'success',
            title: 'Copied to clipboard!',
        });
    } catch (err) {
        console.error('Failed to copy:', err);
    }
};

// Export promo codes
const exportPromoCodes = () => {
    const exportUrl = route('coupons.export-promo-codes', props.coupon.id);
    const searchParams = new URLSearchParams(pickBy(params));

    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = `${exportUrl}?${searchParams.toString()}`;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    Toast.fire({
        icon: 'success',
        title: 'Downloading promo codes...',
        width: 'fit-content',
    });
};
</script>

<template>
    <Head :title="`Coupon: ${coupon.name}`" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('coupons.index')" class="fw-bold text-secondary">Coupons</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ coupon.name }}</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <!-- Coupon Details Card -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4 class="mb-1">
                                <i class="ri-coupon-3-fill me-2 text-primary"></i>
                                {{ coupon.name }}
                            </h4>
                            <p class="mb-0 opacity-75">
                                <i class="ri-calendar-line me-1 text-primary"></i>
                                Validity: <strong>{{ formatDateTime(coupon.valid_from) }}</strong> to
                                <strong>{{ formatDateTime(coupon.valid_until) }}</strong>
                            </p>
                        </div>
                        <div class="mt-2">
                            <span v-if="coupon.is_active" class="badge bg-success fs-6 p-2">
                                <i class="ri-checkbox-circle-line me-1"></i>
                                Active
                            </span>
                            <span v-else class="badge bg-danger fs-6 p-2">
                                <i class="ri-close-circle-line me-1"></i>
                                Inactive
                            </span>
                            <Link :href="route('coupons.edit', coupon.id)" class="btn btn-info btn-sm ms-2">
                                <i class="ri-pencil-line me-1"></i>
                                Edit Coupon
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Stats Row -->
                    <div class="row g-3 mb-4">
                        <!-- Discount Info -->
                        <div class="col-md-3">
                            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="ri-price-tag-3-line fs-1 text-primary"></i>
                                    <h3 class="mt-2 mb-0 text-primary">
                                        <span v-if="coupon.discount_type === 'Percentage'">
                                            {{ coupon.discount_value }}%
                                        </span>
                                        <span v-else>{{ formatCurrency(coupon.discount_value) }}</span>
                                    </h3>
                                    <p class="text-muted mb-0 small">Discount Value</p>
                                    <span class="badge bg-primary mt-1">
                                        {{ coupon.discount_type }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Total Generated -->
                        <div class="col-md-3">
                            <div class="card border-0 bg-info bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="ri-code-line fs-1 text-info"></i>
                                    <h3 class="mt-2 mb-0 text-info">{{ coupon.promo_codes_count }}</h3>
                                    <p class="text-muted mb-0 small">Total Codes Generated</p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Codes -->
                        <div class="col-md-3">
                            <div class="card border-0 bg-success bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="ri-check-double-line fs-1 text-success"></i>
                                    <h3 class="mt-2 mb-0 text-success">{{ coupon.active_promo_codes_count }}</h3>
                                    <p class="text-muted mb-0 small">Active Codes</p>
                                </div>
                            </div>
                        </div>

                        <!-- Used Codes -->
                        <div class="col-md-3">
                            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="ri-shopping-cart-line fs-1 text-warning"></i>
                                    <h3 class="mt-2 mb-0 text-warning">{{ coupon.used_promo_codes_count }}</h3>
                                    <p class="text-muted mb-0 small">Used Codes</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Progress Bar -->
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Usage Progress</span>
                                <span class="badge bg-primary fs-6">{{ usagePercentage }}%</span>
                            </div>
                            <div class="progress" style="height: 25px">
                                <div
                                    class="progress-bar progress-bar-striped progress-bar-animated !tw-bg-rose-500"
                                    role="progressbar"
                                    :style="`width: ${usagePercentage}%`"
                                    :aria-valuenow="usagePercentage"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promo Codes List -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="mb-0 fw-bold text-center text-md-start">
                                <i class="ri-list-check-2 me-2 text-primary"></i>
                                Promo Codes
                            </h5>
                        </div>

                        <div class="col-md-6 d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                            <button
                                @click="exportPromoCodes"
                                class="btn btn-tertiary btn-sm"
                                title="Export filtered promo codes to Excel"
                            >
                                <i class="ri-file-excel-2-line me-1"></i>
                                Export to Excel
                            </button>

                            <div class="navbar-search form-inline" style="min-width: 250px">
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
                                        @click="params.search = ''"
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

                    <div class="table-responsive-xl">
                        <table class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0 rounded-start text-center align-middle">No.</th>
                                    <th class="border-0 text-center align-middle">Promo Code</th>
                                    <th class="border-0 text-center align-middle">Status</th>
                                    <th class="border-0 text-center align-middle">Usage Count</th>
                                    <!-- <th class="border-0 text-center align-middle">Customer</th>
                                    <th class="border-0 text-center align-middle">First Used</th>
                                    <th class="border-0 text-center align-middle">Last Used</th> -->
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(promoCodes.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="5">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(code, index) in promoCodes.data" :key="code.id">
                                    <td class="text-center align-middle">{{ currentRow(index, promoCodes) }}</td>
                                    <td class="text-center align-middle">
                                        <code class="fs-6">{{ code.code }}</code>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span v-if="code.status === 'Active'" class="badge bg-success p-2">
                                            Active
                                        </span>
                                        <span v-else-if="code.status === 'Used'" class="badge bg-warning text-dark p-2">
                                            Used
                                        </span>
                                        <span v-else class="badge bg-secondary p-2" style="font-size: 0.8rem">
                                            {{ code.status }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">{{ code.usage_count }}</td>
                                    <!-- <td class="text-center align-middle">
                                        <span v-if="code.customer">
                                            {{ code.customer.name }}
                                        </span>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <small v-if="code.first_used_at" class="text-muted">
                                            {{ formatDate(code.first_used_at) }}
                                        </small>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <small v-if="code.last_used_at" class="text-muted">
                                            {{ formatDate(code.last_used_at) }}
                                        </small>
                                        <span v-else class="text-muted">-</span>
                                    </td> -->
                                    <td class="text-center align-middle">
                                        <button
                                            @click="copyCode(code.code)"
                                            class="btn btn-sm btn-info"
                                            title="Copy to clipboard"
                                        >
                                            <i class="ri-file-copy-line"></i>
                                        </button>
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
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 offset-md-4 d-flex justify-content-md-end">
                            <nav>
                                <ul class="pagination pagination-sm">
                                    <template v-for="(link, index) in promoCodes.links">
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
.bg-gradient {
    position: relative;
}

.bg-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
}
</style>
