let imcChart;

function createOrUpdateChart(data) {
    const ctx = document.getElementById("imcChart").getContext("2d");

    if (imcChart) {
        imcChart.destroy();
    }

    imcChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.map(d => d.fecha),
            datasets: [{
                label: "IMC",
                data: data.map(d => d.imc),
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: "time",
                    time: {
                        unit: "month",
                        displayFormats: {
                            month: 'MMM YYYY'
                        }
                    },
                    reverse: true,
                    title: {
                        display: true,
                        text: 'Fecha'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'IMC'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `IMC: ${context.parsed.y.toFixed(2)}`;
                        }
                    }
                }
            }
        }
    });
}

function loadCompareDetails(id_temp_deportista, retries = 5) {
    $.ajax({
        url: "compare_details.php",
        type: "POST",
        data: { id_temp_deportista: id_temp_deportista },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error("Error del servidor:", response.error);
                alert("Error al cargar los detalles: " + response.error);
            } else {
                $("#compare-details").html(response.html);
                if (response.grafica_datos && response.grafica_datos.length > 0) {
                    createOrUpdateChart(response.grafica_datos);
                } else {
                    $("#chart-container").html('No hay datos suficientes para generar la gráfica.');
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error AJAX:", textStatus, errorThrown);
            if (retries > 0) {
                console.log("Reintentando... Intentos restantes:", retries - 1);
                setTimeout(function() {
                    loadCompareDetails(id_temp_deportista, retries - 1);
                }, 1000); // Espera 1 segundo antes de reintentar
            } else {
                alert("Error al cargar los detalles del deportista. Por favor, intente de nuevo más tarde.");
            }
        }
    });
}

$(document).ready(function() {
    $("#select-alumno").on("change", function() {
        const id_temp_deportista = $(this).val();
        if (id_temp_deportista) {
            loadCompareDetails(id_temp_deportista);
        } else {
            $("#compare-details").html('');
            if (imcChart) {
                imcChart.destroy();
            }
            $("#chart-container").html('');
        }
    });

    $(document).on("click", ".delete-historical-detail", function() {
        var id = $(this).data("id");
        if (confirm("¿Está seguro de que desea eliminar este detalle histórico?")) {
            $.ajax({
                url: "eliminar_detalle.php",
                type: "POST",
                data: {id_detalle: id},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert("Detalle eliminado con éxito");
                        $("#select-alumno").trigger("change");
                    } else {
                        alert("Error al eliminar el detalle: " + (response.error || "Error desconocido"));
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error AJAX al eliminar:", textStatus, errorThrown);
                    alert("Error de conexión al intentar eliminar el detalle. Por favor, intente de nuevo.");
                }
            });
        }
    });
});