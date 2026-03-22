<script setup lang="ts">
import { motion } from 'motion-v';

const props = defineProps<{
    className?: string;
    delay?: number;
}>();
</script>

<template>
    <motion.div
        :class="props.className"
        :initial="{ opacity: 0, y: 24, filter: 'blur(10px)' }"
        :animate="{ opacity: 1, y: 0, filter: 'blur(0px)' }"
        :transition="{
            duration: 0.45,
            ease: 'easeOut',
            delay: props.delay ?? 0,
        }"
    >
        <slot />
    </motion.div>
</template>
