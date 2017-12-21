<!DOCTYPE>
<html>
<head>
	<title>Stocks</title>
	<script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
<style>
	td.grey{
		background-color: #f2f2f2;
    	border: 1px solid #696969;
    	text-align: center;
    	padding-right:30px;
    	padding-left: 30px;
	}
	td.light{
		background-color: #DCDCDC;
    	border: 1px solid #696969;
    	text-align: left;
    	padding-right:110px;
	}
	table {
    	font-family: arial, sans-serif;
    	border-collapse: collapse;
    	width: 100%;
	}
	td{
		padding-left: 5px;
		padding-right: 5px;
	}
    .left-right-pd{
        padding-left: 15px;
        padding-right: 15px;
    }

</style>
</head>
<body style="margin: 0px">
<div style="width: 100%; height: 200px">
	<div style="width: 35%; height: 100%; float: left"></div>
	<div style="width: 30%; height: 100%;float: left">
		<div style="height:99%;background-color:#f2f2f2;border:0.5px solid;border-color:#cccccc">
			<div>
				<h3 style="text-align: center;margin: 0px;padding-top: 10px;padding-bottom: 10px;font-style: italic;">Stock Search</h3>
				<div style="padding-left: 5px;padding-right: 5px">
				<p style="border-bottom: 1px solid #cccccc;margin: 0px"></p>
			</div>
			<form method="GET" style="padding-top: 10px;padding-left: 10px" name="Input_form">
				<label>Enter Stock Ticker Symbol:*</label><div style="padding-left: 5px; display: inline;"><input type="text" name="ticker" value="<?php echo isset($_GET['ticker']) ? $_GET['ticker'] : '' ?>"></div>
                <br/>
				<input type="submit" name="search" value="search" style="position: relative;left: 190px; border-radius: 10;">
				<input type="button" name="clear" value="clear" style="position: relative;left: 200px" onclick="clear_form()">
				<br>
				<p style="font-style: italic;">* - Mandatory fields</p>
			</form>
			</div>
		</div>
	</div>
