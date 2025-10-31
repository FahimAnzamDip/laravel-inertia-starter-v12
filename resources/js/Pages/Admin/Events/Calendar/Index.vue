<script>
import { useHelpers } from '@/Composables/useHelpers';
import usePermission from '@/Composables/usePermission';
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    layout: MainLayout,
};
</script>

<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Modal } from 'bootstrap';
import { debounce, pickBy } from 'lodash';
import Swal from 'sweetalert2';
import { onMounted, onUnmounted, reactive, ref, watch } from 'vue';

const { formatDateTime, formatDate } = useHelpers();

// Props
const props = defineProps({
    event_types: { type: Object },
});

// State
const events = ref([]);
const loading = ref(false);
const showFilters = ref(false);
const selectedEvent = ref(null);
// Reference to the FullCalendar component for API access
const calendarRef = ref(null);

// Modal control
const modalState = reactive({ modal: null });

// Query parameters
let params = reactive({
    search: '',
    status: '',
    event_type_id: '',
});

// Status options
const statusOptions = ['Pencil Booking', 'Confirmed', 'Cancelled', 'On Hold', 'Converted'];

// Calendar options
const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
    initialView: 'dayGridMonth',
    themeSystem: 'bootstrap5',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    },
    events: [],
    eventClick: handleEventClick,
    height: 'auto',
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        meridiem: 'short',
    },
    // Customize event content to show reference and pax
    eventContent: function (arg) {
        const event = arg.event;
        const props = event.extendedProps;

        // Build title: "Reference - Customer (Event Type) - X pax"
        let title = event.title;
        if (props.reference) {
            title = `${props.reference} - ${event.title}`;
        }
        if (props.event_type && props.event_type !== 'N/A') {
            title += ` (${props.event_type})`;
        }
        if (props.total_pax) {
            title += ` - ${props.total_pax} pax`;
        }

        return { html: `<div style="padding: 2px; overflow: hidden; text-overflow: ellipsis;">${title}</div>` };
    },
    // Apply colors from backend
    eventDidMount: function (info) {
        const event = info.event;
        const bg = event.backgroundColor;
        const border = event.borderColor;
        const isListView = info.view.type === 'listWeek';

        if (bg) {
            info.el.style.backgroundColor = bg;
            info.el.style.setProperty('background-color', bg, 'important');
        }
        if (border) {
            info.el.style.borderColor = border;
            info.el.style.setProperty('border-color', border, 'important');
        }

        // Set text color - black for list view, white for calendar views
        const textColor = isListView ? '#000000' : '#ffffff';
        info.el.style.color = textColor;
        info.el.style.setProperty('color', textColor, 'important');

        // Remove hover effect in list view
        if (isListView) {
            info.el.classList.add('no-hover');
        }
    },
});

// Watch the params object for changes and filter the calendar
watch(
    params,
    debounce(() => {
        fetchEvents();
    }, 300),
    { deep: true },
);

// Methods
async function fetchEvents() {
    loading.value = true;
    try {
        const response = await axios.get('/admin/event-calendar/events', {
            params: pickBy(params),
        });
        events.value = response.data;
        calendarOptions.value.events = response.data;
    } catch (error) {
        console.error('Error fetching events:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load events',
        });
    } finally {
        loading.value = false;
    }
}

function handleEventClick(info) {
    console.log(formatDate(info.view.currentStart));

    const ev = info.event;
    const ext = ev.extendedProps || {};

    // Set selected event data
    selectedEvent.value = {
        id: ev.id,
        reference: ext.reference ?? '—',
        customer: ext.customer ?? ev.title ?? '—',
        mobile: ext.mobile ?? '—',
        status: ext.status ?? '—',
        backgroundColor: ev.backgroundColor,
        event_type: ext.event_type ?? '—',
        event_for: ext.event_for ?? '—',
        event_time: ev.start ? formatDateTime(ev.start) : '—',
        total_pax: ext.total_pax ?? '—',
        note: ext.note ?? '',
    };

    // Show modal
    modalState.modal.show();
}

function openQuotation() {
    if (selectedEvent.value && selectedEvent.value.id) {
        modalState.modal.hide();
        router.visit(`/admin/event-quotations/${selectedEvent.value.id}`);
    }
}

// Toggle Filters
const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

// Clear table search
let clearSearch = () => {
    params.search = '';
};

// Clear all filters
const clearFilters = () => {
    params.status = '';
    params.event_type_id = '';
};

