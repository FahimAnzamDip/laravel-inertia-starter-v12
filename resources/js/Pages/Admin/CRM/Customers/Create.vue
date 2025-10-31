<script>
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useAlert } from '@/Composables/useAlert.js';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Tooltip } from 'bootstrap';
import { onMounted, ref } from 'vue';

// Tooltip
let tooltip = ref(null);
let btnTooltip = null;

onMounted(() => {
    btnTooltip = new Tooltip(tooltip.value);
});

const form = useForm({
    branch_id: usePage().props.current_branch.id,
    name: '',
    email: '',
    mobile: '',
    address: '',
    company_name: '',
    company_gst_number: '',
    request_from: route().params.from ?? null,
});

// Store Customer
const store = () => {
    form.post(route('customers.store'), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'success',
                title: 'Customer Created!',
            });

            form.reset();
        },
    });
};

// Reset Form
const resetForm = () => {
    form.reset();
    form.clearErrors();
    btnTooltip.hide();
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
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('customers.index')" class="fw-bold text-secondary">Customers</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <form @submit.prevent="store">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.name }"
                                    />
                                    <span v-show="form.errors.name" class="text-danger">{{ form.errors.name }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Mobile <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.mobile"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.mobile }"
                                    />
                                    <span v-show="form.errors.mobile" class="text-danger">{{
                                        form.errors.mobile
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Email</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.email }"
                                    />
                                    <span v-show="form.errors.email" class="text-danger">{{ form.errors.email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label>Address</label>
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.address }"
                                    />
                                    <span v-show="form.errors.address" class="text-danger">{{
                                        form.errors.address
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Company Name</label>
                                    <input
                                        v-model="form.company_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.company_name }"
                                    />
                                    <span v-show="form.errors.company_name" class="text-danger">{{
                                        form.errors.company_name
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Company GST Number</label>
                                    <input
                                        v-model="form.company_gst_number"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.company_gst_number }"
                                    />
                                    <span v-show="form.errors.company_gst_number" class="text-danger">{{
                                        form.errors.company_gst_number
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button
                                type="submit"
                                class="btn btn-primary d-flex align-items-center"
                                :disabled="form.processing"
                            >
                                <span>Create Customer</span>
                                <i class="ri-check-double-line ms-1 fs-5"></i>
                            </button>
                            <button
                                type="button"
                                @click.prevent="resetForm"
                                ref="tooltip"
                                class="btn btn-danger d-flex align-items-center ms-2"
                                :disabled="form.processing"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                data-bs-title="Reset Form"
                            >
                                <i class="ri-loop-left-line ms-1 fs-5"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
