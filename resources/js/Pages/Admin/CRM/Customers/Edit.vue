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
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    customer: {
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
    name: props.customer.name,
    email: props.customer.email,
    mobile: props.customer.mobile,
    address: props.customer.address,
    number_of_children: props.customer.number_of_children,
    waiver_id: props.customer.waiver ? props.customer.waiver.id : null,
    photo_video_use_out: props.customer.waiver ? props.customer.waiver.photo_video_use_out : 0,
    how_did_you_hear_about_us: props.customer.waiver ? props.customer.waiver.how_did_you_hear_about_us : '',
    agree_to_terms: props.customer.waiver ? props.customer.waiver.agree_to_terms : 0,
    children: props.customer.children || [],
    valid_until: props.customer.waiver ? props.customer.waiver.valid_until : '',
    company_name: props.customer.company_name || '',
    company_gst_number: props.customer.company_gst_number || '',
});

function addChild() {
    form.children.push({
        name: '',
        date_of_birth: '',
    });
}

function removeChild(index) {
    form.children.splice(index, 1);
}

watch(
    () => form.number_of_children,
    (newVal) => {
        form.children = [];
        for (let i = 1; i <= form.number_of_children; i++) {
            addChild();
        }
    },
);

// Update customer
const update = () => {
    form.put(route('customers.update', props.customer), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'info',
                title: 'Customer Updated!',
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
                        <div v-if="form.waiver_id" class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Number of Children</label>
                                    <input
                                        v-model="form.number_of_children"
                                        type="text"
                                        class="form-control bg-gray-200"
                                        :class="{ 'border-2 border-danger': form.errors.number_of_children }"
                                        readonly
                                    />
                                    <span v-show="form.errors.number_of_children" class="text-danger">{{
                                        form.errors.number_of_children
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Waiver Validity <span class="text-danger">*</span></label>
                                    <v-datepicker
                                        v-model="form.valid_until"
                                        :placeholder="'Select waiver validity'"
                                        :enable-time-picker="false"
                                        :format="'dd/MM/yyyy'"
                                        :action-row="{ showSelect: false, showCancel: false }"
                                        :auto-apply="true"
                                        :class="{ 'border-2 border-danger': form.errors.valid_until }"
                                        required
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.valid_until" class="text-danger">{{
                                        form.errors.valid_until
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.waiver_id" v-for="(child, index) in form.children" class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Child {{ index + 1 }} Name <span class="text-danger">*</span></label>
                                    <input
                                        v-model="child.name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.children }"
                                        placeholder="Enter child name"
                                        required
                                    />
                                    <span v-show="form.errors.children" class="text-danger">{{
                                        form.errors.children
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label
                                        >Child {{ index + 1 }} Date of Birth <span class="text-danger">*</span></label
                                    >
                                    <v-datepicker
                                        v-model="child.date_of_birth"
                                        :placeholder="'Select date of birth'"
                                        :enable-time-picker="false"
                                        :format="'dd/MM/yyyy'"
                                        :action-row="{ showSelect: false, showCancel: false }"
                                        :auto-apply="true"
                                        :class="{ 'border-2 border-danger': form.errors.children }"
                                        required
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.children" class="text-danger">{{
                                        form.errors.children
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
                                <span>Update Customer</span>
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
