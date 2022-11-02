import React from "react";

export default class TextInput extends React.Component {
	static defaultProps = {
		id: "",
		value: "",
		placeholder: "",
		disabled: false,
		onChange: () => false
	};

	render() {
		const {id, value, placeholder, disabled, onChange} = this.props;

		return <input id={id}
					  type="text"
					  className="sui-form-control"
					  onChange={(e) => onChange(e.target.value)}
					  value={value}
					  disabled={disabled}
					  placeholder={placeholder}/>;
	}
}
