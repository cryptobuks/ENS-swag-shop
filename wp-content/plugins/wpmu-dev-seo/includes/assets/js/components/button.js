import React from "react";
import classnames from "classnames";

export default class Button extends React.Component {
	static defaultProps = {
		id: "",
		text: "",
		color: "",
		dashed: false,
		icon: false,
		loading: false,
		ghost: false,
		disabled: false,
		href: "",
		target: "",
		className: "",
		onClick: () => false,
	}

	handleClick(e) {
		e.preventDefault();

		this.props.onClick();
	}

	render() {
		let HtmlTag, props;
		if (this.props.href) {
			HtmlTag = 'a';
			props = {href: this.props.href, target: this.props.target};
		} else {
			HtmlTag = 'button';
			props = {
				disabled: this.props.disabled,
				onClick: e => this.handleClick(e)
			};
		}
		const hasText = this.props.text && this.props.text.trim();

		return (
			<React.Fragment>
				<HtmlTag
					{...props}
					className={classnames(this.props.className, "sui-button-" + this.props.color, {
						"sui-button-onload": this.props.loading,
						"sui-button-ghost": this.props.ghost,
						"sui-button-icon": !hasText,
						"sui-button-dashed": this.props.dashed,
						"sui-button": hasText
					})}
					id={this.props.id}
				>
					{this.text()}
					{this.loadingIcon()}
				</HtmlTag>
			</React.Fragment>
		);
	}

	text() {
		const icon = this.props.icon ? <span className={this.props.icon} aria-hidden="true"/> : "";
		return (
			<span className={classnames({"sui-loading-text": this.props.loading})}>
				{icon} {this.props.text}
			</span>
		);
	}

	loadingIcon() {
		return this.props.loading
			? <span className="sui-icon-loader sui-loading" aria-hidden="true"/>
			: "";
	}
}
