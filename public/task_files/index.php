<html>
<head>
	<title>Informed Consent Statement for Judgments in Web-Based Tasks</title>

	<style>
		body {
			font-family: Garamond, Georgia, Times New Roman, serif;
		}
		#wrapper {
			width: 960px;
			margin: 0 auto;
		}
		#inner {
			padding: 0 120px 0 30px;
		}
	</style>
    <script type="text/javascript">

        window.onload = function() {
            document.getElementById('have_read').checked = false;
        }

        
        function toggle_agree_button() {
            var i_agree = document.getElementById('i_agree');
            if (i_agree.disabled == false) {
                i_agree.disabled = true
            }
            else
            {
                i_agree.disabled = false
            }
        } 

    </script>
</head>

<body>
	<div id="wrapper">
		<h1> Informed Consent Statement for Judgments in Web-Based Tasks </h1>

		<p>This experiment is conducted by Dr. Anthony Vance, Dr. Gove Allen, David Eargle, Joshua Lyman, and Nicholas Sullivan of Brigham Young University to examine how to improve user judgments in web-based tasks.</p>

		<div id="inner">
			<p>Participants will be chosen from the Marriott School Behavioral Lab a volunteer basis.
			Your participation will consist of performing several web-based judgment tasks.</p>

			<p>The experiment is expected to take 20-30 minutes.</p>

			<p>There are minimal risks for participation in this study, no more than normal everyday life.</p>

			<p>There are no personal benefits for participating in the study. However, society may benefit by increased understanding of how to improve web-based judgment tasks.</p>

			<p>Involvement in this research project is voluntary. You may withdraw at any time without penalty or refuse to participate entirely.</p>

			<p>There will be no reference to your identification at any point in the research.</p>

			<p>If you have questions regarding this study you may contact Dr. Vance at (801) 361-2531 or via email at anthony@vance.name.</p>

			<p>If you have questions regarding your rights as a participant in research projects, you may contact BYU IRB Administrator, A-285 ASB, Brigham Young University, Provo, UT 84602, 801-422-1461, irb@byu.edu.</p>
		</div>

        <input type='checkbox' name='have_read' id='have_read' onclick='toggle_agree_button()'/><label for='have_read'>I have read and I agree to participate in the survey</label>
        <div><input id='i_agree' type='button' onclick="window.location.href='welcome'" value='I Agree' disabled/></div>
	</div>
</body>
</html>
