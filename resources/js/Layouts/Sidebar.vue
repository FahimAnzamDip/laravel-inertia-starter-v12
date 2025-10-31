<script setup>
import ArrowLink from '@/Components/Icons/ArrowLink.vue';
import usePermission from '@/Composables/usePermission.js';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const navbar = ref(null);
const body = ref(document.body);

const toggleNavbar = () => {
    if (navbar.value) {
        if (navbar.value.classList.contains('show')) {
            navbar.value.classList.remove('show');
        }
    }
    if (body.value.style.position === 'fixed') {
        body.value.style.position = 'relative';
    }
};

const isMobile = computed(() => {
    return window.innerWidth < 992;
});

if (isMobile) {
    router.on('success', (event) => {
        toggleNavbar();
    });
}
</script>

<template>
    <!-- Sidebar Menu Start -->
    <nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none">
        <!-- Toggled Logo Start -->
        <Link class="navbar-brand me-lg-5" :href="route('dashboard')">
            <!--            &lt;!&ndash; dark &ndash;&gt;-->
            <!--            <img class="navbar-brand-dark" src="/images/app-logo.png" alt="App logo"/>-->
            <!--            &lt;!&ndash; light &ndash;&gt;-->
            <!--            <img class="navbar-brand-light" src="/images/app-logo.png" alt="App logo"/>-->

            <h5 class="text-white mb-0">Cool Soft</h5>
        </Link>
        <!-- Toggled Logo End -->
        <!-- Siderbar Toggler Start -->
        <div class="d-flex align-items-center">
            <button
                class="navbar-toggler d-lg-none collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <!-- Sidebar Toggler End -->
    </nav>

    <nav
        ref="navbar"
        id="sidebarMenu"
        class="sidebar d-lg-block text-white collapse simplebar-scrollable-y shadow"
        data-simplebar
    >
        <div class="sidebar-inner tw-px-3 tw-pt-0">
            <!-- Mobile Nav Top Start -->
            <div
                class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4"
            >
                <div class="d-flex align-items-center">
                    <div class="avatar-lg me-4">
                        <img
                            :src="$page.props.auth.user.avatar"
                            class="card-img-top rounded-circle"
                            alt="Bonnie Green"
                        />
                    </div>
                    <div class="d-block">
                        <h2 class="h5 mb-3 mt-3">Welcome, {{ $page.props.auth.user.name }}</h2>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="btn btn-secondary btn-sm d-inline-flex align-items-center"
                        >
                            <svg
                                class="icon icon-xxs me-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                ></path>
                            </svg>
                            Logout
                        </Link>
                    </div>
                </div>
                <div class="collapse-close d-md-none">
                    <a
                        href="#sidebarMenu"
                        data-bs-toggle="collapse"
                        data-bs-target="#sidebarMenu"
                        aria-controls="sidebarMenu"
                        aria-expanded="true"
                        aria-label="Toggle navigation"
                    >
                        <i class="ri-close-circle-fill fs-1"></i>
                    </a>
                </div>
            </div>
            <!-- Mobile Nav Top End -->
            <ul class="nav flex-column pt-3 pt-md-1">
                <!-- Logo Start -->
                <li class="nav-item">
                    <Link
                        :href="route('dashboard')"
                        class="nav-link d-flex align-items-center justify-content-center hover:!tw-bg-transparent"
                    >
                        <img class="app-logo me-2" width="30" :src="$page.props.settings.logo" alt="App Logo" />
                        <img
                            class="contracted-app-logo ms-4"
                            width="30"
                            :src="$page.props.settings.logo"
                            alt="App Logo"
                        />
                        <h6 class="app-logo mb-0">Cool Soft</h6>
                        <!-- <h6 class="contracted-app-logo">FUN</h6> -->
                    </Link>
                </li>
                <!-- Logo End -->

                <!-- Menu Start -->
                <li class="nav-item" :class="route().current('dashboard') ? 'active' : ''">
                    <Link :href="route('dashboard')" class="nav-link d-flex align-items-center">
                        <span class="sidebar-icon d-flex align-items-center">
                            <i class="ri-home-2-line"></i>
                        </span>
                        <span class="sidebar-text d-inline-block">Dashboard</span>
                    </Link>
                </li>

                <li v-if="usePermission('access_user_management')" class="nav-item">
                    <span
                        id="hasArrow"
                        class="nav-link d-flex justify-content-between align-items-center"
                        :class="route().current('users*') || route().current('roles*') ? '' : 'collapsed'"
                        data-bs-toggle="collapse"
                        data-bs-target="#submenu-user-management"
                    >
                        <span class="d-flex align-items-center">
                            <span class="sidebar-icon d-flex align-items-center">
                                <i
                                    class="ri-admin-line"
                                    :class="
                                        route().current('users*') || route().current('roles*') ? 'text-secondary' : ''
                                    "
                                ></i>
                            </span>
                            <span class="sidebar-text d-inline-block">Access Control</span>
                        </span>
                        <span class="link-arrow">
                            <ArrowLink />
                        </span>
                    </span>
                    <div
                        class="multi-level collapse"
                        :class="route().current('users*') || route().current('roles*') ? 'show' : ''"
                        role="list"
                        id="submenu-user-management"
                        aria-expanded="false"
                    >
                        <ul class="flex-column nav">
                            <li id="subMenuLink" class="nav-item" :class="route().current('users*') ? 'active' : ''">
                                <Link class="nav-link" :href="route('users.index')">
                                    <span class="sidebar-text-contracted">U</span>
                                    <span class="sidebar-text">Users</span>
                                </Link>
                            </li>
                            <li id="subMenuLink" class="nav-item" :class="route().current('roles*') ? 'active' : ''">
                                <Link class="nav-link" :href="route('roles.index')">
                                    <span class="sidebar-text-contracted">RP</span>
                                    <span class="sidebar-text">Manage Roles</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>

                <li
                    v-if="usePermission(['access_settings', 'access_currencies', 'access_payment_methods'])"
                    class="nav-item"
                >
                    <span
                        id="hasArrow"
                        class="nav-link d-flex justify-content-between align-items-center"
                        :class="
                            route().current('settings.edit') ||
                            route().current('currencies*') ||
                            route().current('payment-methods*')
                                ? ''
                                : 'collapsed'
                        "
                        data-bs-toggle="collapse"
                        data-bs-target="#submenu-settings"
                    >
                        <span class="d-flex align-items-center">
                            <span class="sidebar-icon d-flex align-items-center">
                                <i
                                    class="ri-settings-3-line"
                                    :class="
                                        route().current('settings.edit') ||
                                        route().current('currencies*') ||
                                        route().current('payment-methods*')
                                            ? 'text-secondary'
                                            : ''
                                    "
                                ></i>
                            </span>
                            <span class="sidebar-text d-inline-block">Settings</span>
                        </span>
                        <span class="link-arrow">
                            <ArrowLink />
                        </span>
                    </span>
                    <div
                        class="multi-level collapse"
                        :class="
                            route().current('settings.edit') ||
                            route().current('currencies*') ||
                            route().current('payment-methods*')
                                ? 'show'
                                : ''
                        "
                        role="list"
                        id="submenu-settings"
                        aria-expanded="false"
                    >
                        <ul class="flex-column nav">
                            <li
                                v-if="usePermission('access_settings')"
                                id="subMenuLink"
                                class="nav-item"
                                :class="route().current('settings.edit') ? 'active' : ''"
                            >
                                <Link class="nav-link" :href="route('settings.edit')">
                                    <span class="sidebar-text-contracted">GS</span>
                                    <span class="sidebar-text">General</span>
                                </Link>
                            </li>
                            <li
                                v-if="usePermission('access_payment_methods')"
                                id="subMenuLink"
                                class="nav-item"
                                :class="route().current('payment-methods*') ? 'active' : ''"
                            >
                                <Link class="nav-link" :href="route('payment-methods.index')">
                                    <span class="sidebar-text-contracted">PM</span>
                                    <span class="sidebar-text">Payment Methods</span>
                                </Link>
                            </li>
                            <li
                                v-if="usePermission('access_currencies')"
                                id="subMenuLink"
                                class="nav-item"
                                :class="route().current('currencies*') ? 'active' : ''"
                            >
                                <Link class="nav-link" :href="route('currencies.index')">
                                    <span class="sidebar-text-contracted">C</span>
                                    <span class="sidebar-text">Currencies</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>

                <li
                    v-if="usePermission('access_activity_logs')"
                    class="nav-item"
                    :class="route().current('activity-logs.index') ? 'active' : ''"
                >
                    <Link :href="route('activity-logs.index')" class="nav-link d-flex align-items-center">
                        <span class="sidebar-icon d-flex align-items-center">
                            <i class="ri-time-line"></i>
                        </span>
                        <span class="sidebar-text d-inline-block">Activity Logs</span>
                    </Link>
                </li>
                <!-- Menu End -->
            </ul>
        </div>
    </nav>
    <!-- Sidebar Menu End -->
</template>
