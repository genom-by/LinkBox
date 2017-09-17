var chart;
var cData   = getCarData(cars);
var carData = cData.carData;
var catsTop = cData.catsTop;
var catsBot = cData.catsBot;
$(function() {
    $('#container').highcharts({
        chart       : { type    : 'line', alignTicks: true },
        title       : { text: 'Parallel Coordindates' },
        subtitle    : { text: 'Proof of Concept Using the classic \'cars\' data set' },
        legend      : { enabled : false },
        tooltip     : { enabled : true },
    plotOptions : { 
			series : {
				color : 'rgba(20,20,20,.25)',
                events: {
                    mouseOver: function() {                      
                        this.graph.attr('stroke', 'rgba(0,156,255,1)');
                        this.group.toFront();
                    },
                    mouseOut: function() {
                        this.graph.attr('stroke', 'rgba(20,20,20,0.25)');
                    }
                }
			}
		},
		xAxis  : [{
			opposite: true,
			tickInterval:1,
			lineWidth:0,
			tickWidth:0,
			gridLineWidth:1,
			gridLineColor:'rgba(0,0,0,0.5)',
			gridZIndex: 5,
			labels: {
				y:-17,	
				formatter: function() {
					return catsTop[this.value];
				},
				style: {
					fontWeight:'bold'
				}
			}
		},{
			linkedTo:0,
			lineWidth:0,
			tickWidth:0,
			gridLineWidth:0,
			labels: {
                y:10,	
				formatter: function() {
					return catsBot[this.value];
				},
				style: {
					fontWeight:'bold'
				}
			}
		}],
		yAxis  : {
			min:424, //0
			max:480, //100
			gridLineWidth:0,
            tickWidth:0,
			lineWidth:0,
			labels: {
				enabled: true
			}
		},
       	series : carData
	});	
    chart = $('#container').highcharts();
})

