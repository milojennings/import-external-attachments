<style type="text/css">
	#import_posts #processing { background: url( <?php echo EXTERNAL_IMAGES_URL; ?>/images/ajax-loader.gif ) top left transparent no-repeat; padding: 0 0 0 23px; }
</style>

<div class="wrap" style="overflow:hidden;">
	<div class="icon32" id="icon-upload"><br></div>
	<h2>Import external attachments</h2>

	<?php
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'backcatalog' ) {

			echo '<div id="message" class="updated fade" style="background-color:rgb(255,251,204); overflow: hidden; margin: 0 0 10px 0">';
			external_image_backcatalog();
			echo '</div>';

		} elseif ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
			update_option('external_image_whichimgs',   esc_html( $_POST['external_image_whichimgs'] ) );
			update_option('external_image_excludes',   	esc_html( $_POST['external_image_excludes'] ) );

			echo '<div id="message" class="updated fade" style="background-color:rgb(255,251,204);"><p>Settings updated.</p></div>';
		}
	?>


	<form name="external_image-options" method="post" action="" style="width:300px; padding: 0 20px; margin: 20px 20px 0 0 ; float: left; background: #f6f6f6; border: 1px solid #e5e5e5; ">
	<h2 style="margin-top: 0px;">Options</h2>
		<?php settings_fields('external_image'); ?>
		<h3>Which external links to process:</h3>
		<p>By default, all external images and pdfs are processed.  This can be set to ignore certain domains.</p>
		<p>
		<label for="myradio1">
			<input id="myradio1" type="radio" name="external_image_whichimgs" value="all" <?php echo (get_option('external_image_whichimgs')!='exclude'?'checked="checked"':''); ?> /> All attachments
		</label>
		</p>
		<p>
		<label for="myradio2">
			<input id="myradio2" type="radio" name="external_image_whichimgs" value="exclude" <?php echo (get_option('external_image_whichimgs')=='exclude'?'checked="checked"':''); ?> /> Exclude by domain
		</label>
		</p>
		<p><label for="myradio2">Domains to exclude (comma separated):</label></p>
		<p class="howto">Example: smugmug.com, flickr.com, picasa.com, photobucket.com, facebook.com</p>
		<p><textarea style="height:90px; width: 294px;"id="external_image_excludes" name="external_image_excludes"><?php echo ( get_option('external_image_excludes') != '' ? get_option('external_image_excludes') : '' ); ?></textarea></p>

		<div class="submit">
			<input type="hidden" name="external_image_update" value="action" />
			<input type="submit" name="submit" class="button-primary" value="Save Changes" />
		</div>
	</form>

	<div id="import_all_images" style="float:left; margin:0px; display:inline; width:500px; ">

	<h2 style="margin-top: 0px;">Process all posts</h2>

		<?php

			$posts = external_image_get_post_list();

			$count = 0;
			foreach( $posts as $this_post ) {
				$images = external_image_get_img_tags ($this_post->ID);
				if( !empty( $images ) ) {
					$posts_to_fix[$count]['title'] = $this_post->post_title;
					$posts_to_fix[$count]['images'] = $images;
					$posts_to_fix[$count]['id'] = $this_post->ID;
				}
				$count++;
			}

			$import = '<div style="float:left; margin: 0 10px;">';
			$import .= '<p class="submit" id="bulk-resize-examine-button">';
			$import .= '<button class="button-primary" onclick="external_images_import_images();">Import attachments now</button>';
			$import .= '</p>';

			$import .= '<div id="import_posts" style="display:none padding:25px 10px 10px 80px;"></div>';
			$import .= '<div id="import_results" style="display:none"></div>';

    		$import .= '</div>';

			$html = '';

			if ( is_array( $posts_to_fix ) ) {
				$html .= '<p class="howto">Please note that this can take a long time for sites with a lot of posts. You can also edit each post and import one post at a time.</p>';
				$html .= '<p class="howto">We will process up to 50 posts at a time. You should <a class="button-secondary" href="'.admin_url('upload.php?page=external_image').'">refresh the page</a> when done to check if you have more than 50 posts.</p>';
				$html .= '<p class="howto">Only '.EXTERNAL_IMAGES_MAX_COUNT.' per post will be imported at a time to keep things from taking too long. For posts with more than that, they will get added back into the list when you refresh or come back and try again.</p>';

				$html .= $import;
				$html .= '<div id="posts_list" style="padding: 0 5px; margin: 0px; clear:both; ">';
				$html .= '<h4>Here is a look at posts that contain external attachments:</h4>';

				$html .= '<ul style="padding: 0 0 0 5px;">';
				foreach( $posts_to_fix as $post_to_fix ) {
					$html .= '<li>"<strong>'.$post_to_fix['title'].'</strong>" - ' .count($post_to_fix['images']). ' images. <a href="'.admin_url('post.php?post='.$post_to_fix['id'].'&action=edit').'">Edit Post</a>.</li>';
				}
				$html .= '</ul>';
				$html .= '</div>';



			} else {
				$html .= "<p>We didn't find any external attachments to import. You're all set!</p>";

			}
			$html .= '</div>';

			echo $html;

		?>

	</div>
</div>