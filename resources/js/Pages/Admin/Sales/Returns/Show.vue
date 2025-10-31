<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useHelpers } from '@/Composables/useHelpers.js';
import { Head, Link, usePage } from '@inertiajs/vue3';

const { formatCurrency, formatDateTime, formatDate } = useHelpers();

const props = defineProps({
    saleReturn: {
        type: Object,
    },
});
</script>

<template>
    <Head title="Sale Return Details" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('sale-returns.index')" class="text-secondary">Sale Returns</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Details</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-header d-flex flex-wrap justify-content-end">
                    <Link :href="route('sale-return-payments.index', saleReturn.id)" class="btn btn-tertiary btn-sm">
                        <i class="ri-money-rupee-circle-line"></i>
                        <span class="ms-2">Refund Payments</span>
                    </Link>
                    <Link
                        v-if="saleReturn.sale"
                        :href="route('sales.show', saleReturn.sale)"
                        class="btn btn-tertiary btn-sm ms-2"
                    >
                        <i class="ri-file-list-line"></i>
                        <span class="ms-2">View Original Sale</span>
                    </Link>
                    <Link :href="route('sale-returns.edit', saleReturn)" class="btn btn-warning ms-2 btn-sm">
                        <i class="ri-pencil-line"></i>
                        <span class="ms-2">Edit Return</span>
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
                        <div class="col-12">
                            <h4 class="text-warning mb-3"><i class="ri-arrow-go-back-line me-2"></i>SALE RETURN</h4>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="table-responsive">
                            <table class="table table-centered table-bordered table-nowrap mb-0 rounded">
                                <tbody>
                                    <tr class="thead-light">
                                        <th class="text-left align-middle py-2">Return Reference</th>
                                        <td class="text-left align-middle py-2">
                                            {{ saleReturn.reference ?? '-----' }}
                                        </td>
                                        <th class="text-left align-middle py-2">Status</th>
                                        <td class="text-left align-middle py-2">{{ saleReturn.status ?? '-----' }}</td>
                                    </tr>
                                    <tr class="thead-light">
                                        <th class="text-left align-middle py-2">Original Sale</th>
                                        <td class="text-left align-middle py-2">
                                            <Link
                                                v-if="saleReturn.sale"
                                                :href="route('sales.show', saleReturn.sale)"
                                                class="text-tertiary fw-bold"
                                            >
                                                {{ saleReturn.sale.reference }}
                                            </Link>
                                            <span v-else>-----</span>
                                        </td>
                                        <th class="text-left align-middle py-2">Return Date</th>
                                        <td class="text-left align-middle py-2">
                                            {{ formatDate(saleReturn.return_date) }}
                                        </td>
                                    </tr>
                                    <tr class="thead-light">
                                        <th class="text-left align-middle py-2">Client Name</th>
                                        <td class="text-left align-middle py-2">
                                            {{ saleReturn.customer?.name ?? '-----' }}
                                        </td>
                                        <th class="text-left align-middle py-2">Mobile No.</th>
                                        <td class="text-left align-middle py-2">
                                            {{ saleReturn.customer?.mobile ?? '-----' }}
                                        </td>
                                    </tr>
                                    <tr class="thead-light">
                                        <th class="text-left align-middle py-2">Created at</th>
                                        <td class="text-left align-middle py-2" colspan="3">
                                            {{ formatDateTime(saleReturn.created_at) }}
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
                                    <tr v-for="(item, index) in saleReturn.products" :key="item.id">
                                        <td class="text-center align-middle py-2">{{ index + 1 }}</td>
                                        <td class="text-center align-middle py-2">{{ item.product.name }}</td>
                                        <td class="text-center align-middle py-2">
                                            {{ formatCurrency(item.unit_price) }}
                                        </td>
                                        <td class="text-center align-middle py-2">
                                            {{ item.quantity }} {{ item.product.unit?.short_name }}
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
                                    v-html="saleReturn.note ?? '<p>No notes available</p>'"
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
                                                {{ formatCurrency(saleReturn.sub_total) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Tax Total</th>
                                            <td class="text-end align-middle py-2">
                                                {{ formatCurrency(saleReturn.tax_amount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Discount</th>
                                            <td class="text-end align-middle py-2">
                                                {{ formatCurrency(saleReturn.total_discount_amount) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Total</th>
                                            <th class="text-end align-middle py-2">
                                                {{ formatCurrency(saleReturn.total) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Refunded</th>
                                            <td class="text-end align-middle py-2">
                                                {{ formatCurrency(saleReturn.refunded) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle py-2">Due</th>
                                            <th class="text-end align-middle py-2">
                                                {{ formatCurrency(saleReturn.total - saleReturn.refunded) }}
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
