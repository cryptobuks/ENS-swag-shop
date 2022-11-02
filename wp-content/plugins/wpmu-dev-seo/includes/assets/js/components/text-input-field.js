import React from "react";
import FormField from "./form-field";
import TextInput from "./text-input";

export default class TextInputField extends React.Component {
	render() {
		return <FormField
			{...this.props}
			formControl={TextInput}
		/>;
	}
}
