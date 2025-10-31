<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <link rel="icon" type="image/png" sizes="32x32" :href="$page.props.settings?.favicon" />
    </Head>

    <div class="tw-bg-gray-50 tw-text-gray-800">
        <slot />
    </div>
</template>

<style></style>
