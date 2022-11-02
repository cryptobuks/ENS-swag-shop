import React from "react";

const fieldWithValidation = function (WrappedComponent, validator) {
	return class extends React.Component {
		static defaultProps = {
			value: '',
			validateOnInit: false,
			onChange: () => false,
		};

		constructor(props) {
			super(props);

			const {value, validateOnInit} = this.props;

			if (validateOnInit) {
				this.handleChange(value);
			} else {
				this.isValid = true;
				this.errorMessage = '';
			}
		}

		validateValue(value) {
			if (Array.isArray(validator)) {
				validator.some((_validator) => {
					this.isValid = this.runValidator(_validator, value);
					this.errorMessage = this.isValid ? '' : this.getErrorMessage(_validator);

					if (!this.isValid) {
						// No need to continue, we have an invalid value
						return true;
					}
				});
			} else {
				this.isValid = this.runValidator(validator, value);
				this.errorMessage = this.isValid ? '' : this.getErrorMessage(validator);
			}
		}

		getErrorMessage(validator) {
			let errorMessage = '';
			if (validator.getError instanceof Function) {
				errorMessage = validator.getError();
			}
			return errorMessage;
		}

		runValidator(validator, value) {
			let isValid;
			if (validator.isValid instanceof Function) {
				isValid = validator.isValid(value);
			} else if (validator instanceof Function) {
				isValid = validator(value);
			}

			return isValid;
		}

		handleChange(value) {
			this.validateValue(value);
			this.props.onChange(value, this.isValid);
		}

		render() {
			return <WrappedComponent {...this.props}
									 isValid={this.isValid}
									 errorMessage={this.errorMessage}
									 onChange={value => this.handleChange(value)}/>;
		}
	}
};

export default fieldWithValidation;
