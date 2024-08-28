// Asegúrate de incluir Chart.js en tu HTML:
// <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

// Función para obtener los datos de pagos
function fetchPaymentData(filterOption = null) {
    // Realiza una solicitud AJAX a tu servidor para obtener los datos de tab_pagos
    fetch('graficas/get_datos.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text(); // Obtener el texto primero para depuración
        })
        .then(text => {
            console.log('Respuesta del servidor:', text); // Ver el contenido recibido
            try {
                const data = JSON.parse(text); // Intentar convertir el texto a JSON
                updateChart(processPaymentData(data, filterOption));
            } catch (e) {
                console.error('Error al parsear JSON:', e);
            }
        })
        .catch(error => console.log('Error:', error));
}

function processPaymentData(data, filterOption) {
    const monthlyData = {};
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    data.forEach(payment => {
        const date = new Date(payment.FECHA_PAGO); // Asegúrate de usar el campo correcto
        const month = date.getMonth();
        const amount = parseFloat(payment.MONTO);

        if (!monthlyData[month]) {
            monthlyData[month] = { totalAmount: 0, count: 0 };
        }

        monthlyData[month].totalAmount += amount;
        monthlyData[month].count += 1;
    });

    let processedData = monthNames.map((month, index) => ({
        month: month,
        totalPayments: Math.round(monthlyData[index] ? monthlyData[index].totalAmount : 0),
        paymentCount: monthlyData[index] ? monthlyData[index].count : 0
    }));

    // Aplicar filtro si se selecciona uno
    if (filterOption) {
        switch (filterOption) {
            case 'ultimoPago':
                processedData = processedData.filter(data => data.paymentCount > 0).slice(-1);
                break;
            case 'mesAlto':
                processedData = processedData.reduce((max, data) => data.totalPayments > max.totalPayments ? data : max, processedData[0]);
                processedData = [processedData];
                break;
            case 'todo':
            default:
                // No se aplica filtro, se muestra todo
                break;
        }
    }

    console.log('Datos procesados:', processedData); // Imprime los datos procesados
    return processedData;
}

// Variable global para almacenar la instancia del gráfico
let myChart;

// Función para actualizar el gráfico
function updateChart(monthlyData) {
    const ctx = document.getElementById('Chart').getContext('2d');

    if (myChart) {
        myChart.destroy();
    }

    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(data => data.month),
            datasets: [{
                label: 'Total de Pagos Mensuales',
                data: monthlyData.map(data => data.totalPayments),
                fill: false,
                borderColor: 'blue', // Cambia la línea a color azul
                borderWidth: 3, // Aumenta el grosor de la línea
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    grid: {
                        display: false, // Quitar líneas del grid
                    },
                    ticks: {
                        stepSize: 10,
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    },
                    title: {
                        display: true,
                        text: 'Total de Pagos ($)'
                    }
                },
                x: {
                    grid: {
                        display: false, // Quitar líneas del grid
                    },
                    title: {
                        display: true,
                        text: 'Meses'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Desglose de Pagos Mensuales'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            const count = monthlyData[context.dataIndex].paymentCount;
                            return `${label}: $${value.toFixed(2)} (${count} pagos)`;
                        }
                    }
                }
            }
        }
    });
}

// Función para manejar el filtrado
function handleFilter(option) {
    fetchPaymentData(option);
}



// Función para actualizar el gráfico periódicamente
function setupPeriodicUpdate() {
    fetchPaymentData(); // Actualiza inmediatamente
    setInterval(fetchPaymentData, 60000); // Actualiza cada minuto (ajusta según tus necesidades)
}

// Inicia la actualización periódica cuando se carga la página
document.addEventListener('DOMContentLoaded', setupPeriodicUpdate);
