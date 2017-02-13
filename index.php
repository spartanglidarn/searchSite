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
			<li class="active"><a href="#">Alla</a></li>
			<li><a href="#">Google</a></li>
			<li><a href="#">Bing</a></li>
		</ul>
		<!-- lista med sökresultat -->
		<div id="allLinks" class="list-group linkList">
			<!--
			<a href="#" class="list-group-item">Item 1</a>
			<a href="#" class="list-group-item">Item 2</a>
			<a href="#" class="list-group-item">Item 3</a>
			<a href="#" class="list-group-item">Item 4</a>
			<a href="#" class="list-group-item">Item 5</a>
			<a href="#" class="list-group-item">Item 6</a>
			<a href="#" class="list-group-item">Item 7</a>
			<a href="#" class="list-group-item">Item 8</a>
			<a href="#" class="list-group-item">Item 9</a>
			<a href="#" class="list-group-item">Item 10</a>
			-->
		</div><!-- lista med sökresultat slutar här-->
		<div id="googleLinks" class="list-group linkList hidden">
			<a href="#" class="list-group-item">Item 1</a>
			<a href="#" class="list-group-item">Item 2</a>
			<a href="#" class="list-group-item">Item 3</a>
			<a href="#" class="list-group-item">Item 4</a>
			<a href="#" class="list-group-item">Item 5</a>
			<a href="#" class="list-group-item">Item 6</a>
			<a href="#" class="list-group-item">Item 7</a>
			<a href="#" class="list-group-item">Item 8</a>
			<a href="#" class="list-group-item">Item 9</a>
			<a href="#" class="list-group-item">Item 10</a>
		</div>
		<div id="bingLinks" class="list-group linkList hidden">
			<a href="#" class="list-group-item">Item 1</a>
			<a href="#" class="list-group-item">Item 2</a>
			<a href="#" class="list-group-item">Item 3</a>
			<a href="#" class="list-group-item">Item 4</a>
			<a href="#" class="list-group-item">Item 5</a>
			<a href="#" class="list-group-item">Item 6</a>
			<a href="#" class="list-group-item">Item 7</a>
			<a href="#" class="list-group-item">Item 8</a>
			<a href="#" class="list-group-item">Item 9</a>
			<a href="#" class="list-group-item">Item 10</a>
		</div>

		<!-- test paragraf för sökresultat -->
		<p id="returnSearchscript">
			
		</p>
	</div><!--tabbar med sökresultat slutar här-->


	</div>
</body>

<?php
include("footer.php");
?>