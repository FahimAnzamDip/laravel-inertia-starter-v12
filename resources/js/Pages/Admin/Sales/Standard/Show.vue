<script>
import usePermission from '@/Composables/usePermission';
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useHelpers } from '@/Composables/useHelpers.js';
import { Head, Link, usePage } from '@inertiajs/vue3';

const { formatCurrency, formatDateTime } = useHelpers();

const props = defineProps({
    sale: {
        type: Object,
    },
});
</script>

<template>
    <Head title="Sale Details" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link v-if="sale.sale_type === 1" :href="route('sales.index')" class="text-secondary"> Sales </Link>
            <Link v-else :href="route('event-sales.index')" class="text-secondary">Event Sales</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Details</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header d-flex flex-wrap justify-content-end">
                    <Link :href="route('sale-payments.index', sale.id)" class="btn btn-tertiary btn-sm">
                        <i class="ri-money-rupee-circle-line"></i>
                        <span class="ms-2">Payments</span>
                    </Link>
                    <a :href="route('sales.print', sale.id)" target="_blank" class="btn btn-tertiary ms-2 btn-sm">
                        <i class="ri-printer-line"></i>
                        <span class="ms-2">Print</span>
                    </a>
                    <a :href="route('sales.pdf', sale.id)" target="_blank" class="btn btn-tertiary ms-2 btn-sm">
                        <i class="ri-file-download-line"></i>
                        <span class="ms-2">Download</span>
                    </a>
                    <a href="#" class="btn btn-tertiary ms-2 btn-sm">
                        <i class="ri-mail-line"></i>
                        <span class="ms-2">Send on Mail</span>
                    </a>
                    <Link
                        v-if="usePermission('create_sale_returns')"
                        :href="route('sale-returns.create', { sale_id: sale.id })"
                        class="btn btn-warning ms-2 btn-sm"
                    >
                        <i class="ri-arrow-go-back-line"></i>
                        <span class="ms-2">Make Return</span>
                    </Link>
                    <Link :href="route('sales.edit', sale)" class="btn btn-primary ms-2 btn-sm">
                        <i class="ri-pencil-line"></i>
                        <span class="ms-2">Edit Sale</span>
                    </Link>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12 d-flex justify-content-between">
                            <div class="logo">
                                <img width="150px" :src="usePage().props.settings.logo" alt="App Logo" />
                            </div>
                            <div class="text-left">
                                <h4 class="mb-0 mt-2">{{ usePage().props.settings.company_name }}</h4>
                                <p class="mb-0 tw-max-w-[380px]">{{ usePage().props.settings.company_address }}</p>
                                <p class="mb-0">GST No : {{ usePage().props.settings.vat_id }}</p>
                                <p class="mb-0 tw-max-w-[380px]">
                                    FUNXTREME {{ usePage().props.current_branch.address }}
                                </p>
                                <p class="mb-0">Phone : {{ usePage().props.current_branch.phone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="table-responsive">
                            <table class="table table-centered table-bordered table-nowrap mb-0 rounded">
                                <tbody>
                                    <tr class="thead-light">
                                        <th class="text-left align-middle py-2">Reference</th>
                                        <td class="text-left align-middle py-2">
                                            {{ sale.reference ?? '-----' }}
                                        </td>
                                        <th class="text-left align-middle py-2">Status</th>
                                        <td class="text-left align-middle py-2">{{ sale.status ?? '-----' }}</td>
                                    </tr>
                                    <tr class="thead-light">
                                        <th class="text-left align-middle py-2">Client Name</th>
                                        <td class="text-left align-middle py-2">
                                            {{ sale.customer?.name ?? '-----' }}
                                        </td>
                                        <th class="text-left align-middle py-2">Mobile No.</th>
                                        <td class="text-left align-middle py-2">
                                            {{ sale.customer?.mobile ?? '-----' }}
                                        </td>
                                    </tr>
                                    <tr class="thead-light">
                                        <th class="text-left align-middle py-2">Created at</th>
                                        <td class="text-left align-middle py-2">
                                            {{ formatDateTime(sale.created_at) }}
                                        </td>
                                        <th v-if="sale.sale_returns" class="text-left align-middle py-2">
                                            Has Returns
                                        </th>
                                        <td v-if="sale.sale_returns" class="text-left align-middle py-2">
                                            <Link
                                                v-for="(saleReturn, index) in sale.sale_returns"
                                                :key="saleReturn.id"
                                                :href="route('sale-returns.show', saleReturn.id)"
                                                class="badge bg-danger me-1 mb-1"
                                            >
                                                {{ saleReturn.reference }}
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="table-responsive">
                            <table class="table table-centered table-bordered table-nowrap mb-0 rounded">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0 text-center align-middle py-2">No.</th>
                                        <th class="border-0 text-center align-middle py-2">Item</th>
                                        <th class="border-0 text-center align-middle py-2">Price</th>
                                        <th class="border-0 text-center align-middle py-2">Qty</th>
                                        <th class="border-0 text-center align-middle py-2">Tax</th>
                                        <th class="border-0 text-center align-middle py-2">Tax Total</th>
                                        <th class="border-0 text-center align-middle py-2">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in sale.products" :key="item.id">
                                        <td class="text-center align-middle py-2">{{ index + 1 }}</td>
                                        <td class="text-center align-middle py-2">{{ item.product.name }}</td>
                                        <td class="text-center align-middle py-2">
                                            {{ formatCurrency(item.unit_price) }}
                                        </td>
                                        <td class="text-center align-middle py-2">
                                            {{ item.quantity }}
                                            {{ item.product.unit?.short_name }}
                                        </td>
                                        <td class="text-center align-middle py-2">{{ item.tax_rate }}%</td>
                                        <td class="text-center align-middle py-2">
                                            {{ formatCurrency(item.tax_amount) }}
                                        </td>
                                        <td class="text-center align-middle py-2">
                                            {{ formatCurrency(item.sub_total) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-8 mb-4">
                            <div class="mb-4">
                                <h6 class="mb-0 border-bottom pb-2 mb-2">Note</h6>
                                <div
                                    class="tw-prose tw-prose-sm"
                                    v-html="sale.note ?? '<p>No notes available</p>'"
                                ></div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="table-responsive-md">
                                <table class="table table-bordered table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="align-middle py-2">Sub Total</th>
                                            <td class="text-end align-middle py-2">
                                                {{ formatCurrency(sale.sub_total) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Tax Total</th>
                                            <td class="text-end align-middle py-2">
                                                {{ formatCurrency(sale.tax_amount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Discount</th>
                                            <td class="text-end align-middle py-2">
                                                {{ formatCurrency(sale.total_discount_amount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Total</th>
                                            <th class="text-end align-middle py-2">
                                                {{ formatCurrency(sale.total) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Paid</th>
                                            <td class="text-end align-middle py-2">
                                                {{ formatCurrency(sale.paid) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Due</th>
                                            <th class="text-end align-middle py-2">
                                                {{ formatCurrency(sale.total - sale.paid) }}
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
