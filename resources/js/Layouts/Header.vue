<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
    const breakpoints = {
        sm: 540,
        md: 720,
        lg: 960,
        xl: 1140,
    };

    const sidebar = document.getElementById('sidebarMenu');
    const content = document.getElementsByClassName('content')[0];
    const sidebarToggle = document.querySelector('#sidebar-toggle');

    if (sidebar && document.body.clientWidth < breakpoints.lg) {
        sidebar.addEventListener('shown.bs.collapse', function () {
            document.querySelector('body').style.position = 'fixed';
        });
        sidebar.addEventListener('hidden.bs.collapse', function () {
            document.querySelector('body').style.position = 'relative';
        });
    }
    
    if (sidebar) {
        if (localStorage.getItem('sidebar') === 'contracted') {
            sidebar.classList.add('notransition');
            content.classList.add('notransition');
            sidebarToggle.querySelector('i').classList.remove('ri-menu-fold-fill');
            sidebarToggle.querySelector('i').classList.add('ri-menu-unfold-fill');

            sidebar.classList.add('contracted');

            setTimeout(function () {
                sidebar.classList.remove('notransition');
                content.classList.remove('notransition');
            }, 500);
        } else {
            sidebar.classList.add('notransition');
            content.classList.add('notransition');

            sidebarToggle.querySelector('i').classList.add('ri-menu-fold-fill');
            sidebarToggle.querySelector('i').classList.remove('ri-menu-unfold-fill');

            sidebar.classList.remove('contracted');

            setTimeout(function () {
                sidebar.classList.remove('notransition');
                content.classList.remove('notransition');
            }, 500);
        }

        sidebarToggle.addEventListener('click', function () {
            if (sidebar.classList.contains('contracted')) {
                sidebar.classList.remove('contracted');
                sidebarToggle.querySelector('i').classList.add('ri-menu-fold-fill');
                sidebarToggle.querySelector('i').classList.remove('ri-menu-unfold-fill');
                localStorage.removeItem('sidebar', 'contracted');
            } else {
                sidebar.classList.add('contracted');
                sidebarToggle.querySelector('i').classList.remove('ri-menu-fold-fill');
                sidebarToggle.querySelector('i').classList.add('ri-menu-unfold-fill');
                localStorage.setItem('sidebar', 'contracted');
            }
        });

        sidebar.addEventListener('mouseenter', function () {
            if (localStorage.getItem('sidebar') === 'contracted') {
                if (sidebar.classList.contains('contracted')) {
                    sidebar.classList.remove('contracted');
                } else {
                    sidebar.classList.add('contracted');
                }
            }
        });

        sidebar.addEventListener('mouseleave', function () {
            if (localStorage.getItem('sidebar') === 'contracted') {
                if (sidebar.classList.contains('contracted')) {
                    sidebar.classList.remove('contracted');
                } else {
                    sidebar.classList.add('contracted');
                }
            }
        });
    }
});
</script>

<template>
    <!-- Topbar Start -->
    <nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark px-3 bg-white py-1 mb-4 shadow">
        <div class="container-fluid px-0">
            <div class="d-md-flex d-block justify-content-between w-100" id="navbarSupportedContent">
                <div class="d-flex align-items-center justify-content-end justify-content-lg-start">
                    <!-- Toggle Button -->
                    <button
                        id="sidebar-toggle"
                        class="me-3 border-0 bg-white d-none d-lg-inline-block align-items-center justify-content-center"
                    >
                        <i class="ri-menu-fold-fill fs-3 text-secondary"></i>
                    </button>
                    <!-- View Site Button -->
                    <!--                    <a href="#" target="_blank" class="btn btn-primary d-flex align-items-center">-->
                    <!--                        <span>View Site</span>-->
                    <!--                        <i class="bi bi-globe icon icon-xs ms-2"></i>-->
                    <!--                    </a>-->
                    <!-- Branch Selection Start -->
                    <!-- Branch Selection End -->
                </div>
                <!-- Navbar links -->
                <!-- Notification Start -->
                <div class="d-flex align-items-center justify-content-end mt-2 mt-md-0">
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item dropdown">
                            <!-- Add unread class for red icon -->
                            <a
                                class="nav-link text-dark notification-bell dropdown-toggle position-relative"
                                data-unread-notifications="true"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                data-bs-display="static"
                                aria-expanded="false"
                            >
                                <div
                                    class="position-absolute bg-danger rounded-circle d-flex justify-content-center items-center text-white"
                                    style="
                                        height: 15px;
                                        width: 15px;
                                        font-size: 11px;
                                        right: 13px;
                                        top: 7px;
                                        line-height: 16px;
                                    "
                                >
                                    0
                                </div>
                                <i class="ri-notification-4-fill text-gray-600 fs-5"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-center mt-2 py-0">
                                <div class="list-group list-group-flush">
                                    <a href="#" class="text-center text-primary fw-bold border-bottom border-light py-3"
                                        >Notifications</a
                                    >
                                    <a href="#" class="list-group-item list-group-item-action border-bottom">
                                        <div class="row align-items-center justify-content-center">
                                            No notification available
                                        </div>
                                    </a>
                                    <a
                                        href="#"
                                        class="dropdown-item text-center fw-bold rounded-bottom py-3 d-flex align-items-center justify-content-center"
                                    >
                                        <svg
                                            class="icon icon-xxs text-gray-400 me-1"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path
                                                fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"
                                            ></path>
                                        </svg>
                                        View all
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown ms-lg-3">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <div class="media d-flex align-items-center">
                                    <img
                                        class="avatar rounded-circle border-2"
                                        alt="User Image"
                                        :src="$page.props.auth.user.avatar"
                                    />
                                    <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                                        <span class="mb-0 font-small fw-bolder text-gray-900">{{
                                            $page.props.auth.user.name
                                        }}</span>
                                    </div>
                                    <span class="text-secondary ms-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-caret-down-fill"
                                            viewBox="0 0 16 16"
                                        >
                                            <path
                                                d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"
                                            />
                                        </svg>
                                    </span>
                                </div>
                            </a>
                            <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                                <Link class="dropdown-item d-flex align-items-center" :href="route('profile.edit')">
                                    <i class="ri-user-settings-line fs-5 text-gray-600 me-2"></i>
                                    My Profile
                                </Link>
                                <div role="separator" class="dropdown-divider my-1 border-gray-200"></div>
                                <Link
                                    class="dropdown-item d-flex align-items-center"
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    <i class="ri-logout-circle-r-line fs-5 text-danger me-2"></i>
                                    Logout
                                </Link>
                            </div>
                        </li>
                    </ul>
                    <!-- Notification End -->
                </div>
            </div>
        </div>
    </nav>
    <!-- Topbar End -->
</template>
