import React from "react";
import {__} from "@wordpress/i18n";
import PostlistList from "../postlist/postlist-list";
import update from "immutability-helper";

export default class ExcludedPosts extends React.Component {
	static defaultProps = {
		optionName: "",
		exclusions: [],
		postTypes: [],
		nonce: ""
	};

	constructor(props) {
		super(props);

		this.state = {
			openDialog: false,
			exclusions: this.props.exclusions
		};
	}

	render() {
		const {optionName, postTypes, nonce} = this.props;
		const {exclusions} = this.state;

		return (
			<React.Fragment>
				<label className="sui-label">{__("Exclude Posts/Pages", "wds")}</label>

				<PostlistList
					items={exclusions}
					postTypes={postTypes}
					nonce={nonce}
					onRemove={(id) => this.handleRemove(id)}
					onUpdate={(ids) => this.handleUpdate(ids)}
				/>

				<input name={optionName + "[ignorepost]"} type="hidden" value={exclusions.join(",")}/>
			</React.Fragment>
		);
	}

	handleRemove(id) {
		const index = this.state.exclusions.indexOf(id);
		this.setState({exclusions: update(this.state.exclusions, {$splice: [[index, 1]]})});
	}

	handleUpdate(ids) {
		this.setState({exclusions: ids});
	}
}
