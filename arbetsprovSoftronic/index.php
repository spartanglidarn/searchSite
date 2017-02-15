<?php 
include("header.php");

?>
	<div class="container">

	<!--sökrutan-->
	<div class="row">
		<form action="*">
		<div class="col-sm-10">
		  <input id="searchfield" type="text" name="search">
		</div>
		<div class="col-sm-2">
		  <input type="submit" id="submit" value="Search">
		</div>
		</form>
	</div> <!--sökrutan slutar här-->

	<!--tabbar med sökresultat-->
	<div id="resulttabs" class="row">
		<ul class="nav nav-tabs">
			<li id="allButton" class="linkButton active"><a href="#">Alla</a></li>
			<li id="googleButton" class="linkButton"><a href="#">Google</a></li>
			<li id="bingButton" class="linkButton"><a href="#">Bing</a></li>
		</ul>
		<!-- lista med sökresultat -->
		<div id="allLinks" class="list-group linkList">
			<!--
			Alla resultat visas här.
			<a href="#" class="list-group-item">Item 1</a>
			-->
		</div><!-- lista med sökresultat slutar här-->
		<div id="googleLinks" class="list-group linkList hidden">
		<!--Google resultat visas här -->
		</div>
		<div id="bingLinks" class="list-group linkList hidden">
		<!--Bing resultat visas här -->
		</div>

		<!-- test paragraf för sökresultat -->
		<p id="returnSearchscript">
			
		</p>
	</div><!--tabbar med sökresultat slutar här-->


	</div>
</body>


</html>