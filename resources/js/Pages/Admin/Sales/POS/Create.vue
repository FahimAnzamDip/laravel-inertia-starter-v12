<script>
import POSLayout from '@/Layouts/POSLayout.vue';

export default {
    layout: POSLayout,
};
</script>

<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="POS" />

    <!-- Main Content Area -->
    <div class="tw-flex tw-h-screen tw-overflow-hidden">
        <!-- Left Panel - Order Details -->
        <div class="tw-w-3/6 tw-flex tw-flex-col tw-h-full">
            <div class="card border-0 shadow h-100 d-flex flex-column">
                <!-- Customer Selection -->
                <div class="card-header bg-light border-bottom">
                    <div class="row g-2 align-items-center">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">
                                <i class="ri-user-line text-primary me-2"></i>Customer
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <select class="form-select">
                                    <option>Select Customer</option>
                                    <option>John Doe - +1234567890</option>
                                    <option>Jane Smith - +9876543210</option>
                                    <option>Walk-in Customer</option>
                                </select>
                                <button class="btn btn-tertiary">
                                    <i class="ri-user-add-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items List -->
                <div class="card-body flex-grow-1 overflow-auto p-0">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0 text-center align-middle fw-bold">Product</th>
                                    <th class="border-0 text-center align-middle fw-bold">Price</th>
                                    <th class="border-0 text-center align-middle fw-bold">Qty</th>
                                    <th class="border-0 text-center align-middle fw-bold">Discount</th>
                                    <th class="border-0 text-center align-middle fw-bold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center text-danger">
                                    <td colspan="5" class="py-4 text-danger">No product available to show!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Summary & Calculations -->
                <div class="card-footer bg-light">
                    <!-- Order Calculations Table -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-muted">Tax (0%)</td>
                                    <td class="text-end">(+) $0.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Discount (0%)</td>
                                    <td class="text-end">(-) $0.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Shipping</td>
                                    <td class="text-end">(+) $0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-primary h5 mb-0">Grand Total</td>
                                    <td class="text-end fw-bold text-primary h5 mb-0">(=) $0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tax, Discount, Shipping Controls -->
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-bold mb-1">Order Tax (%)</label>
                            <input
                                type="number"
                                class="form-control form-control-sm"
                                placeholder="0"
                                min="0"
                                max="100"
                                step="0.01"
                            />
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold mb-1">Discount (%)</label>
                            <input
                                type="number"
                                class="form-control form-control-sm"
                                placeholder="0"
                                min="0"
                                max="100"
                                step="0.01"
                            />
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold mb-1">Shipping</label>
                            <input
                                type="number"
                                class="form-control form-control-sm"
                                placeholder="0"
                                min="0"
                                step="0.01"
                            />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button
                            class="btn btn-outline-danger flex-fill d-flex align-items-center justify-content-center"
                        >
                            <i class="ri-refresh-line me-1"></i> Reset
                        </button>
                        <button class="btn btn-success flex-fill d-flex align-items-center justify-content-center">
                            <i class="ri-check-line me-1"></i> Proceed
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Product Catalog -->
        <div class="tw-w-4/6 tw-flex tw-h-full">
            <div class="card border-0 shadow h-100 d-flex flex-row w-100">
                <!-- Category Sidebar -->
                <div class="border-end bg-light h-100 d-flex flex-column">
                    <div class="card-header bg-secondary text-white text-center py-2">
                        <h6 class="mb-0 fw-bold">Categories</h6>
                    </div>
                    <div class="p-3 overflow-auto flex-grow-1">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm !tw-text-xs">All</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">New</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Electronics</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Clothing</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Chicken</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">EC</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Free Download</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Boy</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">MOTOS</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Hortaliças</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Shoes</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Ice-Cream</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Panjabi</button>
                            <button class="btn btn-outline-primary btn-sm !tw-text-xs">Snack Food</button>
                        </div>
                    </div>
                </div>

                <!-- Products Area -->
                <div class="flex-grow-1 d-flex flex-column">
                    <!-- Search Bar -->
                    <div class="card-header bg-white border-bottom">
                        <div class="row g-2">
                            <div class="col">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">
                                        <i class="ri-search-line text-secondary"></i>
                                    </span>
                                    <input
                                        type="text"
                                        placeholder="Search by name, code, category"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="card-body overflow-auto flex-grow-1">
                        <div class="row g-3">
                            <!-- Sample Products -->
                            <div v-for="i in 24" :key="i" class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card border h-100 product-card" style="cursor: pointer">
                                    <div class="card-body text-center p-3">
                                        <figure class="tw-mb-4">
                                            <img
                                                src="/images/product_placeholder.jpg"
                                                alt="Product Image"
                                                class="tw-h-[80px] tw-w-full tw-object-cover tw-rounded-xl"
                                            />
                                        </figure>
                                        <h6
                                            class="card-title mb-1 text-truncate fw-semibold"
                                            style="font-size: 0.875rem"
                                        >
                                            iPhone 16 Pro Max - Apple
                                        </h6>
                                        <small class="text-muted d-block mb-2">Electronics</small>
                                        <div class="price">
                                            <span class="badge bg-success fw-bold">$1,299.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.2s ease;
}
</style>
