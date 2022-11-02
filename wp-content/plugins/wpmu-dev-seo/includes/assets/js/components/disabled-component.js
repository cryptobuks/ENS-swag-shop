import React from "react";
import {__} from "@wordpress/i18n";

export default class DisabledComponent extends React.Component {
	static defaultProps = {
		imagePath: false,
		message: '',
		button: false,
	};

	render() {
		return (
			<div className="sui-box sui-message sui-message-lg">
				<img src={this.props.imagePath}
					 className="sui-image"
					 aria-hidden="true" alt={__('Disabled component', 'wds')}/>

				<div className="sui-message-content">
					<p>{this.props.message}</p>
					<p>{this.props.button}</p>
				</div>
			</div>
		);
	}
}
