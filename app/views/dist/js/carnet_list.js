$(function () {
    // Inicializar DataTable
    $("#example1").DataTable({
        "order": [[4, "asc"]], // Ordena por la columna "Impresion" (Pendiente primero)
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "language": {
            "decimal": "",
            "emptyTable": "No hay carnets para emitir este mes",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ carnets",
            "infoEmpty": "Mostrando 0 a 0 de 0 carnets",
            "infoFiltered": "(filtrado de _MAX_ carnets totales)",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ carnets",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron carnets",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });

    // Cambiar encabezado "Condicion" por "Impresion" en esta vista.
    $('#example1 thead th').eq(4).text('Impresion');
    
    // ✅ FUNCIÓN PARA CONSULTAR CARNETS PENDIENTES VÍA AJAX
    function consultarCarnetsPendientes(callback) {
        $.ajax({
            url: APP_URL + 'app/ajax/carnetAjax.php',
            type: 'POST',
            data: {
                modulo_carnet: 'imprimir_carnetspendientes'
            },
            dataType: 'json',
            success: function(response) {
                if(response.tipo === 'success') {
                    callback(response.total);
                } else {
                    console.error('Error al obtener carnets pendientes');
                    callback(0);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                callback(0);
            }
        });
    }
    
    // ✅ ACTUALIZAR CONTADOR AL CARGAR LA PÁGINA
    consultarCarnetsPendientes(function(total) {
        $('#contadorCarnets').html(total);
    });
    
    // ✅ BOTÓN IMPRIMIR TODOS CON CONSULTA AJAX
    $('#btnImprimirTodos').on('click', function() {
        var btn = $(this);
        var badge = $('#contadorCarnets');
        
        // Deshabilitar botón mientras consulta
        btn.prop('disabled', true);
        badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Consultar carnets pendientes en tiempo real
        consultarCarnetsPendientes(function(totalPendientes) {
            // Actualizar badge
            badge.html(totalPendientes);
            
            // Habilitar botón
            btn.prop('disabled', false);
            
            // Validar si hay carnets
            if(totalPendientes === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin carnets pendientes',
                    html: `
                        <div style="padding: 15px;">
                            <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i>
                            <p style="margin-top: 15px; font-size: 16px;">
                                No hay carnets pendientes de impresión este mes
                            </p>
                            <p style="color: #6c757d; font-size: 14px;">
                                Todos los carnets ya han sido generados
                            </p>
                        </div>
                    `,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            // Mostrar modal de confirmación
            Swal.fire({
            title: '¿Imprimir carnets?',
            html: `
                <div style="text-align: left; padding: 12px;">
                    <p style="margin-bottom: 10px;">
                        <i class="fas fa-print" style="color: #28a745; font-size: 18px;"></i> 
                        <strong>Carnets pendientes de impresión:</strong>
                    </p>

                    <p style="text-align: center; margin: 12px 0;">
                        <span style="color: #28a745; font-size: 42px; font-weight: bold;">${totalPendientes}</span>
                    </p>

                    <hr style="margin: 12px 0;">

                    <p style="margin-bottom: 6px;">
                        <i class="fas fa-file-pdf" style="color: #dc3545;"></i> 
                        Se generará un archivo PDF
                    </p>
                    <p style="margin-bottom: 6px;">
                        <i class="fas fa-layer-group" style="color: #17a2b8;"></i> 
                        Formato: <strong>10 carnets por hoja A4</strong>
                    </p>
                   <p style="margin-bottom: 0;">
                        <i class="fas fa-calendar-alt" style="color: #ffc107;"></i> 
                        Mes: <strong>${MES_ACTUAL}</strong>
                    </p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> Sí, imprimir',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            customClass: {
                confirmButton: 'btn btn-success btn-sm mx-1',
                cancelButton: 'btn btn-secondary btn-sm mx-1'
            },
            buttonsStyling: false,
            width: '420px',
            padding: '1em'

            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar mensaje de generación
                    Swal.fire({
                        title: 'Generando PDF...',
                        html: `
                            <div style="padding: 20px;">
                                <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #28a745;"></i>
                                <p style="margin-top: 15px; font-size: 16px;">
                                    Preparando <strong>${totalPendientes}</strong> carnets para impresión
                                </p>
                                <p style="color: #6c757d; font-size: 14px; margin-top: 10px;">
                                    Por favor espere...
                                </p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Preparar lote exacto y abrir PDF en nueva ventana
                    var ventanaPDF = window.open('', '_blank');
                    if(ventanaPDF) {
                        ventanaPDF.document.write('<p style="font-family: Arial; padding: 20px;">Preparando carnets...</p>');
                    }

                    $.ajax({
                        url: APP_URL + 'app/ajax/carnetAjax.php',
                        type: 'POST',
                        data: {
                            modulo_carnet: 'preparar_impresion_mensual'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if(response.tipo === 'redireccionar' && response.url) {
                                if(ventanaPDF) {
                                    ventanaPDF.location.href = response.url;
                                } else {
                                    window.open(response.url, '_blank');
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'PDF Generado',
                                    html: `
                                        <div style="padding: 15px;">
                                            <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i>
                                            <p style="margin-top: 15px; font-size: 16px;">
                                                Los carnets se han enviado a impresion
                                            </p>
                                        </div>
                                    `,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                if(ventanaPDF) {
                                    ventanaPDF.close();
                                }

                                Swal.fire({
                                    icon: response.icono || 'info',
                                    title: response.titulo || 'Aviso',
                                    text: response.texto || 'No hay carnets para imprimir',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    btn.prop('disabled', false);
                                });
                            }
                        },
                        error: function() {
                            if(ventanaPDF) {
                                ventanaPDF.close();
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo preparar el PDF de carnets',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                btn.prop('disabled', false);
                            });
                        }
                    });
                }
            });
        });
    });
    
    // Seleccionar/deseleccionar todos
    $('#seleccionarTodos').on('change', function() {
        $('.chk-reimpresion').prop('checked', $(this).prop('checked'));
    });
    
    // Actualizar checkbox principal si se deselecciona alguno
    $('.chk-reimpresion').on('change', function() {
        if(!$(this).prop('checked')) {
            $('#seleccionarTodos').prop('checked', false);
        }
        
        // Si todos están marcados, marcar el principal
        if($('.chk-reimpresion:checked').length === $('.chk-reimpresion').length) {
            $('#seleccionarTodos').prop('checked', true);
        }
    });
});
