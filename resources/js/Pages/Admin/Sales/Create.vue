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
import { computed, ref, toRef, watch } from 'vue';

useTooltips();

// Helpers composable
const { formatCurrency } = useHelpers();

const props = defineProps({
    taxes: {
        type: Object,
    },
    discounts: {
        type: Object,
    },
    event_types: {
        type: Object,
    },
    statuses: {
        type: Array,
    },
    payment_methods: {
        type: Object,
    },
    quotation: {
        type: Object,
        default: null,
    },
});

// Element references
const editor = ref(null);
const btnTooltip = ref(null);

// Check if coming from quotation
const isFromQuotation = computed(() => props.quotation !== null);

const form = useForm({
    quotation_id: props.quotation?.id || null,
    sale_type: props.quotation?.quotation_type || (route().params.type === 'standard' ? 1 : 2), // 1 = Standard, 2 = Event
    customer_id: props.quotation?.customer_id || null,
    event_type_id: props.quotation?.event_type_id || null,
    event_time: props.quotation?.event_time || null,
    event_for: props.quotation?.event_for || '',
    total_pax: props.quotation?.total_pax || '',
    sub_total: 0,
    tax_id: props.quotation?.tax_id || null,
    tax_rate: props.quotation?.tax_rate || 0,
    tax_amount: 0,
    discount_id: props.quotation?.discount_id || null,
    discount_rate: props.quotation?.discount_rate || 0,
    global_discount_amount: 0,
    line_items_discount_total: 0,
    total_discount_amount: 0,
    note: props.quotation?.note || '',
    status: 'Pending',
    products: [],
    // For Payment
    add_payment: false,
    payment_amount: 0,
    payment_method: null,
});

// Initialize Quill
const { quill, setContents } = useQuill({ containerRef: editor, model: toRef(form, 'note') });

// Populate products from quotation if exists
if (props.quotation && props.quotation.products) {
    props.quotation.products.forEach((item) => {
        form.products.push({
            product_id: item.product_id,
            name: item.product.name,
            code: item.product.code,
            product_type: item.product.product_type || 1,
            quantity: Number(item.quantity),
            unit_price: Number(item.unit_price),
            discount_amount: Number(item.discount_amount) || 0,
            tax_rate: Number(item.tax_rate),
            tax_amount: Number(item.tax_amount),
            sub_total: Number(item.sub_total),
        });
    });
}

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

    // check quantity if product is type 'Product'
    if (product.product_type === 1 && product.quantity <= 0) {
        useAlert().Toast.fire({
            icon: 'error',
            title: 'Product out of stock!',
        });
        clearQuery();
        return;
    }

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
            product_type: product.product_type || 1,
            quantity: 1,
            unit_price: Number(product.price) || 0,
            discount_amount: 0,
            tax_rate: product.tax ? Number(product.tax.rate) : 0,
            tax_amount: 0,
            sub_total: Number(product.price) || 0,
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
    // Note: Product prices are tax-excluded, so we calculate tax on the line total
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
        total += product.sub_total; // Each product.sub_total is pre-tax amount
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
    return subTotal.value + tax.value - totalDiscountAmount.value;
});

// Store sale
const store = () => {
    // Debug: Log the form data before sending
    console.log('Form data before submission:', form.data());
    console.log('Customer ID:', form.customer_id);

    form.transform((data) => ({
        ...data,
        event_time: data.event_time ? moment(data.event_time).format('YYYY-MM-DD HH:mm:ss') : null,
    })).post(route('sales.store'), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'success',
                title: 'Sale Created!',
            });

            form.reset();
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};

// Reset form fields
const resetForm = () => {
    form.reset();
    form.clearErrors();
    if (btnTooltip.value) {
        const tooltip = window.bootstrap?.Tooltip?.getInstance(btnTooltip.value);
        if (tooltip) {
            tooltip.hide();
        }
    }
};

// Show payment form
const togglePayment = () => {
    form.add_payment = !form.add_payment;
    form.payment_amount = 0;
    form.payment_method = null;
};
</script>

