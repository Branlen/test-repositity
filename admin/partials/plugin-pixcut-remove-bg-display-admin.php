<?php

/**
 * Admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://pixcut.wondershare.com
 * @since      1.0.0
 *
 * @package    pixcut-remove-bg
 * @subpackage pixcut-remove-bg/admin/partials
 */
$log_file = false;
$wp_upload_dir = wp_upload_dir();
$log_file_dir = $wp_upload_dir['basedir'] . '/pixcut-remove-bg-log/log.txt';
if (file_exists($log_file_dir)) {
    $log_file = true;
}
global $wpdb;
$sql = "SELECT count(id) as count FROM `" . $wpdb->prefix . "wc_pixcut_remove_bg_backup`";
$count = $wpdb->get_var($sql);

if (!is_admin() || !current_user_can('edit_pages')) {
    return false;
}
$removeBG_apikey = get_option('Pixcut_RemoveBG_ApiKey');
$background_color = get_option('Pixcut_RemoveBG_Background_Color');
$background_option = get_option('Pixcut_RemoveBG_Background');
$removeBG_products =  get_option('Pixcut_RemoveBG_products');
$removeBG_productsIds = get_option('Pixcut_RemoveBG_products_IDs');
$removeBG_thumbnail = get_option('Pixcut_RemoveBG_thumbnail');
$removeBG_gallery = get_option('Pixcut_RemoveBG_gallery');
$removeBG_include_processed = get_option('Pixcut_RemoveBG_Include_Processed');
?>

