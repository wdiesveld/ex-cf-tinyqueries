<?php

$appUrl = 'http://' . $_SERVER["SERVER_NAME"];

?>
<html>
<head>

	<title>TinyQueries - Bluemix example</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

	<script src="javascript/jquery-1.10.2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="javascript/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="javascript/d3-3.5.15.min.js" type="text/javascript" charset="utf-8"></script>

</head>
<body>
	<h1>Welcome to the TinyQueries Sample App</h1>
	
	<p>
		TinyQueries is ready to be used. If the setup has finished properly you should see two bar-charts below. 
		If this is not the case check the Logs section in Bluemix and look for a message 'Error during setup TinyQueries' to find out what went wrong.
	</p>
	
	<h2>What does this app do?</h2>
	
	<p>
		This app consists of an API generated by the TinyQueries compiler and some <a href="https://d3js.org/" target="_blank">D3 visualisations</a> on top of that. 
		The API is at <code><?php echo $appUrl; ?>/api</code>.
		There are two end points: <code>/api/offices</code> and <code>/api/employees</code>. 
		The graphs below visualize the data coming out of these two end points.
		The end points can be edited using the online TinyQueries IDE which you can access through your Bluemix dashboard.
		The data is coming from a the <a href="http://www.mysqltutorial.org/mysql-sample-database.aspx" target="_blank">Classic Models database</a>, which was intialized during setup on a ClearDB MySQL instance.
	</p>
	
	
	<h2>Examples</h2>
	
	<p>
		The data in the graph below is coming from the api which is generated by TinyQueries. The api-call is 
		<a href="/api/offices" target="_blank"><code>/api/offices</code></a>
	</p>
	
	<div id="example-chart-1"></div>

	<p>
		The data in the graph below is coming from the api which is generated by TinyQueries. The api-call is 
		<a href="/api/employees" target="_blank"><code>/api/employees</code></a>
	</p>
	
	<div id="example-chart-2"></div>

	<h2>TinyQueries IDE</h2>
	
	<p>
		To get an idea how you can use TinyQueries you can try out the following. 
		We are going to change the end-point <code>/api/employees</code>.
	</p>
	
	<ul>
		<li>Open TinyQueries in the Bluemix dashboard</li>
		<li>Click on 'start creating queries'</li>
		<li>Go to the Config tab</li>
		<li>Ensure that in the publish section this URL <code><?php echo $appUrl; ?>/admin/</code> is set (if not you can add it manually)</li>
		<li>Click on the query 'employees' to the left</li>
		<li>Click on the tab 'Sheet'</li>
		<li>Click 'Add filter'</li>
		<li>Select 'office' > 'office.city'</li>
		<li>Click on the edit-box and fill in 'Paris' and press enter</li>
		<li>Click on the button 'Test query' - you should see a list of employees - these are the employess who are working at the office in Paris</li>
		<li>If you now refresh this page you will see the filtered list of employees in the second graph</li>
	</ul>
	
	<h2>Further reading</h2>
	
	<p>
		Technical documentation of TinyQueries can be found on <a href="http://docs.tinyqueries.com">docs.tinyqueries.com</a>.
	</p>


<script>	


$( document ).ready( function()
{
	createExampleChart1();
	createExampleChart2();
});

/**
 * Uses D3 to create a bar chart based on data coming from the api generated by TinyQueries
 *
 */
function createExampleChart1()
{
	var margin = {top: 20, right: 20, bottom: 30, left: 40},
		width = 500 - margin.left - margin.right,
		height = 300 - margin.top - margin.bottom;

	var x = d3.scale.ordinal()
		.rangeRoundBands([0, width], .1);

	var y = d3.scale.linear()
		.range([height, 0]);

	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom");

	var yAxis = d3.svg.axis()
		.scale(y)
		.orient("left")
		.ticks(6);

	var svg = d3.select("#example-chart-1").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
	  .append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	// Call api and process the result
	d3.json("/api/offices", function(error, data) 
	{
	  if (error) 
		throw error;

	  x.domain(data.map(function(d) { return d.city; }));
	  y.domain([0, d3.max(data, function(d) { return d['employees.count']; })]);

	  svg.append("g")
		  .attr("class", "x axis")
		  .attr("transform", "translate(0," + height + ")")
		  .call(xAxis);

	  svg.append("g")
		  .attr("class", "y axis")
		  .call(yAxis)
		.append("text")
		  .attr("transform", "rotate(-90)")
		  .attr("y", 6)
		  .attr("dy", "-28px")
		  .style("text-anchor", "end")
		  .text("Number of employees");

	  svg.selectAll(".bar")
		  .data(data)
		.enter().append("rect")
		  .attr("class", "bar")
		  .attr("x", function(d) { return x(d.city); })
		  .attr("width", x.rangeBand())
		  .attr("y", function(d) { return y(d['employees.count']); })
		  .attr("height", function(d) { return height - y(d['employees.count']); });
	});
}

/**
 * Uses D3 to create a bar chart based on data coming from the api generated by TinyQueries
 *
 */
function createExampleChart2()
{
	var margin = {top: 20, right: 100, bottom: 40, left: 100},
		width = 800 - margin.left - margin.right,
		height = 500 - margin.top - margin.bottom;

	var x = d3.scale.linear()
		.range([0, width]);

	var y = d3.scale.ordinal()
		.rangeRoundBands([0, height], .1);
		

	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom")
		.ticks(10);

	var yAxis = d3.svg.axis()
		.scale(y)
		.orient("left");

	var svg = d3.select("#example-chart-2").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
	  .append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	// Call api and process the result
	d3.json("/api/employees", function(error, data) 
	{
	  if (error) 
		throw error;

	  x.domain([0, d3.max(data, function(d) { return d['customers.orders.count']; })]);
	  y.domain(data.map( function(d) { return d.firstName + ' ' + d.lastName; }));

	  svg.append("g")
		  .attr("class", "x axis")
		  .attr("transform", "translate(0," + height + ")")
		  .call(xAxis)
		.append("text")
		  .attr("x", 250)
		  .attr("dy", "30px")
		  .text("Number of orders");

	  svg.append("g")
		  .attr("class", "y axis")
		  .call(yAxis);

	  svg.selectAll(".bar")
		  .data(data)
		.enter().append("rect")
		  .attr("class", "bar")
		  .attr("x", function(d) { return 0; })
		  .attr("width", function(d) { return x(d['customers.orders.count']); })
		  .attr("y", function(d) { return y( d.firstName + ' ' + d.lastName ); })
		  .attr("height", y.rangeBand());
	});
}


</script>

	
	
</body>
</html>