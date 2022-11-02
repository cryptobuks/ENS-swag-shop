import React from "react";
import {render} from "react-dom";
import domReady from "@wordpress/dom-ready";
import ErrorBoundary from "./components/error-boundry";
import EmailRecipients from "./components/email/email-recipients";
import Config_Values from "./es6/config-values";

domReady(() => {
	const recipientContainer = document.getElementById("wds-email-recipients");
	if (recipientContainer) {
		const id = Config_Values.get("id", "email_recipients");
		const recipients = Config_Values.get("recipients", "email_recipients");
		const fieldName = Config_Values.get("field_name", "email_recipients");

		render(
			<ErrorBoundary>
				<EmailRecipients
					id={id}
					recipients={recipients}
					fieldName={fieldName}
				/>
			</ErrorBoundary>,
			recipientContainer
		);
	}

});
