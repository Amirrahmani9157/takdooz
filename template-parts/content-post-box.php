<?php 
global $post, $template_dir;
$post_id 			= $post->ID;
$post_title 		= wp_trim_words(get_the_title($post_id) , '10' , '...');
$post_image			= get_the_post_thumbnail_url($post_id);
$post_date			= date_i18n('Y/m/d' , strtotime(get_the_date($post_id)));
$post_url			= get_the_permalink($post_id);
$post_cat 			= wp_get_post_terms($post_id, 'category', array('fields' => 'names'));
$post_cat 			= $post_cat ? $post_cat : null;
$author_id 			= get_post_field( 'post_author', $post_id );
$author_name 		= get_the_author_meta( 'display_name', $author_id );
 ?>
<div class="rb_post_box">
	<a href="<?php echo $post_url; ?>"><img src="<?php echo $post_image; ?>" alt="<?php echo $post_title; ?>" class="rb_post_box_image"></a>
	<div class="rb_post_box_content">
		<a href="<?php echo $post_url; ?>"></a>
		<div class="post_category"><?php echo $post_cat ? $post_cat[0] : ''; ?></div>
		<a href="<?php echo $post_url; ?>"><div class="post_title"><?php echo $post_title; ?></div></a>
		<div class="rb_flex_wrap">
			<span class="post_author">
				<?php echo 'نوشته شده توسط ' . $author_name; ?>
			</span>
			<span class="post_date">
				<?php echo $post_date; ?>
			</span>
		</div>
	</div>
</div>