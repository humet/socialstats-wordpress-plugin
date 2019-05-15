<?php 
/* Add Image Upload to smt Taxonomy */

// Add Upload fields to "Add New Taxonomy" form
function add_smt_image_field() {
    // this will add the custom meta field to the add new term page
   wp_enqueue_media(); 
    ?>
    <div class="form-field">
        <label for="smt_image"><?php _e( 'Category Image:', 'journey' ); ?></label>
        <input type="text" name="smt_image[image]" id="smt_image[image]" class="smt-image" value="<?php echo $smtimage; ?>">
        <input class="upload_image_button button" name="_add_smt_image" id="_add_smt_image" type="button" value="Select/Upload Image" />
        <script>
            jQuery(document).ready(function() {
                jQuery('#_add_smt_image').click(function() {
                    wp.media.editor.send.attachment = function(props, attachment) {
                        jQuery('.smt-image').val(attachment.url);
                    }
                    wp.media.editor.open(this);
                    return false;
                });
            });
        </script>
    </div>
<?php
}
add_action( 'social_category_add_form_fields', 'add_smt_image_field', 10, 2 );

// Add Upload fields to "Edit Taxonomy" form
function journey_smt_edit_meta_field($term) {
    wp_enqueue_media();
    // put the term ID into a variable
    $t_id = $term->term_id;
 
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "social_category_$t_id" ); ?>
    
    <tr class="form-field">
    <th scope="row" valign="top"><label for="_smt_image"><?php _e( 'Category Image', 'journey' ); ?></label></th>
        <td>
            <?php
                $smtimage = esc_attr( $term_meta['image'] ) ? esc_attr( $term_meta['image'] ) : ''; 
                ?>
            <input type="text" name="smt_image[image]" id="smt_image[image]" class="smt-image" value="<?php echo $smtimage; ?>">
            <input class="upload_image_button button" name="_smt_image" id="_smt_image" type="button" value="Select/Upload Image" />
        </td>
    </tr>
    <tr class="form-field">
    <th scope="row" valign="top"></th>
        <td style="height: 150px;">
            <style>
                div.img-wrap {
                    background: #c3c3c3;
                    border: 2px solid #afafaf;
                    background-size:contain; 
                    max-width: 250px; 
                    max-height: 250px; 
                    width: 100%; 
                    height: 100%; 
                    overflow:hidden; 
                }
                div.img-wrap img {
                    max-width: 100%;
                }
            </style>
            <div class="img-wrap">
              
                <img src="<?php if($smtimage){ ?><?php echo $smtimage; ?><?php } else { echo 'http://placehold.it/250'; } ?>" id="smt-img">
            </div>
            <script>
            jQuery(document).ready(function() {
                jQuery('#_smt_image').click(function() {
                    wp.media.editor.send.attachment = function(props, attachment) {
                        jQuery('#smt-img').attr("src",attachment.url)
                        jQuery('.smt-image').val(attachment.url)
                    }
                    wp.media.editor.open(this);
                    return false;
                });
            });
            </script>
        </td>
    </tr>
<?php
}
add_action( 'social_category_edit_form_fields', 'journey_smt_edit_meta_field', 10, 2 );

// Save Taxonomy Image fields callback function.
function save_smt_custom_meta( $term_id ) {
    if ( isset( $_POST['smt_image'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "social_category_$t_id" );
        $cat_keys = array_keys( $_POST['smt_image'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['smt_image'][$key] ) ) {
                $term_meta[$key] = $_POST['smt_image'][$key];
            }
        }
        // Save the option array.
        update_option( "social_category_$t_id", $term_meta );
    }
}  
add_action( 'edited_social_category', 'save_smt_custom_meta', 10, 2 );  
add_action( 'create_social_category', 'save_smt_custom_meta', 10, 2 );




