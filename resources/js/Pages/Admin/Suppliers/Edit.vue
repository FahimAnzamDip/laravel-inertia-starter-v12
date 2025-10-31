<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useAlert } from '@/Composables/useAlert.js';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Tooltip } from 'bootstrap';
import { onMounted, ref } from 'vue';

const props = defineProps({
    supplier: {
        type: Object,
    },
});

// Tooltip
let tooltip = ref(null);
let btnTooltip = null;

onMounted(() => {
    btnTooltip = new Tooltip(tooltip.value);
});

const form = useForm({
    name: props.supplier.name,
    email: props.supplier.email,
    mobile: props.supplier.mobile,
    address: props.supplier.address,
    company_name: props.supplier.company_name || '',
    company_gst_number: props.supplier.company_gst_number || '',
});

// Update supplier
const update = () => {
    form.put(route('suppliers.update', props.supplier), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'info',
                title: 'Supplier Updated!',
            });

            form.reset();
        },
    });
};

// Reset form fields
const resetForm = () => {
    form.reset();
    form.clearErrors();
    btnTooltip.hide();
};
</script>

<template>
    <Head title="Suppliers" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('suppliers.index')" class="fw-bold text-secondary">Suppliers</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <form @submit.prevent="update">
                <div class="card border-0 shadow">
                    <div class="card-body">
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
                                    <label>Mobile</label>
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
                                <span>Update Supplier</span>
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
