<script setup>
import QrCodePrint from '@/Components/QrCodePrint.vue';
import { useAlert } from '@/Composables/useAlert';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({});

const hearingOptions = ref([
    'Work/School',
    'Instagram',
    'Friends/Family',
    'Youtube',
    'Facebook',
    'Birthday Invitation',
    'Online Search',
    'Street signage',
    'Other',
]);

// Number options for children (1..10) - use objects with label/value to match other v-select usages
const numberOptions = ref(Array.from({ length: 10 }, (_, i) => ({ value: i + 1, label: String(i + 1) })));

const waiverAgreementContent = ref(`
<h3>FunXtreme – Participant Information</h1>
  <p><strong>Soft Play | Interactive Zones | VR Games | Arcade</strong></p>
  <p><strong>PLEASE READ CAREFULLY – THIS IS A LEGAL DOCUMENT</strong></p>
  <p>
    By attending any of the "FunXtreme" locations (legal name OM TSKPLAY LLP), you release OM TSKPLAY LLP
    from any and all liability—financial or otherwise—that may arise from your attendance. By signing this
    waiver, I acknowledge and agree to the following on behalf of my child or guests.
  </p>

  <p><strong>1. Assumption of Risk:</strong> I understand and accept that participation in activities at FunXtreme,
    including soft play, interactive zones (slides, trampolines, wall games, ball pool, swings, sand area, scan-and-draw),
    VR games, and arcade games, involves physical activity and may carry inherent risks of injury, including slips, falls,
    collisions, and equipment-related accidents. I voluntarily assume all such risks on behalf of myself or my child/guest.
  </p>

  <p><strong>2. Physical Condition:</strong> I confirm that my child/guests are in good physical health and have no known
    medical conditions or disabilities that may interfere with their participation. I agree that I or my child/guests
    will not participate if unwell, under the influence of medication impairing function, or otherwise unfit.
  </p>

  <p><strong>3. Release of Liability:</strong> To the fullest extent permitted by law, I hereby release, waive, discharge,
    and hold harmless FunXtreme, its owners, staff, employees, affiliates, contractors, and representatives from any and
    all liability, claims, demands, or causes of action arising from myself/my child’s/guest’s participation in activities,
    whether caused by negligence or otherwise.
  </p>

  <p><strong>4. Supervision and Conduct:</strong> I understand that while FunXtreme staff may supervise play areas, I remain
    ultimately responsible for myself, my child’s/guest’s behavior, and adherence to the rules. I agree that disruptive or
    unsafe behavior may result in removal from the facility without refund.
  </p>

  <p><strong>5. Use of Equipment:</strong> I will ensure that I and my child/guests follow all safety instructions and use
    the equipment appropriately. I accept that misuse of equipment or disregard of rules may increase the risk of injury.
  </p>

  <p><strong>6. VR Experience Disclaimer:</strong> I understand and acknowledge that the Virtual Reality (VR) experiences at
    FunXtreme locations may not be suitable for everyone. Some individuals may experience side effects such as nausea, dizziness,
    disorientation, motion sickness, headaches, or general discomfort during or after VR use. By allowing myself or my child/guest
    to participate, I confirm that I am aware of these risks and accept them on their behalf.
  </p>

  <p><strong>Who Can Play?</strong> VR is not recommended for individuals who are pregnant, suffering from heart conditions or
    serious medical issues, experiencing psychiatric or neurological disorders, or living with pre-existing binocular vision abnormalities.
    I confirm that I or my child/guest does not fall under any of these categories and is fit to safely engage in VR activities.
  </p>

  <p><strong>7. Medical Expenses Agreement:</strong> I acknowledge that injuries can occur despite precautions and supervision.
    Should I or my child/guest require medical attention due to an injury or condition arising from participation at FunXtreme,
    I accept full responsibility for any associated medical costs and agree to cover all related expenses.
  </p>

  <p><strong>8. Media Consent:</strong> I give permission for FunXtreme to use photographs or video recordings of myself or my child/guest
    taken during our visit for promotional and marketing purposes, including on social media, websites, or printed materials.
    Optional: I do not consent to photo/video use.
  </p>

  <p><strong>9. Personal Belongings:</strong> I understand that FunXtreme is not responsible for loss, theft, or damage to personal belongings
    brought onto the premises.
  </p>

  <p><strong>10. Indemnification:</strong> I agree to indemnify and hold harmless FunXtreme from any loss, liability, damage, or cost incurred
    due to me or my child/guest’s participation or any violation of this agreement.
  </p>

  <p><strong>11. Duration of Agreement:</strong> I understand and agree that this waiver will remain in effect indefinitely and will apply
    to all future visits and participation by myself or my child at FunXtreme, unless revoked in writing.
  </p>
`);