<template>
    <Head title="Create Sale" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('sales.index')" class="fw-bold text-secondary">Sales</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <!-- Alert for Quotation Conversion -->
        <div v-if="isFromQuotation" class="col-12">
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="ri-information-line me-2" style="font-size: 1.5rem"></i>
                <div>
                    <strong>Converting from Quotation:</strong>
                    {{ quotation.reference }} - {{ quotation.customer.name }}
                    <br />
                    <small
                        >All data has been pre-filled from the quotation. You can modify as needed before saving.</small
                    >
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <form @submit.prevent="store">
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3"><i class="ri-file-text-line me-2"></i>Basic Information</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Sale Type <span class="text-danger">*</span></label>
                                    <v-select
                                        v-model="form.sale_type"
                                        :options="[
                                            { id: 1, name: 'SL - Standard' },
                                            { id: 2, name: 'SE - Event' },
                                        ]"
                                        :placeholder="'Select Type'"
                                        label="name"
                                        :reduce="(type) => type.id"
                                        :disabled="isFromQuotation"
                                        :class="{ 'border-2 border-danger': form.errors.sale_type }"
                                    ></v-select>
                                    <span v-show="form.errors.sale_type" class="text-danger">{{
                                        form.errors.sale_type
                                    }}</span>
                                    <small v-if="isFromQuotation" class="text-muted">
                                        <i class="ri-information-line"></i> Sale type is locked from quotation
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Customer <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <Link
                                            :href="route('customers.create', { from: 'sale' })"
                                            class="btn btn-tertiary"
                                        >
                                            <i class="ri-user-add-line"></i>
                                        </Link>
                                        <SearchableSelect
                                            class="flex-grow-1 ms-2"
                                            v-model="form.customer_id"
                                            :search-url="route('get.searched.customers')"
                                            placeholder="Search customer by name, mobile ..."
                                            :input-class="form.errors.customer_id ? 'border-2 border-danger' : ''"
                                            value-key="id"
                                            label-key="name"
                                            no-results-text="No customers found!"
                                            loading-text="Searching customers..."
                                            :initial-selected-item="quotation?.customer || null"
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
                                    <span v-show="form.errors.customer_id" class="text-danger">{{
                                        form.errors.customer_id
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
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

                        <!-- Event Information (shown only for Event sales) -->
                        <div v-if="form.sale_type === 2" class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="ri-calendar-event-line me-2"></i>Event Information
                                </h6>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-4">
                                    <label>Event Type <span class="text-danger">*</span></label>
                                    <v-select
                                        v-model="form.event_type_id"
                                        :options="event_types"
                                        :placeholder="'Select Event Type'"
                                        label="name"
                                        :reduce="(eventType) => eventType.id"
                                        :class="{ 'border-2 border-danger': form.errors.event_type_id }"
                                    ></v-select>
                                    <span v-show="form.errors.event_type_id" class="text-danger">{{
                                        form.errors.event_type_id
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-4">
                                    <label>Event Date & Time</label>
                                    <v-datepicker
                                        v-model="form.event_time"
                                        :placeholder="'Select date & time'"
                                        :teleport="true"
                                        :enable-time-picker="true"
                                        :is-24="false"
                                        :format="'dd/MM/yyyy hh:mm aa'"
                                        :auto-apply="true"
                                        :close-on-auto-apply="true"
                                        :action-row="{}"
                                        :class="{ 'border-2 border-danger': form.errors.event_time }"
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.event_time" class="text-danger">{{
                                        form.errors.event_time
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-4">
                                    <label>Event For</label>
                                    <input
                                        v-model="form.event_for"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.event_for }"
                                        placeholder=""
                                    />
                                    <span v-show="form.errors.event_for" class="text-danger">{{
                                        form.errors.event_for
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-4">
                                    <label>Total PAX</label>
                                    <input
                                        v-model="form.total_pax"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.total_pax }"
                                        placeholder=""
                                    />
                                    <span v-show="form.errors.total_pax" class="text-danger">{{
                                        form.errors.total_pax
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Search -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="ri-shopping-cart-line me-2"></i>Products & Services
                                </h6>
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
                                                                    {{ formatCurrency(product.price) }}
                                                                </span>
                                                                <span
                                                                    v-if="product.product_type === 1"
                                                                    class="ms-2 text-white py-1 px-2 rounded"
                                                                    :class="
                                                                        product.quantity > 0 ? 'bg-info' : 'bg-danger'
                                                                    "
                                                                >
                                                                    {{
                                                                        product.quantity +
                                                                        ' ' +
                                                                        product.unit?.short_name
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
                                                    Unit Price
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
                                                    <span class="fs-6 fw-bold">{{
                                                        formatCurrency(product.unit_price)
                                                    }}</span>
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
                                            <!-- keep a hidden textarea for form serialization fallback -->
                                            <textarea v-model="form.note" class="d-none" aria-hidden="true"></textarea>
                                            <span v-show="form.errors.note" class="text-danger">{{
                                                form.errors.note
                                            }}</span>
                                        </div>
                                    </div>
                                    <template v-if="form.add_payment">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label>Payment Method <span class="text-danger">*</span></label>
                                                <v-select
                                                    v-model="form.payment_method"
                                                    :options="payment_methods"
                                                    :placeholder="'Select Method'"
                                                    label="method_name"
                                                    :reduce="(pm) => pm.id"
                                                ></v-select>
                                                <span v-show="form.errors.payment_method" class="text-danger">{{
                                                    form.errors.payment_method
                                                }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label>Payment Amount <span class="text-danger">*</span></label>
                                                <input
                                                    type="number"
                                                    v-model="form.payment_amount"
                                                    class="form-control"
                                                    :class="{ 'border-2 border-danger': form.errors.payment_amount }"
                                                    step=".01"
                                                />
                                                <span v-show="form.errors.payment_amount" class="text-danger">{{
                                                    form.errors.payment_amount
                                                }}</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="card-title text-center mb-3">Sale Summary</h6>
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
                                            <tr class="bg-gray-100">
                                                <th class="text-primary">Total:</th>
                                                <th class="text-end text-primary align-middle">
                                                    {{ formatCurrency(total) }}
                                                </th>
                                            </tr>
                                            <tr v-if="quotation?.advance_paid > 0" class="bg-gray-100">
                                                <th class="text-primary">Advance Paid:</th>
                                                <th class="text-end text-primary align-middle">
                                                    {{ formatCurrency(quotation.advance_paid) }}
                                                </th>
                                            </tr>
                                            <tr v-if="quotation?.advance_paid > 0" class="bg-gray-100">
                                                <th class="text-primary">Due:</th>
                                                <th class="text-end text-primary align-middle">
                                                    {{ formatCurrency(quotation.total - quotation.advance_paid) }}
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end">
                                        <button
                                            @click.prevent="togglePayment"
                                            type="button"
                                            :class="form.add_payment ? 'btn-danger' : 'btn-tertiary'"
                                            class="btn d-flex align-items-center mt-4"
                                        >
                                            <span v-if="form.add_payment === false">Add Payment</span>
                                            <span v-else>Remove Payment</span>
                                            <i v-if="form.add_payment === false" class="ri-add-line ms-1"></i>
                                            <i v-else class="ri-close-line ms-1"></i>
                                        </button>
                                    </div>
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
                                        <span>Create Sale</span>
                                        <i class="ri-check-double-line ms-1 fs-5"></i>
                                    </button>
                                    <button
                                        type="button"
                                        @click.prevent="resetForm"
                                        ref="btnTooltip"
                                        class="btn btn-danger d-flex align-items-center ms-2"
                                        :disabled="form.processing"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        data-bs-title="Reset Form"
                                    >
                                        <i class="ri-loop-left-line fs-5"></i>
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
