<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 */
?>
	</div><!-- #main -->

	<div id="footer" role="contentinfo">
		<div id="colophon">

<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	get_sidebar( 'footer' );
?>

			<div id="site-info">
				<a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</div><!-- #site-info -->

			<div id="site-generator">
				<?php do_action( 'twentyten_credits' ); ?>
				<a href="<?php echo esc_url( __('http://wordpress.org/', 'darktt') ); ?>"
						title="<?php esc_attr_e('Semantic Personal Publishing Platform', 'darktt'); ?>" rel="generator">
					<?php printf( __('Proudly powered by %s.', 'darktt'), 'WordPress' ); ?><br/>
				</a>
				<p class="shake-me">
					<a href="<?php echo esc_url('http://www.seismicthemes.com/dark-tt'); ?>" title="Twenty Ten Dark Child Theme">
						Twenty Ten Dark 
					</a>
					 by Seismic Themes
				</p>
			</div><!-- #site-generator -->

		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->
<div class="scroll-top">
	<a href="#"><?php echo __('Back to Top', 'darktt'); ?> &uarr;</a>
</div>
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>