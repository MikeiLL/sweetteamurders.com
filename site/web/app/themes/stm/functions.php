add_action( 'wp_enqueue_scripts', 'mzoo_sweet_tea_murders_enqueue_styles' );

function mzoo_sweet_tea_murders_enqueue_styles() {
	wp_enqueue_style(
		'sweet-tea-murders-theme-style',
		get_stylesheet_uri()
	);
}
