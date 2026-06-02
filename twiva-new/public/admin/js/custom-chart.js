    // Yearly Revenue Chart //
    var ctx = document.getElementById('revenueChartYearly').getContext('2d');
    var revenueChartYearly = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul','Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                data: x, //[18000, 15000, 18000, 35000, 20000, 4000, 22000, 15000, 18000, 35000, 20000, 45000,],
                borderColor: [
                    '#2F80ED',
                ],
                borderWidth: 2.5,
                tension: 0.5,
                pointRadius: 0,
            }],
        },
        options: {

            scales: {
                y: {
                    beginAtZero: true,
                    steps: 6,
                    stepValue: 10000,
                    max: 60000,

                    grid:{
                        tickColor: 'transparent',
                        lineWidth: 1,
                        borderDash:[2,2],
                        drawBorder: false,

                    },
                    // type: 'logarithmic',
                    // position: 'left',
                    ticks: {
                        color: 'rgba(0,0,0,0.5)',
                        fontFamily : 'Source Sans Pro',
                        font: {
                            size: 12,
                            weight: 400,
                        },
                        min: 0, //minimum tick
                        max: 40000, //maximum tick
                        callback: function(value, index, values) {
                            if(value == 0){
                                return "0";
                            }else{
                                return value / 1000 +"K";
                            }
                        }//return Number(value.toString()); //pass tick values as a string into Number function
                    },
                },

                x: {
                    grid:{
                        display:false,
                        drawBorder: false,
                    },
                    ticks: {
                        color: 'rgba(0,0,0,0.5)',
                        font: {
                            size: 12,
                            weight: 600,
                        },
                    },
                },


            },
            plugins: {
                legend: {
                    display: false,

                },
            },
        }
    });

    // Monthly Revenue Chart //
    var ctx = document.getElementById('revenueChartMonthly').getContext('2d');
    var revenueChartMonthly = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul','Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                data: [18000, 15000, 18000, 35000, 20000, 4000, 22000, 15000, 18000, 35000, 20000, 45000,],
                borderColor: [
                    '#FFB800',
                ],
                borderWidth: 2,
                tension: 0.5,
                pointRadius: 0,
            }]
        },
        options: {

            scales: {
                y: {
                    beginAtZero: true,
                    type:'linear',
                },

            },
            plugins: {
                legend: {
                    display: false,

                }
            }
        }
    });


    // Pie Chart //
    var pie = document.getElementById('saleChart').getContext('2d');

    var saleChart = new Chart(pie, {
        type: 'doughnut',
        data: {
            labels: ['Profit', 'Sale', 'Order'],
            datasets: [{
                data: [6, 8, 3],
                backgroundColor: [
                    '#6F52ED',
                    '#FFB800',
                    '#28D094',
                ],
                borderWidth: 0,
                radius: 80,
                cutout: 140,

            }]
        },
        options: {
            scales: {
                y: {
                    display:false,
                }
            },


            plugins: {
                legend: {
                    display: false,

                }
            },


        }
    });