function getCarData(cars) {
    var kaz6s 	= [];	var kol1s 	= [];	var nem2s 	= [];	var akd4s 	= [];
	var mas3s 	= [];	var spu5s 	= [];	var tra7s 	= [];	
	
	var mins 	= {};	var maxs 	= {};	var ranks 	= {};	var pData 	= {};
	
	var kaz6;	var kol1;	var nem2;	var akd4;	var mas3;	var spu5;	var tra7;
	var paramNames = ['kaz6', 'kol1', 'nem2', 'akd4', 'mas3', 'spu5', 'tra7'];
	
	var totalMIN; var totalMAX; // = Math.min.apply(null, mins);
	
	$.each(cars, function(i, car) {

		//if(typeof car[paramNames[0]] 	!= 'undefined') { kaz6s.push(car[paramNames[0]]		); }	
		if(typeof car.kaz6 	!= 'undefined') { kaz6s.push(car.kaz6		); }	
		if(typeof car.kol1 	!= 'undefined') { kol1s.push(car.kol1		); }	
		if(typeof car.nem2 	!= 'undefined') { nem2s.push(car.nem2		); }	
		if(typeof car.akd4  	!= 'undefined') { akd4s.push(car.akd4		); }	
		if(typeof car.mas3 	!= 'undefined') { mas3s.push(car.mas3		); }	
		if(typeof car.spu5 	!= 'undefined') { spu5s.push(car.spu5		); }	
		if(typeof car.tra7 	!= 'undefined') { tra7s.push(car.tra7	); }	

		kaz6 	= typeof car.kaz6 	!= 'undefined' ? car.kaz6 	: null;
		kol1 	= typeof car.kol1 	!= 'undefined' ? car.kol1 	: null;
		nem2 	= typeof car.nem2 	!= 'undefined' ? car.nem2 	: null;
		akd4	= typeof car.akd4 	!= 'undefined' ? car.akd4 	: null;
		mas3 	= typeof car.mas3 	!= 'undefined' ? car.mas3 	: null;
		spu5 	= typeof car.spu5 	!= 'undefined' ? car.spu5 	: null;
		tra7 	= typeof car.tra7 	!= 'undefined' ? car.tra7 	: null;

		pData[car.name] = [];
		pData[car.name].push(
			{name : 'kol1',  value : kol1 }, 
			{name : 'nem2',  value : nem2 }, 
			{name : 'mas3',  value : mas3 }, 
			{name : 'akd4',  value : akd4  }, 
			{name : 'spu5',  value : spu5 }, 
			{name : 'kaz6',  value : kaz6 }, 
			{name : 'tra7',  value : tra7}
		);
		
	});
var wholeCars = kol1s.concat(nem2s,mas3s,akd4s,spu5s,kaz6s,tra7s);
totalMIN = Math.min.apply(null,wholeCars);
totalMAX = Math.max.apply(null,wholeCars);
console.log("min - max: "+totalMIN+" - "+totalMAX);	

	/*ranks['kaz6' ] = percentileRank(kaz6s );
	ranks['kol1' ] = percentileRank(kol1s );
	ranks['nem2' ] = percentileRank(nem2s );
	ranks['akd4'  ] = percentileRank(akd4s  );
	ranks['mas3' ] = percentileRank(mas3s );
	ranks['spu5' ] = percentileRank(spu5s );
	ranks['tra7'] = percentileRank(tra7s);
	*/
	ranks['kaz6' ] = kaz6s;
	ranks['kol1' ] = kol1s;
	ranks['nem2' ] = nem2s;
	ranks['akd4' ] = akd4s;
	ranks['mas3' ] = mas3s;
	ranks['spu5' ] = spu5s;
	ranks['tra7' ] = tra7s;

	$.each(paramNames, function(i_,param){
		mins[param] = totalMIN;
		maxs[param] = totalMAX;
	});
/*	
	mins['kaz6' ] = Math.min.apply(null, kaz6s );
	mins['kol1' ] = Math.min.apply(null, kol1s );
	mins['nem2' ] = Math.min.apply(null, nem2s );
	mins['akd4'  ] = Math.min.apply(null, akd4s  );
	mins['mas3' ] = Math.min.apply(null, mas3s );
	mins['spu5' ] = Math.min.apply(null, spu5s );
	mins['tra7'] = Math.min.apply(null, tra7s);


	maxs['kaz6' ] = Math.max.apply(null, kaz6s );
	maxs['kol1' ] = Math.max.apply(null, kol1s );
	maxs['nem2' ] = Math.max.apply(null, nem2s );
	maxs['akd4'  ] = Math.max.apply(null, akd4s  );
	maxs['mas3' ] = Math.max.apply(null, mas3s );
	maxs['spu5' ] = Math.max.apply(null, spu5s );
	maxs['tra7'] = Math.max.apply(null, tra7s);
	
*/
	var colNames = ['Кольцова','Немига','пл.Мясникова','акад.Управления','г-ца Спутник',
	'пл.Казинца','з-д Транзистор'];
	/*
	for (var i_=0; i_<7; i_++){
		catsTop[i_] = 
			colNames[0]+'<br/><span style="font-weight:normal;">'+maxs['kol1']+'</span>';
	}*/
	var catsTop = [
		colNames[0]+'<br/><span style="font-weight:normal;">'+maxs['kol1']+'</span>', 
    	colNames[1]+'<br/><span style="font-weight:normal;">'+maxs['nem2']+'</span>', 
    	colNames[2]+'<br/><span style="font-weight:normal;">'+maxs['mas3']+'</span>', 
        colNames[3]+'<br/><span style="font-weight:normal;">'+maxs['akd4']+'</span>', 
        colNames[4]+'<br/><span style="font-weight:normal;">'+maxs['spu5']+'</span>', 
        colNames[5]+'<br/><span style="font-weight:normal;">'+maxs['kaz6']+'</span>', 
        colNames[6]+'<br/><span style="font-weight:normal;">'+maxs['tra7']+'</span>'
	]; 
	var catsBot = [
       	colNames[0]+'<br/><span style="font-weight:normal;">'+mins['kol1']+'</span>', 
        colNames[1]+'<br/><span style="font-weight:normal;">'+mins['nem2']+'</span>', 
        colNames[2]+'<br/><span style="font-weight:normal;">'+mins['mas3']+'</span>', 
        colNames[3]+'<br/><span style="font-weight:normal;">'+mins['akd4']+'</span>', 
        colNames[4]+'<br/><span style="font-weight:normal;">'+mins['spu5']+'</span>', 
        colNames[5]+'<br/><span style="font-weight:normal;">'+mins['kaz6']+'</span>', 
    	colNames[6]+'<br/><span style="font-weight:normal;">'+mins['tra7']+'</span>'
	]; 
	       	
	var carData = [];
	var i = 0;
	$.each(pData, function(car, measures) {
		carData[i] = {};
		carData[i].name = car;
		carData[i].data = [];
		var val; 
		$.each(measures, function() {
			var val = typeof ranks[this.name][this.value] != 'undefined' ? ranks[this.name][this.value] : null; 
			//console.info("name[ %s ] and value [ %d ]",this.name, ranks[this.name][this.value]);
			carData[i].data.push(val);
		});
		i++;
	});    
    rData = {};
    rData.carData = carData;
    rData.catsTop = catsTop;
    rData.catsBot = catsBot;
    return rData;
}
//crude percentile ranking
function percentileRank(data, reverse=false) {
	data.sort(numSort);
	if(reverse === true) {
		data.reverse();
	}
	//var len   = data.length;
	var len   = 10;
	var sData = {};
	$.each(data, function(i, point) {
		sData[point] = (i / (len / 100));
	});
	return sData;
}
//because .sort() doesn't sort numbers correctly
function numSort(a,b) { 
    return a - b; 
}
