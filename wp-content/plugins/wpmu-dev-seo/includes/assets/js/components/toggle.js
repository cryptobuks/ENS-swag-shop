import React from "react";
import {__, sprintf} from "@wordpress/i18n";
import classnames from "classnames";
import uniqueId from "lodash-es/uniqueId";

export default class Toggle extends React.Component {
	static defaultProps = {
		id: "",
		label: "",
		description: "",
		checked: false,
		disabled: false,
		children: null,
		onChange: () => false,
	};

	handleChange(e) {
		if (this.props.onChange) {
			this.props.onChange(e.target.checked, e.target);
		}
	}

	render() {
		if (this.props.label) {
			return <div className="sui-form-field">{this.inner()}</div>;
		}
		return this.inner();
	}

	inner() {
		const {label, description, children, checked, disabled} = this.props;
		const uniqId = uniqueId();
		let {id} = this.props;

		if (!id) {
			id = "wds-toggle-" + uniqId;
		}

		return (
			<React.Fragment>
				<label htmlFor={id} className="sui-toggle">
					<input
						type="checkbox"
						id={id}
						checked={checked}
						onChange={(e) => this.handleChange(e)}
						disabled={disabled}
						aria-labelledby={label && `sui-toggle-label-${uniqId}`}
						aria-describedby={description && `sui-toggle-description-${uniqId}`}
						aria-controls={children && `sui-toggle-children-${uniqId}`}
					/>

					<span className="sui-toggle-slider" aria-hidden="true"/>

					{label && (
						<span id={`sui-toggle-label-${uniqId}`} className="sui-toggle-label">
							{label}
						</span>
					)}

					{description && (
						<span id={`sui-toggle-description-${uniqId}`} className="sui-description">
							{description}
						</span>
					)}
				</label>
				{children && (
					<div
						id={`sui-toggle-children-${uniqId}`}
						className={classnames("sui-toggle-children", {
							"sui-hidden": !checked,
						})}
						aria-label={sprintf(
							/* translators: %s: toggle label. */
							__("Children of '%s'", "wds"),
							label,
						)}
					>
						{children}
					</div>
				)}
			</React.Fragment>
		);
	}
}
