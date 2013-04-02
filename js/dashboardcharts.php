<?php
include_once("../config/config.php");
//date_default_timezone_set('UTC');

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
$ps = $pdo->prepare("SET time_zone = 'US/Pacific';");
$ps->execute();

		
$timespan = strtotime("-31 days");
$y = date("Y", $timespan);
$m = date("m", $timespan)-1;
$d = date("d", $timespan);

//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$ps = $pdo->prepare("SELECT DATE(FROM_UNIXTIME(randate)) as d, sum(credit) as total, randate FROM transactions WHERE randate > $timespan GROUP BY d ORDER BY randate");
$ps->execute();
$transactions = $ps->fetchAll();
foreach ($transactions as $transaction) {
	$y = date("Y", $transaction['randate']);
	$m = date("m", $transaction['randate'])-1;
	$d = date("d", $transaction['randate']);
	$randate = "Date.UTC($y,$m,$d)"; 
	$total[] = "[$randate,".round($transaction['total'], 2)."]";
}

$ps = $pdo->prepare("SELECT DATE(FROM_UNIXTIME(randate)) as d, sum(credit) as creditcard, randate FROM transactions WHERE randate > $timespan AND approvalCode != '' GROUP BY d ORDER BY randate");
$ps->execute();
$transactions = $ps->fetchAll();
foreach ($transactions as $transaction) {
	$y = date("Y", $transaction['randate']);
	$m = date("m", $transaction['randate'])-1;
	$d = date("d", $transaction['randate']);
	$randate = "Date.UTC($y,$m,$d)"; 
	$creditcard[] = "[$randate,".round($transaction['creditcard'], 2)."]";
}

$ps = $pdo->prepare("SELECT DATE(FROM_UNIXTIME(randate)) as d, sum(credit) as cashcheck, randate FROM transactions WHERE randate > $timespan AND approvalCode = '' GROUP BY d ORDER BY randate");
$ps->execute();
$transactions = $ps->fetchAll();
foreach ($transactions as $transaction) {
	$y = date("Y", $transaction['randate']);
	$m = date("m", $transaction['randate'])-1;
	$d = date("d", $transaction['randate']);
	$randate = "Date.UTC($y,$m,$d)"; 
	$cashcheck[] = "[$randate,".round($transaction['cashcheck'], 2)."]";
}
//print_r($transactions);
?>
new Highcharts.Chart({
					    chart: {
					        renderTo: 'monthChart',
					        defaultSeriesType: 'spline',
					        height: 250,
					        plotBorderColor: '#e3e6e8',
					        plotBorderWidth: 1,
					        plotBorderRadius: 0,
					        backgroundColor: '',
					        spacingLeft: 0,
					        plotBackgroundColor: '#FFFFFF',
					        marginTop: 5,
					        marginBottom: 35,
					        zoomType: 'x,y'
					    },
					
					    
					    credits: {
					        style: {
					            color: '#9fa2a5'
					        }
					    },
					
					    title: {
					        text: ''
					    },
					
					    legend: {
					        align: 'left',
					        floating: true,
					        verticalAlign: 'top',
					        borderWidth: 0,
					        y: 3,
					        x: 10,
					        itemStyle: {
					            fontSize: '11px',
					            color: '#1E1E1E'
					        }
					    },
					
					    yAxis: {
					        title: {
					            text: ''
					        },
					        gridLineColor: '#EEE',
					        opposite: true,
					        labels: {
					            style: {
					                color: '#9fa2a5'
					            }
					        }
					    },
					
					    xAxis: {
					        type: 'datetime',
					        lineWidth: 0,
					        maxZoom: 5 * 24 * 3600 * 1000, // 5 days
					        tickPixelInterval: 50,
					        labels: {
					            formatter: function() {
					                return Highcharts.dateFormat('%e', this.value);
					            },
					            x: 0,
					            style: {
					                color: '#9fa2a5'
					            }
					        }
					    },

					    tooltip: {
					    		valueDecimals: 2,
		    		            valuePrefix: '$',
		    		            shared: true
					            },
					
					    plotOptions: {
					        series: {
					            marker: {
					                lineWidth: 1, // The border of each point (defaults to white)
					                radius: 3 // The thickness of each point
					            },
					
					            lineWidth: 3, // The thickness of the line between points
					            shadow: false
					        }
					    },
					
					    /*
					     * Colors for the main lines.
					     */
					    colors: [
					        '#4c97d7', // orange
					        '#E35733', // blue
					        '#52d74c', // green
					        '#e268de' // purple
					    ],
					
					    series: [ {
					        name: 'Total Revenue',
					        marker: {
					            symbol: 'circle'
					        },
					        data: [<?php echo implode(",", $total) ?>]
					    }, {
					        name: 'Credit Card',
					        marker: {
					            symbol: 'circle'
					        },
					        data: [<?php echo implode(",", $creditcard) ?>]
					    }, {
					        name: 'Check/Cash',
					        marker: {
					            symbol: 'circle'
					        },
					        data: [<?php echo implode(",", $cashcheck) ?>]
					    }]
					});
					
<?php
$timespan = strtotime("-12 months");
$y = date("Y", $timespan);
$m = date("m", $timespan)-1;
$d = date("d", $timespan);

$ps = $pdo->prepare("SELECT MONTH(FROM_UNIXTIME(randate)) as d, sum(credit) as sum, randate FROM transactions WHERE randate >= $timespan GROUP BY d ORDER BY randate");
$ps->execute();
$transactions = $ps->fetchAll();
$sum = array();
foreach ($transactions as $transaction) {
	$y = date("Y", $transaction['randate']);
	$m = date("m", $transaction['randate'])-1;
	$d = date("d", $transaction['randate']);
	$randate = "Date.UTC($y,$m,$d)"; 
	$sum[] = "[$randate,".round($transaction['sum'], 2)."]";
}
//print_r($transactions);
?>
					
