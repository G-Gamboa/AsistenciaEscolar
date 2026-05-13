function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function botonesPermiso(estado_permiso, id) {
    const esc = escapeHtml(id);
    if (estado_permiso == 1) {
        return `
            <form method="post" style="display:inline;">
                <input type="hidden" name="id_permiso" value="${esc}">
                <input type="hidden" name="action" value="aprobar">
                <button type="submit" class="btn btn-success opciones">Aprobar</button>
            </form>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id_permiso" value="${esc}">
                <input type="hidden" name="action" value="rechazar">
                <button type="submit" class="btn btn-danger opciones">Rechazar</button>
            </form>`;
    }
    if (estado_permiso == 2) {
        return `
            <form method="post" style="display:inline;">
                <input type="hidden" name="id_permiso" value="${esc}">
                <input type="hidden" name="action" value="pendiente">
                <button type="submit" class="btn btn-primary opciones">Pendiente</button>
            </form>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id_permiso" value="${esc}">
                <input type="hidden" name="action" value="rechazar">
                <button type="submit" class="btn btn-danger opciones">Rechazar</button>
            </form>`;
    }
    if (estado_permiso == 3) {
        return `
            <form method="post" style="display:inline;">
                <input type="hidden" name="id_permiso" value="${esc}">
                <input type="hidden" name="action" value="pendiente">
                <button type="submit" class="btn btn-primary opciones">Pendiente</button>
            </form>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id_permiso" value="${esc}">
                <input type="hidden" name="action" value="aprobar">
                <button type="submit" class="btn btn-success opciones">Aprobar</button>
            </form>`;
    }
    return '';
}

function cargarSolicitudes(estado_permiso) {
    fetch('controladores/obtenerPermisos.php?estado_permiso=' + encodeURIComponent(estado_permiso))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                const tbody = document.getElementById("tabla_solicitudes");
                tbody.innerHTML = "";
                data.data.forEach(permi => {
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${escapeHtml(permi.nombre + ' ' + permi.apellido)}</td>
                        <td>${escapeHtml(permi.fecha_inicio)}</td>
                        <td>${escapeHtml(permi.fecha_fin)}</td>
                        <td>${escapeHtml(permi.descripcion)}</td>
                        <td>${botonesPermiso(estado_permiso, permi.id)}</td>`;
                    tbody.appendChild(fila);
                });
            } else {
                console.error('Error en la respuesta:', data.message);
            }
        })
        .catch(error => console.error('Error al cargar las solicitudes:', error));
}

document.addEventListener("DOMContentLoaded", function() {
    const estado_permiso = document.getElementById("estado_permiso").value;
    cargarSolicitudes(estado_permiso);
});
