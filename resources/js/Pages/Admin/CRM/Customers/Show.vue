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
    customer: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="`${customer.name} - Customer Details`" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item">
            <Link :href="route('customers.index')">Customers</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ customer.name }}</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <!-- Customer Information Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow">
                <div
                    class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap tw-space-y-3 sm:tw-space-y-0"
                >
                    <h5 class="card-title mb-0"><i class="ri-user-line text-primary me-2"></i>Customer Information</h5>
                    <Link :href="route('customers.edit', customer)" class="btn btn-sm btn-outline-primary">
                        <i class="ri-pencil-line me-1"></i>Edit Customer
                    </Link>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Full Name</label>
                                <p class="mb-0 fw-medium">{{ customer.name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Mobile Number</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-phone-line text-success me-1"></i>{{ customer.mobile }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Email Address</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-mail-line text-info me-1"></i>{{ customer.email || 'Not provided' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Company Name</label>
                                <p class="mb-0 fw-medium">{{ customer.company_name || 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Company GST Number</label>
                                <p class="mb-0 fw-medium">{{ customer.company_gst_number || 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Number of Children</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-parent-line text-warning me-1"></i
                                    >{{ customer.number_of_children || 'Not specified' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Address</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-map-pin-line text-danger me-1"></i
                                    >{{ customer.address || 'No address provided' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Customer Since</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-calendar-line text-secondary me-1"></i
                                    >{{ formatDate(customer.created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Last Updated</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-time-line text-secondary me-1"></i
                                    >{{ formatDate(customer.updated_at) }}
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
                        <a :href="`tel:${customer.mobile}`" class="btn btn-outline-success btn-sm">
                            <i class="ri-phone-fill me-1"></i>Call Customer
                        </a>
                        <a :href="`mailto:${customer.email}`" class="btn btn-outline-info btn-sm">
                            <i class="ri-mail-fill me-1"></i>Send Email
                        </a>
                        <Link
                            v-if="!customer.waiver"
                            :href="
                                route('customers.waiver', {
                                    id: customer.id,
                                    name: customer.name,
                                    mobile: customer.mobile,
                                    email: customer.email,
                                    branch: customer.branch_id,
                                })
                            "
                            class="btn btn-outline-warning btn-sm"
                        >
                            <i class="ri-file-text-fill me-1"></i>Fill Waiver
                        </Link>
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
                        <p class="mb-0 fw-medium">{{ customer.branch?.name || 'Unknown Branch' }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small">Branch Code</label>
                        <p class="mb-0 fw-medium">
                            <span class="badge bg-secondary">{{ customer.branch?.code || 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Waiver Information -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-shield-check-line text-success me-2"></i>Waiver Information
                    </h5>
                    <div v-if="new Date(customer.waiver?.valid_until) > new Date()">
                        <span class="badge bg-success">
                            <i class="ri-checkbox-circle-fill me-1"></i>Waiver Completed
                        </span>
                    </div>
                    <div v-else>
                        <span class="badge tw-bg-orange-500 text-dark">
                            <i class="ri-alert-line me-1"></i>Waiver Pending
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div v-if="customer.waiver" class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Valid Until</label>
                                <p
                                    v-if="new Date(customer.waiver.valid_until) > new Date()"
                                    class="mb-0 fw-medium text-success"
                                >
                                    <i class="ri-calendar-check-line me-1"></i
                                    >{{ formatDate(customer.waiver.valid_until) }}
                                </p>
                                <p v-else class="mb-0 fw-medium text-danger">
                                    <i class="ri-calendar-x-line me-1"></i>
                                    {{ formatDate(customer.waiver.valid_until) }} (Expired)
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Photo/Video Consent</label>
                                <p class="mb-0 fw-medium">
                                    <span v-if="customer.waiver.photo_video_use_out" class="text-danger">
                                        <i class="ri-close-circle-line me-1"></i>Opted Out
                                    </span>
                                    <span v-else class="text-success">
                                        <i class="ri-check-circle-line me-1"></i>Consented
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Terms Agreement</label>
                                <p class="mb-0 fw-medium">
                                    <span v-if="customer.waiver.agree_to_terms" class="text-success">
                                        <i class="ri-check-circle-line me-1"></i>Agreed
                                    </span>
                                    <span v-else class="text-danger">
                                        <i class="ri-close-circle-line me-1"></i>Not Agreed
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">How They Heard About Us</label>
                                <p class="mb-0 fw-medium">
                                    <span class="badge bg-info">{{
                                        customer.waiver.how_did_you_hear_about_us || 'Not specified'
                                    }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Waiver Completed On</label>
                                <p class="mb-0 fw-medium">
                                    <i class="ri-time-line text-secondary me-1"></i
                                    >{{ formatDate(customer.waiver.created_at) }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="new Date(customer.waiver.valid_until) < new Date()"
                            class="col-md-12 d-flex justify-content-center mt-3"
                        >
                            <Link
                                :href="
                                    route('customers.waiver', {
                                        id: customer.id,
                                        name: customer.name,
                                        mobile: customer.mobile,
                                        email: customer.email,
                                        branch: customer.branch_id,
                                    })
                                "
                                class="btn btn-primary"
                            >
                                <i class="ri-file-text-fill me-1"></i>Resubmit Waiver Form
                            </Link>
                        </div>
                    </div>
                    <div v-else class="text-center py-4">
                        <i class="ri-file-text-line text-muted" style="font-size: 3rem"></i>
                        <h6 class="text-muted mt-3">No Waiver on File</h6>
                        <p class="text-muted mb-3">This customer hasn't completed the waiver form yet.</p>
                        <Link
                            :href="
                                route('customers.waiver', {
                                    id: customer.id,
                                    name: customer.name,
                                    mobile: customer.mobile,
                                    email: customer.email,
                                    branch: customer.branch_id,
                                })
                            "
                            class="btn btn-primary"
                        >
                            <i class="ri-file-text-fill me-1"></i>Complete Waiver Form
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Information -->
        <div v-if="customer.children && customer.children.length > 0" class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-heart-line text-info me-2"></i>Children Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div v-for="(child, index) in customer.children" :key="child.id" class="col-md-6 col-lg-4 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-2">
                                    <i class="ri-user-smile-line text-primary me-1"></i>{{ child.name }}
                                </h6>
                                <p class="mb-0 text-muted small">
                                    <i class="ri-cake-line me-1"></i>Born: {{ formatDate(child.date_of_birth) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
