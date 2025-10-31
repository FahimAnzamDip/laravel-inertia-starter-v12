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
    saleReturn: {
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
    payment_methods: {
        type: Object,
    },
});

// Element references
const editor = ref(null);
const btnTooltip = ref(null);

const form = useForm({
    sale_id: null,
    customer_id: null,
    return_date: null,
    sub_total: 0,
    tax_id: null,
    tax_rate: 0,
    tax_amount: 0,
    discount_id: null,
    discount_rate: 0,
    global_discount_amount: 0,
    line_items_discount_total: 0,
    total_discount_amount: 0,
    note: '',
    status: 'Pending',
    products: [],
});

// Initialize Quill
const { quill, setContents } = useQuill({ containerRef: editor, model: toRef(form, 'note') });

// Pre-populate form with sale return data
onMounted(() => {
    form.sale_id = props.saleReturn.sale_id;
    form.customer_id = props.saleReturn.customer_id;
    form.return_date = props.saleReturn.return_date ? moment(props.saleReturn.return_date) : moment();
    form.sub_total = props.saleReturn.sub_total;
    form.tax_id = props.saleReturn.tax_id;
    form.tax_rate = props.saleReturn.tax_rate || 0;
    form.tax_amount = props.saleReturn.tax_amount || 0;
    form.discount_id = props.saleReturn.discount_id;
    form.discount_rate = props.saleReturn.discount_rate || 0;
    form.global_discount_amount = props.saleReturn.global_discount_amount || 0;
    form.line_items_discount_total = props.saleReturn.line_items_discount_total || 0;
    form.total_discount_amount = props.saleReturn.total_discount_amount || 0;
    form.note = props.saleReturn.note || '';
    form.status = props.saleReturn.status;

    // Pre-populate products
    form.products = props.saleReturn.products.map((product) => ({
        product_id: product.product_id,
        name: product.product.name,
        code: product.product.code,
        product_type: product.product.product_type || 1,
        quantity: Number(product.quantity),
        unit_price: Number(product.unit_price),
        discount_amount: Number(product.discount_amount) || 0,
        tax_rate: Number(product.tax_rate) || 0,
        tax_amount: Number(product.tax_amount) || 0,
        sub_total: Number(product.sub_total),
    }));

    // Set selected discount type if discount exists
    if (props.saleReturn.discount_id) {
        const selectedDiscount = props.discounts.find((discount) => discount.id === props.saleReturn.discount_id);
        if (selectedDiscount) {
            selectedDiscountType.value = selectedDiscount.type ? selectedDiscount.type : '';
        }
    }
});

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
        total += product.discount_amount;
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

// Update sale return
const update = () => {
    form.transform((data) => ({
        ...data,
        return_date: data.return_date ? moment(data.return_date).format('YYYY-MM-DD') : moment().format('YYYY-MM-DD'),
    })).put(route('sale-returns.update', props.saleReturn.id), {
        preserveScroll: true,
        onSuccess: () => {
            let { Toast } = useAlert();

            Toast.fire({
                icon: 'success',
                title: 'Sale Return Updated!',
            });
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};

// Reset form fields
const resetForm = () => {
    // Reset to original values
    form.reset();
    if (btnTooltip.value) {
        const tooltip = window.bootstrap?.Tooltip?.getInstance(btnTooltip.value);
        if (tooltip) {
            tooltip.hide();
        }
    }
};
</script>

<template>
    <Head title="Edit Sale Return" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('sale-returns.index')" class="fw-bold text-secondary">Sale Returns</Link>
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
                                <h6 class="text-muted mb-3">
                                    <i class="ri-arrow-go-back-line me-2"></i>Return Information
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Original Sale <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="saleReturn.sale?.reference || 'N/A'"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Customer <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="saleReturn.customer?.name || 'N/A'"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Return Date <span class="text-danger">*</span></label>
                                    <v-datepicker
                                        v-model="form.return_date"
                                        :placeholder="'Select return date'"
                                        :teleport="true"
                                        :enable-time-picker="false"
                                        :format="'dd/MM/yyyy'"
                                        :auto-apply="true"
                                        :close-on-auto-apply="true"
                                        :class="{ 'border-2 border-danger': form.errors.return_date }"
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.return_date" class="text-danger">{{
                                        form.errors.return_date
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="ri-shopping-cart-line me-2"></i>Products to Return
                                </h6>
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
                                                    No products available for return
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
                                            <textarea v-model="form.note" class="d-none" aria-hidden="true"></textarea>
                                            <span v-show="form.errors.note" class="text-danger">{{
                                                form.errors.note
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="card-title text-center mb-3">Return Summary</h6>
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
                                                <th class="text-primary">Refund Total:</th>
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
                                        class="btn btn-warning d-flex align-items-center"
                                        :disabled="form.processing"
                                    >
                                        <span>Update Return</span>
                                        <i class="ri-save-line ms-1 fs-5"></i>
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
</style>
