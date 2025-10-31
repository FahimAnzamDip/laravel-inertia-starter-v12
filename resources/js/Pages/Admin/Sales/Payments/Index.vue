<script>
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';
export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useAlert } from '@/Composables/useAlert.js';
import { useHelpers } from '@/Composables/useHelpers.js';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import { debounce, pickBy } from 'lodash';
import { onMounted, reactive, ref, watch } from 'vue';

const { currentRow, formatCurrency, formatDate } = useHelpers();

const props = defineProps({
    sale: {
        type: Object,
        required: true,
    },
    payments: {
        type: Object,
        required: true,
    },
    payment_methods: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
    },
});

let payment = ref(null);

// Query parameters
let params = reactive({
    search: props.filters.search ?? '',
    per_page: props.filters.per_page ?? 10,
    field: props.filters.field ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
});

// Watch params object for changes and filter the table
watch(
    params,
    debounce(() => {
        router.get(route('sale-payments.index', props.sale.id), pickBy(params), {
            preserveScroll: true,
            preserveState: true,
        });
    }, 300),
    { deep: true },
);

// Sort the table
let sort = (field) => {
    params.field = field;
    params.direction = params.direction === 'asc' ? 'desc' : 'asc';
};

// Clear table search
let clearSearch = () => {
    params.search = '';
};

// Modal control
const modalState = reactive({
    modal: null,
    editing: false,
});

onMounted(() => {
    modalState.modal = new Modal('#paymentModal', {});
});

const form = useForm({
    sale_id: props.sale.id,
    payment_date: new Date().toISOString().split('T')[0],
    amount: '',
    payment_method_id: '',
});

// Show modal in create mode
const createModal = () => {
    modalState.editing = false;
    form.reset();
    form.sale_id = props.sale.id;
    form.payment_date = new Date().toISOString().split('T')[0];
    modalState.modal.show();
};

// Get the payment for editing in the modal
const editModal = (id) => {
    modalState.editing = true;

    fetch(route('sale-payments.edit', id), {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            payment.value = data;
            form.sale_id = data.sale_id;
            form.payment_date = data.payment_date;
            form.amount = data.amount;
            form.payment_method_id = data.payment_method_id;
        });

    modalState.modal.show();
};

// Store payment
const store = () => {
    if (!modalState.editing) {
        form.post(route('sale-payments.store'), {
            preserveScroll: true,
            onSuccess: () => {
                let { Toast } = useAlert();

                Toast.fire({
                    icon: 'success',
                    title: 'Payment Created!',
                });

                form.reset();
                form.sale_id = props.sale.id;
                form.payment_date = new Date().toISOString().split('T')[0];
                modalState.modal.hide();
            },
        });
    } else {
        form.put(route('sale-payments.update', payment.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                let { Toast } = useAlert();

                Toast.fire({
                    icon: 'info',
                    title: 'Payment Updated!',
                });

                form.reset();
                form.sale_id = props.sale.id;
                form.payment_date = new Date().toISOString().split('T')[0];
                modalState.modal.hide();
            },
        });
    }
};

// Delete payment
const deletePayment = (payment) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('sale-payments.destroy', payment), {
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Payment Deleted!',
                    });
                },
                onError: () => {
                    Toast.fire({
                        icon: 'danger',
                        title: 'Something Went Wrong!',
                    });
                },
            });
        }
    });
};

// Modal Title
const modalTitle = computed(() => (modalState.editing ? 'Edit Payment' : 'Add Payment'));

// Back route based on sale type
const backRoute = computed(() => {
    if (props.sale.sale_type === 2) {
        return route('event-sales.show', props.sale.id);
    }
    return route('sales.show', props.sale.id);
});
</script>

