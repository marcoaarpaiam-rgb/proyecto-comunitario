<template>
    <div class="expirado-page min-vh-100 d-flex flex-column bg-light" style="font-family: system-ui, -apple-system, sans-serif;">
        <!-- Barra Superior Institucional -->
        <header class="bg-dark text-white py-3 border-bottom border-danger border-3 shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <img :src="'/images/images.png'" alt="Logo UPTP" style="height: 48px; object-fit: contain;" />
                    <div>
                        <h1 class="h5 m-0 fw-bold text-white lh-1">UPTP "Juan de Jesús Montilla"</h1>
                        <small class="text-white-50">Portal de Evaluación de Socialización &bull; PNFI</small>
                    </div>
                </div>
                <a href="/" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-house-door me-1"></i> Inicio
                </a>
            </div>
        </header>

        <!-- Contenedor Principal Centrado -->
        <main class="container my-auto py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg border-2 border-danger rounded-4 overflow-hidden bg-white">
                        <div class="card-body p-4 p-md-5 text-center">
                            <!-- Ícono de Expiración / Alerta -->
                            <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle text-danger mb-4 shadow-sm" style="font-size: 3rem; width: 90px; height: 90px; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history"></i>
                            </div>

                            <h2 class="h3 fw-bold text-dark mb-2">
                                {{ invalido ? 'Enlace No Válido' : 'Enlace de Evaluación Expirado' }}
                            </h2>

                            <div class="badge bg-danger text-white text-uppercase px-3 py-2 fs-6 rounded-pill mb-4 shadow-sm">
                                <i class="bi bi-hourglass-bottom me-1"></i> Vigencia de 12 Horas Superada
                            </div>

                            <div class="alert alert-danger text-start p-3 rounded-3 mb-4 border-danger-subtle">
                                <p class="mb-2 fw-semibold text-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                    {{ mensaje || 'Este enlace temporal de evaluación ha superado su vigencia de 12 horas o no es válido.' }}
                                </p>
                                <ul class="mb-0 small text-dark ps-3" v-if="expirado">
                                    <li v-if="codigo_equipo" class="mb-1"><strong>Equipo:</strong> {{ codigo_equipo }}</li>
                                    <li v-if="expira_formateado" class="mb-1"><strong>Fecha de caducidad:</strong> {{ expira_formateado }}</li>
                                    <li v-if="tiempo_transcurrido"><strong>Estado:</strong> Caducó {{ tiempo_transcurrido }}</li>
                                </ul>
                            </div>

                            <div class="card p-3 bg-light border-0 rounded-3 mb-4 text-start">
                                <h6 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-shield-check text-success me-1"></i> ¿Qué debo hacer ahora?
                                </h6>
                                <p class="small text-muted mb-0" style="line-height: 1.6;">
                                    Por motivos de seguridad académica y auditoría institucional, los enlaces de evaluación para jurados y responsables de la comunidad expiran a las 12 horas.
                                    <strong>Contacte al Docente de Proyecto</strong> de este equipo para que genere y le envíe un nuevo enlace temporal actualizado.
                                </p>
                            </div>

                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                                <a href="/" class="btn btn-dark btn-lg px-4 py-2 fw-bold rounded-pill">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Ir al Inicio
                                </a>
                                <a href="javascript:location.reload()" class="btn btn-outline-secondary btn-lg px-4 py-2 fw-bold rounded-pill">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reintentar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
            <div class="container">
                &copy; {{ new Date().getFullYear() }} SISCON-PST &bull; Universidad Politécnica Territorial de Portuguesa "Juan de Jesús Montilla".
            </div>
        </footer>
    </div>
</template>

<script setup>
defineProps({
    invalido: Boolean,
    expirado: Boolean,
    expira_formateado: String,
    tiempo_transcurrido: String,
    codigo_equipo: String,
    mensaje: String,
});
</script>

<style scoped>
.expirado-page {
    color: #111111;
}
</style>
