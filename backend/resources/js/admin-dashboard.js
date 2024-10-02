import Chart from 'chart.js/auto';
import axios from 'axios';

// Fetch and render Total Users Chart
axios.get('/admin/dashboard/users')
    .then(function (response) {
        var data = response.data;
        var ctx = document.getElementById('totalUsersChart').getContext('2d');

        var gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, '#36a2eb');
        gradient1.addColorStop(1, '#8ecae6');

        var gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, '#ff6384');
        gradient2.addColorStop(1, '#fb8500');

        var gradient3 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient3.addColorStop(0, '#4bc0c0');
        gradient3.addColorStop(1, '#00f5d4');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Users', 'Customers', 'Service Providers'],
                datasets: [{
                    label: 'Users Count',
                    data: [data.total, data.customers, data.serviceProviders],
                    backgroundColor: [gradient1, gradient2, gradient3],
                    borderColor: ['#36a2eb', '#ff6384', '#4bc0c0'],
                    borderWidth: 2,
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeInOutBounce'
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#333',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#fff',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.label}: ${tooltipItem.raw} users`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: '#ccc'
                        },
                        ticks: {
                            color: '#333'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#333'
                        }
                    }
                }
            }
        });
    });


// Fetch and render Booking Heatmap (Location)
axios.get('/admin/dashboard/bookings-heatmap')  // Use the exact route
    .then(function (response) {
        var data = response.data;
        var locations = data.map(item => item.location);
        var bookings = data.map(item => item.total);

        var ctx = document.getElementById('bookingHeatmapChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: locations,
                datasets: [{
                    label: 'Bookings by Location',
                    data: bookings,
                    backgroundColor: '#4bc0c0',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });

// Fetch and render Growth Rate Chart
axios.get('/admin/dashboard/growth-rate')
    .then(function (response) {
        var growthData = response.data.growthData;

        var ctx = document.getElementById('growthRateChart').getContext('2d');

        // Create gradient for line fill
        var gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(75, 192, 192, 0.4)');
        gradient.addColorStop(1, 'rgba(75, 192, 192, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['6 months ago', '5 months ago', '4 months ago', '3 months ago', '2 months ago', 'Last month'],
                datasets: [{
                    label: 'Growth Rate (%)',
                    data: growthData,
                    backgroundColor: gradient,  // Gradient fill under the line
                    borderColor: '#4bc0c0',  // Color of the line
                    borderWidth: 2,
                    fill: true,  // Enable gradient fill
                    pointBackgroundColor: '#fff',  // White background for points
                    pointBorderColor: '#4bc0c0',  // Border color for points
                    pointRadius: 6,  // Make the points larger
                    pointHoverRadius: 8,  // Larger points on hover
                    borderDash: [5, 5],  // Dotted line
                    tension: 0.4  // Smooth the curve of the line
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + '%';  // Show percentage on Y-axis
                            },
                            color: '#333',  // Y-axis text color
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',  // Light grid lines for Y-axis
                        }
                    },
                    x: {
                        grid: {
                            display: false  // No grid lines for X-axis
                        },
                        ticks: {
                            color: '#333'  // X-axis text color
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',  // Custom tooltip color
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#4bc0c0',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function (tooltipItem) {
                                return `${tooltipItem.raw}% growth`;
                            }
                        }
                    },
                    legend: {
                        display: false  // Disable legend for simplicity
                    },
                    annotation: {
                        annotations: growthData.map((value, index) => ({
                            type: 'line',
                            mode: 'horizontal',
                            scaleID: 'y',
                            value: value,
                            borderColor: '#ff6384',
                            borderWidth: 1,
                            label: {
                                enabled: true,
                                content: `${value}%`,
                                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                                position: 'center',
                                yAdjust: -15
                            }
                        }))
                    }
                },
                animation: {
                    duration: 1500,  // Smooth transitions
                    easing: 'easeInOutQuart'  // Smooth easing for animations
                },
                hover: {
                    mode: 'index',
                    intersect: false  // Hover over the whole area, not just points
                }
            }
        });
    });


