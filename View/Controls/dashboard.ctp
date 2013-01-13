<?php
App::import('Vendor', 'Z.PasswordHash');
?>
<?php
echo $this->Html->css('/z/js/jquery.jqplot.min.css');
echo $this->Html->script('/z/js/jquery.min.js');
echo $this->Html->script('/z/js/jquery.jqplot.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.barRenderer.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.pointLabels.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.categoryAxisRenderer.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.logAxisRenderer.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.dateAxisRenderer.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.canvasTextRenderer.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.canvasAxisTickRenderer.js');
echo $this->Html->script('/z/js/plugins/jqplot.canvasAxisLabelRenderer.min.js');
echo $this->Html->script('/z/js/plugins/jqplot.highlighter.min.js');
?>

<div class="accounts index">
<?php //debug($this); ?>
<?php //debug($z_wordlists); ?>
	<h2><?php echo __d('z', 'Settings and Dashboard'); ?></h2>
	<li>version=<?php echo $z_version; ?>
	<li>mt_getrandmax=<?php echo mt_getrandmax(); ?>
	<li>token_length=<?php echo $z_token_length; ?>
	<li>password hash cost=<?php echo $z_hash_cost; ?>
	<li>word lists (used: <?php echo $z_use_password_blacklist?"yes":"no" ?>) : <?php echo implode(', ', $z_wordlists); ?>
	<li>accounts total=<?php echo $accounts_total; ?>
	<li>accounts active=<?php echo $accounts_active; ?>
	<li>tokens active=<?php echo $tokens; ?>
	<?php //debug(json_encode($logins['good']));?>
	<?php //debug(json_encode($logins['bad']));?>
	<?php //debug(max($logins['good']));?>
	<?php //debug(max($logins['bad']));?>
	<?php //debug(json_encode($accounts['good']));?>
	<?php //debug(json_encode($accounts['bad']));?>

	<div id="logins_chartdiv" class="resetcss" style="height:400px;width:500px; margin:auto; text-align:left;"></div>
	<div id="accounts_chartdiv" class="resetcss" style="height:400px;width:500px; margin:auto; text-align:left;"></div>

</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'accounts')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Tokens'), array('action' => 'tokens')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Cryptography tests'), array('action' => 'cryptotest')); ?></li>
	</ul>
</div>

<?php
	if ( empty($logins['good']) ) {
		$log_good = '[[null]]';
		$max_log['good'] = 0;
	} else {
		$log_good = json_encode($logins['good']);
		$max_log['good'] = max($logins['good'])[1];
	}
	if ( empty($logins['bad']) ) {
		$log_bad = '[[null]]';
		$max_log['bad'] = 0;
	} else {
		$log_bad = json_encode($logins['bad']);
		$max_log['bad'] = max($logins['bad'])[1];
	}
?>
<script>
$(document).ready(function(){
    var logindata;
    $logindata = [<?php echo $log_good.",".$log_bad; ?>];
    var jqplot_logins = $('#logins_chartdiv').jqplot("logins_chartdiv", $logindata,
    		{ 
			axesDefaults: {
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			},
			axes:{
				xaxis:{
					label: "Date",
					renderer:$.jqplot.DateAxisRenderer,
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					tickOptions:{
						//formatString:'%#d %b %Y', 
						formatString:'%Y-%m-%d', 
						angle: -30
					},
				},
				yaxis: {
					label: "Login attempts",
					//renderer: $.jqplot.LogAxisRenderer,
					rendererOptions: {
						//tickDistribution:'power',
						minorTicks: 1
					},
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					tickOptions:{formatString:'%d'},
					min:0,
					max: <?php echo ceil(max($max_log) / 10)*10; ?>,
				}
			},
			highlighter: {
			        show: true,
				sizeAdjust: 7.5
			},
			seriesDefaults: {
				rendererOptions: {
					smooth: true,
					//animation: {
					//	show: true
					//}
				}
			},
			series: [
				{
					label: 'Successful'
				},
				{
					label: 'Failed'
				}
			],
			legend: {
				show: true,
				placement: 'inside',
				location: 'nw'
			}
		}
    	);
});
</script>
<?php
	if ( empty($accounts['good']) ) {
		$acc_good = '[[null]]';
		$max_acc['good'] = 0;
	} else {
		$acc_good = json_encode($accounts['good']);
		$max_acc['good'] = max($accounts['good'])[1];
	}
	if ( empty($accounts['bad']) ) {
		$acc_bad = '[[null]]';
		$max_acc['bad'] = 0;
	} else {
		$acc_bad = json_encode($accounts['bad']);
		$max_acc['bad'] = max($accounts['bad'])[1];
	}
?>
<script>
$(document).ready(function(){
    var accountdata;
    $accountdata = [<?php echo $acc_good.",".$acc_bad; ?>];
    var jqplot_accounts = $('#accounts_chartdiv').jqplot("accounts_chartdiv", $accountdata,
    		{ 
			//stackSeries: true,
			seriesDefaults: {
				renderer:$.jqplot.BarRenderer,
				pointLabels: { show: true, stackedValue: false, stackSeries: true },
				rendererOptions: { barMargin: 30, barPadding: 8 }
			},
			series: [
				{ label: 'Active' },
				{ label: 'Not activated' }
			],
			axesDefaults: {
				labelRenderer: $.jqplot.CanvasAxisLabelRenderer
			},
			axes:{
				xaxis:{
					label: "Date",
					renderer:$.jqplot.DateAxisRenderer,
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					tickOptions:{
						//formatString:'%#d %b %Y', 
						formatString:'%Y-%m-%d', 
						angle: -30
					},
				},
				yaxis: {
					label: "New accounts",
					//renderer: $.jqplot.LogAxisRenderer,
					//rendererOptions: {
					//	minorTicks: 1
					//},
					tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					tickOptions:{formatString:'%d'},
					min:0,
					max: <?php echo ceil(max($max_acc) / 10)*10; ?>,
					padMin: 0
				}
			},
			highlighter: {
				show: true,
				sizeAdjust: 7.5
			},
			legend: {
				show: true,
				placement: 'inside',
				location: 'nw'
			}
		}
    	);
});
</script>
