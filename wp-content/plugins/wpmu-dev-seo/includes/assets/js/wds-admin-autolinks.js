import ErrorBoundary from "./components/error-boundry";
import domReady from '@wordpress/dom-ready';
import {render} from "react-dom";
import React from "react";
import CustomKeywordPairs from "./components/autolinks/custom-keyword-pairs";
import Config_Values from "./es6/config-values";
import Redirects from "./components/redirects/redirects";
import WooSettingsTab from "./components/woocommerce/woo-settings-tab";
import ExcludedPosts from "./components/autolinks/excluded-posts";

domReady(() => {
	const pairsPlaceholder = document.getElementById('wds-custom-keyword-pairs');
	if (pairsPlaceholder) {
		const customKeywords = Config_Values.get('custom_keywords', 'autolinks') || '';
		render(<ErrorBoundary><CustomKeywordPairs data={customKeywords}/></ErrorBoundary>, pairsPlaceholder);
	}

	const redirectsContainer = document.getElementById('wds-redirects-container');
	if (redirectsContainer) {
		const redirects = Config_Values.get('redirects', 'redirects') || {};
		const nonce = Config_Values.get('nonce', 'redirects') || {};
		const homeUrl = Config_Values.get('home_url', 'redirects') || {};
		const csvTypes = Config_Values.get('accepted-csv-types', 'redirects') || [];
		render(<ErrorBoundary><Redirects redirects={redirects}
										 homeUrl={homeUrl}
										 nonce={nonce}
										 csvTypes={csvTypes}/></ErrorBoundary>, redirectsContainer);
	}

	const wooTab = document.getElementById('wds-woo-settings-tab');
	if (wooTab) {
		const options = Config_Values.get('options', 'woo') || {};
		const permalinkSettings = Config_Values.get('permalink_settings', 'woo');
		render(<ErrorBoundary><WooSettingsTab {...options}
											  permalinkSettingsUrl={permalinkSettings}
											  disabledImagePath={Config_Values.get('image_path', 'woo')}
											  nonce={Config_Values.get('nonce', 'woo')}/></ErrorBoundary>, wooTab);
	}

	const excludedPostsPlaceholder = document.getElementById('wds-excluded-posts');
	if (excludedPostsPlaceholder) {
		let exclusions = Config_Values.get("exclusions", "excluded_posts");

		exclusions = exclusions.split(",").filter(excl => !!excl).map(excl => parseInt(excl.trim()));

		let postTypes = Config_Values.get("post_types", "excluded_posts");

		for (const key in postTypes) {
			postTypes[key] = postTypes[key].label || postTypes[key].name;
		}

		render(
			<ErrorBoundary>
				<ExcludedPosts
					optionName={Config_Values.get('option_name', 'excluded_posts')}
					postTypes={Config_Values.get('post_types', 'excluded_posts')}
					exclusions={exclusions}
					nonce={Config_Values.get('nonce', 'excluded_posts')}
				/>
			</ErrorBoundary>,
			excludedPostsPlaceholder
		);
	}
});

;(function ($) {

	function submit_dialog_form_on_enter(e) {
		var $button = $(this).find('.wds-action-button'),
			key = e.which;

		if ($button.length && 13 === key) {
			e.preventDefault();
			e.stopPropagation();

			$button.trigger('click');
		}
	}

	function validate_moz_form(e) {
		var is_valid = true,
			$form = $(this),
			$submit_button = $('button[type="submit"]', $form);

		$('.sui-form-field', $form).each(function () {
			var $form_field = $(this),
				$input = $('input', $form_field);

			if (!$input.val().trim()) {
				is_valid = false;
				$form_field.addClass('sui-form-field-error');

				$input.on('focus keydown', function () {
					$(this).closest('.sui-form-field-error').removeClass('sui-form-field-error');
				});
			}
		});

		if (is_valid) {
			$submit_button.addClass('sui-button-onload');
		} else {
			$submit_button.removeClass('sui-button-onload');
			e.preventDefault();
		}
	}

	function adjust_robots_field_height() {
		let scrollHeight = this.scrollHeight;
		if (!scrollHeight && this.value.includes("\n")) {
			scrollHeight = (this.value.split("\n").length + 1) * 22;
		}
		this.style.height = "1px";
		this.style.height = scrollHeight + "px";
	}

	function open_add_redirect_form() {
		var query = new URLSearchParams(window.location.search);
		if (
			query.get('tab') === 'tab_url_redirection'
			&& query.get('add_redirect')
		) {
			$('button.wds-add-redirect').trigger('click');
		}
	}

	$(function () {
		$('.wds-vertical-tabs').on('wds_vertical_tabs:tab_change', function (event, active_tab) {
			$(active_tab)
				.find('.wds-vertical-tab-section')
				.removeClass('hidden');
		});

		$(document)
			.on('submit', '.wds-moz-form', validate_moz_form)
			.on('input propertychange', '.tab_robots_editor textarea', adjust_robots_field_height)
			.on('keydown', '.sui-modal', submit_dialog_form_on_enter);

		$('.tab_robots_editor textarea').each(function () {
			adjust_robots_field_height.apply(this);
		});
		window.Wds.link_dropdown();
		window.Wds.accordion();
		window.Wds.vertical_tabs();
		window.Wds.hook_toggleables();

		open_add_redirect_form();
	});

})(jQuery);
