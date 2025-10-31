<script>
import { useHelpers } from '@/Composables/useHelpers';
import MainLayout from '@/Layouts/MainLayout.vue';
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: Object,
});

const { formatCurrency, formatDate } = useHelpers();

// Product type detection based on product_type field
const isService = computed(() => {
    return props.product.product_type === 2;
});

const isInventoryProduct = computed(() => {
    return props.product.product_type === 1;
});
</script>

<template>
    <Head :title="`${product.name} - Product Details`" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('products.index')" class="fw-bold text-secondary">Products</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ product.name }}</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row justify-content-center">
        <!-- Product Overview Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i :class="isService ? 'ri-time-line' : 'ri-box-3-line'" class="text-primary me-2"></i>
                        {{ isService ? 'Service/Plan Overview' : 'Product Overview' }}
                    </h5>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <div v-if="product.is_active" class="badge bg-success fs-6 p-2">
                            <i class="ri-check-line me-1"></i>Active
                        </div>
                        <div v-else class="badge bg-danger fs-6 p-2">
                            <i class="ri-close-circle-line me-1"></i>Inactive
                        </div>
                        <span v-if="isService" class="badge bg-info fs-6 p-2">
                            <i class="ri-time-line me-1"></i>Service
                        </span>
                        <span v-else class="badge bg-warning fs-6 p-2">
                            <i class="ri-box-3-line me-1"></i>Product
                        </span>
                        <Link :href="route('products.edit', product)" class="btn btn-sm btn-primary">
                            <i class="ri-pencil-line me-1"></i>Edit {{ isService ? 'Service' : 'Product' }}
                        </Link>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <figure class="mb-0">
                                <img
                                    class="img-fluid rounded shadow-sm w-100"
                                    style="height: 250px; object-fit: cover"
                                    :src="
                                        product.media && product.media.length > 0
                                            ? product.media[0]?.original_url
                                            : '/images/product_placeholder.jpg'
                                    "
                                    :alt="product.name"
                                />
                            </figure>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <h1 class="display-5 fw-bold text-primary mb-2">{{ product.name }}</h1>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <span class="badge bg-primary px-3 py-2 fs-6">
                                        <i class="ri-code-line me-1"></i>{{ product.code }}
                                    </span>
                                    <span class="badge bg-info px-3 py-2 fs-6">
                                        <i class="ri-folder-line me-1"></i>{{ product.category?.name }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-money-rupee-circle-line text-success fs-2 mb-2"></i>
                                        <h4 class="text-success mb-0">{{ formatCurrency(product.price) }}</h4>
                                        <small class="text-muted">{{
                                            isService ? 'Service Price' : 'Selling Price'
                                        }}</small>
                                    </div>
                                </div>
                                <div v-if="isInventoryProduct" class="col-6">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-money-rupee-circle-line text-secondary fs-2 mb-2"></i>
                                        <h4 class="text-secondary mb-0">{{ formatCurrency(product.cost || 0) }}</h4>
                                        <small class="text-muted">Cost Price</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Information -->
                            <div v-if="product.tax" class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="ri-file-text-line me-2"></i>Tax Information
                                    </h6>
                                </div>
                            </div>
                            <div v-if="product.tax" class="row mb-4">
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-percent-line text-info fs-2 mb-2"></i>
                                        <h5 class="mb-0">{{ product.tax.rate }}%</h5>
                                        <small class="text-muted">{{ product.tax.name }}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-percent-line text-warning fs-2 mb-2"></i>
                                        <h5 class="mb-0">{{ product.tax.cgst }}%</h5>
                                        <small class="text-muted">CGST</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-percent-line text-primary fs-2 mb-2"></i>
                                        <h5 class="mb-0">{{ product.tax.sgst }}%</h5>
                                        <small class="text-muted">SGST</small>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="ri-information-line me-2 fs-5"></i>
                                        <span>No tax is applied to this {{ isService ? 'service' : 'product' }}.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory Information (Only for Products) -->
                            <div v-if="isInventoryProduct" class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="ri-box-3-line me-2"></i>Inventory Information
                                    </h6>
                                </div>
                            </div>
                            <div v-if="isInventoryProduct" class="row mb-4">
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-stack-line text-warning fs-2 mb-2"></i>
                                        <h5 class="mb-0">{{ product.quantity || 0 }}</h5>
                                        <small class="text-muted">Current Stock</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-alert-line text-danger fs-2 mb-2"></i>
                                        <h5 class="mb-0">{{ product.alert_quantity || 0 }}</h5>
                                        <small class="text-muted">
                                            Alert Quantity
                                            <i
                                                class="ri-information-line text-info ms-1"
                                                data-bs-toggle="tooltip"
                                                title="When stock falls below this quantity, you will be alerted."
                                            ></i>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <i class="ri-ruler-line text-info fs-2 mb-2"></i>
                                        <h5 class="mb-0">
                                            {{
                                                product.unit
                                                    ? `${product.unit.name} (${product.unit.short_name})`
                                                    : 'N/A'
                                            }}
                                        </h5>
                                        <small class="text-muted">
                                            Unit
                                            <i
                                                class="ri-information-line text-info ms-1"
                                                data-bs-toggle="tooltip"
                                                title="Unit of measurement for this product (e.g., pcs, kg, box, etc.)"
                                            ></i>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Service Duration (Only for Services) -->
                            <div v-if="isService" class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="ri-time-line me-2"></i>Service Information
                                    </h6>
                                </div>
                            </div>
                            <div v-if="isService" class="mb-4">
                                <div class="border rounded p-3 text-center bg-light d-inline-block">
                                    <i class="ri-time-line text-info fs-2 mb-2"></i>
                                    <h5 class="mb-0">{{ product.duration_in_minutes }} Minutes</h5>
                                    <small class="text-muted">Service Duration</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Description</label>
                                <p class="mb-0 text-dark" style="line-height: 1.6">
                                    {{ product.description || 'No description available for this product.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