<template>
    <Head title="Sale Payments" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item">
            <Link :href="sale.sale_type === 2 ? route('event-sales.index') : route('sales.index')">
                {{ sale.sale_type === 2 ? 'Event Sales' : 'Sales' }}
            </Link>
        </li>
        <li class="breadcrumb-item">
            <Link :href="backRoute">{{ sale.reference }}</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Payments</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <!-- Sale Summary Card -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="text-muted mb-1">Sale Reference</h6>
                            <p class="fw-bold mb-0">{{ sale.reference }}</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-1">Customer</h6>
                            <p class="fw-bold mb-0">{{ sale.customer.name }}</p>
                            <p class="mb-0 text-muted">{{ sale.customer.mobile ?? '-----' }}</p>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted mb-1">Total Amount</h6>
                            <p class="fw-bold mb-0">{{ formatCurrency(sale.total) }}</p>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted mb-1">Paid</h6>
                            <p class="fw-bold mb-0 text-success">{{ formatCurrency(sale.paid) }}</p>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted mb-1">Due</h6>
                            <p class="fw-bold mb-0 text-danger">{{ formatCurrency(sale.total - sale.paid) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table Card -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <button @click="createModal" class="btn btn-primary">
                                Add Payment <i class="ri-add-line"></i>
                            </button>
                        </div>

                        <div class="col-md-4">
                            <div class="navbar-search form-inline" id="navbar-search-main">
                                <div class="input-group input-group-merge search-bar">
                                    <input
                                        v-model="params.search"
                                        type="text"
                                        class="form-control"
                                        id="searchTable"
                                        placeholder="Search ...."
                                        aria-label="searchTable"
                                        aria-describedby="searchTable"
                                    />
                                    <span v-if="!params.search" class="input-group-text" id="searchTable">
                                        <i class="ri-search-line text-secondary"></i>
                                    </span>
                                    <span
                                        @click="clearSearch"
                                        v-else
                                        class="input-group-text"
                                        style="cursor: pointer"
                                        id="removeParameter"
                                    >
                                        <i class="ri-close-line text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0 rounded-start text-center align-middle">No.</th>
                                    <th
                                        @click="sort('payment_date')"
                                        class="border-0 text-center align-middle cursor-pointer d-flex align-items-center justify-content-center"
                                    >
                                        <span class="me-2">Payment Date</span>
                                        <span v-if="params.field === 'payment_date' && params.direction === 'asc'"
                                            ><i class="ri-sort-asc text-secondary"></i
                                        ></span>
                                        <span v-if="params.field === 'payment_date' && params.direction === 'desc'"
                                            ><i class="ri-sort-desc text-secondary"></i
                                        ></span>
                                    </th>
                                    <th class="border-0 text-center align-middle">Payment Method</th>
                                    <th class="border-0 text-center align-middle">Amount</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(payments.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="5">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(payment, index) in payments.data" :key="payment.id">
                                    <td class="text-center align-middle">{{ currentRow(index, payments) }}</td>
                                    <td class="text-center align-middle">{{ formatDate(payment.payment_date) }}</td>
                                    <td class="text-center align-middle">
                                        {{ payment.payment_method?.method_name ?? '-----' }}
                                    </td>
                                    <td class="text-center align-middle">{{ formatCurrency(payment.amount) }}</td>
                                    <td class="text-center align-middle">
                                        <button @click.prevent="editModal(payment.id)" class="btn btn-info btn-sm me-2">
                                            <i class="ri-pencil-fill"></i>
                                        </button>
                                        <a
                                            @click.prevent="deletePayment(payment)"
                                            href=""
                                            id="delete"
                                            class="btn btn-danger btn-sm"
                                        >
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 row">
                        <div class="col-md-2 mb-3 mb-md-0">
                            <div class="d-flex align-items-center showing">
                                <div class="me-2 show-text">Showing</div>
                                <select
                                    v-model="params.per_page"
                                    class="show-select form-select px-3 py-1"
                                    id="perPage"
                                >
                                    <option value="10">10</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 offset-md-4 d-flex justify-content-md-end">
                            <nav>
                                <ul class="pagination pagination-sm">
                                    <template v-for="(link, index) in payments.links">
                                        <li v-if="link.url === null" class="page-item">
                                            <button
                                                class="page-link text-muted"
                                                tabindex="-1"
                                                v-html="link.label"
                                                disabled
                                            ></button>
                                        </li>

                                        <li v-else class="page-item">
                                            <Link
                                                preserve-scroll
                                                class="page-link"
                                                :href="link.url"
                                                v-html="link.label"
                                                :class="{ active: link.active }"
                                            ></Link>
                                        </li>
                                    </template>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div
        ref="modalState.modalElement"
        class="modal fade"
        id="paymentModal"
        tabindex="-1"
        aria-labelledby="paymentModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">{{ modalTitle }}</h5>
                </div>
                <form @submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Payment Date <span class="text-danger">*</span></label>
                                    <v-datepicker
                                        v-model="form.payment_date"
                                        :placeholder="'Select payment date'"
                                        :enable-time-picker="false"
                                        :format="'dd/MM/yyyy'"
                                        :action-row="{ showSelect: false, showCancel: false }"
                                        :auto-apply="true"
                                        :class="{ 'border-2 border-danger': form.errors.payment_date }"
                                    >
                                    </v-datepicker>
                                    <span v-show="form.errors.payment_date" class="text-danger">{{
                                        form.errors.payment_date
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-control"
                                        :class="{ 'border-2 border-danger': form.errors.amount }"
                                    />
                                    <span v-show="form.errors.amount" class="text-danger">{{
                                        form.errors.amount
                                    }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label>Payment Method <span class="text-danger">*</span></label>
                                    <select
                                        v-model="form.payment_method_id"
                                        class="form-select"
                                        :class="{ 'border-2 border-danger': form.errors.payment_method_id }"
                                    >
                                        <option value="">Select Payment Method</option>
                                        <option v-for="method in payment_methods" :key="method.id" :value="method.id">
                                            {{ method.method_name }}
                                        </option>
                                    </select>
                                    <span v-show="form.errors.payment_method_id" class="text-danger">{{
                                        form.errors.payment_method_id
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button :disabled="form.processing" type="submit" class="btn btn-primary">
                            <span v-if="!modalState.editing">Add Payment</span>
                            <span v-else>Update Payment</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