<div class="pixcut-wrap px-4">
    <div class="mt-4 d-flex align-items-center justify-content-between pr-5">
        <span class="d-flex align-items-center "> <img src="https://neveragain.allstatics.com/2019/assets/icon/logo/pixcut-square.svg" class="mr-2" alt="PixCut Logo icon" style="width: 32px; height: 32px;">
            <h2>Wocommerce Pixcut remove background</h2>
        </span>
        <button class="btn btn-primary mr-2 " type="button" id="buyCredits" style="width: 120px;">
            Buy Credits
        </button>
    </div>


    <?php if (!$removeBG_apikey) : ?>
        <div id="appkeyWarning" class="bd-callout bd-callout-warning">
            <p><span class="bold">WARNING:</span> You have not entered your <a href="https://www.remove.bg/?aid=qzfprflpwxrcxmbm" target="_blank">remove.bg</a> API key. This plugin doesn't work without it. To obtain the API key, please follow next steps:
            <ol>
                <li>Sign up to <a target="_blank" href="https://www.remove.bg/?aid=qzfprflpwxrcxmbm">remove.bg</a> site by going <a target="_blank" href="https://www.remove.bg/users/sign_up/?aid=qzfprflpwxrcxmbm">here</a>. Skip this step if you have already signed up;</li>
                <li>Sign in to your account at <a target="_blank" href="https://www.remove.bg/?aid=qzfprflpwxrcxmbm">remove.bg</a> by going <a target="_blank" href="https://www.remove.bg/users/sign_in/?aid=qzfprflpwxrcxmbm">here</a>;</li>
                <li>Navigate to API key tab at your <a target="_blank" href="https://www.remove.bg/?aid=qzfprflpwxrcxmbm">remove.bg</a> profile by going <a target="_blank" href="https://www.remove.bg/profile#api-key/?aid=qzfprflpwxrcxmbm">here</a>;</li>
                <li>Click the button SHOW and copy-paste the revealed API-key into appropriate field of this plugin.</li>
            </ol>

            </p>
        </div>
    <?php endif; ?>


    <form method="post" id="Pixcut_RemoveBG_Form">
        <?php echo wp_nonce_field('update-options');  ?>
        <input type="hidden" value="Not all options selected" id="alert-text" />
        <input type="hidden" value="No images to process" id="alert-text-no-images" />
        <input type="hidden" value="<?php echo get_current_user_id(); ?>" id="schk" />

        <table class="table table-striped table-borderless mt-2">

            <tr valign="center">
                <th scope="row">
                    <p class="d-flex align-items-center table-title">RemoveBG Api key:
                        <span class="tooltip ml-2">
                            <span class="dashicons dashicons-info-outline" style="font-size: 18px;"></span>
                            <span class="tooltiptext">Get the API key from remove.bg profile</span></span>
                    </p>
                </th>
                <td>
                    <div class="table-content">
                        <input type="text" class="input" style="width: 60%" name="Pixcut_RemoveBG_ApiKey" value="<?php echo esc_attr($removeBG_apikey) ?>" />
                    </div>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="vertical-align: top;">
                    <p class="d-flex align-items-center table-title">Choose target products:
                        <span class="tooltip ml-2">
                            <span class="dashicons dashicons-info-outline" style="font-size: 18px;"></span>
                            <span class="tooltiptext">Whether to process all products or only products with provided IDs</span></span>
                    </p>
                </th>
                <td>

                    <div class="table-content">
                        <input type="radio" id="products_all" name="Pixcut_RemoveBG_products" value="all" <?php echo checked(('all' == $removeBG_products || 'specified' != $removeBG_products), true, false) ?> /><label for="products_all">Remove background from all products</label><br>
                        <input type="radio" id="products_spec" name="Pixcut_RemoveBG_products" value="specified" <?php echo checked('specified' == $removeBG_products, true, false) ?> /><label for="products_spec">Remove background only from specified products </label><span class="desc">&nbsp; (IDs of products to process: comma separated or ranges, i.e. 3,9,20-27,40-45)</span>
                        <input type="text" class="input  mt-2" style="width: 60%;" <?php if ('specified' != $removeBG_products) echo ' readonly disable'; ?> placeholder="4,156,271" name="Pixcut_RemoveBG_products_IDs" value="<?php echo esc_attr($removeBG_productsIds) ?>" />
                    </div>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row" style="vertical-align: top;">
                    <p class="d-flex align-items-center table-title">Choose target images:
                        <span class="tooltip ml-2">
                            <span class="dashicons dashicons-info-outline" style="font-size: 18px;"></span>
                            <span class="tooltiptext">"Main image" - processes only main image of a product, "Product gallery" - processes only product gallery images. Check both to process all images of a product</span></span>
                    </p>
                </th>
                <td>
                    <div class="table-content">
                        <input type="checkbox" id="target_main" name="Pixcut_RemoveBG_thumbnail" value="1" <?php echo checked(1 == $removeBG_thumbnail, true, false) ?> /><label for="target_main">Main image</label><br>
                        <input type="checkbox" id="target_gallery" name="Pixcut_RemoveBG_gallery" value="1" <?php echo checked(1 == $removeBG_gallery, true, false) ?> /><label for="target_gallery">Product gallery</label>
                    </div>
                </td>
            </tr>
            <tr valign="center">
                <th scope="row">
                    <p class="d-flex align-items-center table-title">Include processed images
                        <span class="tooltip ml-2">
                            <span class="dashicons dashicons-info-outline" style="font-size: 18px;"></span>
                            <span class="tooltiptext">By default, plugin processes each image only once. If checked, plugin will not skip earlier processed images and will overwrite them</span></span>
                    </p>
                </th>
                <td>

                    <div class="table-content">
                        <input type="checkbox" class="mb-0" name="Pixcut_RemoveBG_Include_Processed" value="1" <?php echo checked(1 == $removeBG_include_processed, true, false) ?> />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="vertical-align: top;">
                    <p class="d-flex align-items-center table-title">Make new background
                        <span class="tooltip ml-2">
                            <span class="dashicons dashicons-info-outline" style="font-size: 18px;"></span>
                            <span class="tooltiptext">"Transparent" - processed images will have transarent background, "Color" - sets chosen color as new background of processed images, "Custom image" - sets your image as new background of processed images</span></span>
                    </p>
                </th>
                <td>

                    <div class="table-content">
                        <input type="radio" id="newbg_transp" name="Pixcut_RemoveBG_Background" value="transparent " <?php echo checked(('transparent' == $background_option || 'color' != $background_option ), true, false) ?> /><label for="newbg_transp">Transparent</label><br>
                        <!-- <div style="height:10px"></div> -->
                        <input type="radio" id="newbg_color" name="Pixcut_RemoveBG_Background" value="color" <?php echo checked('color' == $background_option, true, false) ?> /><label for="newbg_color" style="height: 27px;line-height: 27px;"> Color </label>

                        <input type="color" class="ml-2" name="Pixcut_RemoveBG_Background_Color" id="background_color" value="<?php echo $background_color ?>">
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="vertical-align: top;">
                    <p class="d-flex align-items-center table-title">Preview a product
                        <span class="tooltip ml-2">
                            <span class="dashicons dashicons-info-outline" style="font-size: 18px;"></span>
                            <span class="tooltiptext">You can test and preview the background removal for any product before starting actual process. Enter ID of a product to see its main image after backround removal process with current settings. This will not affect actual image of the product at your site</span></span>
                    </p>
                </th>
                <td>
                    <div class="table-content">
                        <div class="d-flex align-items-center">
                            <input type="text" class="input mr-2" placeholder="Enter a product id to preview result" name="Pixcut_RemoveBG_TestProduct" value="" style="width: 30%;" /> <input type="button" class="btn btn-primary pixcut-button-click mb-0" id="start_preview" value="Preview">
                        </div>
                        <div id="previewresult" style="display:none;" class=" align-items-center">
                            <img src="" class="img-before-remove-bg" />
                            <svg t="1632646593717" class="icon mx-4" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="9840" width="48" height="48">
                                <path d="M512.299934 1023.800044c-10.797617 0-21.595234-3.999117-29.993381-11.797396-17.496139-16.496359-18.195984-44.090269-1.799602-61.586408l412.508958-437.10353c8.398147-8.898036 8.298169-23.894726-0.599868-32.692784L481.606708 74.409578c-17.096227-16.896271-17.296183-44.490181-0.299934-61.586408 16.896271-16.896271 44.390203-17.196205 61.586408-0.299934l410.809333 406.11037c42.290666 41.790777 43.590379 111.075485 2.699404 154.465909l-412.508958 437.003552c-8.69808 9.097992-20.195543 13.696977-31.593027 13.696977z" fill="#4a47ff" p-id="9841"></path>
                                <path d="M86.093999 924.821889c-10.697639 0-21.495256-3.999117-29.793425-11.897374-17.496139-16.496359-18.295962-44.090269-1.799603-61.586408l315.930274-334.626147c8.398147-9.097992 8.298169-24.094682-0.599868-32.792762L55.500751 173.587689c-16.996249-16.896271-17.196205-44.490181-0.299934-61.686386 16.896271-16.996249 44.390203-17.296183 61.586408-0.199956L431.017873 422.032856c42.290666 41.790777 43.490402 111.075485 2.799382 154.465909l-315.930273 334.626147c-8.69808 9.097992-20.195543 13.696977-31.792983 13.696977z" fill="#4a47ff" p-id="9842"></path>
                            </svg>
                            <img src="" class="img-after-remove-bg" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <div id="loader">

            <div class="loader-inner line-scale-pulse-out">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>

        </div>
        <p class="submit">
            <input type="submit" class="btn btn-success button-primary saveSetting pixcut-button-click mr-4" value="Save settings" />
            <input type="submit" class="btn btn-primary button-primary startRemove pixcut-button-click mr-4" value="Start background removal" />

            <input type="submit" class="btn btn-warning pixcut-button-click  mr-4 <?php if ($count == 0) { ?>d-none<?php } ?> " id="pixcut_restore_backup" value="Restore backup" />
            <input type="submit" class="btn btn-danger pixcut-button-click mr-4 <?php if ($count == 0) { ?>d-none<?php } ?> " id="pixcut_delete_backup" value="Delete backup" />
            <input type="hidden" id="restore_backup_confirm" value="This will restore your original images. Do you want to continue?">
            <input type="hidden" id="delete_backup_confirm" value="This will permanently delete your original images. Do you want to continue?">
        <div class="block-count" <?php if ($count == 0) { ?>style="display:none;" <?php } ?>> Images backed up - <?php echo '<span>' . $count . '</span>'; ?></div>
        </p>
        <div class="pixcut_remove_bg-log-live">

        </div>
        <div class="pixcut_remove_bg-process-stop btn btn-danger d-none">
            Abort process
        </div>
        <div class="pixcut_remove_bg-log mt-2" <?php echo !$log_file ? 'style="display: none"' : ''; ?>>
            <a class="btn btn-secondary" href="<?php echo $wp_upload_dir['baseurl']; ?>/pixcut-remove-bg-log/log.txt" target="_blank">View last log</a>
        </div>
    </form>


    <div class="model" id="model">
        <div class="model-content">
            <div class="model-desc" id="modelDesc">
            </div>
            <div class="model-action d-flex justify-content-end">
                <button class="btn btn-secondary mr-2" id="cancelAction" style="padding-left: 20px;padding-right:20px; display:none;">cancel</button>
                <button class="btn btn-primary" style="padding-left: 20px;padding-right:20px">ok</button>
            </div>
        </div>
        <div class="model-mask">
        </div>
    </div>
    <div class="toast" id="toast"></div>
</div>