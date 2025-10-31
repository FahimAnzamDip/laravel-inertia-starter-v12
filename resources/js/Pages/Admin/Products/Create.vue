<script>
import { useTooltips } from '@/Composables/useTooltips';
import MainLayout from '@/Layouts/MainLayout.vue';
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useAlert } from '@/Composables/useAlert.js';
import { useUploadImage } from '@/Composables/useUploadImage.js';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

useTooltips();

const props = defineProps({
    categories: Object,
    units: Object,
    taxes: Object,
});

const form = useForm({
    name: '',
    code: '',
    product_category_id: null,
    description: '',
    price: 0,
    product_type: 1,
    cost: 0,
    quantity: 0,
    alert_quantity: 0,
    unit_id: null,
    tax_id: null,
    duration_in_minutes: 0,
    is_active: 1,
    images: [],
});

// Product type options for v-select
const productTypeOptions = [
    { value: 1, label: 'Product', icon: 'ri-box-3-line' },
    { value: 2, label: 'Service', icon: 'ri-time-line' },
];

// Product type detection based on product_type field
const isService = computed(() => {
    return parseInt(form.product_type) === 2;
});

const isInventoryProduct = computed(() => {
    return parseInt(form.product_type) === 1;
});

// Watch for product type changes to clear irrelevant fields
watch(
    () => form.product_type,
    (newType) => {
        const typeInt = parseInt(newType);
        if (typeInt === 2) {
            // Service
            // Clear inventory-specific fields
            form.cost = 0;
            form.quantity = 0;
            form.alert_quantity = 0;
            form.unit_id = null;
        } else if (typeInt === 1) {
            // Product
            // Clear service-specific fields
            form.duration_in_minutes = 0;
        }
    },
);

// Filepond image upload
let savedImages = ref([]);
let imagePaths = ref([]);

if (imagePaths.value) {
    for (let i = 0; i < imagePaths.value.length; i++) {
        let filename = imagePaths.value[i].substring(imagePaths.value[i].lastIndexOf('/') + 1);
        form.images.push(filename);
    }
}

let { FilePond, pond, handleFilePondLoad, handleFilePondRevert, handleFilePondInit, handleFilePondRemove } =
    useUploadImage(form, savedImages, imagePaths);

// Store Product
const store = () => {
    form.post(route('products.store'), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'success',
                title: 'Product Created!',
            });

            form.reset();
        },
    });
};

// Reset form fields
const resetForm = () => {
    form.reset();
    form.clearErrors();
    // Force reset to ensure reactivity
    setTimeout(() => {
        form.product_type = 1;
    }, 0);
    // No need to manually hide tooltip - it auto-handles
};
</script>