// Get current month and year from calendar
const getCurrentMonthYear = () => {
    if (!calendarRef.value) {
        const now = new Date();
        return {
            month: String(now.getMonth() + 1).padStart(2, '0'),
            year: String(now.getFullYear()),
        };
    }
    const api = calendarRef.value.getApi();
    const currentDate = api.getDate();
    return {
        month: String(currentDate.getMonth() + 1).padStart(2, '0'),
        year: String(currentDate.getFullYear()),
    };
};

// Print calendar
const printCalendar = () => {
    const { month, year } = getCurrentMonthYear();
    const queryParams = new URLSearchParams({
        month,
        year,
        ...(params.status && { status: params.status }),
        ...(params.event_type_id && { event_type_id: params.event_type_id }),
        ...(params.search && { search: params.search }),
    });
    window.open(`/admin/event-calendar/print?${queryParams.toString()}`, '_blank');
};

// Download PDF
const downloadPdf = () => {
    const { month, year } = getCurrentMonthYear();
    const queryParams = new URLSearchParams({
        month,
        year,
        ...(params.status && { status: params.status }),
        ...(params.event_type_id && { event_type_id: params.event_type_id }),
        ...(params.search && { search: params.search }),
    });
    window.open(`/admin/event-calendar/pdf?${queryParams.toString()}`, '_blank');
};

onMounted(() => {
    fetchEvents();
    modalState.modal = new Modal('#eventDetailModal', {});
});

// Responsive behavior: switch view and update size on window resize
function adjustCalendarView() {
    if (!calendarRef.value) return;
    const api = calendarRef.value.getApi();
    const w = window.innerWidth;
    // Small screens -> list view, medium -> timeGridWeek, large -> month
    const newView = w < 768 ? 'listWeek' : w < 1024 ? 'timeGridWeek' : 'dayGridMonth';
    if (api.view.type !== newView) {
        api.changeView(newView);
    }
    // ensure sizes update
    api.updateSize();
}

const debouncedAdjust = debounce(adjustCalendarView, 150);

window.addEventListener('resize', debouncedAdjust);

onUnmounted(() => {
    window.removeEventListener('resize', debouncedAdjust);
});

// Export event calendar
const exportEventCalendar = () => {
    const exportUrl = route('event-calendar.export');
    const { month, year } = getCurrentMonthYear();
    const queryParams = new URLSearchParams({
        month,
        year,
        ...(params.status && { status: params.status }),
        ...(params.event_type_id && { event_type_id: params.event_type_id }),
        ...(params.search && { search: params.search }),
    });

    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = `${exportUrl}?${queryParams.toString()}`;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    Toast.fire({
        icon: 'success',
        title: 'Downloading event calendar...',
        width: 'fit-content',
    });
};
</script>

