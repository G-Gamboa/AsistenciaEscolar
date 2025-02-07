    document.addEventListener("DOMContentLoaded", function() {
        var dropdownItems = document.querySelectorAll('.dropdown-item');
        var solicitudesDiv = document.querySelector('.solicitudes');
        var reporteAsistenciaDiv = document.querySelector('.reporteAsistencia');
        var generarVacacionesDiv = document.querySelector('.generarVacaciones');
        
        function ocultarDivs() {
            solicitudesDiv.style.display = 'none';
            reporteAsistenciaDiv.style.display = 'none';
            generarVacacionesDiv.style.display = 'none';
        }

        ocultarDivs();
        solicitudesDiv.style.display = 'block';

        dropdownItems.forEach(function(item) {
            item.addEventListener('click', function() {
                ocultarDivs();
                var opcionSeleccionada = item.textContent.trim();

                switch (opcionSeleccionada) {
                    case 'Solicitudes Permisos':
                        solicitudesDiv.style.display = 'block';
                        break;
                    case 'Reporte Asistencias':
                        reporteAsistenciaDiv.style.display = 'block';
                        break;
                    case 'Generar Vacaciones':
                        generarVacacionesDiv.style.display = 'block';
                        break;
                    default:
                        ocultarDivs();
                        break;
                }
            });
        });
    });

