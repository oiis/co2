<div class="col-lg-4 col-md-5 col-sm-6 padding-top-15 hidden pull-right bg-white shadow2" id="amendement-container">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<h5 class="pull-left"><i class="fa fa-angle-down"></i> Amendements</h5>
		<button class="btn btn-default pull-right" id="btn-hide-amendement"><i class="fa fa-times"></i></button>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12">
		<hr>
		<button class="btn btn-link radius-5 text-purple col-lg-12 col-md-12 col-sm-12 btn-create-amendement">
			<i class="fa fa-pencil"></i> Proposer un amendement
		</button>
	</div>

	 <div class="form-group col-lg-12 col-md-12 col-sm-12 hidden" id="form-amendement">
	  <hr>
	  <label><i class="fa fa-pencil"></i> Rédiger votre amendement :</label><br>
	  <small><i>Si votre amendement est accepté, il sera ajouté à la suite de la proposition principale,<br>et fera parti de la proposition finale, soumise au vote.</i></small><br><br>
	  <textarea class="form-control" rows="5" id="txtAmdt" placeholder="votre amendement"></textarea>
	  <br>
	  <small class="pull-right"><i>Les amendements ne peuvent dépasser la taille de 500 caractères.</i></small>
	  <br>
	  <button class="btn btn-sm btn-link radius-5 bg-green-k pull-right" id="btn-save-amendement">
			<i class="fa fa-save"></i> Enregistrer mon amendement
	  </button>
	  <button class="btn btn-sm btn-link radius-5 bg-red pull-right margin-right-10" id="btn-save-amendement">
			<i class="fa fa-times"></i> Annuler
	  </button>
	  <hr class="col-lg-12 col-md-12 col-sm-12 no-padding">
	</div> 

	<?php 
		$i=0;		
		$allVotesRes = array();
		if(@$amendements)
		foreach($amendements as $key => $am){ $i++;
			//var_dump($am); //exit;
			$author = Person::getSimpleUserById(@$am["idUserAuthor"]);
			$allVotes = @$am["votes"] ? $am["votes"] : array();
			$myId = Yii::app()->session["userId"];
			$hasVoted = Cooperation::userHasVoted($myId, $allVotes);
	 		$voteRes = Proposal::getAllVoteRes($am);
	 		unset($voteRes["uncomplet"]);
	 		$allVotesRes[$key] = $voteRes;

	 		$this->renderPartial('../cooperation/pod/amendement', array("key"=>$key, "am"=>$am,
		 																"author" => $author,
		 																"voteRes" => $voteRes,
		 																"allVotes" => $allVotes,
		 																"myId" => $myId,
		 																"hasVoted" => $hasVoted));
		} 
	 ?>
		
</div>

<script type="text/javascript">
	var myPieChart;
	var amendements = <?php echo json_encode(@$amendements); ?> ;
	var allVotesRes = <?php echo json_encode($allVotesRes); ?>;
	jQuery(document).ready(function() { //alert("start loadchart");
		
		var i=0;
		if(allVotesRes != null){
			$.each(allVotesRes, function(key, voteRes){
				var voteValues = new Array();
				var totalVotant = 0;

				if(voteRes.up != "undefined") 		{ voteValues.push(voteRes.up.percent); totalVotant+=voteRes.up.votant; }
				if(voteRes.down != "undefined") 	{ voteValues.push(voteRes.down.percent); totalVotant+=voteRes.down.votant; }
				if(voteRes.white != "undefined") 	{ voteValues.push(voteRes.white.percent); totalVotant+=voteRes.white.votant; }
				
				if(totalVotant > 0)
				chartInitAm(key, voteValues);
			});
		}

		$("#btn-save-amendement").click(function(){
			uiCoop.saveAmendement(idParentProposal, "add");
		});

		$(".btn-send-vote-amendement").click(function(){
			var voteValue = $(this).data('vote-value');
			var idAmdt = $(this).data('vote-id-amdt');
			console.log("send vote", voteValue),
			uiCoop.sendVote("amendement", idParentProposal, voteValue, idParentRoom, idAmdt);
		});
	});

	function chartInitAm(key, data){ 
		console.log("chartInitAm", key, data);
		var data = {
		    datasets: [{
		    	data: data,
			    // These labels appear in the legend and in the tooltips when hovering different arcs
			    backgroundColor: [
	                '#34a853',
	                '#E33551',
	                '#FFF',
	            ],
	            borderColor: [
	                '#34a853',
	                '#E33551',
	                '#aba9a9',
	            ],
	            borderWidth: 1
            }],
            labels: [
			        'Pour',
			        'Contre',
			        'Blanc',
			    ],
			    
		};
		var ctx = $("#res-vote-chart-"+key).get(0).getContext("2d");
		var options;
		myPieChart = new Chart(ctx,{
		    type: 'pie',
		    data: data,
			options: {
				responsive: true,
				//maintainAspectRatio:false,
				legend: {
					display: false
				},
				animation: {
					duration: 300
				}
			},
		    //options: options
		});
	}
</script>