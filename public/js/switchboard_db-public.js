(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(document).ready(function(){

		const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0)
		if ( vw <= 479 ) {
			document.getElementById("search").placeholder = "Search...";
		} else if ( vw <= 991 ) {
			document.getElementById("search").placeholder = "Search by keyword, business stage, support category...";
		}

		addClickEvents();

		document.querySelectorAll(".formTab").forEach(item=>{
			//var activeForm = item.getAttribute("active-form");
			//var hiddenForm = item.getAttribute("hidden-form");
			var targetForm = item.getAttribute("target-form");

			item.addEventListener('click', event => {
				event.preventDefault();
				formTabs(targetForm);
			});
		});

		document.getElementById('more_filters').addEventListener('click', event => {
			event.preventDefault();
			if( $('#more_filters').html()=="Show more filter options") {
				$('#more_filters').html("Show less filter options");
			}
			else {
				$('#more_filters').html("Show more filter options");
			}
			$('.filters-secondary').toggle(200);
		});

		$("#apply_filter").click(function() {
		
			var btn = $("#apply_filter");
			btn.text("Applying filters...");

			var stages = $('input[name="stages"]:checked').map(function() {
			  return $(this).val();
			}).get().join(',');
			
			var categories = $('input[name="categories"]:checked').map(function() {
			  return $(this).val();
			}).get().join(',');
		
			var types = $('input[name="types"]:checked').map(function() {
			  return $(this).val();
			}).get().join(',');
		
			var providers = $('input[name="providers"]:checked').map(function() {
			  return $(this).val();
			}).get().join(',');
		
			var groups = $('input[name="groups"]:checked').map(function() {
			  return $(this).val();
			}).get().join(',');

			var free = document.getElementById('free-resources').checked ? true: false;
			
			$.ajax({
			  type : "POST",
			  dataType : "json",
			  url : myAjax.ajaxurl,
			  data : {
				'action' : 'filter_switchboard_resources',
				'stages' : stages,
				'categories' : categories,
				'types' : types,
				'providers' : providers,
				'groups' : groups,
				'free' : free,
			  },
			  success: function(response) {
				btn.text("Apply filters");
				display_resources(response);
			  }
			})
		  });
	  
		  $("#hide_filters").click(function() {
			  $("#filter_panel").toggle();
			var btn = $("#hide_filters");
			if (btn.text()=="Show Filters") {
			  btn.text("Hide Filters");
			}
			else {
			  btn.text("Show Filters");
			}
		  });
	  
		  $("#search_filter").click(function() {
			var btn = $("#search_filter");
			btn.text("Searching...");
			var free = document.getElementById('free-resources').checked ? true: false;  
			$.ajax({
			  type: "POST",
			  dataType: "json",
			  url: myAjax.ajaxurl,
			  data: {
				'action' : 'search_switchboard',
				'search' : $("#search").val(),
				'free'	 : free,
			  },
			  success: function(response) {
				btn.text("Search List");
				display_resources(response);
			  }
			});
		  });

	 });

	function display_resources(response) {
		var resources = $("#results");
		resources.empty();
		if ( response != '' ) {
			response.forEach(function(resource){
				var department = resource.departmentName != null ? ': ' + resource.departmentName : '';
				//var all = resource.stageList.includes("All");

				//if (all) {
				//	var exploration, idea, startup, established, ready = "";
				//}
				//else {
					var exploration = resource.stageList.includes("Exploration") ? "" : " stage-img-off";
					var idea = resource.stageList.includes("Idea") ? "" : " stage-img-off";
					var startup = resource.stageList.includes("Startup") ? "" : " stage-img-off";
					var established = resource.stageList.includes("Established") ? "" : " stage-img-off";
					var ready = resource.stageList.includes("Ready") ? "" : " stage-img-off";
				//}
				
				if ( resource.costList!=null && resource.costList.includes("Free") ) {
					var free = '<div class="free-tag">Free Resource</div>';
				}
				else {
					var free = "";
				}
	
				var categoryList = "";
				try{
				var categories = resource.categoryList.split("*"); // if only 1 category will throw an error
				categories.forEach(function(category){
					categoryList += `<li class="types">${category}</li>`;
				})	
				}
				catch(err){
					categoryList = `<li class="types">${resource.categoryList}</li>`; // if error output the categoryList without trying to split
				}
				var row = `<div class="resource" name="expanding-container${resource.resourceID}">
				<div class="card-1">
				
				  <div class="resource-titles" data-resource="${resource.resourceID}">
					<div>
					  <h4 class="resource-name">${resource.resourceName}</h4>
					  <p class="provider">${resource.organizationName + department}</p>
					</div>`
					+ free +
					`<img src="${switchboard_data.theme_uri}/images/chevron-up-solid.svg" height="40" alt="Expand Resource ${resource.resourceID}" title="Expand Resource ${resource.resourceID}" class="chevron" name="chevron${resource.resourceID}"></div>
				  <div class="filtered-data">
					<div class="item-lists business-stages">
					  <p class="filter-heading">Business Stage</p>
					  <ul role="list" class="stage-list" name="list-2${resource.resourceID}">
						<li class="stage-img-list` + exploration + `">
						  <p class="hidden-stage" name="hidden-stage${resource.resourceID}">Exploration</p>
						  <img src="${switchboard_data.theme_uri}/images/SWITCHBOARD_exploration_icon_vector.svg" width="84" alt="Exploration icon Binoculars" title="Exploration" class="stage_icon"></li>
						<li class="stage-img-list` + idea + `">
						  <p class="hidden-stage" name="hidden-stage${resource.resourceID}">Idea</p>
						  <img src="${switchboard_data.theme_uri}/images/SWITCHBOARD_ideas_icon_vector.svg" width="52.5" alt="Ideas icon lightbulb" title="Ideas" class="stage_icon"></li>
						<li class="stage-img-list` + startup + `">
						  <p class="hidden-stage" name="hidden-stage${resource.resourceID}">Startup</p>
						  <img src="${switchboard_data.theme_uri}/images/SWITCHBOARD_startup_icon_vector.svg" width="75.5" alt="startup icon gear" title="Startup" class="stage_icon"></li>
						<li class="stage-img-list` + established + `">
						  <p class="hidden-stage" name="hidden-stage${resource.resourceID}">Established</p>
						  <img src="${switchboard_data.theme_uri}/images/SWITCHBOARD_established_icon_vector.svg" width="47.5" alt="Established icon hot air balloon" title="Established" class="stage_icon"></li>
						<li class="stage-img-list` + ready + `">
						  <p class="hidden-stage" name="hidden-stage${resource.resourceID}">Ready to Scale</p>
						  <img src="${switchboard_data.theme_uri}/images/SWITCHBOARD_readytoscale_icon_vector.svg" width="65.5" alt="Ready to scale icon rocketship" title="Ready to Scale" class="stage_icon"></li>
					  </ul>
					</div>
					<div class="item-lists support-type">
					  <p class="filter-heading">Support Type</p>
					  <ul role="list" class="resource-list support-list">
						<li class="types">
						  ${resource.supportName}
						</li>
					  </ul>
					</div>
					<div class="item-lists support-category">
					  <p class="filter-heading">Support Category</p>
					  <ul role="list" class="resource-list">`
					  + categoryList +
					  `</ul>
					</div>
					<div class="item-lists region">
          			  <p class="filter-heading">Region</p>
          			  <ul role="list" class="resource-list support-list">
              		    <li class="types">
                		  ${resource.regionList}
            		    </li>
          			  </ul>
        			</div>
					<div class="item-lists description" id="description${resource.resourceID}" name="description${resource.resourceID}">
					  <p class="filter-heading">Description</p>
					  <p class="description-body">${resource.resourceDescription}</p>
					</div>
				  </div>
				</div>
				<div class="provider-card" name="provider-card${resource.resourceID}">
				  <div class="provider-brand"><img src="${switchboard_data.theme_uri}/images/${resource.organizationLogo}" alt="${resource.organizationName} Logo">
				  <a href="${resource.organizationWebsite}" target="_blank" class="link">${resource.organizationWebsite}</a></div>
				  <div class="w-form">
			
					<form action="https://myswitchboard.ca/wp-admin/admin-post.php" method="POST" id="email-form${resource.resourceID}" class="form" name="email-form${resource.resourceID}">
						<input type="hidden" name="action" value="salesForce_form" />
						<input type=hidden name="oid" value="00D60000000JHbB">
						<input type=hidden name="retURL" value="${switchboard_data.url}/resources/?open=${resource.resourceID}">
						<input type=hidden name="recordType" value="0125x000000URaw">
						<input type=hidden id="00N5x00000ENWRs" name="00N5x00000ENWRs" value="Individual Resource Inquiry">
						<input type=hidden id="00N5x00000ENWCn" name="00N5x00000ENWCn" value="${resource.organizationName}">
						<input type=hidden id="00N5x00000ENWBL" name="00N5x00000ENWBL" value="${resource.resourceName}">
						<input type=hidden id="00N5x00000Ef2E7" name="00N5x00000Ef2E7" value="${resource.resourceEmail}">
            			<input type=hidden id="00N5x00000Ef2EC" name="00N5x00000Ef2EC" value="https://myswitchboard.ca/resources/?provider=${resource.resourceID}">
			
						<div class="grid-container">
						  <div class="salesforce-header">
							<h1 class="form-header">Message this Resource Provider</h1>
						  </div>
						  <div class="first-name">
							<label for="first_name" class="form-label">First Name</label>
							<input  id="first_name" maxlength="40" name="first_name" size="20" type="text" class="text-field w-input" />
						  </div>
						  <div class="last-name">
							<label for="last_name" class="form-label">Last Name</label>
							<input  id="last_name" maxlength="80" name="last_name" size="20" type="text" class="text-field w-input" />
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
							<label for="00N60000003JIEL" class="form-label">Your Message (Please include details like what your company does, who your customer is and what challenges you’re facing)</label>
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
								<input  id="00N60000002i94D" name="00N60000002i94D" type="checkbox" value="1" />
								Send me promotional emails about new funding opportunities, programs, services and events</label>
							<br>
							<label for="00N60000002i94S" class="form-label">
                  				<input  id="00N60000002i94S" name="00N60000002i94S" type="checkbox" value="Electronic consent received when submitting Switchboard Individual Resource Inquiry Intake Form." /> I understand that the information submitted in this form will be shared with partners that are outside of the Switchboard Business Support Hub organization</label>
                			<br>
							<input type=hidden id="00N60000002i94N" name="00N60000002i94N" title="CASL Consent Method" value="Entrepreneurial Ecosystem Project">
						  </div>
						</div>
			
						<input type="submit" name="submit" value="Send to the resource provider" class="form-button w-button">
						<div id="captcha${resource.resourceID}"></div>
					</form>
					
				  </div>
				</div>
			  </div>`;

			  resources.append(row);
			});
		}
		else {
			resources.append('<div class="no-results"><h2 class="h2_centered">Sorry, there are currently no resources matching your criteria</h2></div>');
		}
		
		$( document.body ).trigger( 'post-load' ); 
		addClickEvents();
	};

})( jQuery );

