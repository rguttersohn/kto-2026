<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';

</script>
<template>
    <header class="flex justify-end gap-x-10 mx-10">
        <Link href="/">Keeping Track Online</Link>
        <nav>
            <ul class="flex gap-x-3">
                <li>
                    <Link href="/indicators">Indicators</Link>
                </li>
                <li>
                    <Link href="/community-profiles">Community Profiles</Link>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <slot/>
    </main>
</template>