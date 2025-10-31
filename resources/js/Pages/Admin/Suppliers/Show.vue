<script>
import MainLayout from '@/Layouts/MainLayout.vue';
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useHelpers } from '@/Composables/useHelpers.js';
import { Head, Link } from '@inertiajs/vue3';

const { formatDate } = useHelpers();

const props = defineProps({
    supplier: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="`${supplier.name} - Supplier Details`" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item">
            <Link :href="route('suppliers.index')">Suppliers</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ supplier.name }}</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <!-- Supplier Information Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow">
                <div
                    class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap tw-space-y-3 sm:tw-space-y-0"
                >
                    <h5 class="card-title mb-0"><i class="ri-user-line text-primary me-2"></i>Supplier Information</h5>
                    <Link :href="route('suppliers.edit', supplier)" class="btn btn-sm btn-outline-primary">
                        <i class="ri-pencil-line me-1"></i>Edit Supplier
                    </Link>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Full Name</label>
                                <p class="mb-0 fw-medium">{{ supplier.name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Mobile Number</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-phone-line text-success me-1"></i>{{ supplier.mobile }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Email Address</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-mail-line text-info me-1"></i>{{ supplier.email || 'Not provided' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Company Name</label>
                                <p class="mb-0 fw-medium">{{ supplier.company_name || 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Company GST Number</label>
                                <p class="mb-0 fw-medium">{{ supplier.company_gst_number || 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Address</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-map-pin-line text-danger me-1"></i>
                                    {{ supplier.address || 'No address provided' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Supplier Since</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-calendar-line text-secondary me-1"></i>
                                    {{ formatDate(supplier.created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Last Updated</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-time-line text-secondary me-1"></i>
                                    {{ formatDate(supplier.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Branch Info -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0"><i class="ri-settings-3-line text-primary me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a :href="`tel:${supplier.mobile}`" class="btn btn-outline-success btn-sm">
                            <i class="ri-phone-fill me-1"></i>Call Supplier
                        </a>
                        <a :href="`mailto:${supplier.email}`" class="btn btn-outline-info btn-sm">
                            <i class="ri-mail-fill me-1"></i>Send Email
                        </a>
                    </div>
                </div>
            </div>

            <!-- Branch Information -->
            <div class="card border-0 shadow">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="ri-building-line text-primary me-2"></i>Branch Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label text-muted small">Branch Name</label>
                        <p class="mb-0 fw-medium">{{ supplier.branch?.name || 'Unknown Branch' }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small">Branch Code</label>
                        <p class="mb-0 fw-medium">
                            <span class="badge bg-secondary">{{ supplier.branch?.code || 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
