<footer class="footer">&copy; 2023 Finsta</footer>
</div>

<?php
if( DEBUG_MODE ){
	include('includes/debug-output.php');
} ?>


<?php if( $logged_in_user ){ ?>
<script type="text/javascript" src="js/like.js"></script>
<script type="text/javascript" src="js/follow.js"></script>
<script type="text/javascript" src="js/rating.js"></script>
<?php } //end if logged in ?>
</body>
</html>