<script>
import { useTooltips } from '@/Composables/useTooltips';
import MainLayout from '@/Layouts/MainLayout.vue';
import { debounce, parseInt } from 'lodash';

export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';
import { useAlert } from '@/Composables/useAlert.js';
import { useHelpers } from '@/Composables/useHelpers.js';
import { useQuill } from '@/Composables/useQuill.js';
import { Head, Link, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import 'quill/dist/quill.snow.css';
import { computed, onMounted, ref, toRef, watch } from 'vue';

useTooltips();

// Helpers composable
const { formatCurrency } = useHelpers();

const props = defineProps({
    purchase: {
        type: Object,
        required: true,
    },
    taxes: {
        type: Object,
    },
    discounts: {
        type: Object,
    },
    statuses: {
        type: Array,
    },
});

// Element references
const editor = ref(null);

const form = useForm({
    supplier_id: null,
    reference: '',
    purchase_date: new Date(),
    sub_total: 0,
    tax_id: null,
    tax_rate: 0,
    tax_amount: 0,
    discount_id: null,
    discount_rate: 0,
    global_discount_amount: 0,
    line_items_discount_total: 0,
    total_discount_amount: 0,
    shipping_amount: 0,
    note: '',
    status: 'Completed',
    products: [],
});

// Pre-populate form with purchase data
onMounted(() => {
    form.supplier_id = props.purchase.supplier_id;
    form.reference = props.purchase.reference;
    form.purchase_date = props.purchase.purchase_date ? moment(props.purchase.purchase_date) : new Date();
    form.sub_total = props.purchase.sub_total;
    form.tax_id = props.purchase.tax_id;
    form.tax_rate = props.purchase.tax_rate || 0;
    form.tax_amount = props.purchase.tax_amount || 0;
    form.discount_id = props.purchase.discount_id;
    form.discount_rate = props.purchase.discount_rate || 0;
    form.global_discount_amount = props.purchase.global_discount_amount || 0;
    form.line_items_discount_total = props.purchase.line_items_discount_total || 0;
    form.total_discount_amount = props.purchase.total_discount_amount || 0;
    form.shipping_amount = props.purchase.shipping_amount || 0;
    form.note = props.purchase.note || '';
    form.status = props.purchase.status;

    // Pre-populate products
    form.products = props.purchase.products.map((product) => ({
        product_id: product.product_id,
        name: product.product.name,
        code: product.product.code,
        quantity: Number(product.quantity),
        unit_price: Number(product.unit_price),
        discount_amount: Number(product.discount_amount) || 0,
        tax_rate: Number(product.tax_rate) || 0,
        tax_amount: Number(product.tax_amount) || 0,
        sub_total: Number(product.sub_total),
    }));

    // Initialize discount type if discount is selected
    if (form.discount_id) {
        const selectedDiscount = props.discounts.find((discount) => discount.id === form.discount_id);
        if (selectedDiscount) {
            selectedDiscountType.value = selectedDiscount.type || '';
        }
    }
});

// Initialize Quill
const { quill, setContents } = useQuill({ containerRef: editor, model: toRef(form, 'note') });

// Watch tax_id and update tax_rate
watch(
    () => form.tax_id,
    () => {
        if (form.tax_id) {
            const selectedTax = props.taxes.find((tax) => tax.id === form.tax_id);
            if (selectedTax) {
                form.tax_rate = selectedTax.rate;
            }
        } else {
            form.tax_rate = 0;
        }

        // Recalculate all products when global tax changes
        form.products.forEach((product) => {
            updateProductCalculations(product);
        });
    },
);

// Watch discount_id and update discount_rate and type
const selectedDiscountType = ref('');
watch(
    () => form.discount_id,
    () => {
        if (form.discount_id) {
            const selectedDiscount = props.discounts.find((discount) => discount.id === form.discount_id);
            if (selectedDiscount) {
                form.discount_rate = selectedDiscount.rate;
                selectedDiscountType.value = selectedDiscount.type ? selectedDiscount.type : '';
            }
        } else {
            form.discount_rate = 0;
            selectedDiscountType.value = '';
        }
    },
);

// Search products
let query = ref('');
let products = ref([]);
let loading = ref(false);

// Get products
watch(
    query,
    debounce((value) => {
        if (value.length < 3) {
            return;
        }

        loading.value = true;

        fetch(
            route('get.searched.products', {
                query: value,
            }),
            {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            },
        )
            .then((response) => response.json())
            .then((data) => {
                products.value = data;
                loading.value = false;
            })
            .catch((error) => console.log(error));
    }, 300),
    { deep: true },
);

// Clear search query
const clearQuery = () => {
    query.value = '';
    products.value = [];
};

// Add product
const addProduct = (product) => {
    // check if product already exists
    let exists = form.products.find((p) => p.product_id === product.id);

    // if product exists, update the quantity
    if (exists) {
        useAlert().Toast.fire({
            icon: 'warning',
            title: 'Product already exists!',
        });
    } else {
        const newProduct = {
            product_id: product.id,
            name: product.name,
            code: product.code,
            quantity: 1,
            unit_price: Number(product.cost || product.price) || 0,
            discount_amount: 0,
            tax_rate: product.tax ? Number(product.tax.rate) : 0,
            tax_amount: 0,
            sub_total: Number(product.cost || product.price) || 0,
        };

        form.products.push(newProduct);

        // Calculate tax for the new product
        updateProductCalculations(newProduct);
    }

    // clear search query
    clearQuery();
};

// Remove product
const removeProduct = (index) => {
    form.products.splice(index, 1);
};

// Update product calculations
const updateProductCalculations = (product, changedField = null) => {
    // If discount was changed, validate it doesn't exceed total line price
    if (changedField === 'discount') {
        const maxDiscount = product.unit_price * product.quantity;
        if (product.discount_amount > maxDiscount) {
            product.discount_amount = maxDiscount;
        }
    }

    // If quantity changed, ensure discount doesn't exceed new total
    if (changedField === 'quantity') {
        const maxDiscount = product.unit_price * product.quantity;
        if (product.discount_amount > maxDiscount) {
            product.discount_amount = maxDiscount;
        }
    }

    // Calculate tax amount for this product
    const lineTotal = product.quantity * product.unit_price - product.discount_amount;

    // Use product's own tax rate if it has one, otherwise use global tax rate
    const effectiveTaxRate = product.tax_rate > 0 ? product.tax_rate : form.tax_rate || 0;
    product.tax_amount = (effectiveTaxRate / 100) * lineTotal;

    // sub_total is the pre-tax amount (base amount after discount)
    product.sub_total = lineTotal;
};

// calculate sub_total (sum of pre-tax amounts)
const subTotal = computed(() => {
    let total = 0;
    form.products.forEach((product) => {
        total += product.sub_total;
    });

    form.sub_total = total;
    return total;
});

// calculate tax
const tax = computed(() => {
    let totalTax = 0;

    form.products.forEach((product) => {
        // Ensure tax calculation is up to date
        updateProductCalculations(product);
        totalTax += product.tax_amount;
    });

    form.tax_amount = totalTax;
    return totalTax;
});

// calculate line item discounts total
const lineItemDiscountsTotal = computed(() => {
    let total = 0;
    form.products.forEach((product) => {
        total += product.discount_amount; // Already a number
    });
    form.line_items_discount_total = total;
    return total;
});

// calculate global discount amount
const globalDiscountAmount = computed(() => {
    if (form.discount_id && selectedDiscountType.value === 'fixed') {
        // Fixed amount discount
        form.global_discount_amount = Number(form.discount_rate) || 0;
    } else {
        // Percentage discount
        form.global_discount_amount = (Number(form.discount_rate) / 100) * subTotal.value;
    }
    return form.global_discount_amount;
});

// calculate total discount (line items + global)
const totalDiscountAmount = computed(() => {
    const total = lineItemDiscountsTotal.value + globalDiscountAmount.value;
    form.total_discount_amount = total;
    return total;
});

// calculate total
const total = computed(() => {
    return subTotal.value + tax.value - totalDiscountAmount.value + parseFloat(form.shipping_amount || 0);
});

// Update purchase
const update = () => {
    form.transform((data) => ({
        ...data,
        purchase_date: data.purchase_date ? moment(data.purchase_date).format('YYYY-MM-DD') : null,
    })).put(route('purchases.update', props.purchase.id), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'success',
                title: 'Purchase Updated!',
            });
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <Head title="Edit Purchase" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('purchases.index')" class="fw-bold text-secondary">Purchases</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <form @submit.prevent="update">
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3"><i class="ri-file-text-line me-2"></i>Basic Information</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Reference / PO# <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.reference"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.reference }"
                                        placeholder="e.g., PO-2025-001"
                                    />
                                    <span v-show="form.errors.reference" class="text-danger">{{
                                        form.errors.reference
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Supplier <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <Link
                                            :href="route('suppliers.create', { from: 'purchase' })"
                                            class="btn btn-tertiary"
                                        >
                                            <i class="ri-user-add-line"></i>
                                        </Link>
                                        <SearchableSelect
                                            class="flex-grow-1 ms-2"
                                            v-model="form.supplier_id"
                                            :initial-selected-item="props.purchase.supplier"
                                            :search-url="route('get.searched.suppliers')"
                                            placeholder="Search supplier by name, mobile ..."
                                            :input-class="form.errors.supplier_id ? 'border-2 border-danger' : ''"
                                            value-key="id"
                                            label-key="name"
                                            no-results-text="No suppliers found!"
                                            loading-text="Searching suppliers..."
                                        >
                                            <template #item="{ item }">
                                                <div>
                                                    <strong>{{ item.name }}</strong>
                                                    <span v-if="item.company_name" class="text-muted">
                                                        ({{ item.company_name }})</span
                                                    >
                                                    <br />
                                                    <small class="text-muted">
                                                        {{ item.mobile }}
                                                        <span v-if="item.email"> • {{ item.email }}</span>
                                                    </small>
                                                </div>
                                            </template>
                                        </SearchableSelect>
                                    </div>
                                    <span v-show="form.errors.supplier_id" class="text-danger">{{
                                        form.errors.supplier_id
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Purchase Date <span class="text-danger">*</span></label>
                                    <v-datepicker
                                        v-model="form.purchase_date"
                                        :placeholder="'Select purchase date'"
                                        :teleport="true"
                                        :enable-time-picker="false"
                                        :format="'dd/MM/yyyy'"
                                        :auto-apply="true"
                                        :close-on-auto-apply="true"
                                        :class="{ 'border-2 border-danger': form.errors.purchase_date }"
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.purchase_date" class="text-danger">{{
                                        form.errors.purchase_date
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <v-select
                                        v-model="form.status"
                                        :options="statuses"
                                        :placeholder="'Select Status'"
                                        :class="{ 'border-2 border-danger': form.errors.status }"
                                    ></v-select>
                                    <span v-show="form.errors.status" class="text-danger">{{
                                        form.errors.status
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Search -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3"><i class="ri-shopping-cart-line me-2"></i>Products</h6>
                                <div class="position-relative">
                                    <label>Search Products</label>
                                    <div class="input-group">
                                        <input
                                            v-model="query"
                                            type="text"
                                            class="form-control"
                                            placeholder="Search by name, code, description..."
                                        />
                                        <button
                                            v-if="query.length > 0"
                                            @click.prevent="clearQuery"
                                            type="button"
                                            class="btn btn-danger"
                                        >
                                            <i class="ri-close-line"></i>
                                        </button>
                                        <button v-else type="button" class="btn btn-primary">
                                            <i class="ri-search-2-line"></i>
                                        </button>
                                    </div>
                                    <template v-if="query.length > 0">
                                        <div
                                            class="card position-absolute border-0 shadow-sm mt-2"
                                            style="left: 0; right: 0; z-index: 9999999"
                                        >
                                            <div class="card-body">
                                                <div v-if="!loading">
                                                    <ul v-if="products.length > 0" class="list-group pt-3">
                                                        <li
                                                            v-for="product in products"
                                                            :key="product.id"
                                                            class="list-group-item cursor-pointer bg-gray-50 mb-3 rounded"
                                                            @click.prevent="addProduct(product)"
                                                        >
                                                            <div>
                                                                <span
                                                                    class="bg-tertiary text-white py-1 px-2 rounded"
                                                                    >{{ product.code }}</span
                                                                >
                                                                <span
                                                                    class="ms-2 bg-primary text-white py-1 px-2 rounded"
                                                                    >{{ product.name }}</span
                                                                >
                                                                <span
                                                                    class="ms-2 bg-success text-white py-1 px-2 rounded"
                                                                >
                                                                    {{ formatCurrency(product.cost || product.price) }}
                                                                </span>
                                                                <span
                                                                    v-if="product.product_type === 1"
                                                                    class="ms-2 text-white py-1 px-2 rounded"
                                                                    :class="
                                                                        product.quantity > 0
                                                                            ? 'bg-success'
                                                                            : 'bg-danger'
                                                                    "
                                                                >
                                                                    {{
                                                                        product.quantity + ' ' + product.unit.short_name
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <p v-else class="text-danger text-center mb-0">
                                                        No products found!
                                                    </p>
                                                </div>
                                                <div v-else class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap mb-0 rounded mobile-min-width">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="border-0 rounded-start text-center align-middle">Product</th>
                                                <th class="border-0 text-center align-middle" style="min-width: 150px">
                                                    Quantity
                                                </th>
                                                <th class="border-0 text-center align-middle" style="min-width: 150px">
                                                    Unit Cost
                                                </th>
                                                <th class="border-0 text-center align-middle" style="min-width: 120px">
                                                    Tax
                                                </th>
                                                <th class="border-0 text-center align-middle" style="min-width: 150px">
                                                    Discount
                                                </th>
                                                <th class="border-0 text-center align-middle">Sub Total</th>
                                                <th class="border-0 rounded-end text-center align-middle">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="!(form.products.length > 0)">
                                                <td class="text-center align-middle text-danger" colspan="7">
                                                    No products available in the cart
                                                </td>
                                            </tr>
                                            <tr v-for="(product, index) in form.products" :key="product.product_id">
                                                <td class="text-left align-middle overflow-hidden">
                                                    <p class="mb-0" style="max-width: 300px">{{ product.name }}</p>
                                                    <p class="mb-0 text-muted small">{{ product.code }}</p>
                                                </td>
                                                <td class="text-center align-middle" style="width: 200px">
                                                    <div class="input-group">
                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary btn-sm"
                                                            @click="
                                                                product.quantity = Math.max(1, product.quantity - 1);
                                                                updateProductCalculations(product, 'quantity');
                                                            "
                                                        >
                                                            <i class="ri-subtract-line"></i>
                                                        </button>
                                                        <input
                                                            v-model="product.quantity"
                                                            type="number"
                                                            class="form-control text-center"
                                                            min="1"
                                                            step="1"
                                                            @input="updateProductCalculations(product, 'quantity')"
                                                        />
                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary btn-sm"
                                                            @click="
                                                                product.quantity = parseInt(product.quantity) + 1;
                                                                updateProductCalculations(product, 'quantity');
                                                            "
                                                        >
                                                            <i class="ri-add-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <input
                                                        v-model="product.unit_price"
                                                        type="number"
                                                        class="form-control text-center"
                                                        min="0"
                                                        step="0.01"
                                                        @input="updateProductCalculations(product)"
                                                    />
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span>
                                                        {{
                                                            product.tax_rate > 0
                                                                ? product.tax_rate + '%'
                                                                : form.tax_rate > 0
                                                                ? form.tax_rate + '%'
                                                                : 'No Tax'
                                                        }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle" style="width: 200px">
                                                    <input
                                                        v-model="product.discount_amount"
                                                        type="number"
                                                        class="form-control text-center"
                                                        min="0"
                                                        :max="product.unit_price * product.quantity"
                                                        step="0.01"
                                                        @input="updateProductCalculations(product, 'discount')"
                                                    />
                                                </td>
                                                <td class="text-center align-middle" style="width: 250px">
                                                    <span class="fs-6 fw-bold text-primary">{{
                                                        formatCurrency(product.sub_total)
                                                    }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button
                                                        @click.prevent="removeProduct(index)"
                                                        type="button"
                                                        class="btn btn-danger btn-sm"
                                                    >
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing and Summary -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Tax</label>
                                            <v-select
                                                v-model="form.tax_id"
                                                :options="taxes"
                                                :placeholder="'Select Tax'"
                                                label="name"
                                                :reduce="(tax) => tax.id"
                                                :class="{ 'border-2 border-danger': form.errors.tax_id }"
                                            ></v-select>
                                            <span v-show="form.errors.tax_id" class="text-danger">{{
                                                form.errors.tax_id
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Discount</label>
                                            <v-select
                                                v-model="form.discount_id"
                                                :options="discounts"
                                                :placeholder="'Select Discount'"
                                                label="name"
                                                :reduce="(discount) => discount.id"
                                                :class="{ 'border-2 border-danger': form.errors.discount_id }"
                                            >
                                                <template #option="option">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ option.name }}</span>
                                                        <span v-if="option.type" class="badge bg-info text-white">{{
                                                            option.type
                                                        }}</span>
                                                    </div>
                                                </template>
                                            </v-select>
                                            <span v-show="form.errors.discount_id" class="text-danger">{{
                                                form.errors.discount_id
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label>Note</label>
                                            <!-- Quill editor container -->
                                            <div
                                                ref="editor"
                                                class="quill-editor border rounded p-2"
                                                :class="{ 'border-2 border-danger': form.errors.note }"
                                                style="min-height: 150px"
                                            ></div>
                                            <textarea v-model="form.note" class="d-none" aria-hidden="true"></textarea>
                                            <span v-show="form.errors.note" class="text-danger">{{
                                                form.errors.note
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="card-title text-center mb-3">Purchase Summary</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <th>Sub Total:</th>
                                                <td class="text-end align-middle">
                                                    {{ formatCurrency(subTotal) }}
                                                </td>
                                            </tr>
                                            <tr v-if="tax > 0">
                                                <th>Tax Total:</th>
                                                <td class="text-end align-middle">{{ formatCurrency(tax) }}</td>
                                            </tr>
                                            <tr v-if="totalDiscountAmount > 0">
                                                <th>Discount:</th>
                                                <td class="text-end text-danger align-middle">
                                                    -{{ formatCurrency(totalDiscountAmount) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Shipping:</th>
                                                <td class="text-end align-middle">
                                                    <input
                                                        v-model="form.shipping_amount"
                                                        type="number"
                                                        class="form-control form-control-sm text-end"
                                                        min="0"
                                                        step="0.01"
                                                        placeholder="0.00"
                                                    />
                                                </td>
                                            </tr>
                                            <tr class="bg-gray-100">
                                                <th class="text-primary">Total:</th>
                                                <th class="text-end text-primary align-middle">
                                                    {{ formatCurrency(total) }}
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-center">
                                    <button
                                        type="submit"
                                        class="btn btn-primary d-flex align-items-center"
                                        :disabled="form.processing"
                                    >
                                        <span>Update Purchase</span>
                                        <i class="ri-check-double-line ms-1 fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media (max-width: 576px) {
    .mobile-min-width {
        min-width: 1000px;
    }
}

.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    background-color: #f8f9fa !important;
}
</style>
