<script>
import usePermission from '@/Composables/usePermission';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed, ref } from 'vue';
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
import { onMounted, reactive, watch } from 'vue';

const { currentRow } = useHelpers();

const props = defineProps({
    filters: {
        type: Object,
    },
    categories: {
        type: Object,
        required: true,
    },
});

let category = ref(null);

// Query parameters
let params = reactive({
    search: props.filters.search ?? '',
    per_page: props.filters.per_page ?? 10,
});

// Watch params object for changes and filter the table
watch(
    params,
    debounce(() => {
        router.get(route('product-categories.index'), pickBy(params), {
            preserveScroll: true,
            preserveState: true,
        });
    }, 300),
    { deep: true },
);

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
    modalState.modal = new Modal('#categoryModal', {});
});

const form = useForm({
    name: '',
    code: '',
    description: '',
    is_active: 1,
});

// Show modal in create mode
const createModal = () => {
    modalState.editing = false;
    form.reset();
    modalState.modal.show();
};

// Get the product category for editing in the modal
const editModal = (id) => {
    modalState.editing = true;

    fetch(route('product-categories.edit', id), {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            category.value = data;
            Object.assign(form, data);
        });

    modalState.modal.show();
};

// Store category
const store = () => {
    if (!modalState.editing) {
        form.post(route('product-categories.store'), {
            preserveScroll: true,
            onSuccess: () => {
                let { Toast } = useAlert();

                Toast.fire({
                    icon: 'success',
                    title: 'Product Category Created!',
                });

                form.reset();
                modalState.modal.hide();
            },
        });
    } else {
        form.put(route('product-categories.update', category.value), {
            preserveScroll: true,
            onSuccess: () => {
                let { Toast } = useAlert();

                Toast.fire({
                    icon: 'info',
                    title: 'Product Category Updated!',
                });

                form.reset();
                modalState.modal.hide();
            },
        });
    }
};

// Delete product category
const deleteProductCategory = (category) => {
    let { Toast, AlertWithButtons } = useAlert();

    AlertWithButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('product-categories.destroy', category), {
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Product Category Deleted!',
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
const modalTitle = computed(() => (modalState.editing ? 'Edit Product Category' : 'Create Product Category'));
</script>

<template>
    <Head title="Product Categories" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Product Categories</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <button
                                v-if="
                                    $page.props.current_branch !== 'all' && usePermission('create_product_categories')
                                "
                                @click="createModal"
                                class="btn btn-primary"
                            >
                                Add Product Category <i class="ri-add-line"></i>
                            </button>
                            <button v-else disabled class="btn btn-primary">
                                Add Product Category <i class="ri-add-line"></i>
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
                                    <th class="border-0 text-center align-middle">Category Name</th>
                                    <th class="border-0 text-center align-middle">Code</th>
                                    <th class="border-0 text-center align-middle">Description</th>
                                    <th class="border-0 rounded-end text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!(categories.data.length > 0)">
                                    <td class="text-center align-middle text-danger" colspan="5">
                                        There is no data available to show
                                    </td>
                                </tr>
                                <tr v-for="(category, index) in categories.data" :key="category.id">
                                    <td class="text-center align-middle">
                                        {{ currentRow(index, categories) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="me-2">{{ category.name }}</span>
                                        <span v-if="category.is_active == 1" class="badge bg-success"> Active </span>
                                        <span v-else class="badge bg-danger"> Inactive </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ category.code }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ category.description ?? 'N/A' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <button
                                            v-if="usePermission('edit_product_categories')"
                                            @click.prevent="editModal(category.id)"
                                            class="btn btn-info btn-sm me-2"
                                        >
                                            <i class="ri-pencil-fill"></i>
                                        </button>
                                        <a
                                            v-if="usePermission('delete_product_categories')"
                                            @click.prevent="deleteProductCategory(category)"
                                            href=""
                                            id="delete"
                                            class="btn btn-danger btn-sm"
                                        >
                                            <i class="ri-delete-bin-2-fill"></i>
                                        </a>
                                        <span
                                            class="text-muted"
                                            v-if="
                                                !usePermission('edit_product_categories') &&
                                                !usePermission('delete_product_categories')
                                            "
                                            >No action available</span
                                        >
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
                                    <template v-for="(link, index) in categories.links">
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
        id="categoryModal"
        tabindex="-1"
        aria-labelledby="categoryModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">{{ modalTitle }}</h5>
                </div>
                <form @submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Category Name <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'border-2 border-danger': form.errors.name,
                                        }"
                                    />
                                    <span v-show="form.errors.name" class="text-danger">{{ form.errors.name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label>Code <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'border-2 border-danger': form.errors.code,
                                        }"
                                    />
                                    <span v-show="form.errors.code" class="text-danger">
                                        {{ form.errors.code }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label>Description</label>
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        class="form-control"
                                        :class="{
                                            'border-2 border-danger': form.errors.description,
                                        }"
                                    ></textarea>
                                    <span v-show="form.errors.description" class="text-danger">
                                        {{ form.errors.description }}
                                    </span>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button :disabled="form.processing" type="submit" class="btn btn-primary">
                            <span v-if="!modalState.editing">Create Category</span>
                            <span v-else>Update Category</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
