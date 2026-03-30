<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import StudentProfilePanel from '@/components/student/StudentProfilePanel.vue';
import StudentLayout from '@/layouts/StudentLayout.vue';

defineProps<{
    profile: {
        name: string;
        email: string;
        avatar: string | null;
        points: number;
        bio: string;
        bio_raw?: string | null;
        links: {
            github: string | null;
            linkedin: string | null;
        };
        status: {
            completed_activities: number;
            ranking_position: number;
        };
        classroom: {
            name: string;
            code: string;
            point_of_school: string;
            teacher: string;
        };
    };
    viewer_mode: boolean;
    update_url: string;
}>();
</script>

<template>
    <Head title="Perfil do Aluno" />

    <StudentLayout>
        <StudentProfilePanel :profile="profile" :viewer-mode="viewer_mode" :editable="true" :update-url="update_url" />
    </StudentLayout>
</template>
