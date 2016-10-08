$(function(){
	$.get("goods.php?act=get_province_distribution", function(data){
		createRegionChart(data);
	}, 'json')
});

function createRegionChart(data) {
	var avg;
	var k=0;
	var sum=0;
	for ( var p in data) {
		sum = sum + parseFloat(data[p].value);
		k++;
	}
	avg = sum/k*2;

	var s = echarts.init(document.getElementById("echarts-map-chart")), c = {
	    title: {
	        text: "销量分布图",
	        subtext: "",
	        x: "center"
	    },
	    tooltip: {trigger: "item"},
	    legend: {orient: "vertical", x: "left", data: ["销售金额"]},
	    //dataRange: {min: 0, max: 2500, x: "left", y: "bottom", text: ["高", "低"], calculable: !0},
	    toolbox: {
	        show: !0,
	        orient: "vertical",
	        x: "right",
	        y: "center",
	        feature: {
	            mark: {show: !0},
	            dataView: {show: !0, readOnly: !1},
	            restore: {show: !0},
	            saveAsImage: {show: !0}
	        }
	    },
	    roamController: {show: !0, x: "right", mapTypeControl: {china: !0}},
	    dataRange: {
	        min: 0,
	        max : avg,
	        text:['高','低'],           // 文本，默认为数值文本
	        calculable : true,
	        x: 'left',
	        color: ['orangered','yellow','lightskyblue']
	    },
	    series: [{
	        name: "销售金额",
	        type: "map",
	        mapType: "china",
	        roam: !1,
	        itemStyle: {normal: {label: {show: !0}}, emphasis: {label: {show: !0}}},
	        data: data
	    }]
	};
	s.setOption(c), $(window).resize(s.resize);
}