<div style="width: 35%; height: 100%;"></div>
</div>
<?php 
if(isset($_GET["search"])){ 
                            $API_KEY='QOBQ26JRIG4OOLKH';
                            if($_GET['ticker']==""){
                                ?>
                                <script type="text/javascript">
                                    window.alert("ticker cannot be empty");
                                </script>
                                <?php
                                exit();
                            }
                            $symbol=$_GET['ticker'];
                            $URL = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol='.$symbol."&outputsize=full".'&apikey='.$API_KEY;
                            try{
                            $jsonData = json_decode(@file_get_contents($URL));
                            }
                            catch(Exception $e){
                            	echo "Exception!!!";
                            }
    //echo $jsonData->{'Meta Data'}->{'3. Last Refreshed'};
        if(isset($jsonData->{'Error Message'})){
                                ?>
                                <div style="width: 100%; height: 272px;">
                                    <div style="width:10%;height: 100%; float: left"></div>
                                    <div style="width:80%;height: 100%; float: left">
                                    <table id="error_table"><tr><td class='light'>Error</td><td class='grey'>Please provide a valid input symbol</td></tr></table>
                                    </div>
                                    <div style="width:10%;height: 100%; float: left">
                                </div>
                                <script type="text/javascript">
                                	function clear_form(){
                                		document.getElementById("error_table").style.display="none";
                                        document.Input_form.ticker.value="";
                                	}
                                </script>
                            <?php    
                            }
        else if(isset($jsonData->{'Time Series (Daily)'})){
                            $i=0;
                            $table_data=array();
                            $graph_data=$jsonData->{'Time Series (Daily)'};
                            // try{
                            function add_commas($val){
                            	$val_str="";
                            	$val_arr=array();
                            	for($i=0;$val>0;$i++){
                                   $val_arr[$i]=$val%1000;
                                   if($val_arr[$i]<10){
                                    $val_arr[$i]='00'+$val_arr[$i];
                                   }
                                   else if($val_arr[$i]<100){
                                    $val_arr[$i]='0'+$val_arr[$i];
                                   }
                                   $val=floor($val/1000);
                            	}
                                $val_str=$val_arr[$i-1];
                            	for($i=count($val_arr)-2;$i>=0;$i--){
                            		$val_str=$val_str.','.$val_arr[$i];
                            	}
                            	return($val_str);
                            }
                            foreach ($graph_data as $name => $key) {
                                    if($i==0){
                                        $table_data['ticker_symbol']=$symbol;
                                        $table_data['close']= $key->{'4. close'};
                                        $table_data['open']= $key->{'1. open'};
                                        $table_data['low']= $key->{'3. low'};
                                        $table_data['high']= $key->{'2. high'};
                                        $table_data['volume']= number_format($key->{'5. volume'});
                                        $table_data['date']= $name;
                                    }
                                    if ($i==1) {
                                        $table_data['previous_close']= $key->{'4. close'};
                                        $table_data['change']= round(floatval($table_data['close']-$table_data['previous_close']),2);
                                        if($table_data['change']<0){
                                            $table_data['src']="http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png";
                                            //$table_data['change']=round(floatval(-1 * $table_data['change']),2);
                                        }
                                    else{
                                            $table_data['src']="http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png";
                                        }
                                    $table_data['change Percent']=round(floatval($table_data['change']*100 /$table_data['previous_close'] ),2).'%'; 
                                    break;
                                    }
                                $i=$i+1;
                               } 
                            //echo json_encode($table_data);
                          ?>
<div id="stockDataAndNews">
    <div style="width: 100%; height: 272px;">
    	<div style="width:10%;height: 100%; float: left"></div>
    	<div style="width:80%;height: 100%; float: left">
    		<table id="stock_data">
    			<tr><td class='light'>Stock Ticker symbol</td><td class='grey'><?php echo $table_data['ticker_symbol']; ?></td></tr>
    			<tr><td class='light'>Close</td><td class='grey'><?php echo $table_data['close']; ?></td></tr>
    			<tr><td class='light'>Open</td><td class='grey'><?php  echo $table_data['open']; ?></td></tr>
    			<tr><td class='light'>Previous Close</td><td class='grey'><?php echo $table_data['previous_close'] ?></td></tr>
    			<tr><td class='light'>Change</td><td class='grey'><?php echo $table_data['change']?><img src=<?php echo $table_data['src']?> width='20' height='20'></td></tr>
    			<tr><td class='light'>Change Percent</td><td class='grey'><?php echo $table_data['change Percent']?><img src=<?php echo $table_data['src']?> width='20' height='20'></td></tr>
    			<tr><td class='light'>Day's Range</td><td class='grey'><?php echo $table_data['low'];?>-<?php echo $table_data['high'] ?></td></tr>
    			<tr><td class='light'>Volume</td><td class='grey'><?php echo $table_data['volume'];?></td></tr>
    			<tr><td class='light'>Timestamp</td><td class='grey'><?php echo $table_data['date'];?></td></tr>
    			<tr><td class='light'>Indicator</td><td class='grey'><table id='indicator_table' class='left-right-pd'><tr><td>Price</td><td>SMA</td><td>EMA</td><td>STOCH</td><td>RSI</td><td>ADX</td><td>CCI</td><td>BBANDS</td><td>MACD</td></tr></table></td></tr>
    		</table>
    	</div>
    	<div style="width:10%;height: 100%; float: left"></div>	
    </div>
</div>    

                            <?php
                                class json_obj{
                                    var $last_Refreshed;
                                    var $price_vol_data;
                                }
                                $response_obj=new json_obj;
                                $response_obj->last_Refreshed=$jsonData->{'Meta Data'}->{'3. Last Refreshed'};
                                class price_volume{
                                    var $price;
                                    var $volume;
                                }
                                $graph_data=$jsonData->{'Time Series (Daily)'};
                                $chart_data=array();
                                foreach ($graph_data as $name => $key) {
                                    $chart_data[$name]=new price_volume;
                                    $chart_data[$name]->price=$key->{'4. close'};
                                    $chart_data[$name]->volume=$key->{'5. volume'};
                                }
                                $response_obj->price_vol_data=$chart_data;?>
<div id="chart" style="width: 100%; height: 600px;">
    	<div style="width:10%;height: 100%; float: left"></div>
    	<div style="width:80%; height: 100%;float: left">
    			<div id="container" style="border-style:solid; border-width: 1px; padding-left: 1px;width: 99.85%;height: 99.75%"></div>
    	</div>	
    	<div style="width:10%;height: 100%; float: left"></div>	
</div>
<div id="XML_button" style="width: 100%; height: 50px; display: none;">
    <div style="width: 40%;height: 100%; float: left"></div>
    <div style="width: 20%;height: 100%; float: left;">
       <div style="float: left;">
        <p id="para" style="padding-left: 55px;margin: 0px">Click here to show stock news</p>
        <img onclick="XML_Parse(this)" id="XML_news" width="30" height="20" src="http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png" style="padding-left: 155px; padding-top: 5px;">
        </div>
    </div>
    <div style="width: 40%;height: 100%;float: left;"></div>
</div>
<div style="width: 100%; height: 272px;display: none;" id="stock_news_div">
    <div style="width:10%;height: 100%; float: left"></div>
    <div style="width:80%;height: 100%; float: left">
        <table id="stock_news_table">
        </table>
    </div>
    <div style="width:10%;height: 100%;float: left;"></div> 
</div>

<script type="text/javascript">
indicator_table_handler();
Price_data(); 
 function clear_form(){
        document.getElementById("stockDataAndNews").style.display= "none";
        document.getElementById("chart").style.display= "none";
        document.getElementById("stock_news_div").style.display= "none";
        //document.getElementById("stock_news").style.display= "none";
        document.getElementById("XML_button").style.display= "none";
        document.getElementById("XML_news").setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png");
        document.getElementById("para").innerHTML="Click here to show the stock news";
        //document.getElementById("error_table").style.display="none";
        '<php $_GET["ticker"]=""; ?>'
        document.Input_form.reset();
        document.Input_form.ticker.value="";
    }

function Extract_Date(date)
{
return(date.split(" ")[0]+" "+"14:07:23");
}  

 function Price_data(){   
    var xmlDoc= JSON.parse('<?php echo json_encode($response_obj) ;?>');
    var Refreshed_date= xmlDoc["last_Refreshed"];
    var symbol= '<?php echo $symbol; ?>';
        //var display_date=Refreshed_date.split(" ")[0];
        //var time_stamp= "14:07:23";
        //Refreshed_date = Refreshed_date +" "+time_stamp;
        var display_date=Refreshed_date.split(" ")[0];
        Refreshed_date= Extract_Date(Refreshed_date);
        var new_date=new Date(Refreshed_date);
        var compare_month=new_date.getMonth()+1;
        display_date=compare_month+"/"+ new_date.getDate()+"/"+new_date.getFullYear();
        var priceData = xmlDoc["price_vol_data"];
        var volume=[];
        var price=[];
        var category_data=[];
        var low=10000,f=0;
        for(var values in priceData){
            //start_date=values.split(" ")[0]+" "+"14:07:23";
            start_date=Extract_Date(values);
            console.log(start_date+"\n")
            var XDate=new Date(start_date);
            var XDay= XDate.getDate();
            var XMonth= XDate.getMonth()+1;
            day_month=InString(XMonth)+"/"+InString(XDay);
            category_data.push(day_month);
            volume.push(parseFloat(priceData[values]["volume"]/1000000));
            price.push(parseFloat(priceData[values]["price"]));
            if(parseFloat(priceData[values]["price"])<low){
                low=parseFloat(priceData[values]["price"]);
            }
            if(compare_month-XMonth>5 && f%5==0) {
                            break;
                        }
            f=f+1;            
        }
        low=low/10;
        low=(low*10)-5;
        volume=volume.reverse();
        price=price.reverse();
Highcharts.chart('container', {
     title: {
       text: "Stock Price"+" ("+display_date+")"
    }, 
     navigation: {
        buttonOptions: {
            enabled: true
        }
    },  
    subtitle:{
                text: '<a target="_blank" style="color:blue" href="https://www.alphavantage.co">Source: Alpha Vantage</a>'
            },
    legend: {
                align: 'right',
                verticalAlign: 'top',
                layout: 'vertical',
                x: 0,
                y: 100
            },     
    xAxis: {
                categories: category_data.reverse(),
                labels: {
                            //step: 5,
                            rotation: -30,
                            align: 'right'
                        },
                tickInterval: 5,
                tickLength: 5,
            },
    yAxis: [{ // Primary yAxis
        min:low,       
        tickInterval: 5, 
        labels: {
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        title: {
            text: 'Stock Price',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        }
    }, { // Secondary yAxis
        min: 0,
        tickInterval: 80,
        title: {
            text: 'Volume',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            format: '{value}M',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        opposite: true
    }],     
     series: [{
        name: symbol,
        type: 'area',
        zoomType: 'x',
        panning: true,
        panKey: 'shift',
        data: price,
        color:'#F98A7A'

    }, {
        name: symbol+ ' Volume',
        type: 'column',
        color: "white",
        yAxis: 1,
        data: volume,
         tooltip: {
            valueSuffix: 'M'
        }
    }]   
})
document.getElementById("XML_button").style.display="";
document.getElementById("XML_news").setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png");
 document.getElementById("para").innerHTML="Click here to show the stock news";
}

function indicator_table_handler(){
		var indicator_tab_element= document.getElementById("indicator_table");
		var table_rows= indicator_tab_element.getElementsByTagName("tr");
		var i;
		for(i=0;i<table_rows.length;i++){
			var table_cells= table_rows[i].getElementsByTagName("td");
			for(var j=0;j<table_cells.length;j++){
				table_cells[j].style.color="blue";
				//var css = 'table tr td table tr td:hover{cursor:pointer}';
				//var style = document.createElement('style');
				//if (style.styleSheet) {
    			//	style.styleSheet.cssText = css;
				//}
				//else{
				//style.appendChild(document.createTextNode(css));
			    //}
				//table_cells[j].appendChild(style);
                table_cells[j].style.cursor="pointer";
                table_cells[j].addEventListener("mouseover",function(){
                    this.style.color="black";
                })
                table_cells[j].addEventListener("mouseout",function(){
                    this.style.color="blue";
                })
				table_cells[j].addEventListener("click",function(){
					var indicator=this.childNodes[0].nodeValue;
					var symbol=document.Input_form.ticker.value;
					var API_KEY="QOBQ26JRIG4OOLKH";
                    var URL
                    if(indicator=="STOCH"){
                      URL="https://www.alphavantage.co/query?function="+indicator+"&symbol="+symbol+"&interval=daily&time_period=10&series_type=close&slowkmatype=1&slowdmatype=1&apikey="+API_KEY;
                    }
                    else if(indicator=="BBANDS"){
                    URL="https://www.alphavantage.co/query?function="+indicator+"&symbol="+symbol+"&interval=daily&time_period=5&series_type=close&nbdevup=3&nbdevdn=3&apikey="+API_KEY;    
                    }
                    else{
					URL="https://www.alphavantage.co/query?function="+indicator+"&symbol="+symbol+"&interval=daily&time_period=10&series_type=close&apikey="+API_KEY;
                    }
					if(indicator!="Price"){
					createAsynchronusRequest(URL,display_indicators,symbol,indicator);
                    }
                    else{
                        Price_data();
                    }
                    
				})
			} 
		}
	}
function XML_Parse(val){
    //console.log(val.getAttribute("src"));
        //document.getElementById("XML_button").style.display="";
    	if(val.getAttribute("src")=="http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png"){
		val.setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Up.png");
		document.getElementById("para").innerHTML="Click here to hide the stock news"
		document.getElementById("stock_news_div").style.display= "";
		//document.getElementById("stock_news").style.display= "";
		display_XML_Data();          
		//var symbol=document.Input_form.ticker.value;
		//var URL="stock_updated.php?ticker="+symbol+"&search_xml="+"xml_parse";
		//createAsynchronusRequest(URL,display_XML_Data,symbol,"");
		}
 		else{
				document.getElementById("stock_news_div").style.display= "none";
				//document.getElementById("stock_news").style.display= "none";     
				val.setAttribute("src","http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png");
				document.getElementById("para").innerHTML="Click here to show the stock news"
 			}

		}
function InString(num){
		if(num < 10){

			return('0'+num);
		}
		else{
    		return (num.toString());
		}
	}

function createAsynchronusRequest(URL,call_back,symbol,indicator){
    	var xmlhttp;
    	var xmlDoc;
    	if(window.XMLHttpRequest){
    			xmlhttp=new XMLHttpRequest(); 
    		}
    	else
			{
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
 			}
 		xmlhttp.onreadystatechange = function() {
 				console.log("status"+ xmlhttp.status +"state"+ xmlhttp.statusText);
    		if (this.readyState == 4 && this.status == 200) {
     			xmlDoc = JSON.parse(xmlhttp.responseText);
     			call_back(xmlDoc,symbol,indicator);
     			}
     		else if(this.status==404){
                window.alert("The requested file cannot be found");
                var error_obj={"file_error":"no_file"};
                xmlDoc=JSON.stringify(error_obj);
             }
             else if(this.status==502 || this.status==503 || this.status==505){
             	window.alert("server error occured! Please try later")
             }
            } 
            xmlhttp.open("GET",URL,true); //open, send, responseText are
 			xmlhttp.send(); //properties of XMLHTT
 			//return xmlDoc;   		
    }

var display_indicators = function (xmlDoc,symbol,indicator){
    var indicator_symbol = xmlDoc["Meta Data"]["2: Indicator"];
                    var Refreshed_date= xmlDoc["Meta Data"]["3: Last Refreshed"];
                    //var time_stamp= "14:07:23";
                    //Refreshed_date = Refreshed_date +" "+time_stamp
                    Refreshed_date=Extract_Date(Refreshed_date);
                    console.log(Refreshed_date);
                    var compare_month=new Date(Refreshed_date).getMonth()+1;
                    chart_data= xmlDoc["Technical Analysis: "+indicator];
                    category_data=[];
                    series_data={};
                    var flag=1;
                    var week=0;
                    var f=0;
                    for(var data in chart_data ){
                            //start_date=data+" "+time_stamp;
                            start_date=Extract_Date(data);
                            console.log(start_date+"\n")
                            var XDate=new Date(start_date);
                            var XDay= XDate.getDate();
                            var XMonth= XDate.getMonth()+1;
                            var XYear= XDate.getFullYear();
                            console.log(XMonth+"/"+XDay+"/"+XYear+"\n");
                        day_month=InString(XMonth)+"/"+InString(XDay);
                        category_data.push(day_month);
                        //create_lists(object.keys(chart_data[data]).length,series_data);
                        if(flag==1){
                            var field_keys=Object.keys(chart_data[data]);
                            var symbol_display;
                            if(field_keys.length==1){
                                symbol_display=false;
                            }
                            else{
                                symbol_display=true;
                            }
                            for(var k=0;k<field_keys.length;k++){
                                if(symbol_display){
                                var name=symbol+" "+field_keys[k];
                                }
                                else{
                                 var name=symbol;   
                                }
                                series_data[name]=[];
                            }
                            flag=0;
                        }
                        for(var data_in_data in chart_data[data]){
                            console.log(chart_data[data][data_in_data]);
                            if(symbol_display){
                            var key=symbol+" "+data_in_data;
                            }
                            else{
                                var key=name;
                            }
                            series_data[key].push(parseFloat(chart_data[data][data_in_data]));
                        }

                        if(compare_month-XMonth>5 && f%5==0) {
                            console.log(f);
                            break;
                        }
                        f=f+1;
                    }
                    console.log("categories= ",category_data);
                    console.log("\nseries= ",series_data);
                    var series_data_list=[];
                    var colors=["red","black","green"];
                    var c=0;
                    for(var t in series_data){
                        var obj={
                            name :  t,
                            data : series_data[t].reverse(),
                            color: colors[c++]
                        }
                        series_data_list.push(obj);
                    }

                    Highcharts.chart('container', {
                        title: {
                                text: indicator_symbol
                                },
                         navigation: {
                                buttonOptions: {
                                    enabled: true
                                }
                            },        
                        subtitle:{
                                text: '<a style="color:blue" href="https://www.alphavantage.co">Source: Alpha Vantage</a>'
                                },
                        chart: {
                                marginRight: 180
                                },

                        legend: {
                                align: 'right',
                                verticalAlign: 'top',
                                layout: 'vertical',
                                x: 0,
                                y: 100
                                },

                        xAxis: {
                                categories: category_data.reverse(),
                                labels: {
                                            rotation: -30,
                                            align: 'right'
                                },
                                 tickInterval: 5,
                                 tickLength: 5,
                             },
                        yAxis:{
                               title:{
                                              text:indicator
                               }   
                        },    
                            series:series_data_list
                        });
}
</script>
<br>                            
<?php       
$symbol=$_GET["ticker"];
$URL="https://seekingalpha.com/api/sa/combined/".$symbol.".xml";
//$XML_DATA=file_get_contents($URL);
libxml_use_internal_errors(true);
class XML_JSON_obj{
    var $obje;
}
$xml=@simplexml_load_file($URL);
if($xml===FALSE){
   $error_object= new XML_JSON_obj;
   $error_object->obje="invalid";
}
else{
$ns = $xml->getNamespaces(true);
//echo $xml->channel->item[0]->title;
$arr=array();
class xml_data{
  var $title;
  var $link;
  var $time;
  function setTitle($param){
    $this->title=$param;
  }
   function setLink($param){
    $this->link=$param;
  }  
   function setTime($param){
    $this->time=$param;
  }    
}
$j=0;
for($i=0;$j<5;$i++){
$test_link = $xml->channel->item[$i]->link;
 if(in_array("article",explode("/",$test_link))) {
        $obj= new xml_data;
        $obj->setTitle($xml->channel->item[$i]->title);
        $obj->setLink($xml->channel->item[$i]->link);
        $obj->setTime($xml->channel->item[$i]->pubDate);
        $arr[$j]=$obj;
        $j=$j+1;
        if($j>=5){
            break;
        }
 }
}
$xml_val=new XML_JSON_obj;
$xml_val->obje=$arr;
}
?>
<br>
<script type="text/javascript">
function display_XML_Data(){
var errorObject=<?php echo json_encode($error_object); ?>;
    if(errorObject==null)
    {
        var xmlDoc=<?php echo json_encode($xml_val) ; ?>;
        var xmlDoc=xmlDoc["obje"];
        table_data="";
        for(var i=0;i<xmlDoc.length;i++){
            table_data+="<tr style='border: 1px solid #696969'><td style='background-color: #f2f2f2;font-size:18px'>";
//    console.log(xmlDoc[i]["link"][0]);
        table_data+="<a style='text-decoration: none; padding-right:40px;font-size: 18px' target='_blank' href="+xmlDoc[i]["link"][0]+">"+xmlDoc[i]["title"][0]+"</a>";
        table_data+="   Publicated time:"+xmlDoc[i]["time"][0].split("-")[0]+"</td>"; 
        table_data+="</tr>";
    }
        document.getElementById('stock_news_table').innerHTML = table_data;
    }
    else{
        //window.alert("invalid symbol for XML file");
        document.getElementById('stock_news_table').innerHTML= "<tr style='border: 1px solid #696969'><td style='background-color: #f2f2f2;font-size:18px'>XML file does not exists</td></tr>";
        }
    }

</script>
<?php
 }
 else{?>
	<script type="text/javascript">
    window.alert("Looks like some error occurred!!");
    </script>
    <?php
} 
}
?>
</body>
</html>