function addClickEvents() {
	document.querySelectorAll(".resource-titles").forEach(item=>{
		var value = item.getAttribute("data-resource");

		item.addEventListener('click', event => {
			event.preventDefault();
			openCard(value);
		});
	});
}

function openCard(idNum){

	// Open resource card
	if ( cardOpen == null ) { //no card open - just open resource
		for ( target in values ) {
			if ( !values.hasOwnProperty(target)) continue;
	
			document.getElementsByName( target + idNum ).forEach(item=>{
				item.classList.add(values[target]);
			});

		}

		//load captcha for that resource
		grecaptcha.render(document.getElementById('captcha'+idNum), {
			'sitekey' : "6LcmxtQZAAAAABruElIt1ElI0FhjbYjiXJef9_0b"
		});


		cardOpen = idNum;
	}
	else if ( cardOpen == idNum ) { //clicked on open card - close it
		for ( target in values ) {
			if ( !values.hasOwnProperty(target)) continue;
	
			document.getElementsByName( target + idNum ).forEach(item=>{
				item.classList.remove(values[target]);
			});

		}

		cardOpen = null;
	}
	else { //other resource already open - open new one, close old
		for ( target in values ) {
			if ( !values.hasOwnProperty(target)) continue;
	
			document.getElementsByName( target + idNum ).forEach(item=>{
				item.classList.add(values[target]);
			});

			document.getElementsByName( target + cardOpen ).forEach(item=>{
				item.classList.remove(values[target]);
			});

		}
		//load captcha for that resource
		grecaptcha.render(document.getElementById('captcha'+idNum), {
			'sitekey' : "6LcmxtQZAAAAABruElIt1ElI0FhjbYjiXJef9_0b"
		});
		cardOpen = idNum;
	}

}

