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
                barThickness: 14,
                borderRadius: 7,
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
                    },
                    border: {
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

function BarChartGrouped(canvas, options = {
    labels: ['One', 'Two', 'Three'],
    datasets: [{
        label: 'Label',
        data: [500, 100, 300],
        backgroundColor: '#47BD3C'
    }]
}) {
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: options?.labels,
            datasets: options?.datasets?.map(item => {
                return {
                    label: item?.label,
                    data: item?.data,
                    borderRadius: 5,
                    backgroundColor: item?.backgroundColor,
                    barPercentage: 0.5,
                    categoryPercentage: 0.5
                };
            })
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

function DoughnutChartNormal(canvas, options = {
    labels: ['Amount', 'No Of Invoice', '%'],
    data: [70, 18, 12],
    backgroundColor: [
        '#4E79A7',
        '#F28E2B',
        '#E15759'
    ]
}) {
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: options?.labels,
            datasets: [{
                label: 'Dataset 1',
                data: options?.data,
                backgroundColor: options?.backgroundColor
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: {
                    display: false
                }
            },
            layout: {
                padding: 0
            }
        }
    });
}

const cardChartSmallLabels = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
CardChartSmall(document.getElementById('chartTotalTarget'), { labels: cardChartSmallLabels, data: [0, 20, 10, 15, 20, 10, 20, 30, 10, 20, 30, 40] });
CardChartSmall(document.getElementById('chartRevenue'), { labels: cardChartSmallLabels, data: [10, 20, 30, 10, 20, 30, 40, 0, 20, 10, 15, 20] });
CardChartSmall(document.getElementById('chartOnProcessDeal'), { labels: cardChartSmallLabels, data: [20, 10, 20, 30, 10, 0, 20, 10, 15, 20, 30, 10] });
CardChartSmall(document.getElementById('chartForecast'), { labels: cardChartSmallLabels, data: [10, 20, 30, 0, 20, 10, 15, 20, 10, 20, 30, 20] });

BarChartGrouped(document.getElementById('chartSales'), {
    labels: ['Amount', 'GP', 'GP %', 'No Deal', 'NC', 'OC', 'IC', 'NC Ratio'],
    datasets: [{
        label: 'Revenue',
        data: [500, 500, 500, 500, 500, 500, 500, 500],
        backgroundColor: '#47BD3C'
    },{
        label: 'On Process',
        data: [300, 300, 300, 300, 300, 300, 300, 300],
        backgroundColor: '#C0B742'
    },{
        label: 'Forecast',
        data: [400, 400, 400, 400, 400, 400, 400, 400],
        backgroundColor: '#250EF4'
    }]
});

DoughnutChartNormal(document.getElementById('chartReceivableOutstanding1'), {
    labels: ['Amount', 'No Of Invoice', '%'],
    data: [70, 18, 12],
    backgroundColor: [
        '#4E79A7',
        '#F28E2B',
        '#E15759'
    ]
});
DoughnutChartNormal(document.getElementById('chartReceivableOutstanding2'), {
    labels: ['Amount', 'No Of Invoice', '%'],
    data: [70, 18, 12],
    backgroundColor: [
        '#4E79A7',
        '#F28E2B',
        '#E15759'
    ]
});
DoughnutChartNormal(document.getElementById('chartReceivableOutstanding3'), {
    labels: ['Amount', 'No Of Invoice', '%'],
    data: [70, 18, 12],
    backgroundColor: [
        '#4E79A7',
        '#F28E2B',
        '#E15759'
    ]
});
DoughnutChartNormal(document.getElementById('chartReceivableOutstanding4'), {
    labels: ['Amount', 'No Of Invoice', '%'],
    data: [70, 18, 12],
    backgroundColor: [
        '#4E79A7',
        '#F28E2B',
        '#E15759'
    ]
});

DoughnutChartNormal(document.getElementById('chartCustomerDatabase'), {
    labels: ['Active Customers', 'In Active Customers', 'Potential Customers', 'Open Customers', 'Active Conversion Rate'],
    data: [27, 23, 20, 22, 18],
    backgroundColor: [
        '#86BCB6',
        '#EDC948',
        '#4E79A7',
        '#F28E2B',
        '#E15759'
    ]
});

BarChartNormal(document.getElementById('chartTopBrand'), {
    labels: ['Avaya', 'Fortinet', 'Grand Stream', 'Allied Tellssies', 'Arruba Qio', 'Avaya', 'Dream Nest', 'Fortinet', 'Avaya'],
    data: [50, 70, 40, 100, 90, 80, 80, 80, 80],
    backgroundColor: [
        '#3A4DE9',
        '#40B9AF',
        '#4E526B',
        '#A63AE9',
        '#FB975B',
        '#7797CC',
        '#24AD20',
        '#9E2828',
        '#15489D'
    ]
});
BarChartNormal(document.getElementById('chartBrandStock'), {
    labels: ['Avaya', 'Fortinet', 'Grand Stream', 'Allied Tellssies', 'Arruba Qio', 'Avaya', 'Dream Nest', 'Fortinet', 'Avaya'],
    data: [50, 70, 40, 100, 90, 80, 80, 80, 80],
    backgroundColor: [
        '#3A4DE9',
        '#40B9AF',
        '#4E526B',
        '#A63AE9',
        '#FB975B',
        '#7797CC',
        '#24AD20',
        '#9E2828',
        '#15489D'
    ]
});