const form = useForm({
    branch_id: route().params.branch || usePage().props.current_branch.id || null,
    id: route().params.id || null,
    parent_name: route().params.name || '',
    number_of_children: '',
    email: route().params.email || '',
    mobile: route().params.mobile || '',
    photo_video_use_out: 0,
    how_did_you_hear_about_us: '',
    agree_to_terms: 0,
    children: [],
});

// Choose layout based on authentication and get full url for the QR code
const layout = computed(() => (usePage().props.auth?.user ? MainLayout : GuestLayout));
const page = usePage();
const fullUrl = computed(() => {
    if (typeof window !== 'undefined' && window.location && window.location.href) {
        return window.location.href;
    }
    return String(page.url || page.props?.url || page.props?.settings?.app_url || '');
});

function addChild() {
    form.children.push({
        name: '',
        date_of_birth: '',
    });
}

function removeChild(index) {
    form.children.splice(index, 1);
}

watch(
    () => form.number_of_children,
    (newVal) => {
        form.children = [];
        for (let i = 1; i <= form.number_of_children; i++) {
            addChild();
        }
    },
);

// Go back to previous page
onMounted(() => {
    const goBack = document.querySelector('.go-back');
    goBack?.addEventListener('click', () => {
        window.history.back();
    });
});

const submit = () => {
    form.post(route('customers.waiver.store'), {
        preserveScroll: true,
        onSuccess: () => {
            let { Alert } = useAlert();

            Alert.fire({
                icon: 'success',
                title: 'Waiver Form Submitted!',
                text: `We have emailed you a copy of the waiver form at ${form.email}`,
            });

            form.reset();
        },
    });
};
</script>

