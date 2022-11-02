import React from "react";
import FormField from "./form-field";
import Text from "./text";

export default class TextField extends React.Component {
	render() {
		return <FormField
			{...this.props}
			className="sui-form-control"
			formControl={Text}
		/>;
	}
}