<template>
    <Head title="Products" />

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
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <form @submit.prevent="store">
                        <div class="row">
                            <div class="col-xl-9">
                                <!-- Product Type Indicator -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert" :class="isService ? 'alert-info' : 'alert-warning'">
                                            <i :class="isService ? 'ri-time-line' : 'ri-box-3-line'" class="me-2"></i>
                                            <strong>{{
                                                isService ? 'Service/Plan Product' : 'Inventory Product'
                                            }}</strong>
                                            <small class="d-block mt-1">
                                                {{
                                                    isService
                                                        ? 'This product is a service without stock management.'
                                                        : 'This product is a physical item with stock management.'
                                                }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Basic Information -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">
                                            <i class="ri-information-line me-2"></i>Basic Information
                                        </h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Name <span class="text-danger">*</span></label>
                                            <input
                                                v-model="form.name"
                                                type="text"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.name }"
                                            />
                                            <span v-show="form.errors.name" class="text-danger">{{
                                                form.errors.name
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <label>Code <span class="text-danger">*</span></label>
                                            <input
                                                v-model="form.code"
                                                type="text"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.code }"
                                            />
                                            <span v-show="form.errors.code" class="text-danger">{{
                                                form.errors.code
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <label>Type <span class="text-danger">*</span></label>
                                            <v-select
                                                v-model="form.product_type"
                                                :options="productTypeOptions"
                                                label="label"
                                                :reduce="(option) => option.value"
                                                placeholder="Select type"
                                                :clearable="false"
                                                :searchable="false"
                                                :class="{ 'is-invalid': form.errors.product_type }"
                                                :key="`product-type-${form.product_type}`"
                                            >
                                                <template #option="{ icon, label }">
                                                    <i :class="icon" class="me-2"></i>{{ label }}
                                                </template>
                                                <template #selected-option="{ icon, label }">
                                                    <i :class="icon" class="me-2"></i>{{ label }}
                                                </template>
                                            </v-select>
                                            <span v-show="form.errors.product_type" class="text-danger">{{
                                                form.errors.product_type
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label>Category <span class="text-danger">*</span></label>
                                            <v-select
                                                v-model="form.product_category_id"
                                                label="name"
                                                :options="categories"
                                                :placeholder="'Select Category'"
                                                :reduce="(category) => category.id"
                                                :clearable="false"
                                            ></v-select>
                                            <span v-show="form.errors.product_category_id" class="text-danger">{{
                                                form.errors.product_category_id
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label>
                                                Price <sup>(without tax)</sup>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                v-model="form.price"
                                                type="number"
                                                step="0.01"
                                                class="form-control tw-appearance-none tw-[&::-webkit-outer-spin-button]:appearance-none tw-[&::-webkit-inner-spin-button]:appearance-none"
                                                :class="{ 'border-2 border-danger': form.errors.price }"
                                            />
                                            <span v-show="form.errors.price" class="text-danger">{{
                                                form.errors.price
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label>
                                                Tax
                                                <i
                                                    class="ri-information-line text-info ms-1"
                                                    data-bs-toggle="tooltip"
                                                    title="Select applicable tax for this product. Leave empty if no tax applies."
                                                ></i>
                                            </label>
                                            <v-select
                                                v-model="form.tax_id"
                                                :get-option-label="(tax) => `${tax.name} (${tax.rate}%)`"
                                                :options="taxes"
                                                :placeholder="'No Tax'"
                                                :reduce="(tax) => tax.id"
                                                :clearable="true"
                                            ></v-select>
                                            <span v-show="form.errors.tax_id" class="text-danger">{{
                                                form.errors.tax_id
                                            }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditional Fields Based on Product Type -->
                                <div v-if="isInventoryProduct" class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">
                                            <i class="ri-box-3-line me-2"></i>Inventory Management
                                        </h6>
                                    </div>
                                </div>
                                <div v-if="isInventoryProduct" class="row">
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <label>Cost</label>
                                            <input
                                                v-model="form.cost"
                                                type="number"
                                                step="0.01"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.cost }"
                                            />
                                            <span v-show="form.errors.cost" class="text-danger">{{
                                                form.errors.cost
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <label>Quantity</label>
                                            <input
                                                v-model="form.quantity"
                                                type="number"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.quantity }"
                                            />
                                            <span v-show="form.errors.quantity" class="text-danger">{{
                                                form.errors.quantity
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <label>
                                                Alert Quantity
                                                <i
                                                    class="ri-information-line text-info ms-1"
                                                    data-bs-toggle="tooltip"
                                                    title="When stock falls below this quantity, you will be alerted."
                                                ></i>
                                            </label>
                                            <input
                                                v-model="form.alert_quantity"
                                                type="number"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.alert_quantity }"
                                            />
                                            <span v-show="form.errors.alert_quantity" class="text-danger">{{
                                                form.errors.alert_quantity
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <label>
                                                Unit
                                                <i
                                                    class="ri-information-line text-info ms-1"
                                                    data-bs-toggle="tooltip"
                                                    title="Unit short name will be shown after quantity (e.g., 10 pcs, 1 kg, 5 box, etc.)"
                                                ></i>
                                            </label>
                                            <v-select
                                                v-model="form.unit_id"
                                                :get-option-label="(unit) => `${unit.name} (${unit.short_name})`"
                                                :options="units"
                                                :placeholder="'Select Unit'"
                                                :reduce="(unit) => unit.id"
                                                :clearable="true"
                                            ></v-select>
                                            <span v-show="form.errors.unit_id" class="text-danger">{{
                                                form.errors.unit_id
                                            }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="isService" class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">
                                            <i class="ri-time-line me-2"></i>Service Configuration
                                        </h6>
                                    </div>
                                </div>
                                <div v-if="isService" class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Duration (minutes)</label>
                                            <input
                                                v-model="form.duration_in_minutes"
                                                type="number"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.duration_in_minutes }"
                                                placeholder="e.g., 60 for 1 hour service"
                                            />
                                            <span v-show="form.errors.duration_in_minutes" class="text-danger">{{
                                                form.errors.duration_in_minutes
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label>Description</label>
                                            <textarea
                                                v-model="form.description"
                                                class="form-control"
                                                rows="3"
                                                :class="{ 'border-2 border-danger': form.errors.description }"
                                            ></textarea>
                                            <span v-show="form.errors.description" class="text-danger">{{
                                                form.errors.description
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div>
                                            <label>Status <span class="text-danger">*</span></label>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input cursor-pointer"
                                                    type="checkbox"
                                                    id="status"
                                                    v-model="form.is_active"
                                                    true-value="1"
                                                    false-value="0"
                                                />
                                                <label class="form-check-label cursor-pointer" for="status">
                                                    <span v-if="form.is_active == 1" class="text-success">Active</span>
                                                    <span v-else class="text-danger">Inactive</span>
                                                </label>
                                            </div>
                                            <span v-show="form.errors.is_active" class="text-danger">
                                                {{ form.errors.is_active }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <label>Image</label>
                                <file-pond
                                    name="image"
                                    ref="pond"
                                    label-idle="Drag & Drop your files or <span class='filepond--label-action' tabindex='0'>Browse</span>"
                                    :allow-multiple="true"
                                    accepted-file-types="image/jpeg, image/png"
                                    maxFileSize="1MB"
                                    maxParallelUploads="1"
                                    :server="{
                                        url: '',
                                        timeout: 7000,
                                        process: {
                                            url: route('filepond.upload'),
                                            method: 'POST',
                                            onload: handleFilePondLoad,
                                        },
                                        revert: handleFilePondRevert,
                                        remove: handleFilePondRemove,
                                        headers: {
                                            'X-CSRF-TOKEN': $page.props.csrf_token,
                                        },
                                    }"
                                    :credits="''"
                                    :files="savedImages"
                                    @init="handleFilePondInit"
                                    filePosterMaxHeight="150"
                                />
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button
                                type="submit"
                                class="btn btn-primary d-flex align-items-center"
                                :disabled="form.processing"
                            >
                                <span>Create Product</span>
                                <i class="ri-check-double-line ms-1 fs-5"></i>
                            </button>
                            <button
                                type="button"
                                @click.prevent="resetForm"
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
