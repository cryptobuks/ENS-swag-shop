import React from "react";
import {__} from "@wordpress/i18n";
import Button from "../button";
import Modal from "../modal";
import TextInputField from "../text-input-field";
import {isEmailValid, isNonEmpty, isValuePlainText, Validator} from "../utils/validators";
import fieldWithValidation from "../field-with-validation";

const RecipientNameField = fieldWithValidation(TextInputField, [isNonEmpty, isValuePlainText]);
const recipientEmailValidator = new Validator(
	isEmailValid,
	__('Email is invalid.', 'wds')
);
const RecipientEmailField = fieldWithValidation(TextInputField, [isNonEmpty, isValuePlainText, recipientEmailValidator]);

export default class EmailRecipientModal extends React.Component {
	static defaultProps = {
		id: "",
		onSubmit: () => false,
		onClose: () => false
	};

	constructor(props) {
		super(props);

		this.state = {
			name: '',
			email: '',
			isNameValid: true,
			isEmailValid: true
		};
	}

	render() {
		const {id, onClose} = this.props;
		const {name, email, isNameValid, isEmailValid} = this.state;
		const disabled = !isNameValid || !isEmailValid;

		return (<Modal
			id={id}
			title={__("Add Recipient", "wds")}
			description={__("Add as many recipients as you like, they will receive email reports as per the schedule you set.", "wds")}
			small={true}
			onEnter={() => this.handleSubmit()}
			enterDisabled={disabled}
			onClose={onClose}
			footer={
				<React.Fragment>
					<Button
						className="wds-cancel-button" ghost={true} text={__('Cancel', 'wds')} onClick={onClose}
					/>
					<div className="sui-actions-right">
						<Button
							id="wds-add-email-recipient"
							text={__('Add', 'wds')}
							onClick={() => this.handleSubmit()}
							disabled={disabled}
						/>
					</div>
				</React.Fragment>
			}
		>
			<RecipientNameField
				id="wds-recipient-name"
				label={__('First name', 'wds')}
				placeholder={__('E.g. John', 'wds')}
				value={name}
				onChange={(value, isValid) => this.handleChangeName(value, isValid)}
				isValid={isNameValid}
			/>
			<RecipientEmailField
				id="wds-recipient-email"
				label={__('Email address', 'wds')}
				placeholder={__('E.g. john@doe.com', 'wds')}
				value={email}
				onChange={(value, isValid) => this.handleChangeEmail(value, isValid)}
				isValid={isEmailValid}
			/>
		</Modal>);
	}

	handleSubmit() {
		const {name, email} = this.state;

		this.props.onSubmit(name, email);
	}

	handleChangeName(value, isValid) {
		this.setState({
			name: value,
			isNameValid: isValid
		});
	}

	handleChangeEmail(value, isValid) {
		this.setState({
			email: value,
			isEmailValid: isValid
		})
	}
}
