<style type="text/css">
	.highcharts-figure,
	.highcharts-data-table table {
	  min-width: 310px;
	  max-width: 800px;
	  margin: 1em auto;
	}

	#container {
	  height: 400px;
	}

	.highcharts-data-table table {
	  font-family: Verdana, sans-serif;
	  border-collapse: collapse;
	  border: 1px solid #ebebeb;
	  margin: 10px auto;
	  text-align: center;
	  width: 100%;
	  max-width: 500px;
	}

	.highcharts-data-table caption {
	  padding: 1em 0;
	  font-size: 1.2em;
	  color: #555;
	}

	.highcharts-data-table th {
	  font-weight: 600;
	  padding: 0.5em;
	}

	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
	  padding: 0.5em;
	}

	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
	  background: #f8f8f8;
	}

	.highcharts-data-table tr:hover {
	  background: #f1f7ff;
	}
</style>
<figure class="highcharts-figure">
  <div id="container"></div>
</figure>
<script type="text/javascript">
	// Data retrieved from https://gs.statcounter.com/browser-market-share#monthly-202201-202201-bar
	var tqams_report = <?php echo ($tqams_report); ?>;

  var drilldown_report = <?php echo ($drilldown_report); ?>;
  var school_name = <?php echo ($school_name); ?>;

  Highcharts.addEvent(Highcharts.Chart, 'render', function() {
  var table = this.dataTableDiv;
  console.log(table);
  if (table) {

    $(table).find('caption').remove();


    // Apply styles inline because stylesheets are not passed to the exported SVG
    Highcharts.css(table.querySelector('table'), {
      'border-collapse': 'collapse',
      'border-spacing': 0,
      'background': 'white',
      'min-width': '100%',
      'font-family': 'sans-serif',
      'font-size': '12px',
      'width': '550px'
    });

    Highcharts.css(table.querySelectorAll('tr:last-child'), {
      'background-color': 'red',
    });

    [].forEach.call(table.querySelectorAll('td, th, caption'), function(elem) {
      Highcharts.css(elem, {
        border: '1px solid silver',
        padding: '0.2em'
      });
    });

    [].forEach.call(table.querySelectorAll('td'), function(elem) {
      Highcharts.css(elem, {
        'text-align': 'center'
      });
    });

    $(table).find("tbody tr:eq(5)").css("background-color", "#cdcdcd");
    $(table).find("tbody th:eq(5)").css("font-weight", "bold");
    $(table).find("tbody td:eq(5)").css("font-weight", "bold");


    // Add the table as the subtitle to make it part of the export
    this.setTitle(null, {
      text: table.innerHTML,
      useHTML: true
    });
    if (table.parentNode) {
      table.parentNode.removeChild(table);
    }
    delete this.dataTableDiv;
  }
});

// Create the chart
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        align: 'center',
        text: school_name
    },
    subtitle: {
        align: 'center',
        text: 'Evaluated by bdeducation'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Scale'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b> of total<br/>'
    },

    series: [
        {
            name: 'Points',
            colorByPoint: true,
            data: tqams_report
        }
    ],
    drilldown: {
        breadcrumbs: {
            position: {
                align: 'right'
            }
        },
        series: drilldown_report
    },
    exporting: {
      showTable: true,
      allowHTML: true
    }
});

</script>