function formTabs(targetForm) {

	document.querySelectorAll(".formTab").forEach(item=>{
		//alert("clicked tab " + targetForm);

		if ( item.getAttribute("target-form") == targetForm ) {
			item.classList.add("w--current");
			//document.getElementById("form" + targetForm).classList.add("w--tab-active");
		}
		else {
			item.classList.remove("w--current");
			//document.getElementById("form" + targetForm).classList.remove("w--tab-active");
		}
		
	});
	document.querySelectorAll(".form").forEach(item=>{
		//alert("id is " + item.id);
		if( item.id == "form" + targetForm) {
			item.classList.add("w--tab-active");
		}
		else {
			item.classList.remove("w--tab-active");
		}
	})

}

function updateSelect() {
	var selected = $('input[name="categories"]:checked').map(function() {
		return $(this).val();
	  }).get().join(',');
	$.each(selected.split(","), function(i,e){
		$("#00N5x00000ENWBB option[id='" + e + "']").prop("selected", true);
	  });
}

var cardOpen = null;


var values = {
	"chevron" : "chevron-open",
	"description" : "description-open",
	"provider-card" : "provider-card-open",
	"expanding-container" : "resource-open",
	"list-2" : "stage-list-open",
	"hidden-stage" : "hidden-stage-open"
};