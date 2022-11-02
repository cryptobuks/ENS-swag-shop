import React from "react";
import update from 'immutability-helper';
import {__, sprintf} from "@wordpress/i18n";
import EmailRecipient from "./email-receipient";
import EmailRecipientModal from "./email-recipient-modal";
import Button from "../button";
import Notice from "../notice";
import NoticeUtil from "../utils/notice-util";
import FloatingNoticePlaceholder from "../floating-notice-placeholder";

export default class EmailRecipients extends React.Component {
	static defaultProps = {
		id: "",
		recipients: "",
		fieldName: ""
	};

	constructor(props) {
		super(props);

		this.state = {
			recipients: this.props.recipients,
			openDialog: false
		};
	}

	render() {
		const {id, fieldName} = this.props;
		const {recipients, openDialog} = this.state;

		return (<React.Fragment>
			<FloatingNoticePlaceholder id="wds-email-recipient-notice"/>
			{!recipients.length && (
				<Notice
					type="warning"
					message={__("You've removed all recipients. If you save without a recipient, we'll automatically turn off reports.", 'wds')}
				/>
			)}
			<div>
				{recipients.map((recipient, index) => (
					<EmailRecipient
						key={index}
						index={index}
						recipient={recipient}
						fieldName={fieldName}
						onRemove={index => this.handleRemove(index)}
					/>
				))}
			</div>

			<Button
				ghost={true}
				icon="sui-icon-plus"
				onClick={() => this.toggleModal()}
				text={__("Add Recipient", "wds")}
			/>
			{openDialog && (
				<EmailRecipientModal
					id={id}
					onSubmit={(name, email) => this.handleAdd(name, email)}
					onClose={() => this.toggleModal()}
				/>
			)}
		</React.Fragment>);
	}

	handleAdd(name, email) {
		this.setState({
			recipients: update([{
				name: name,
				email: email
			}], {
				$push: this.state.recipients
			}),
			openDialog: false
		}, () => {
			NoticeUtil.showInfoNotice(
				'wds-email-recipient-notice',
				sprintf(
					__('%s has been added as a recipient. Please save your changes to set this live.', 'wds'),
					name
				),
				false
			);
		});
	}

	handleRemove(index) {
		this.setState({recipients: update(this.state.recipients, {$splice: [[index, 1]]})});
	}

	toggleModal() {
		this.setState({openDialog: !this.state.openDialog})
	}
}
