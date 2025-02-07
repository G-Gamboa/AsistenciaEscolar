function cargarSolicitudes(estado_permiso) {
    fetch('controladores/obtenerPermisos.php?estado_permiso=' + estado_permiso)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                document.getElementById("tabla_solicitudes").innerHTML = "";
                console.log(data)
                data.data.forEach(permi => {
                    var nombreCompleto = permi.nombre + ' ' + permi.apellido;
                    var fila = `
                        <tr>
                            <td>${nombreCompleto}</td>
                            <td>${permi.fecha_inicio}</td>
                            <td>${permi.fecha_fin}</td>
                            <td>${permi.descripcion}</td>
                            <td>`;
                            if(estado_permiso==1){
                                fila+=`
                                    <form method="post" style="display:inline;">
                                    <input type="hidden" name="id_permiso" value="${permi.id}">
                                    <input type="hidden" name="action" value="aprobar">
                                    <button type="submit" class="btn btn-success opciones">Aprobar</button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id_permiso" value="${permi.id}">
                                    <input type="hidden" name="action" value="rechazar">
                                    <button type="submit" class="btn btn-danger opciones">Rechazar</button>
                                </form>`;
                            }
                            else if(estado_permiso==2){
                                fila+=`
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id_permiso" value="${permi.id}">
                                    <input type="hidden" name="action" value="pendiente">
                                    <button type="submit" class="btn btn-primary opciones">Pendiente</button>
                                </form>

                                <form method="post" style="display:inline;">
                                <input type="hidden" name="id_permiso" value="${permi.id}">
                                <input type="hidden" name="action" value="rechazar">
                                <button type="submit" class="btn btn-danger opciones">Rechazar</button>
                            </form>`;
                            }
                            else if(estado_permiso==3){
                                fila+=`
                                <form method="post" style="display:inline;">
                                <input type="hidden" name="id_permiso" value="${permi.id}">
                                <input type="hidden" name="action" value="pendiente">
                                <button type="submit" class="btn btn-primary opciones">Pendiente</button>
                            </form>

                                <form method="post" style="display:inline;">
                                <input type="hidden" name="id_permiso" value="${permi.id}">
                                <input type="hidden" name="action" value="aprobar">
                                <button type="submit" class="btn btn-success opciones">Aprobar</button>
                                </form>`;
                            }

                            fila+=`
                            </td>
                        </tr>`;
                    document.getElementById("tabla_solicitudes").innerHTML += fila;
                });
            } else {
                console.error('Error en la respuesta:', data.message);
            }
        })
        .catch(error => console.error('Error al cargar las solicitudes:', error));
}

document.addEventListener("DOMContentLoaded", function() {
    var estado_permiso = document.getElementById("estado_permiso").value;
    console.log(estado_permiso);
    cargarSolicitudes(estado_permiso);
});
