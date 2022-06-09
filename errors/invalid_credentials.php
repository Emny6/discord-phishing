<script>
	let pol1 = document.querySelector("#pole1");
	let pol2 = document.querySelector("#pole2");
	pol1.classList.add("unselect");
	pol2.classList.add("unselect");
	document.getElementById("elprimo1").innerText = <?php print_r('"- ' . $alphabet[15] . '"'); ?>;
	document.getElementById("elprimo2").innerText = <?php print_r('"- ' . $alphabet[15] . '"'); ?>;
	document.getElementById("elprimo1").hidden = false;
	document.getElementById("elprimo2").hidden = false;
	document.getElementById("pole1-text").style.color = "#ed4245";
	document.getElementById("pole2-text").style.color = "#ed4245";
	document.querySelector("#sade").classList.remove("submitting-3qlO9O");
	document.getElementById("loadi").style.display = "none";
</script>