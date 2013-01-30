<?php
include_once("../config/config.php");

$pdo = new PDO($config['db']['dsn'], $config['db']['un'], $config['db']['pw']);
		
$timespan = strtotime("-31 days");
$y = date("Y", $timespan);
$m = date("m", $timespan)-1;
$d = date("d", $timespan);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$ps = $pdo->prepare("SELECT DATE(FROM_UNIXTIME(randate)) as d, sum(credit) as sum, randate FROM transactions WHERE randate > $timespan GROUP BY d ORDER BY randate");
$ps->execute();
$transactions = $ps->fetchAll();
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
					        gridLineColor: '#FAFAFA',
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
	$sum[] = "[$randate,".round($transaction['sum'])."]";
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
					        gridLineColor: '#FAFAFA',
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
					                return Highcharts.dateFormat('%b', this.value);
					            },
					            x: 0,
					            style: {
					                color: '#9fa2a5'
					            }
					        }
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