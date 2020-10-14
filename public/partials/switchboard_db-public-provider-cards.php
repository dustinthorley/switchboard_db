<?php

/**
 * This builds the provider cards.
 *
 * @link       https://www.sparkslc.ca/
 * @since      1.0.0
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/public/partials
 */
?>

<div class="card provider">
    <div class="card-content-wrapper">
        <div class="logo_wrapper">
        <img src="<?php echo get_template_directory_uri() ?>/images/<?php echo $provider->organizationLogo ?>" alt="<?php echo $provider->organizationName ?> Logo" class="image-2">
        </div>
        <h2 class="h2 provider"><?php echo $provider->organizationName ?></h2>
        <h4 class="description">Description</h4>
        <p class="paragraph-6"><?php echo $provider->organizationDescription ?></p>
        <!-- <div class="div-block-20"> -->
          <h4 class="description">Website</h4>
          
            <a href="<?php echo $provider->organizationWebsite ?>" class="link"><?php echo $provider->organizationWebsite ?></a>
        <!-- </div> -->
    </div>
    <div data-w-id="d74f6292-34ac-3a0b-2322-44103d20b19c" class="text-button">
        <a href="<?php echo get_site_url()?>/resources?provider=<?php echo $provider->organizationID  ?>" class="button comparison w-button">See resources from 
        <?php 

            switch ($provider->organizationID) {
                case 4:
                    echo "KEDC";
                    break;
                case 5:
                    echo "SOAN";
                    break;
                default:
                echo $provider->organizationName;
            }

        ?></a>
    </div>
</div>