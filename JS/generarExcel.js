$(document).ready(function() {
    $('#generarVacacionesBtn').on('click', function() {
        $.ajax({
            url: 'controladores/generarExcel.php',
            type: 'GET',
            success: function(response) {
                console.log(response);
                let data = response.data;
                let filename = response.filename;

                $('#vistaPreviaTabla tbody').empty();
                data.forEach(row => {
                    $('#vistaPreviaTabla tbody').append(
                        `<tr>
                            <td>${row.nombre}</td>
                            <td>${row.apellido}</td>
                            <td>${row.correo}</td>
                            <td>${row.vacaciones}</td>
                            <td>${row.vacacionesTotales}</td>
                        </tr>`
                    );
                });

                $('#descargarExcelLink').attr('href', filename).show();
                $('#confirmarBtn').show();
                $('.generarVacaciones').show();
            },
            error: function(error) {
                console.error('Error al generar el Excel:', error);
            }
        });
    });

    $('#confirmarBtn').on('click', function() {
        $.ajax({
            url: 'enviarCorreos.php',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    alert('Correos enviados con éxito');
                } else {
                    alert('Hubo un error al enviar los correos');
                }
            },
            error: function(error) {
                console.error('Error al enviar los correos:', error);
            }
        });
    });
});