new Highcharts.Chart({
					    chart: {
					        renderTo: 'sixmonthChart',
					        defaultSeriesType: 'spline',
					        height: 250,
					        plotBorderColor: '#e3e6e8',
					        plotBorderWidth: 1,
					        plotBorderRadius: 0,
					        backgroundColor: '',
					        spacingLeft: 0,
					        plotBackgroundColor: '#FFFFFF',
					        marginTop: 5,
					        marginBottom: 35,
					        zoomType: 'x,y'
					    },
					
					    
					    credits: {
					        style: {
					            color: '#9fa2a5'
					        }
					    },
					
					    title: {
					        text: ''
					    },
					
					    legend: {
					        align: 'left',
					        floating: true,
					        verticalAlign: 'top',
					        borderWidth: 0,
					        itemStyle: {
					            fontSize: '11px',
					            color: '#1E1E1E'
					        }
					    },
					
					    yAxis: {
					        title: {
					            text: ''
					        },
					        gridLineColor: '#EEE',
					        opposite: true,
					        labels: {
					            style: {
					                color: '#9fa2a5'
					            }
					        }
					    },
					
					    xAxis: {
					        type: 'datetime',
					        lineWidth: 0,
					        tickPixelInterval: 50,
					        labels: {
					            formatter: function() {
					                return Highcharts.dateFormat('%b', this.value);
					            },
					            x: 0,
					            style: {
					                color: '#9fa2a5'
					            }
					        }
					    },

					    tooltip: {
					                xDateFormat: '%B %Y',
					                valueDecimals: 2,
		    		            	valuePrefix: '$'
					            },
					
					    plotOptions: {
					        series: {
					            marker: {
					                lineWidth: 1, // The border of each point (defaults to white)
					                radius: 3 // The thickness of each point
					            },
					
					            lineWidth: 3, // The thickness of the line between points
					            shadow: false
					        }
					    },
					
					    /*
					     * Colors for the main lines.
					     */
					    colors: [
					        '#4c97d7', // orange
					        '#4c97d7', // blue
					        '#52d74c', // green
					        '#e268de' // purple
					    ],
					
					    series: [ {
					        name: 'Revenue ($)',
					        marker: {
					            symbol: 'circle'
					        },
					        data: [<?php echo implode(",", $sum) ?>]
					    }]
					});


					<?php
$timespan = strtotime("-31 days");
$y = date("Y", $timespan);
$m = date("m", $timespan)-1;
$d = date("d", $timespan);

$ps = $pdo->prepare("SELECT sum(credit) as sum, (SELECT name FROM franchises WHERE id = franchise) as franchise FROM transactions WHERE randate >= $timespan GROUP BY franchise ORDER BY sum DESC LIMIT 10");
$ps->execute();
$transactions = $ps->fetchAll();

$sum = array();
$franchises = array();

foreach ($transactions as $transaction) {
	if ($transaction['sum'] > 0) {
		$transaction['franchise'] = str_replace("KidzArt", "", $transaction['franchise']);

		$franchises[] = "'{$transaction['franchise']}'";
		$sum[] = round($transaction['sum'], 2);
	}
}
//print_r($transactions);
?>
					
new Highcharts.Chart({
					    chart: {
					        renderTo: 'revByFranchisee31Days',
					        height: 300,
					        plotBorderColor: '#e3e6e8',
					        plotBorderWidth: 1,
					        plotBorderRadius: 0,
					        backgroundColor: '',
					        spacingLeft: 0,
					        plotBackgroundColor: '#FFFFFF',
					        type: 'bar',
					        marginTop: 5,
					        marginBottom: 35,
					        zoomType: 'x,y'
					    },
					
					    
					    credits: {
					        style: {
					            color: '#9fa2a5'
					        }
					    },
					
					    title: {
					        text: ''
					    },
					
					    legend: {
					        align: 'left',
					        floating: true,
					        align: 'right',
					        verticalAlign: 'bottom',
                			y: -30,
                			x: -5,
					        borderWidth: 0,
					        itemStyle: {
					            fontSize: '11px',
					            color: '#1E1E1E'
					        }
					    },
					
					    yAxis: {
					        title: {
					            text: ''
					        },
					        min: 0,
	                        title: {
	                            text: '',
	                            align: 'high'
	                        },
	                        labels: {
	                            overflow: 'justify',
	                            style: {
					                color: '#9fa2a5'
					            }
	                        },
					        gridLineColor: '#EEE',
					    },
					
					    xAxis: {
					        categories: [<?php echo implode(",", $franchises) ?>]
					    },

					    tooltip: {
					                xDateFormat: '%B %Y',
					                valueDecimals: 2,
		    		            	valuePrefix: '$'
					            },
					
					    plotOptions: {
					        series: {
					            marker: {
					                lineWidth: 1, // The border of each point (defaults to white)
					                radius: 3 // The thickness of each point
					            },
					
					            lineWidth: 3, // The thickness of the line between points
					            shadow: false
					        }
					    },
					
					    /*
					     * Colors for the main lines.
					     */
					    colors: [
					        '#4c97d7', // orange
					        '#4c97d7', // blue
					        '#52d74c', // green
					        '#e268de' // purple
					    ],
					
					    series: [ {
					        name: 'Revenue ($)',
					        data: [<?php echo implode(",", $sum) ?>]
					    }]
					});