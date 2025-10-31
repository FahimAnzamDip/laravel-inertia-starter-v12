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
import moment from 'moment';

const props = defineProps({
    coupon: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.coupon.name,
    discount_type: props.coupon.discount_type,
    discount_value: Number(props.coupon.discount_value),
    valid_from: props.coupon.valid_from ? moment(props.coupon.valid_from) : null,
    valid_until: props.coupon.valid_until ? moment(props.coupon.valid_until) : null,
    is_active: props.coupon.is_active,
});

// Update coupon
const update = () => {
    form.transform((data) => ({
        ...data,
        valid_from: data.valid_from ? moment(data.valid_from).format('YYYY-MM-DD HH:mm:ss') : null,
        valid_until: data.valid_until ? moment(data.valid_until).format('YYYY-MM-DD HH:mm:ss') : null,
    })).put(route('coupons.update', props.coupon.id), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'success',
                title: 'Coupon Updated!',
            });
        },
    });
};
</script>

<template>
    <Head :title="`Edit: ${coupon.name}`" />

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
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('coupons.show', coupon.id)" class="fw-bold text-secondary">
                {{ coupon.name }}
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <strong>Note:</strong> You cannot change the quantity of promo codes after creation. The
                        existing {{ coupon.promo_codes_count }} codes will remain unchanged.
                    </div>

                    <form @submit.prevent="update">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="ri-information-line me-2"></i>Basic Information
                                </h6>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Coupon Name -->
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label>Coupon Name <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.name }"
                                        placeholder="e.g., Eid Sale 2025, Black Friday Deal"
                                    />
                                    <span v-show="form.errors.name" class="text-danger">{{ form.errors.name }}</span>
                                </div>
                            </div>

                            <!-- Discount Type -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Discount Type <span class="text-danger">*</span></label>
                                    <v-select
                                        v-model="form.discount_type"
                                        :options="[
                                            { label: 'Percentage', value: 'Percentage' },
                                            { label: 'Fixed', value: 'Fixed' },
                                        ]"
                                        :class="{ 'border-2 border-danger !tw-rounded-lg': form.errors.discount_type }"
                                        :clearable="false"
                                    />
                                    <span v-show="form.errors.discount_type" class="text-danger">{{
                                        form.errors.discount_type
                                    }}</span>
                                </div>
                            </div>

                            <!-- Discount Value -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Discount Value <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span v-if="form.discount_type === 'Fixed'" class="input-group-text fw-bold">
                                            {{ $page.props.settings.currency.code }}
                                        </span>
                                        <input
                                            v-model="form.discount_value"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="form-control"
                                            :class="{ 'border-2 border-danger': form.errors.discount_value }"
                                            placeholder="e.g., 20"
                                        />
                                        <span
                                            v-if="form.discount_type === 'Percentage'"
                                            class="input-group-text fw-bold"
                                        >
                                            %
                                        </span>
                                    </div>
                                    <span v-show="form.errors.discount_value" class="text-danger">{{
                                        form.errors.discount_value
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3"><i class="ri-time-line me-2"></i>Validity</h6>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Valid From -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Valid From <span class="text-danger">*</span></label>
                                    <v-datepicker
                                        v-model="form.valid_from"
                                        :placeholder="'Select date & time'"
                                        :teleport="true"
                                        :enable-time-picker="true"
                                        :is-24="false"
                                        :format="'dd/MM/yyyy hh:mm aa'"
                                        :auto-apply="true"
                                        :close-on-auto-apply="true"
                                        :action-row="{}"
                                        :class="{ 'border-2 border-danger !tw-rounded-lg': form.errors.valid_from }"
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.valid_from" class="text-danger">{{
                                        form.errors.valid_from
                                    }}</span>
                                </div>
                            </div>

                            <!-- Valid Until -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Valid Until <span class="text-danger">*</span></label>
                                    <v-datepicker
                                        v-model="form.valid_until"
                                        :placeholder="'Select date & time'"
                                        :teleport="true"
                                        :enable-time-picker="true"
                                        :is-24="false"
                                        :format="'dd/MM/yyyy hh:mm aa'"
                                        :auto-apply="true"
                                        :close-on-auto-apply="true"
                                        :action-row="{}"
                                        :class="{ 'border-2 border-danger !tw-rounded-lg': form.errors.valid_until }"
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.valid_until" class="text-danger">{{
                                        form.errors.valid_until
                                    }}</span>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input
                                            v-model="form.is_active"
                                            class="form-check-input"
                                            type="checkbox"
                                            id="is_active"
                                        />
                                        <label class="form-check-label" for="is_active"> Coupon is Active </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-center">
                            <button
                                type="submit"
                                class="btn btn-primary d-flex align-items-center"
                                :disabled="form.processing"
                            >
                                <span>Update Coupon</span>
                                <i class="ri-check-double-line ms-1 fs-5"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
