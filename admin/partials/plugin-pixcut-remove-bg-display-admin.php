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

$background_color = get_option('Pixcut_RemoveBG_Background_Color');
$background_option = get_option('Pixcut_RemoveBG_Background');
?>

<div class="pixcut-wrap px-4">
    <div class="mt-4 d-flex align-items-center justify-content-between pr-5" style="width: 100%;">
        <span class="d-flex align-items-center "> <img src="https://neveragain.allstatics.com/2019/assets/icon/logo/pixcut-square.svg" class="mr-2" alt="PixCut Logo icon" style="width: 32px; height: 32px;">
            <h2>Wocommerce Pixcut remove background</h2>
        </span>
        <a class="mr-5" href="https://pixcut.wondershare.com/pricing.html">How to Buy</a>
    </div>


    <?php if (!get_option('Pixcut_RemoveBG_ApiKey')) : ?>
        <div id="apiwarning">
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

        <table class="form-table">

            <tr valign="top">
                <th scope="row">
                    <p class="d-flex align-items-center">RemoveBG Api key
                        <span class="tooltip ml-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.75C6.54822 3.75 3.75 6.54822 3.75 10C3.75 13.4518 6.54822 16.25 10 16.25C13.4518 16.25 16.25 13.4518 16.25 10C16.25 6.54822 13.4518 3.75 10 3.75ZM2.25 10C2.25 5.71979 5.71979 2.25 10 2.25C14.2802 2.25 17.75 5.71979 17.75 10C17.75 14.2802 14.2802 17.75 10 17.75C5.71979 17.75 2.25 14.2802 2.25 10ZM10 8.25C10.4142 8.25 10.75 8.58579 10.75 9L10.75 14C10.75 14.4142 10.4142 14.75 10 14.75C9.58579 14.75 9.25 14.4142 9.25 14L9.25 9C9.25 8.58579 9.58579 8.25 10 8.25ZM10.9 6.5C10.9 6.00294 10.4971 5.6 10 5.6C9.50294 5.6 9.1 6.00294 9.1 6.5L9.1 6.6C9.1 7.09706 9.50294 7.5 10 7.5C10.4971 7.5 10.9 7.09705 10.9 6.6L10.9 6.5Z" fill="#232341" />
                            </svg>
                            <span class="tooltiptext">Get the API key from remove.bg profile</span></span>
                    </p>
                </th>
                <td><input type="text" class="input" style="width: 60%" name="Pixcut_RemoveBG_ApiKey" value="<?php echo esc_attr(get_option('Pixcut_RemoveBG_ApiKey')) ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <p class="d-flex align-items-center">Choose target products
                        <span class="tooltip ml-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.75C6.54822 3.75 3.75 6.54822 3.75 10C3.75 13.4518 6.54822 16.25 10 16.25C13.4518 16.25 16.25 13.4518 16.25 10C16.25 6.54822 13.4518 3.75 10 3.75ZM2.25 10C2.25 5.71979 5.71979 2.25 10 2.25C14.2802 2.25 17.75 5.71979 17.75 10C17.75 14.2802 14.2802 17.75 10 17.75C5.71979 17.75 2.25 14.2802 2.25 10ZM10 8.25C10.4142 8.25 10.75 8.58579 10.75 9L10.75 14C10.75 14.4142 10.4142 14.75 10 14.75C9.58579 14.75 9.25 14.4142 9.25 14L9.25 9C9.25 8.58579 9.58579 8.25 10 8.25ZM10.9 6.5C10.9 6.00294 10.4971 5.6 10 5.6C9.50294 5.6 9.1 6.00294 9.1 6.5L9.1 6.6C9.1 7.09706 9.50294 7.5 10 7.5C10.4971 7.5 10.9 7.09705 10.9 6.6L10.9 6.5Z" fill="#232341" />
                            </svg>
                            <span class="tooltiptext">Whether to process all products or only products with provided IDs</span></span>
                    </p>
                </th>
                <td>
                    <input type="radio" id="products_all" name="Pixcut_RemoveBG_products" value="all" <?php echo checked(('all' == get_option('Pixcut_RemoveBG_products') || 'specified' != get_option('Pixcut_RemoveBG_products')), true, false) ?> /><label for="products_all">Remove background from all products</label><br>
                    <input type="radio" id="products_spec" name="Pixcut_RemoveBG_products" value="specified" <?php echo checked('specified' == get_option('Pixcut_RemoveBG_products'), true, false) ?> /><label for="products_spec">Remove background only from specified products </label><span class="desc">(IDs of products to process: comma separated or ranges, i.e. 3,9,20-27,40-45)</span>
                    <input type="text" class="input  mt-2" style="width: 80%; <?php if ('specified' != get_option('Pixcut_RemoveBG_products')) echo ' visibility:hidden'; ?>" placeholder="4,156,271" name="Pixcut_RemoveBG_products_IDs" value="<?php echo esc_attr(get_option('Pixcut_RemoveBG_products_IDs')) ?>" />
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <p class="d-flex align-items-center">Choose target images
                        <span class="tooltip ml-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.75C6.54822 3.75 3.75 6.54822 3.75 10C3.75 13.4518 6.54822 16.25 10 16.25C13.4518 16.25 16.25 13.4518 16.25 10C16.25 6.54822 13.4518 3.75 10 3.75ZM2.25 10C2.25 5.71979 5.71979 2.25 10 2.25C14.2802 2.25 17.75 5.71979 17.75 10C17.75 14.2802 14.2802 17.75 10 17.75C5.71979 17.75 2.25 14.2802 2.25 10ZM10 8.25C10.4142 8.25 10.75 8.58579 10.75 9L10.75 14C10.75 14.4142 10.4142 14.75 10 14.75C9.58579 14.75 9.25 14.4142 9.25 14L9.25 9C9.25 8.58579 9.58579 8.25 10 8.25ZM10.9 6.5C10.9 6.00294 10.4971 5.6 10 5.6C9.50294 5.6 9.1 6.00294 9.1 6.5L9.1 6.6C9.1 7.09706 9.50294 7.5 10 7.5C10.4971 7.5 10.9 7.09705 10.9 6.6L10.9 6.5Z" fill="#232341" />
                            </svg>
                            <span class="tooltiptext">"Main image" - processes only main image of a product, "Product gallery" - processes only product gallery images. Check both to process all images of a product</span></span>
                    </p>
                </th>
                <td>
                    <input type="checkbox" id="target_main" name="Pixcut_RemoveBG_thumbnail" value="1" <?php echo checked(1 == get_option('Pixcut_RemoveBG_thumbnail'), true, false) ?> /><label for="target_main">Main image</label><br>
                    <input type="checkbox" id="target_gallery" name="Pixcut_RemoveBG_gallery" value="1" <?php echo checked(1 == get_option('Pixcut_RemoveBG_gallery'), true, false) ?> /><label for="target_gallery">Product gallery</label><br>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p class="d-flex align-items-center">Include processed images
                        <span class="tooltip ml-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.75C6.54822 3.75 3.75 6.54822 3.75 10C3.75 13.4518 6.54822 16.25 10 16.25C13.4518 16.25 16.25 13.4518 16.25 10C16.25 6.54822 13.4518 3.75 10 3.75ZM2.25 10C2.25 5.71979 5.71979 2.25 10 2.25C14.2802 2.25 17.75 5.71979 17.75 10C17.75 14.2802 14.2802 17.75 10 17.75C5.71979 17.75 2.25 14.2802 2.25 10ZM10 8.25C10.4142 8.25 10.75 8.58579 10.75 9L10.75 14C10.75 14.4142 10.4142 14.75 10 14.75C9.58579 14.75 9.25 14.4142 9.25 14L9.25 9C9.25 8.58579 9.58579 8.25 10 8.25ZM10.9 6.5C10.9 6.00294 10.4971 5.6 10 5.6C9.50294 5.6 9.1 6.00294 9.1 6.5L9.1 6.6C9.1 7.09706 9.50294 7.5 10 7.5C10.4971 7.5 10.9 7.09705 10.9 6.6L10.9 6.5Z" fill="#232341" />
                            </svg>
                            <span class="tooltiptext">By default, plugin processes each image only once. If checked, plugin will not skip earlier processed images and will overwrite them</span></span>
                    </p>
                </th>
                <td>
                    <input type="checkbox" name="Pixcut_RemoveBG_Include_Processed" value="1" <?php echo checked(1 == get_option('Pixcut_RemoveBG_Include_Processed'), true, false) ?> />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p class="d-flex align-items-center">Make new background
                        <span class="tooltip ml-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.75C6.54822 3.75 3.75 6.54822 3.75 10C3.75 13.4518 6.54822 16.25 10 16.25C13.4518 16.25 16.25 13.4518 16.25 10C16.25 6.54822 13.4518 3.75 10 3.75ZM2.25 10C2.25 5.71979 5.71979 2.25 10 2.25C14.2802 2.25 17.75 5.71979 17.75 10C17.75 14.2802 14.2802 17.75 10 17.75C5.71979 17.75 2.25 14.2802 2.25 10ZM10 8.25C10.4142 8.25 10.75 8.58579 10.75 9L10.75 14C10.75 14.4142 10.4142 14.75 10 14.75C9.58579 14.75 9.25 14.4142 9.25 14L9.25 9C9.25 8.58579 9.58579 8.25 10 8.25ZM10.9 6.5C10.9 6.00294 10.4971 5.6 10 5.6C9.50294 5.6 9.1 6.00294 9.1 6.5L9.1 6.6C9.1 7.09706 9.50294 7.5 10 7.5C10.4971 7.5 10.9 7.09705 10.9 6.6L10.9 6.5Z" fill="#232341" />
                            </svg>
                            <span class="tooltiptext">"Transparent" - processed images will have transarent background, "Color" - sets chosen color as new background of processed images, "Custom image" - sets your image as new background of processed images</span></span>
                    </p>
                </th>
                <td>
                    <input type="radio" id="newbg_transp" name="Pixcut_RemoveBG_Background" value="transparent " <?php echo checked(('transparent' == get_option('Pixcut_RemoveBG_Background') || 'color' != get_option('Pixcut_RemoveBG_Background') || 'image' != get_option('Pixcut_RemoveBG_Background')), true, false) ?> /><label for="newbg_transp">Transparent</label><br>
                    <input type="radio" id="newbg_color" name="Pixcut_RemoveBG_Background" value="color" <?php echo checked('color' == get_option('Pixcut_RemoveBG_Background'), true, false) ?> /><label for="newbg_color"> Color </label>

                    <input type="color" class="ml-2" name="Pixcut_RemoveBG_Background_Color" id="background_color" value="<?php echo $background_color ?>">

                    <!-- <input type="radio" id="newbg_image" name="Pixcut_RemoveBG_Background" value="image" <?php echo checked('image' == get_option('Pixcut_RemoveBG_Background'), true, false) ?>/><label for="newbg_image">Custom image</label><br>
                    <div class="fit_fill">
                        <img src="" class="RemoveBG_Background_img">
                        <input type="file" name="Pixcut_RemoveBG_Background_Image" class="RemoveBG_Background_Image">
                    </div> -->
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <p class="d-flex align-items-center">Preview a product
                        <span class="tooltip ml-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.75C6.54822 3.75 3.75 6.54822 3.75 10C3.75 13.4518 6.54822 16.25 10 16.25C13.4518 16.25 16.25 13.4518 16.25 10C16.25 6.54822 13.4518 3.75 10 3.75ZM2.25 10C2.25 5.71979 5.71979 2.25 10 2.25C14.2802 2.25 17.75 5.71979 17.75 10C17.75 14.2802 14.2802 17.75 10 17.75C5.71979 17.75 2.25 14.2802 2.25 10ZM10 8.25C10.4142 8.25 10.75 8.58579 10.75 9L10.75 14C10.75 14.4142 10.4142 14.75 10 14.75C9.58579 14.75 9.25 14.4142 9.25 14L9.25 9C9.25 8.58579 9.58579 8.25 10 8.25ZM10.9 6.5C10.9 6.00294 10.4971 5.6 10 5.6C9.50294 5.6 9.1 6.00294 9.1 6.5L9.1 6.6C9.1 7.09706 9.50294 7.5 10 7.5C10.4971 7.5 10.9 7.09705 10.9 6.6L10.9 6.5Z" fill="#232341" />
                            </svg>
                            <span class="tooltiptext">You can test and preview the background removal for any product before starting actual process. Enter ID of a product to see its main image after backround removal process with current settings. This will not affect actual image of the product at your site</span></span>
                    </p>
                </th>
                <td>
                    <input type="text" class="input" placeholder="Enter a product id to preview result" name="Pixcut_RemoveBG_TestProduct" value="" style="width: 30%;" /> <input type="button" class="btn btn-primary button-click" id="startpreview" value="Preview">
                    <div id="previewresult" style="display:none;"><img src="" class="img-before-remove-bg" /> -> <img src="" class="img-after-remove-bg" />
                </td>
            </tr>
        </table>
        <div id="loader">
            <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="160px" height="20px" viewBox="0 0 128 16" xml:space="preserve">
                <path fill="#949494" fill-opacity="0.42" d="M6.4,4.8A3.2,3.2,0,1,1,3.2,8,3.2,3.2,0,0,1,6.4,4.8Zm12.8,0A3.2,3.2,0,1,1,16,8,3.2,3.2,0,0,1,19.2,4.8ZM32,4.8A3.2,3.2,0,1,1,28.8,8,3.2,3.2,0,0,1,32,4.8Zm12.8,0A3.2,3.2,0,1,1,41.6,8,3.2,3.2,0,0,1,44.8,4.8Zm12.8,0A3.2,3.2,0,1,1,54.4,8,3.2,3.2,0,0,1,57.6,4.8Zm12.8,0A3.2,3.2,0,1,1,67.2,8,3.2,3.2,0,0,1,70.4,4.8Zm12.8,0A3.2,3.2,0,1,1,80,8,3.2,3.2,0,0,1,83.2,4.8ZM96,4.8A3.2,3.2,0,1,1,92.8,8,3.2,3.2,0,0,1,96,4.8Zm12.8,0A3.2,3.2,0,1,1,105.6,8,3.2,3.2,0,0,1,108.8,4.8Zm12.8,0A3.2,3.2,0,1,1,118.4,8,3.2,3.2,0,0,1,121.6,4.8Z" />
                <g>
                    <path fill="#000000" fill-opacity="1" d="M-42.7,3.84A4.16,4.16,0,0,1-38.54,8a4.16,4.16,0,0,1-4.16,4.16A4.16,4.16,0,0,1-46.86,8,4.16,4.16,0,0,1-42.7,3.84Zm12.8-.64A4.8,4.8,0,0,1-25.1,8a4.8,4.8,0,0,1-4.8,4.8A4.8,4.8,0,0,1-34.7,8,4.8,4.8,0,0,1-29.9,3.2Zm12.8-.64A5.44,5.44,0,0,1-11.66,8a5.44,5.44,0,0,1-5.44,5.44A5.44,5.44,0,0,1-22.54,8,5.44,5.44,0,0,1-17.1,2.56Z" />
                    <animateTransform attributeName="transform" type="translate" values="23 0;36 0;49 0;62 0;74.5 0;87.5 0;100 0;113 0;125.5 0;138.5 0;151.5 0;164.5 0;178 0" calcMode="discrete" dur="1170ms" repeatCount="indefinite" />
                </g>
            </svg>

        </div>
        <p class="submit">
            <input type="submit" class="btn btn-success button-primary saveSetting button-click mr-4" value="Save settings" />
            <input type="submit" class="btn btn-primary button-primary startRemove button-click mr-4" value="Start background removal" />

            <input type="submit" class="btn btn-warning button-click  mr-4 <?php if ($count == 0) { ?>d-none<?php } ?> " id="restore_backup" value="Restore backup" />
            <input type="submit" class="btn btn-danger button-click mr-4 <?php if ($count == 0) { ?>d-none<?php } ?> " id="delete_backup" value="Delete backup" />
            <input type="hidden" id="restore_backup_confirm" value="This will restore your original images. Do you want to continue?">
            <input type="hidden" id="delete_backup_confirm" value="This will permanently delete your original images. Do you want to continue?">
        <div class="block-count" <?php if ($count == 0) { ?>style="display:none;" <?php } ?>> Images backed up - <?php echo '<span>' . $count . '</span>'; ?></div>
        </p>
        <div class="pixcut_remove_bg-log-live">

        </div>
        <div class="pixcut_remove_bg-process-stop">
            Abort process
        </div>
        <div class="pixcut_remove_bg-log" <?php echo !$log_file ? 'style="display: none"' : ''; ?>>
            <a href="<?php echo $wp_upload_dir['baseurl']; ?>/pixcut-remove-bg-log/log.txt" target="_blank">View last log</a>
        </div>
    </form>
    <div class="bottomlinks">
        <a href="http://fresh-d.biz/wocommerce-remove-background.html" target="_blank">Description</a> | <a href="http://fresh-d.biz/wocommerce-remove-background.html#support" target="_blank">Support</a> | <a href="http://fresh-d.biz/about-us.html" target="_blank">Authors</a> | <a href="https://secure.wayforpay.com/payment/s7f497f68a340
" target="_blank">Donate</a> | <a href="https://fresh-d.biz/wocommerce-remove-background.html#feedback" target="_blank">Feedback</a>
    </div>

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
</div>