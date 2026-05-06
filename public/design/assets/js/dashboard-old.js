function CardChartSmall(canvas, options = {
    labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
    data: [0, 20, 10, 15, 20, 10, 20, 30, 10, 20, 30, 40]
}) {
    const ctx = canvas.getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(221, 255, 225, 1)');
    gradient.addColorStop(0.2, 'rgba(255, 255, 255, 0)');
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

    const data = {
        labels: options.labels,
        datasets: [
            {
                animations: {
                    y: {
                        duration: 2000,
                        delay: 500
                    }
                },
                data: options.data,
                borderColor: '#20BE06',
                borderWidth: 1.5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.5,
                pointRadius: 0,
                pointHoverRadius: 0
            }
        ]
    };

    new Chart(canvas, {
        type: 'line',
        data: data,
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        display: false
                    }
                }
            }
        }
    });
}

function BarChartNormal(canvas, options = {
    labels: ['Prospecting', 'Quote', 'Closure', 'Won', 'Lost', 'Project', 'Channel', 'Corporate'],
    data: [500, 100, 300, 200, 200, 400, 100, 350],
    backgroundColor: [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)'
    ]
}) {
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: options?.labels,
            datasets: [{
                label: 'Votes',
                data: options?.data,
                barThickness: 30,
                borderRadius: 8,
                backgroundColor: options?.backgroundColor
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

const cardChartSmallLabels = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
CardChartSmall(document.getElementById('chartSalesOverview'), { labels: cardChartSmallLabels, data: [0, 20, 10, 15, 20, 10, 20, 30, 10, 20, 30, 40] });
CardChartSmall(document.getElementById('chartProjectOverview'), { labels: cardChartSmallLabels, data: [10, 20, 30, 10, 20, 30, 40, 0, 20, 10, 15, 20] });
CardChartSmall(document.getElementById('chartServiceOverview'), { labels: cardChartSmallLabels, data: [20, 10, 20, 30, 10, 0, 20, 10, 15, 20, 30, 10] });
CardChartSmall(document.getElementById('chartAMCOverview'), { labels: cardChartSmallLabels, data: [10, 20, 30, 0, 20, 10, 15, 20, 10, 20, 30, 20] });

BarChartNormal(document.getElementById('chartDeals'), {
    labels: ['Prospecting', 'Quote', 'Closure', 'Won', 'Lost', 'Project', 'Channel', 'Corporate'],
    data: [500, 100, 300, 200, 200, 400, 100, 300],
    backgroundColor: [
        '#775DD0',
        '#165BAA',
        '#FFAD33',
        '#4BC9C9',
        '#6CC94B',
        '#C94BB8',
        '#D85627',
        '#AA165E'
    ]
});
BarChartNormal(document.getElementById('chartLeads'), {
    labels: ['New', 'Qualified', 'Unqualified', 'Project', 'Channel', 'Corporate', 'Pending', 'Converted'],
    data: [300, 400, 200, 400, 100, 300, 300, 300],
    backgroundColor: [
        '#408C24',
        '#FF7E0E',
        '#E55252',
        '#C94BB8',
        '#D85627',
        '#AA165E',
        '#EE2121',
        '#165BAA'
    ]
});
BarChartNormal(document.getElementById('chartBrandSalesWithMonth'), {
    labels: ['Allied Telesis', 'Avaya', 'Cisco', 'Fortinet', 'Huawei', 'Linksys', 'Netgear', 'Sonicwall', 'Ubiquiti', 'Seceon', 'Apphaz', 'Sisa', 'Securden', 'Xcitium', 'Instasafe'],
    data: [950, 750, 100, 850, 50, 600, 950, 300, 700, 300, 850, 500, 100, 700, 800],
    backgroundColor: [
        '#2683EC',
        '#14B8A6',
        '#F4BB5B',
        '#FA6A6A',
        '#48D96F',
        '#EC7926',
        '#7B42CA',
        '#CC4CD2',
        '#E44833',
        '#56CBEC',
        '#C2A248',
        '#DB5C8F',
        '#498338',
        '#31628B',
        '#A35749'
    ]
});