<template>
    <component :is="layout">
        <Head>
            <title>Waiver</title>
            <link rel="icon" type="image/png" sizes="32x32" :href="$page.props.settings?.favicon" />
        </Head>
        <section class="my-5 bg-soft d-flex align-items-center">
            <div :class="$page.props.auth.user ? '' : 'container'">
                <div class="row justify-content-center">
                    <div v-if="$page.props.auth?.user" class="col-md-12 d-flex justify-content-end mb-4">
                        <QrCodePrint
                            :show-preview="false"
                            :value="fullUrl"
                            :size="400"
                            alt="Waiver Page QR Code"
                            filename="waiver-page-qr.png"
                        />
                        <button class="ms-2 go-back btn btn-secondary fw-bold d-flex align-items-center">
                            <i class="ri-arrow-go-back-fill"></i>
                            <span class="ms-2">Go Back</span>
                        </button>
                    </div>
                    <div class="col-md-12 d-flex align-items-center justify-content-center">
                        <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-4 w-100">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <img width="100" src="/images/app-logo-dark.png" alt="App Logo" />
                                <h2 class="mt-3 display-4 mb-5 fw-normal">Funxtreme Waiver Form</h2>
                            </div>
                            <form @submit.prevent="submit" class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Parent/Guardian Name <span class="text-danger">*</span></label>
                                            <input
                                                v-model="form.parent_name"
                                                type="text"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.parent_name }"
                                                placeholder="Enter parent/guardian name"
                                                required
                                            />
                                            <span v-show="form.errors.parent_name" class="text-danger">{{
                                                form.errors.parent_name
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>No. of Children <span class="text-danger">*</span></label>
                                            <v-select
                                                v-model="form.number_of_children"
                                                :options="numberOptions"
                                                :class="{ 'border-2 border-danger': form.errors.number_of_children }"
                                                :placeholder="'Select number of children'"
                                                label="label"
                                                :reduce="(opt) => opt.value"
                                                :searchable="false"
                                            ></v-select>
                                            <span v-show="form.errors.number_of_children" class="text-danger">{{
                                                form.errors.number_of_children
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div v-for="(child, index) in form.children" class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Child {{ index + 1 }} Name <span class="text-danger">*</span></label>
                                            <input
                                                v-model="child.name"
                                                type="text"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.children }"
                                                placeholder="Enter child name"
                                                required
                                            />
                                            <span v-show="form.errors.children" class="text-danger">{{
                                                form.errors.children
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label
                                                >Child {{ index + 1 }} Date of Birth
                                                <span class="text-danger">*</span></label
                                            >
                                            <v-datepicker
                                                v-model="child.date_of_birth"
                                                :placeholder="'Select date of birth'"
                                                :enable-time-picker="false"
                                                :format="'dd/MM/yyyy'"
                                                :action-row="{ showSelect: false, showCancel: false }"
                                                :auto-apply="true"
                                                :class="{ 'border-2 border-danger': form.errors.children }"
                                                required
                                            >
                                            </v-datepicker>
                                            <span v-show="form.errors.children" class="text-danger">{{
                                                form.errors.children
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Mobile Number <span class="text-danger">*</span></label>
                                            <input
                                                v-model="form.mobile"
                                                type="text"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.mobile }"
                                                placeholder="Enter mobile number"
                                                required
                                            />
                                            <span v-show="form.errors.mobile" class="text-danger">{{
                                                form.errors.mobile
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label>Email Address <span class="text-danger">*</span></label>
                                            <input
                                                v-model="form.email"
                                                type="text"
                                                class="form-control"
                                                :class="{ 'border-2 border-danger': form.errors.email }"
                                                placeholder="Enter email address"
                                                required
                                            />
                                            <span v-show="form.errors.email" class="text-danger">{{
                                                form.errors.email
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label>Opt out of Photo/Video use</label>
                                            <div class="form-check">
                                                <input
                                                    v-model="form.photo_video_use_out"
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    :value="1"
                                                    id="photo_video_use_out"
                                                />
                                                <label
                                                    class="form-check-label fw-normal tw-cursor-pointer tw-text-red-500"
                                                    for="photo_video_use_out"
                                                >
                                                    I do not consent to the use of my photo/video
                                                </label>
                                            </div>
                                            <span v-show="form.errors.photo_video_use_out" class="text-danger">{{
                                                form.errors.photo_video_use_out
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label>How did you hear about us? <span class="text-danger">*</span></label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div
                                                    v-for="(option, index) in hearingOptions"
                                                    :key="index"
                                                    class="form-check"
                                                >
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="radioOptions"
                                                        :id="'radioOptions' + index"
                                                        :value="option"
                                                        v-model="form.how_did_you_hear_about_us"
                                                        required
                                                    />
                                                    <label
                                                        class="form-check-label tw-cursor-pointer fw-normal"
                                                        :for="'radioOptions' + index"
                                                    >
                                                        {{ option }}
                                                    </label>
                                                </div>
                                            </div>
                                            <span v-show="form.errors.how_did_you_hear_about_us" class="text-danger">{{
                                                form.errors.how_did_you_hear_about_us
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label>Waiver Agreement <span class="text-danger">*</span></label>
                                            <div
                                                v-html="waiverAgreementContent"
                                                class="tw-prose-full tw-max-h-72 tw-w-full overflow-y-scroll tw-bg-gray-100 tw-p-2 rounded"
                                            ></div>
                                            <div class="form-check mt-4">
                                                <input
                                                    v-model="form.agree_to_terms"
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    :value="1"
                                                    id="agree_to_terms"
                                                    required
                                                />
                                                <label
                                                    class="form-check-label fw-normal tw-cursor-pointer tw-text-red-500"
                                                    for="agree_to_terms"
                                                >
                                                    I acknowledge and accept all terms stated in the waiver
                                                </label>
                                            </div>
                                            <span v-show="form.errors.agree_to_terms" class="text-danger">{{
                                                form.errors.agree_to_terms
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid d-xl-flex justify-content-center">
                                    <button
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                        type="submit"
                                        class="btn btn-secondary fw-bolder text-white"
                                    >
                                        <span>Submit Form</span>
                                        <i class="ri-check-line ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </component>
</template>