<template>
    <Head title="Event Calendar" />

    <!-- Breadcrumb Start -->
    <Breadcrumb>
        <li class="breadcrumb-item fw-bold">
            <Link :href="route('dashboard')">
                <i class="ri-home-3-fill text-secondary"></i>
            </Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Event Calendar</li>
    </Breadcrumb>
    <!-- Breadcrumb End -->

    <!-- Page Content Start -->
    <div class="row">
        <div class="col-12 mb-2">
            <!-- Filters Card (Collapsible) -->
            <div v-if="showFilters" class="card border-0 shadow mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventTypeFilter" class="form-label fw-bold">Event Type</label>
                            <v-select
                                v-model="params.event_type_id"
                                :options="event_types"
                                label="name"
                                :reduce="(eventType) => eventType.id"
                                :placeholder="'Select Event Type'"
                                :clearable="true"
                            ></v-select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="statusFilter" class="form-label fw-bold">Status</label>
                            <v-select
                                v-model="params.status"
                                :options="statusOptions"
                                :placeholder="'Select Status'"
                                :clearable="true"
                            ></v-select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button @click="clearFilters" class="btn btn-secondary">
                                <i class="ri-refresh-line me-1"></i> Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <button @click.prevent="toggleFilters" class="btn btn-tertiary">
                                <i v-if="!showFilters" class="ri-filter-line"></i>
                                <i v-else class="ri-filter-off-line"></i>
                            </button>
                            <button @click.prevent="printCalendar" class="btn btn-tertiary ms-2">
                                <i class="ri-printer-line"></i>
                                <span class="d-none d-md-inline ms-1">Print</span>
                            </button>
                            <button @click.prevent="downloadPdf" class="btn btn-tertiary ms-2">
                                <i class="ri-file-download-line"></i>
                                <span class="d-none d-md-inline ms-1">Download</span>
                            </button>
                            <button @click="exportEventCalendar" class="btn btn-tertiary ms-2">
                                <i class="ri-file-excel-2-line"></i>
                                <span class="d-none d-md-inline ms-1">Export to Excel</span>
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

                    <!-- Calendar -->
                    <div v-if="loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div v-else class="table-responsive">
                        <FullCalendar ref="calendarRef" :options="calendarOptions" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend for Calendar View -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <h6 class="text-muted mb-3"><i class="ri-palette-line me-2"></i>Status Legend</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <span class="tw-text-blue-500 me-1"><i class="ri ri-circle-fill"></i></span>
                            <span>Pencil Booking</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="tw-text-teal-500 me-1"><i class="ri ri-circle-fill"></i></span>
                            <span>Confirmed</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="tw-text-green-500 me-1"><i class="ri ri-circle-fill"></i></span>
                            <span>Converted</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="tw-text-red-500 me-1"><i class="ri ri-circle-fill"></i></span>
                            <span>Cancelled</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="tw-text-orange-500 me-1"><i class="ri ri-circle-fill"></i></span>
                            <span>On Hold</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page Content End -->
    </div>

    <!-- Event Detail Modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-calendar-event-line me-2 text-danger"></i>
                        {{ selectedEvent?.reference ?? 'Event Details' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" v-if="selectedEvent">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Reference</label>
                            <div class="fw-semibold">{{ selectedEvent.reference }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Status</label>
                            <div>
                                <span class="badge p-2" :style="{ backgroundColor: selectedEvent.backgroundColor }">
                                    {{ selectedEvent.status }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Customer</label>
                            <div>{{ selectedEvent.customer }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Mobile</label>
                            <div>{{ selectedEvent.mobile }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Event Type</label>
                            <div>{{ selectedEvent.event_type }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Event For</label>
                            <div>{{ selectedEvent.event_for }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Event Time</label>
                            <div>{{ selectedEvent.event_time }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Total Pax</label>
                            <div>{{ selectedEvent.total_pax }}</div>
                        </div>
                        <div v-if="selectedEvent.note" class="col-md-12 mb-3">
                            <label class="form-label fw-bold text-muted">Note</label>
                            <div class="tw-prose tw-prose-sm" v-html="selectedEvent.note"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Close
                    </button>
                    <button
                        v-if="usePermission('view_quotations')"
                        type="button"
                        class="btn btn-primary"
                        @click="openQuotation"
                    >
                        <i class="ri-file-list-3-line me-1"></i>Open Quotation
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

/* FullCalendar overrides */

:deep(.fc-button) {
    border-radius: 8px;
}

:deep(.fc-event) {
    border-radius: 8px;
    padding-left: 5px;
    padding-right: 5px;
}

:deep(.fc) {
    font-family: inherit;
}

:deep(.fc-event) {
    cursor: pointer;
}

:deep(.fc-event:hover) {
    opacity: 0.8;
}

/* Remove hover effect in list view */
:deep(.fc-event.no-hover:hover) {
    opacity: 1;
}

:deep(.fc-toolbar-title) {
    font-size: 1.5rem;
    font-weight: 600;
}

/* Make the FullCalendar toolbar flexible and wrap on small screens */
:deep(.fc-toolbar) {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

:deep(.fc-toolbar-chunk) {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

/* Ensure the title chunk can shrink and center */
:deep(.fc-toolbar-chunk:nth-child(2)) {
    flex: 1 1 auto !important;
    justify-content: center !important;
}

/* Make toolbar buttons wrap to next line on very small screens */
@media (max-width: 576px) {
    :deep(.fc-toolbar-chunk) {
        width: 100% !important;
        justify-content: center !important;
    }
    :deep(.fc-toolbar-chunk:nth-child(2)) {
        order: 2 !important;
        margin: 0.25rem 0 !important;
    }
}

:deep(.fc-button) {
    background-color: #4b5563 !important;
    border-color: #4b5563 !important;
}

:deep(.fc-button:hover) {
    background-color: #374151 !important;
    border-color: #374151 !important;
}

:deep(.fc-button-active) {
    background-color: #1f2937 !important;
    border-color: #1f2937 !important;
}
/* Calendar day numbers and column headers -> dark gray */
:deep(.fc .fc-daygrid-day-number),
:deep(.fc .fc-col-header-cell .fc-col-header-cell-cushion),
:deep(.fc .fc-daygrid-day-top .fc-daygrid-day-number),
:deep(.fc .fc-col-header-cell) {
    color: #374151 !important;
}

/* Print styles */
@media print {
    .page-title-box,
    .card:not(#printable-table),
    .btn-group {
        display: none !important;
    }
}
</style>
