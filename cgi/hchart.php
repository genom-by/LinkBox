<?php
namespace obus;
include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

?>
<html>
<head>
<title>Chart scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/highcharts.js"></script>
<script type="text/javascript" src="../js/highcharts-more.js"></script>
<script type="text/javascript" src="../js/exporting.js"></script>
<script>
var d          = new Date();
var pointStart = d.getTime();
Highcharts.setOptions({
    global: {
        useUTC:false
    },
    colors: [
        'rgba( 0,   154, 253, 0.9 )', //bright blue
        'rgba( 253, 99,  0,   0.9 )', //bright orange
        'rgba( 40,  40,  56,  0.9 )', //dark
        'rgba( 253, 0,   154, 0.9 )', //bright pink
        'rgba( 154, 253, 0,   0.9 )', //bright green
        'rgba( 145, 44,  138, 0.9 )', //mid purple
        'rgba( 45,  47,  238, 0.9 )', //mid blue
        'rgba( 177, 69,  0,   0.9 )', //dark orange
        'rgba( 140, 140, 156, 0.9 )', //mid
        'rgba( 238, 46,  47,  0.9 )', //mid red
        'rgba( 44,  145, 51,  0.9 )', //mid green
        'rgba( 103, 16,  192, 0.9 )'  //dark purple
    ],
    chart: {
        alignTicks:false,
        type:'',
        margin:[80,25,50,25],
        //borderRadius:10,
        //borderWidth:1,
        //borderColor:'rgba(156,156,156,.25)',
        //backgroundColor:'rgba(204,204,204,.25)',
        //plotBackgroundColor:'rgba(255,255,255,1)',
        style: {
            fontFamily: 'Abel,serif'
        },        
    events:{
            load: function() {
                this.credits.element.onclick = function() {
                    window.open(
                      'http://stackoverflow.com/users/1011544/jlbriggs?tab=profile'
                    );
                 }
            }
        }           
    },
    credits: {
        text : 'http://stackoverflow.com/users/1011544/jlbriggs',
        href : 'http://stackoverflow.com/users/1011544/jlbriggs?tab=profile'
    },
    title: {
        text:'Test Chart Title',
        align:'left',
        margin:10,
        x: 10,
        style: {
            fontWeight:'bold',
            color:'rgba(0,0,0,.9)'
        }
    },
    subtitle: {
        text:'Test Chart Subtitle',   
        align:'left',
        x: 12,
    },
    legend: { enabled: true },
    plotOptions: {
        area: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        arearange: {
            lineWidth:1
        },
        areaspline: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        areasplinerange: {
            lineWidth:1
        },
        boxplot: {
            groupPadding:0.05,
            pointPadding:0.05,
            fillColor:'rgba(255,255,255,.75)'
        },
		bubble: {
			minSize:'0.25%',
			maxSize:'17%'
		},
        column: {
            //stacking:'normal',
            groupPadding:0.05,
            pointPadding:0.05
        },
        columnrange: {
            groupPadding:0.05,
            pointPadding:0.05
        },
        errorbar: {
            groupPadding:0.05,
            pointPadding:0.05,
        	showInLegend:true        
        },
        line: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        scatter: {
            marker: {
                symbol: 'circle',
                radius:5
            }
        },
        spline: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        series: {
            shadow: false,
            borderWidth:0,
            states: {
                hover: {
                    lineWidtakd4lus:0,
                }
            }
        }
    },
    xAxis: {
        title: {
            text: null,
            rotation:0,
            textAlign:'center',
            style:{ 
                color:'rgba(0,0,0,.9)'
            }
        },
        labels: { 
            style: {
                color: 'rgba(0,0,0,.9)',
                fontSize:'9px'
            }
        },
        lineWidth:.5,
        lineColor:'rgba(0,0,0,.5)',
        tickWidth:.5,
        tickLength:3,
        tickColor:'rgba(0,0,0,.75)'
    },
    yAxis: {
        minPadding:0,
        maxPadding:0,
        gridLineColor:'rgba(20,20,20,.25)',
        gridLineWidth:0.5,
        title: { 
            text: null,
            rotation:0,
            textAlign:'right',
            style:{ 
                color:'rgba(0,0,0,.9)',
            }
        },
        labels: { 
            style: {
                color: 'rgba(0,0,0,.9)',
                fontSize:'9px'
            }
        },
        lineWidth:.5,
        lineColor:'rgba(0,0,0,.5)',
        tickWidth:.5,
        tickLength:3,
        tickColor:'rgba(0,0,0,.75)'
    }
});	
    /*
function randomData(points, positive, multiplier) {
    points     = !points            ? 1     : points;
    positive   = positive !== true  ? false : true;
    multiplier = !multiplier        ? 1     : multiplier;
    
    function rnd() {
        return ((
            Math.random() + 
            Math.random() + 
            Math.random() + 
            Math.random() + 
            Math.random() + 
            Math.random()
        ) - 3) / 3;
    }
    var rData = [];
    for (var i = 0; i < points; i++) {
        val = rnd();
        val = positive   === true ? Math.abs(val)      : val;
        val = multiplier >   1    ? (val * multiplier) : val;
        rData.push(val);    
    }
    return rData;
}*/
<?php $pitstops = Way::getPitstopsByItinerary(); 
//\LinkBox\Logger::log(serialize($pitstops));
echo HTML::normalizeWays2JSON($pitstops);
echo ';';
/*
var cars = [
{name:"chevrolet chevelle malibu", kaz6:18, kol1:8, nem2:307, akd4:130, mas3:3504, spu5:12, tra7:70, origin:1},
{name:"buick skylark 320", kaz6:15, kol1:8, nem2:350, akd4:165, mas3:3693, spu5:11.5, tra7:70, origin:1},
{name:"plymouth satellite", kaz6:18, kol1:8, nem2:318, akd4:150, mas3:3436, spu5:11, tra7:70, origin:1},
{name:"citroen ds-21 pallas", kaz6:undefined, kol1:4, nem2:133, akd4:115, mas3:3090, spu5:17.5, tra7:70, origin:2},
{name:"chevrolet chevelle concours (sw)", kaz6:undefined, kol1:8, nem2:350, akd4:165, mas3:4142, spu5:11.5, tra7:70, origin:1},
{name:"amc concord dl", kaz6:23, kol1:4, nem2:151, akd4:undefined, mas3:3035, spu5:20.5, tra7:82, origin:1},
{name:"dodge rampage", kaz6:32, kol1:4, nem2:135, akd4:84, mas3:2295, spu5:11.6, tra7:82, origin:1},
{name:"ford ranger", kaz6:28, kol1:4, nem2:120, akd4:79, mas3:2625, spu5:18.6, tra7:82, origin:1},
{name:"chevy s-10", kaz6:31, kol1:4, nem2:119, akd4:82, mas3:2720, spu5:19.4, tra7:82, origin:1}
];*/
?>
</script><pre>
<?php
var_dump($pitstops);
?></pre>
<script type="text/javascript" src="../js/hchart.js"></script>
<link rel="stylesheet" type="text/css" href="../css/hchart.css">
</head>
<body>
<div id="container" style="width:1000px;height:600px;margin:.5em;"></div>
<a href="obus-test.php" >settings</a>
</body>
</html>