function smt_all_js_css_scripts(){
	?>
	<!-- Add JS in WP Admin for CPT -->
	<script type="text/javascript">
		jQuery(document).ready(function($){

            // Uploading files
            var file_frame;

            jQuery.fn.upload_logo_image = function( button ) {
                var button_id = button.attr('id');
                var field_id = button_id.replace( '_button', '' );

                // If the media frame already exists, reopen it.
                if ( file_frame ) {
                  file_frame.open();
                  return;
                }

                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                  title: jQuery( this ).data( 'uploader_title' ),
                  button: {
                    text: jQuery( this ).data( 'uploader_button_text' ),
                  },
                  multiple: false
                });

                // When an image is selected, run a callback.
                file_frame.on( 'select', function() {
                  var attachment = file_frame.state().get('selection').first().toJSON();
                  jQuery("#"+field_id).val(attachment.id);
                  jQuery("#logoimagediv img").attr('src',attachment.url);
                  jQuery( '#logoimagediv img' ).show();
                  jQuery( '#' + button_id ).attr( 'id', 'remove_logo_image_button' );
                  jQuery( '#remove_logo_image_button' ).text( 'Remove logo image' );
                });

                // Finally, open the modal
                file_frame.open();
            };

            jQuery('#logoimagediv').on( 'click', '#upload_logo_image_button', function( event ) {
                event.preventDefault();
                jQuery.fn.upload_logo_image( jQuery(this) );
            });

            jQuery('#logoimagediv').on( 'click', '#remove_logo_image_button', function( event ) {
                event.preventDefault();
                jQuery( '#upload_logo_image' ).val( '' );
                jQuery( '#logoimagediv img' ).attr( 'src', '' );
                jQuery( '#logoimagediv img' ).hide();
                jQuery( this ).attr( 'id', 'upload_logo_image_button' );
                jQuery( '#upload_logo_image_button' ).text( 'Set logo image' );
            });

            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_fb_select_format' ) === 'Automatically' ){ ?>
                    $("#male_female_auto").show();  
                    $("input#male_female_man").hide();   
                    $("input#age_gender_man").hide();
                    $("input#fb_logo_auto").show();
                    $("input#fb_logo_man").hide();
                    $("input#age_gender_auto").show();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_fb_select_format' ) === 'Manually' ){ ?>
                    $("input#male_female_man").show();
                    $("#male_female_auto").hide(); 
                    $("input#age_gender_man").show();
                    $("input#age_gender_auto").hide();
                    $("input#fb_logo_man").show();
                    $("input#fb_logo_auto").hide();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_ig_select_format' ) === 'Automatically' ){ ?>
                    $("input#ig_logo_auto").show();
                    $("input#ig_logo_man").hide();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_ig_select_format' ) === 'Manually' ){ ?>
                    $("input#ig_logo_man").show();
                    $("input#ig_logo_auto").hide();
                <?php
            } 
            ?>


            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_tw_select_format' ) === 'Automatically' ){ ?>
                    $("input#tw_logo_auto").show();
                    $("input#tw_logo_man").hide();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_tw_select_format' ) === 'Manually' ){ ?>
                    $("input#tw_logo_man").show();
                    $("input#tw_logo_auto").hide();
                <?php
            } 
            ?>


            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_yt_select_format' ) === 'Automatically' ){ ?>
                    $("input#yt_gender_auto").show();  
                    $("input#yt_age_man").hide();   
                    $("input#yt_gender_man").hide();
                    $("input#yt_age_auto").show();
                    $("input#yt_logo_auto").show();
                    $("input#yt_logo_man").hide();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_yt_select_format' ) === 'Manually' ){ ?>
                    $("input#yt_gender_auto").hide();  
                    $("input#yt_age_man").show();   
                    $("input#yt_gender_man").show();
                    $("input#yt_age_auto").hide();
                    $("input#yt_logo_auto").hide();
                    $("input#yt_logo_man").show();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_facebook' ) === 'facebook' ){ ?>
                 $("#smt_facebook_content_cb,div#facebook_container").slideDown();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_instagram' ) === 'instagram' OR  social_media_analytics_get_meta( 'social_media_analytics_instagram' ) === 'API Error'){ ?>
                 $("#smt_ig_content_cb,.ig_container").slideDown();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_youtube' ) === 'youtube' ){ ?>
                 $("#smt_yt_content_cb,div#youtube_container").slideDown();
                <?php
            } 
            ?>
            <?php  if(social_media_analytics_get_meta( 'social_media_analytics_twitter' ) === 'twitter' ){ ?>
                 $("#smt_tw_content_cb,div#twitter_container").slideDown();
                <?php
            } 
            ?>
            $('input#social_media_analytics_facebook').change(function() {
                if($(this).is(":checked")) {
                    $("#smt_facebook_content_cb,div#facebook_container").slideDown();
                }      
                else{
                    $("#smt_facebook_content_cb,div#facebook_container").slideUp();
                }
            });
            $('input#social_media_analytics_instagram').change(function() {
                if($(this).is(":checked")) {
                    $("#smt_ig_content_cb,.ig_container").slideDown();
                }      
                else{
                    $("#smt_ig_content_cb,.ig_container").slideUp();
                }
            });
            $('input#social_media_analytics_youtube').change(function() {
                if($(this).is(":checked")) {
                    $("#smt_yt_content_cb,div#youtube_container").slideDown();
                }      
                else{
                    $("#smt_yt_content_cb,div#youtube_container").slideUp();
                }
            });
            $('input#social_media_analytics_twitter').change(function() {
                if($(this).is(":checked")) {
                    $("#smt_tw_content_cb,div#twitter_container").slideDown();
                }      
                else{
                    $("#smt_tw_content_cb,div#twitter_container").slideUp();
                }
            });
			$('input[type=radio][name=social_media_analytics_yt_select_format]').change(function() {
			    if (this.value == 'Automatically') {
                    $("#yt_gender_auto").show();  
                    $("input#yt_gender_man").hide();   
                    $("input#yt_age_man").hide();
                    $("input#yt_age_auto").show();
                    $("input#yt_logo_auto").show();
                    $("input#yt_logo_man").hide();
                }
		        else if (this.value == 'Manually') {
                    $("#yt_gender_auto").hide();  
                    $("input#yt_gender_man").show();   
                    $("input#yt_age_man").show();
                    $("input#yt_age_auto").hide();
                    $("input#yt_logo_auto").hide();
                    $("input#yt_logo_man").show();
 		        }
		    });
            $('input[type=radio][name=social_media_analytics_fb_select_format]').change(function() {
                if (this.value == 'Automatically') {
                    $("#male_female_auto").show();  
                    $("input#male_female_man").hide();   
                    $("input#age_gender_man").hide();
                    $("input#age_gender_auto").show();
                    $("input#fb_logo_auto").show();
                    $("input#fb_logo_man").hide();
                }
                else if (this.value == 'Manually') {
                    $("input#male_female_man").show();
                    $("#male_female_auto").hide(); 
                    $("input#age_gender_man").show();
                    $("input#age_gender_auto").hide();
                    $("input#fb_logo_auto").hide();
                    $("input#fb_logo_man").show();
                }
            });
            $('input[type=radio][name=social_media_analytics_ig_select_format]').change(function() {
                if (this.value == 'Automatically') {
                    $("input#ig_logo_auto").show();
                    $("input#ig_logo_man").hide();
                }
                else if (this.value == 'Manually') {
                    $("input#ig_logo_man").show();
                    $("input#ig_logo_auto").hide();
                }
            });
            $('input[type=radio][name=social_media_analytics_tw_select_format]').change(function() {
                if (this.value == 'Automatically') {
                    $("input#tw_logo_auto").show();
                    $("input#tw_logo_man").hide();
                }
                else if (this.value == 'Manually') {
                    $("input#tw_logo_man").show();
                    $("input#tw_logo_auto").hide();
                }
            });
		});
	</script>
	<!-- Adding CSS to Support admin -->
	<style type="text/css">
        div#twitter_container {
            background: #1da1f2;
            padding: 20px;
        }
        div#youtube_container {
    background: #ff0002;
    padding: 20px;
}
td.smt_td {
    width: 50%;
}
.smt_total_sub_inner {
    background: #607D8B;
    color: #fff;
    font-size: 16px;
    padding: 15px;
    text-align: center;
}

        table.smt_table td {
    font-size: 15px;
    color: #fff;
    text-align: left;
    font-weight: bold;
}
th.smt_head {
    font-size: 16px;
    color: #fff;
    text-align: left;
    background: #0882ce;
    padding: 13px;
}
th.smt_head {
    font-size: 16px;
    color: #fff;
    text-align: left;
    background: #0882ce;
    padding: 13px;
}
table.smt_table input {
    width: 100%;
    height: 45px;
    box-shadow: none;
    padding: 15px;
}
div#facebook_container {
    background: #3b5998;
}
table.smt_table {
    width: 100%;
}
div#youtube_container th.smt_head {
    background: #c30205;
}
table.smt_table td {
    padding: 10px;
}
.ig_container {
    background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%,#d6249f 60%,#285AEB 90%);
}
.ig_container  th.smt_head {
    background: #1e1f21;
}
.api_error_smt {
    color: #fff;
    background: #F44336;
    font-size: 15px;
    text-align: center;
    padding: 20px;
}
select#social_media_analytics_social_media_account {
    height: 50px;
    width: 100%;
    max-width: 300px;
}
label.smt_label_admin {
    display: block;
    background: #607D8B;
    padding: 14px;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
}
input#social_media_analytics_social_media_account_link {
    height: 50px;
    width: 100%;
    max-width: 300px;
}
div#edit-slug-box {
    display: none;
}
.api_info_smt {
    background: #4CAF50;
    color: #fff;
    font-size: 16px;
    text-align: center;
    padding: 16px;
}
a.authorize-yt-acc {
    width: 100%;
    display: block;
    text-align: center;
    background: #ff0004;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    padding: 10px 0;
}
input#social_media_analytics_authentication_yt_cid, input#social_media_analytics_authentication_yt_cst {
    width: 100%;
    height: 40px;
}
.smt_profile_user img {
    max-width: 120px;
    border-radius: 50%;
    border: 4px solid #444;
    margin: 0 auto;
    display: block;
    margin-bottom: 16px;
}
.ig_container {
    padding-top: 20px;
}
div#facebook_container {
    padding-top: 20px;
}
label.smt_label_admin.fb_color_smt {
    background: #3b5998;
}
label.smt_label_admin.ig_color_smt {
    background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%,#d6249f 60%,#285AEB 90%);
    text-shadow: 1px 1px 8px #000000ad;
}
label.smt_label_admin.yt_color_smt {
    background: #ff0002;
}
label.smt_label_admin.tw_color_smt {
    background: #1da1f2;
}
.smt_social_cb_boxes {
    padding-left: 10px;
    padding-bottom: 3px;
    padding-top: 4px;
}
#smt_facebook_content_cb, #smt_ig_content_cb, #smt_yt_content_cb, #smt_tw_content_cb{
    display: none;
    border: 2px solid #607d8a;
    margin-bottom: 20px;
    padding: 10px;
}
.smt_input_links {
    height: 48px;
    width: 100%;
    max-width: 310px;
    padding-left: 15px;
}
div#facebook_container, div#youtube_container, div#twitter_container, .ig_container {
    display: none;
}
.smt-select-format-sec {
    padding-bottom: 15px;
    padding-top: 15px;
}
.smt-select-format-sec input {
    margin-left: 11px;
}
.smt-select-format-sec {
    background: #eaecec;
    border: 1px solid #ccd4d8;
    padding-top: 0;
    margin-top: 15px;
}
div#facebook_container, div#youtube_container, div#twitter_container, .ig_container {
    margin-bottom: 12px;
}
span.smt_numperctage {
    display: block;
    font-style: italic;
    font-weight: normal;
    font-size: 14px;
}
table.smt_table td {
    text-transform: capitalize;
}

	</style>
	<?php
}