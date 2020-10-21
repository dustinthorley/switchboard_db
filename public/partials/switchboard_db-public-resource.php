<?php

/**
 *
 * This file is used to markup individual resources.
 *
 * @link       https://www.sparkslc.ca/
 * @since      1.0.0
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/public/partials
 */
?>
<a name="<?php echo $resource->resourceID ?>"></a>
<div class="resource" name="expanding-container<?php echo $resource->resourceID ?>">
    <div class="card-1">
    
      <div class="resource-titles" data-resource="<?php echo $resource->resourceID ?>">
        <div>
          <h4 class="resource-name"><?php echo $resource->resourceName ?></h4>
          <p class="provider"><?php echo $resource->organizationName; if( $resource->departmentName != null ) {echo ": " . $resource->departmentName;}; ?></p>
        </div>
        <?php 
          $free = strpos($resource->costList, "Free");
          if ( $free !== false ){
            echo '<div class="free-tag">Free Resource</div>';
          }
        ?>
        <img src="<?php echo get_template_directory_uri(); ?>/images/chevron-up-solid.svg" height="40" alt="Expand Resource <?php echo $resource->resourceID ?>" title="Expand Resource <?php echo $resource->resourceID ?>" class="chevron" name="chevron<?php echo $resource->resourceID ?>">
      </div>
      <div class="filtered-data">
        <div class="item-lists business-stages">
          <p class="filter-heading">Business Stage</p>
          <?php $all = strpos( $resource->stageList, "All"); ?>
          <ul role="list" class="stage-list" name="list-2<?php echo $resource->resourceID ?>">
            <li class="stage-img-list<?php 
                if (strpos($resource->stageList, 'Exploration') === false && $all === false) {
                    echo ' stage-img-off';
                } ?>">
              <p class="hidden-stage" name="hidden-stage<?php echo $resource->resourceID ?>">Exploration</p>
              <img src="<?php echo get_template_directory_uri(); ?>/images/SWITCHBOARD_exploration_icon_vector.svg" width="84" alt="Exploration icon Binoculars" title="Exploration" class="stage_icon"></li>
            <li class="stage-img-list<?php 
                if (strpos($resource->stageList, 'Idea') === false && $all === false) {
                    echo ' stage-img-off';
                } ?>">
              <p class="hidden-stage" name="hidden-stage<?php echo $resource->resourceID ?>">Idea</p>
              <img src="<?php echo get_template_directory_uri(); ?>/images/SWITCHBOARD_ideas_icon_vector.svg" width="52.5" alt="Ideas icon lightbulb" title="Ideas" class="stage_icon"></li>
            <li class="stage-img-list<?php 
                if (strpos($resource->stageList, 'Startup') === false && $all === false) {
                    echo ' stage-img-off';
                } ?>">
              <p class="hidden-stage" name="hidden-stage<?php echo $resource->resourceID ?>">Startup</p>
              <img src="<?php echo get_template_directory_uri(); ?>/images/SWITCHBOARD_startup_icon_vector.svg" width="75.5" alt="startup icon gear" title="Startup" class="stage_icon"></li>
            <li class="stage-img-list<?php 
                if (strpos($resource->stageList, 'Established') === false && $all === false) {
                    echo ' stage-img-off';
                } ?>">
              <p class="hidden-stage" name="hidden-stage<?php echo $resource->resourceID ?>">Established</p>
              <img src="<?php echo get_template_directory_uri(); ?>/images/SWITCHBOARD_established_icon_vector.svg" width="47.5" alt="Established icon hot air balloon" title="Established" class="stage_icon"></li>
            <li class="stage-img-list<?php 
                if (strpos($resource->stageList, 'Ready') === false && $all === false) {
                    echo ' stage-img-off';
                } ?>">
              <p class="hidden-stage" name="hidden-stage<?php echo $resource->resourceID ?>">Ready to Scale</p>
              <img src="<?php echo get_template_directory_uri(); ?>/images/SWITCHBOARD_readytoscale_icon_vector.svg" width="65.5" alt="Ready to scale icon rocketship" title="Ready to Scale" class="stage_icon"></li>
          </ul>
        </div>
        <div class="item-lists support-type">
          <p class="filter-heading">Support Type</p>
          <ul role="list" class="resource-list support-list">
              <li class="types">
                <?php 
                    echo $resource->supportName;
                ?>
            </li>
          </ul>
        </div>
        <div class="item-lists support-category">
          <p class="filter-heading">Support Category</p>
          <ul role="list" class="resource-list">
          <?php 
            $types = explode( '*', $resource->categoryList);
            foreach ($types as $type) {
                echo '<li class="types">' . $type . '</li>';
            }
           ?>
          </ul>
        </div>
        <div class="item-lists region">
          <p class="filter-heading">Region</p>
          <ul role="list" class="resource-list support-list">
              <li class="types">
                <?php 
                    echo $resource->regionList;
                ?>
            </li>
          </ul>
        </div>
        <div class="item-lists description" id="description<?php echo $resource->resourceID ?>" name="description<?php echo $resource->resourceID ?>">
          <p class="filter-heading">Description</p>
          <p class="description-body"><?php echo $resource->resourceDescription ?></p>
        </div>
      </div>
    </div>
    <div class="provider-card" name="provider-card<?php echo $resource->resourceID ?>">
      <div class="provider-brand"><img src="<?php echo get_template_directory_uri(); ?>/images/<?php echo $resource->organizationLogo; ?>" alt="<?php echo $resource->organizationName; ?> Logo">
      <a href="<?php echo $resource->organizationWebsite; ?>" target="_blank" class="link"><?php echo $resource->organizationWebsite; ?></a></div>
      <div class="w-form">

        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST" id="email-form<?php echo $resource->resourceID ?>" class="form" name="email-form<?php echo $resource->resourceID ?>">
            <input type="hidden" name="action" value="salesForce_form" />
            <input type=hidden name="oid" value="00D60000000JHbB">
            <input type=hidden name="retURL" value="<?php echo get_site_url() ?>/resources/">
            <input type="hidden" name="open" value="<?php echo $resource->resourceID ?>">
            <input type=hidden name="recordType" value="0125x000000URaw">
            <input type=hidden id="00N5x00000ENWRs" name="00N5x00000ENWRs" value="Individual Resource Inquiry">
            <input type=hidden id="00N5x00000ENWCn" name="00N5x00000ENWCn" value="<?php echo $resource->organizationName ?>">
            <input type=hidden id="00N5x00000ENWBL" name="00N5x00000ENWBL" value="<?php echo $resource->resourceName ?>">
            <input type=hidden id="00N5x00000Ef2E7" name="00N5x00000Ef2E7" value="<?php echo $resource->resourceEmail ?>">
            <input type=hidden id="00N5x00000Ef2EC" name="00N5x00000Ef2EC" value="https://myswitchboard.ca/resources/?provider=<?php echo $resource->resourceID ?>">
            <input type="hidden" id="activeFilters" name="activeFilters" value="">

            <div class="grid-container">
              <div class="salesforce-header">
                <h1 class="form-header">Message this Resource Provider</h1>
              </div>
              <div class="first-name">
                <label for="first_name" class="form-label">First Name</label>
                <input  id="first_name" maxlength="40" name="first_name" size="20" type="text" class="text-field w-input" required />
              </div>
              <div class="last-name">
                <label for="last_name" class="form-label">Last Name</label>
                <input  id="last_name" maxlength="80" name="last_name" size="20" type="text" class="text-field w-input" required />
              </div>
              <div class="company">
                <label for="company" class="form-label">Company Name</label>
                <input  id="company" maxlength="40" name="company" size="20" type="text" class="text-field w-input" />  
              </div>
              <div class="email">
                <label for="email" class="form-label">Email</label>
                <input  id="email" maxlength="80" name="email" size="20" type="email" class="text-field w-input" required />
              </div>
              <div class="provider-message">
                <label for="00N60000003JIEL" class="form-label">Your Message <span class="instruction">(Please include details like what your company does, who your customer is and what challenges you’re facing)</span></label>
                <textarea  id="00N60000003JIEL" name="00N60000003JIEL" rows="3" type="text" wrap="soft"></textarea>
              </div>
              <div class="business-stage">
                <label for="00N5x000003Hkpg" class="form-label">Your current Business Stage</label>
                <select  id="00N5x000003Hkpg" name="00N5x000003Hkpg" title="Business Stage" class="select-field w-select">
                    <option value="">--None--</option>
                    <option value="Exploration">Exploration</option>
                    <option value="Idea">Idea</option>
                    <option value="Startup">Startup</option>
                    <option value="Established">Established</option>
                    <option value="Ready to Scale">Ready to Scale</option>
                </select>
              </div>
              <div class="support-categories">
                <label for="00N5x00000ENWBB" class="form-label">In what areas do you need support (select all that apply)</label>
                <select  id="00N5x00000ENWBB" multiple="multiple" name="00N5x00000ENWBB" title="Support Category" class="select-field w-select">
                    <option id="2" value="Business Strategy &amp; Mentorship">Business Strategy &amp; Mentorship</option>
                    <option id="3" value="Financial Management &amp; Strategy">Financial Management &amp; Strategy</option>
                    <option id="4" value="Sales &amp; Exporting">Sales &amp; Exporting</option>
                    <option id="5" value="Marketing &amp; Customer Acquisition">Marketing &amp; Customer Acquisition</option>
                    <option id="6" value="Funding">Funding</option>
                    <option id="7" value="Talent &amp; HR">Talent &amp; HR</option>
                    <option id="8" value="Space">Space</option>
                    <option id="9" value="Research &amp; Development">Research &amp; Development</option>
                    <option id="10" value="Information Technology">Information Technology</option>
                    <option id="11" value="Legal, Licensing &amp; Permits">Legal, Licensing &amp; Permits</option>
                    <option id="12" value="Manufacturing &amp; Supply Chain Management">Manufacturing &amp; Supply Chain Management</option>
                </select>
                <span class="instruction">Hold CTRL or CMD to select multiple.</span>
              </div>
              <div class="casl">
                <label for="00N60000002i94D" class="form-label" style="padding-top:10px">
                  <input  id="00N60000002i94D" name="00N60000002i94D" type="checkbox" value="1" /> Send me promotional emails about new funding opportunities, programs, services and events</label>
                <br>
                <label for="00N60000002i94S" class="form-label">
                  <input  id="00N60000002i94S" name="00N60000002i94S" type="checkbox" value="Electronic consent received when submitting Switchboard Individual Resource Inquiry Intake Form." required /> I understand that the information submitted in this form will be shared with partners that are outside of the Switchboard Business Support Hub organization</label>
                <br>
                <input type=hidden id="00N60000002i94N" name="00N60000002i94N" title="CASL Consent Method" value="Entrepreneurial Ecosystem Project">
              </div>
            </div>

            <div id="captcha<?php echo $resource->resourceID ?>"></div>
            <input type="submit" name="submit" value="Send to the resource provider" class="form-button w-button">

            <?php 
              if ( isset( $_GET['message'] ) ) {
                ?>
                <div class="w-form-done" <?php if ( $_GET['message']=="success" && $_GET['open']==$resource->resourceID ) { echo ' style="display: block"'; };?>>
                  <div>Thank you! Your submission has been received!</div>
                </div>
                <div class="w-form-fail" <?php if ( $_GET['message']=="captcha" && $_GET['open']==$resource->resourceID ) { echo ' style="display: block"'; };?>>
                  <div>Please complete the captcha before submitting this form.</div>
                </div>
                <?php
              }
            ?>
        </form>
      </div>
    </div>
  </div>