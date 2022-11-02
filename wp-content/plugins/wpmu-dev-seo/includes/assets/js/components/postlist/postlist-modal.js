import React from "react";
import {__, sprintf} from "@wordpress/i18n";
import Button from "../button";
import Modal from "../modal";
import SelectField from "../select-field";
import ajaxUrl from "ajaxUrl";
import update from "immutability-helper";

export default class PostlistModal extends React.Component {
	static defaultProps = {
		id: "",
		nonce: "",
		postTypes: [],
		onPostsUpdate: () => false,
		onSubmit: () => false,
		onClose: () => false
	};

	constructor(props) {
		super(props);

		this.state = {
			items: [],
			selectedType: Object.keys(this.props.postTypes)[0]
		};
	}

	render() {
		const {id, postTypes, onClose} = this.props;
		const {items, selectedType} = this.state;

		return (<Modal
			id={id}
			title={__("Add Exclusion", "wds")}
			description={__("Choose which post you want to exclude.", "wds")}
			small={true}
			onEnter={() => this.handleSubmit()}
			onClose={onClose}
			footer={
				<React.Fragment>
					<Button ghost={true} text={__("Cancel", "wds")} onClick={onClose}/>
					<div className="sui-actions-right">
						<Button text={__("Add", "wds")} onClick={() => this.handleSubmit()} disabled={!items.length}/>
					</div>
				</React.Fragment>
			}
		>
			<SelectField
				label={__("Type", "wds")}
				options={postTypes}
				selectedValue={selectedType}
				onSelect={(value) => this.handleChangeType(value)}
			/>
			<SelectField
				label={__("Post", "wds")}
				placeholder={__("Start typing to search ...", "wds")}
				selectedValue={items}
				multiple={true}
				tagging={true}
				ajaxUrl={() => this.getAjaxSearchUrl()}
				processResults={(data) => this.processResults(data)}
				onSelect={(values) => this.handleUpdateItems(values)}
			/>
		</Modal>);
	}

	handleSubmit() {
		if (this.props.onSubmit) {
			this.props.onSubmit(update(this.state.items, {$set: this.state.items}));
		}
	}

	handleChangeType(type) {
		this.setState({selectedType: type});
	}

	handleUpdateItems(items) {
		this.setState({items: items});
	}

	getAjaxSearchUrl() {
		if (!ajaxUrl) {
			return false;
		}

		return sprintf(
			"%s?action=wds-load_exclusion_posts-posts_data-paged&type=%s&_wds_nonce=%s",
			ajaxUrl,
			this.state.selectedType,
			this.props.nonce
		);
	}

	processResults(data) {
		const results = [],
			posts = [];

		data.posts.forEach((post) => {
			results.push({
				id: post.id,
				text: post.title
			});

			posts.push(post);
		});

		if (this.props.onPostsUpdate) {
			this.props.onPostsUpdate(posts);
		}

		return {
			results: results
		};
	}
}
