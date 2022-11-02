import React from "react";
import classnames from "classnames";

export default class FormField extends React.Component {
	static defaultProps = {
		label: '',
		description: '',
		errorMessage: '',
		isValid: true,
		isRequired: false,
		formControl: false,
	};

	render() {
		const {label, isRequired, errorMessage, description, isValid} = this.props;
		const FormControl = this.props.formControl;

		return <div className={classnames('sui-form-field', {
			'sui-form-field-error': !isValid
		})}>
			<label className="sui-label">
				{label} {isRequired && <span className="wds-required-asterisk">*</span>}
			</label>

			<FormControl {...this.props}/>

			{!isValid && !!errorMessage &&
			<span className="sui-error-message" role="alert">
				{errorMessage}
			</span>
			}

			{!!description &&
			<p className="sui-description">
				<small>{description}</small>
			</p>
			}
		</div>;
	}
}
