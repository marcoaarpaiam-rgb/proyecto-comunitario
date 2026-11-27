<template>
    <div
        v-if="mostrarPaginador"
        class="d-flex flex-wrap justify-content-between align-items-center py-2 px-3 border-top bg-light-subtle rounded-bottom"
    >
        <div class="text-muted small mb-2 mb-sm-0">
            Mostrando
            <span class="fw-semibold">{{ paginador?.from ?? (links.length > 0 ? 1 : 0) }}</span>
            a
            <span class="fw-semibold">{{ paginador?.to ?? (paginador?.total ?? links.length) }}</span>
            de
            <span class="fw-semibold">{{ paginador?.total ?? links.length }}</span>
            registros ({{ paginador?.per_page ?? 3 }} por página)
        </div>

        <nav aria-label="Navegación de páginas" v-if="links && links.length > 3">
            <ul class="pagination pagination-sm mb-0">
                <li
                    v-for="(link, index) in links"
                    :key="index"
                    class="page-item"
                    :class="{
                        active: link.active,
                        disabled: !link.url,
                    }"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="page-link"
                        :class="{
                            'bg-danger border-danger text-white': link.active,
                            'text-dark': !link.active,
                        }"
                        preserve-scroll
                        preserve-state
                        v-html="limpiarEtiqueta(link.label)"
                    />
                    <span
                        v-else
                        class="page-link text-muted"
                        v-html="limpiarEtiqueta(link.label)"
                    />
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    links: {
        type: Array,
        default: () => [],
    },
    paginador: {
        type: Object,
        default: () => ({}),
    },
});

const links = computed(() => {
    if (props.links && props.links.length > 0) return props.links;
    if (props.paginador && props.paginador.links) return props.paginador.links;
    return [];
});

const mostrarPaginador = computed(() => {
    if (props.paginador && typeof props.paginador === "object") {
        if (Number(props.paginador.total) > 0) return true;
        if (Array.isArray(props.paginador.links) && props.paginador.links.length > 3) return true;
    }
    return links.value && links.value.length > 3;
});

const limpiarEtiqueta = (label) => {
    if (label === null || label === undefined) return "";
    const str = String(label);
    if (str.includes("Previous") || str.includes("&laquo;")) {
        return '<i class="bi bi-chevron-left"></i> Anterior';
    }
    if (str.includes("Next") || str.includes("&raquo;")) {
        return 'Siguiente <i class="bi bi-chevron-right"></i>';
    }
    return str;
};
</script>

<style scoped>
.page-link {
    font-size: 0.85rem;
    padding: 0.25rem 0.6rem;
}
.page-item.active .page-link {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #fff !important;
}
</style>