// Fetch and render Top Service Categories Chart
axios.get('/admin/dashboard/top-categories')
    .then(function (response) {
        var data = response.data;
        var categories = data.map(item => item.service_type);
        var totals = data.map(item => item.total);

        // Array of emojis corresponding to each service category
        var emojis = ['🔧', '🔩', '🎨', '💡', '🛠️'];

        var ctx = document.getElementById('topServiceCategoriesChart').getContext('2d');

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Top Service Categories',
                    data: totals,
                    backgroundColor: ['#36a2eb', '#ff6384', '#4bc0c0', '#ffcd56', '#c45850'],
                    hoverBackgroundColor: ['#36a2eb', '#ff6384', '#4bc0c0', '#ffcd56', '#c45850'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                let percentage = ((tooltipItem.raw / totals.reduce((a, b) => a + b, 0)) * 100).toFixed(2);
                                return `${tooltipItem.label}: ${percentage}% (${tooltipItem.raw} services)`;
                            }
                        }
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 20,
                            fontSize: 14,
                            fontColor: '#333'
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            },
            plugins: [{
                afterDraw: function (chart) {
                    var ctx = chart.ctx;
                    var chartArea = chart.chartArea;
                    var centerX = (chartArea.left + chartArea.right) / 2;
                    var centerY = (chartArea.top + chartArea.bottom) / 2;
                    var radius = (chart.outerRadius + chart.innerRadius) / 2;

                    chart.data.datasets[0].data.forEach((value, index) => {
                        var angle = (chart.getDatasetMeta(0).data[index].startAngle + chart.getDatasetMeta(0).data[index].endAngle) / 2;

                        // Calculate emoji position at the center of the slice
                        var emojiX = centerX + (Math.cos(angle) * radius * 0.5);
                        var emojiY = centerY + (Math.sin(angle) * radius * 0.5);

                        // Draw emoji text in the slice
                        ctx.font = '24px Arial';
                        ctx.textAlign = 'center';
                        ctx.fillText(emojis[index], emojiX, emojiY);  // Add emoji from array
                    });
                }
            }]
        });
    });


    axios.get('/admin/dashboard/total-bookings')
    .then(function (response) {
        var data = response.data;

        var ctx = document.getElementById('totalBookingsChart').getContext('2d');

        // Custom gradient for a glassy, 3D effect
        var gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, 'rgba(75, 192, 192, 0.8)');
        gradient1.addColorStop(1, 'rgba(75, 192, 192, 0.3)');

        var gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(255, 99, 132, 0.8)');
        gradient2.addColorStop(1, 'rgba(255, 99, 132, 0.3)');

        var gradient3 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient3.addColorStop(0, 'rgba(255, 205, 86, 0.8)');
        gradient3.addColorStop(1, 'rgba(255, 205, 86, 0.3)');

        // 3D glassy doughnut chart with center text and interactive glow effect
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Total Bookings', 'Completed', 'Pending'],
                datasets: [{
                    label: 'Bookings',
                    data: [data.total, data.completed, data.pending],
                    backgroundColor: [gradient1, gradient2, gradient3],
                    hoverBackgroundColor: ['#4bc0c0', '#ff6384', '#ffcd56'],
                    hoverBorderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let total = data.total;
                                let value = tooltipItem.raw;
                                let percentage = ((value / total) * 100).toFixed(2);
                                return `${tooltipItem.label}: ${percentage}% (${value} bookings)`;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            fontSize: 16,
                            fontColor: '#333',
                            usePointStyle: true  // Circles instead of squares for legend
                        }
                    },
                    datalabels: {
                        display: true,
                        formatter: (value, ctx) => {
                            let percentage = (value / data.total) * 100;
                            return percentage.toFixed(1) + '%';  // Show percentage inside slices
                        },
                        color: '#fff',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    doughnutlabel: {
                        labels: [{
                            text: 'Hover a slice!',
                            font: {
                                size: 24,
                                weight: 'bold'
                            },
                            color: '#333'
                        }]
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1500,  // Slow down the animations for fluidity
                    easing: 'easeInOutQuart'  // Smooth easing
                },
                hover: {
                    onHover: function (e, elements) {
                        if (elements.length) {
                            let segment = elements[0];
                            segment.element.outerRadius += 10;  // Expand the hovered segment
                            var hoveredLabel = segment.element.$context.raw;  // Get the hovered label
                            document.getElementById('centerText').innerText = `Bookings: ${hoveredLabel}`;  // Change center text dynamically
                        } else {
                            document.getElementById('centerText').innerText = 'Hover a slice!';  // Reset center text
                        }
                    }
                }
            },
            plugins: [{
                beforeDraw: function(chart) {
                    // Display custom text in the center
                    var ctx = chart.ctx;
                    var chartArea = chart.chartArea;
                    ctx.save();
                    ctx.font = 'bold 28px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#333';
                    ctx.fillText('Total: ' + data.total, (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
                    ctx.restore();
                }
            }]
        });
    });
    axios.get('/admin/dashboard/top-providers')
    .then(function (response) {
        var data = response.data;
    
        // Log the fetched data to the console for debugging
        console.log(data);
        
        var providerNames = data.map(item => item.name);
        var appointmentsCount = data.map(item => item.appointments_count);
    
        // Emoji array to represent top 5 ranks visually
        var emojis = ['🥇', '🥈', '🥉', '🏅', '🏅'];
    
        var ctx = document.getElementById('topServiceProvidersChart').getContext('2d');
    
        new Chart(ctx, {
            type: 'bar', // Bar chart for ranking
            data: {
                labels: providerNames,
                datasets: [{
                    label: 'Completed Appointments',
                    data: appointmentsCount,
                    backgroundColor: ['#36a2eb', '#ff6384', '#4bc0c0', '#ffcd56', '#c45850'],
                    borderColor: ['#36a2eb', '#ff6384', '#4bc0c0', '#ffcd56', '#c45850'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                let percentage = ((tooltipItem.raw / appointmentsCount.reduce((a, b) => a + b, 0)) * 100).toFixed(2);
                                return `${tooltipItem.label}: ${percentage}% (${tooltipItem.raw} appointments)`;
                            }
                        }
                    },
                    legend: {
                        display: false // Hide legend as the data is self-explanatory
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Completed Appointments'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Service Providers'
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            },
            plugins: [{
                afterDraw: function (chart) {
                    var ctx = chart.ctx;
                    var chartArea = chart.chartArea;
                    var centerX = (chartArea.left + chartArea.right) / 2;
                    var centerY = (chartArea.top + chartArea.bottom) / 2;
    
                    chart.data.datasets[0].data.forEach((value, index) => {
                        // Get the bar element for positioning the emoji
                        var meta = chart.getDatasetMeta(0).data[index];
                        var barCenterX = meta.x;
                        var barTopY = meta.y - 20;
    
                        // Draw emoji on top of the bar
                        ctx.font = '24px Arial';
                        ctx.textAlign = 'center';
                        ctx.fillText(emojis[index], barCenterX, barTopY); // Add emoji from array
                    });
                }
            }]
        });
    })
    .catch(function (error) {
        console.log(error);
    });
    



    axios.get('/admin/dashboard/peak-hours')
    .then(function (response) {
        var data = response.data;
        var hours = data.map(item => item.hour);
        var totals = data.map(item => item.total);

        var ctx = document.getElementById('peakBookingHoursChart').getContext('2d');

        // Create gradient for the line chart
        var gradientFill = ctx.createLinearGradient(0, 0, 0, 400);
        gradientFill.addColorStop(0, 'rgba(54, 162, 235, 0.7)');
        gradientFill.addColorStop(1, 'rgba(54, 162, 235, 0.1)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Peak Booking Hours',
                    data: totals,
                    borderColor: '#36a2eb',
                    backgroundColor: gradientFill,  // Gradient fill under the line
                    borderWidth: 3,  // Thicker line for a bold look
                    pointRadius: 5,  // Point size
                    pointHoverRadius: 8,  // Larger points on hover
                    pointBackgroundColor: '#fff',  // White background for points
                    pointBorderColor: '#36a2eb',  // Border color of points
                    pointHoverBorderColor: '#ff6384',  // Hover border color for points
                    fill: true,  // Fill the area under the line with gradient
                    tension: 0.4  // Smooth curve for the line
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false  // Hide grid lines for the x-axis
                        },
                        ticks: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,  // Start y-axis at 0
                        grid: {
                            color: 'rgba(54, 162, 235, 0.2)',  // Custom grid color for y-axis
                            lineWidth: 1
                        },
                        ticks: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: '#ff6384',  // Custom background color for tooltips
                        titleFont: {
                            size: 16,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        callbacks: {
                            label: function(tooltipItem) {
                                return `Bookings: ${tooltipItem.raw}`;
                            },
                            title: function(tooltipItem) {
                                return `Hour: ${tooltipItem[0].label}:00`;  // Custom title for tooltips
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            fontSize: 16,
                            fontColor: '#333'
                        }
                    }
                },
                animation: {
                    tension: {
                        duration: 1000,
                        easing: 'easeInOutCubic',  // Smooth easing effect for the line
                        from: 0.6,
                        to: 0,
                        loop: true  // Subtle animation for continuous movement
                    },
                    duration: 1500  // Duration for the chart load animation
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,  // Make the hover focus on the closest data point
                    animationDuration: 400,  // Smooth hover effect
                }
            }
        });
    });


    axios.get('/admin/dashboard/avg-completion-time')
    .then(function (response) {
        var avgCompletionTime = response.data.avgCompletionTime;

        // Convert hours and minutes to display in the center of the chart
        var formattedTime = `${avgCompletionTime.hours} hrs ${avgCompletionTime.minutes} mins`;

        var ctx = document.getElementById('avgCompletionTimeChart').getContext('2d');

        var maxCompletionTime = 24; // Set max time as 24 hours for the gauge

        var gaugeGradient = ctx.createLinearGradient(0, 0, 0, 400);
        gaugeGradient.addColorStop(0, 'rgba(75, 192, 192, 0.7)');
        gaugeGradient.addColorStop(0.5, 'rgba(255, 205, 86, 0.7)');
        gaugeGradient.addColorStop(1, 'rgba(255, 99, 132, 0.7)');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completion Time (hrs)'],
                datasets: [{
                    label: 'Average Completion Time',
                    data: [avgCompletionTime.hours + avgCompletionTime.minutes / 60, maxCompletionTime - (avgCompletionTime.hours + avgCompletionTime.minutes / 60)], // Completed vs remaining time
                    backgroundColor: [gaugeGradient, 'rgba(220, 220, 220, 0.3)'],
                    borderWidth: 2,
                    hoverBackgroundColor: ['#36a2eb', '#f0f0f0'],
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                rotation: Math.PI,  // Start at the top (12 o'clock)
                circumference: Math.PI,  // Only display a semi-circle (gauge shape)
                cutout: '70%',
                plugins: {
                    tooltip: {
                        enabled: false // Disable tooltips for simplicity
                    },
                    legend: {
                        display: false // Hide legend to keep it clean
                    },
                    datalabels: {
                        display: true,
                        formatter: (value, ctx) => {
                            return value + ' hrs';
                        },
                        color: '#333',
                        font: {
                            size: 20,
                            weight: 'bold'
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 1500
                }
            },
            plugins: [{
                beforeDraw: function (chart) {
                    var ctx = chart.ctx;
                    var chartArea = chart.chartArea;
                    ctx.save();

                    // Display the exact completion time in the center
                    ctx.font = 'bold 28px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#333';
                    ctx.fillText(formattedTime, (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 1.3);
                    ctx.restore();
                }
            }]
        });
    })
    .catch(function (error) {
        console.log(error);
    });



// Fetch and render Avg Response Time Chart
axios.get('/admin/dashboard/avg-response-time')  // Use the exact route
    .then(function (response) {
        var avgResponseTime = response.data.avgResponseTime;
        var ctx = document.getElementById('avgResponseTimeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Average Response Time (minutes)'],
                datasets: [{
                    label: 'Response Time',
                    data: [avgResponseTime],
                    backgroundColor: '#ffcd56'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });

// Fetch and display the Total Revenue as a large text
axios.get('/admin/dashboard/total-revenue')
    .then(function (response) {
        var totalRevenue = response.data.totalRevenue;

        // Format the revenue amount as currency
        var formattedRevenue =  totalRevenue.toLocaleString() + ' LKR';

        // Insert the revenue value into the HTML
        document.getElementById('totalRevenue').innerHTML = formattedRevenue;
    })
    .catch(function (error) {
        console.error('Error fetching total revenue:', error);
        document.getElementById('totalRevenue').innerHTML = 'Error loading revenue';
    });


// Fetch and render Revenue by Category Chart
axios.get('/admin/dashboard/revenue-by-category')
    .then(function (response) {
        var data = response.data;
        var serviceTypes = data.map(item => item.service_type);
        var revenue = data.map(item => item.total);

        var ctx = document.getElementById('revenueByCategoryChart').getContext('2d');

        // Create dynamic gradient backgrounds
        var gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, '#36a2eb');
        gradient1.addColorStop(1, '#ff6384');

        var gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, '#4bc0c0');
        gradient2.addColorStop(1, '#ffcd56');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: serviceTypes,
                datasets: [{
                    label: 'Revenue by Category',
                    data: revenue,
                    backgroundColor: [gradient1, gradient2, '#ffcd56', '#c45850', '#7e57c2'],
                    hoverOffset: 10,  // Expands the slice on hover
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function (tooltipItem) {
                                return serviceTypes[tooltipItem.dataIndex] + ': $' + revenue[tooltipItem.dataIndex].toLocaleString();
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 14
                            },
                            color: '#333',
                        }
                    },
                    datalabels: {
                        display: false
                    }
                },
                animation: {
                    animateScale: true,
                    duration: 1500,
                },
                cutout: '70%',  // Increase the cutout for a cleaner look
            }
        });
    });

    axios.get('/admin/dashboard/booking-forecast')
    .then(function (response) {
        var forecast = response.data.forecast;
        var ctx = document.getElementById('bookingForecastChart').getContext('2d');

        // Simulate historical data (for visualization purposes)
        var historicalData = [120, 150, 180, 140, 200, 220];  // Replace with actual historical data if available

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Historical Data',
                        data: historicalData,
                        borderColor: '#4bc0c0',
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        tension: 0.4,  // Smooth the line curve
                        fill: true  // Fill the area under the curve
                    },
                    {
                        label: 'Forecast',
                        data: [...historicalData.slice(-1), forecast],  // Extend the last historical point with the forecast
                        borderColor: '#ff6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        tension: 0.4,
                        fill: true,
                        borderDash: [5, 5],  // Dashed line for forecast to differentiate from actual data
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            },
                            color: '#333',
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 14
                            },
                            color: '#333',
                            beginAtZero: true
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            borderDash: [5, 5]  // Dashed grid for a clean look
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function (tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw.toLocaleString() + ' bookings';
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333',
                        }
                    },
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1500  // Smooth animation
                }
            }
        });
    });

